<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\PaymentGateway;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Checkout;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Admin;
use App\Models\OrderSupplier;
use App\Notifications\Admin\NewOrderPlacedNotification;
use App\Notifications\Supplier\NewOrderNotification;
use App\PaymentGateways\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        protected PaymentGatewayManager $paymentGatewayManager
    ) {}

    /**
     * Display the checkout page.
     */
    public function index()
    {
        $user = Auth::guard('clinic')->user();
        $cart = Cart::where('clinic_user_id', $user->id)
            ->where('clinic_id', $user->clinic_id)
            ->with(['items.product', 'items.supplier'])
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $availableGateways = $this->paymentGatewayManager->getAvailableGateways();

        return view('frontend.pages.checkout.index', compact('cart', 'availableGateways'));
    }

    /**
     * Process the checkout and create order.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_gateway' => 'required|string|in:'.implode(',', PaymentGateway::values()),
            'pay_method' => 'nullable|string|in:card,wallet',
            'wallet_phone' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::guard('clinic')->user();

            // Get cart
            $cart = Cart::where('clinic_user_id', $user->id)
                ->where('clinic_id', $user->clinic_id)
                ->with(['items.product' => function ($q) {
                    $q->lockForUpdate(); // lock stock for safe concurrent updates
                }, 'items.supplier'])
                ->first();

            if (! $cart || $cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty',
                ], 400);
            }

            // validate stock
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                if (! $product || ! $product->active()) {
                    throw new \Exception("Product {$product->name} is unavailable or inactive.");
                }

                if ($product->stock < $cartItem->quantity) {
                    throw new \Exception("Not enough stock for {$product->name}.");
                }
            }

            // Get payment gateway
            $gatewayName = $request->payment_gateway;
            $gateway = $this->paymentGatewayManager->gateway($gatewayName);
            $payMethod = $request->input('pay_method', 'card');
            $walletPhone = $request->input('wallet_phone');

            if (! $gateway->isEnabled()) {
                throw new \Exception("Payment gateway '{$gatewayName}' is not enabled.");
            }

            // For online payment gateways, process payment first before creating order
            // For COD, create order immediately
            $isOnlinePayment = $gatewayName !== 'cod';

            if ($isOnlinePayment) {
                // If paymob wallet selected, require wallet phone
                if ($gatewayName === 'paymob' && $payMethod === 'wallet' && empty($walletPhone)) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Wallet phone is required for wallet payments',
                    ], 422);
                }
                // Prepare payment data (without order ID yet)
                $nameParts = explode(' ', $user->name ?? 'Customer', 2);
                $firstName = $nameParts[0] ?? 'Customer';
                $lastName = $nameParts[1] ?? 'Name';

                $orderNumber = Order::generateOrderNumber();

                $paymentData = [
                    'amount' => $cart->total,
                    'order_id' => null, // Will be set after order creation
                    'order_number' => $orderNumber,
                    'currency' => 'EGP',
                    'method' => $payMethod,
                    'wallet_phone' => $walletPhone,
                    'customer' => [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $user->email ?? 'customer@example.com',
                        'phone' => $user->phone ?? '01000000000',
                        'city' => 'Cairo',
                        'country' => 'EG',
                        'street' => $user->clinic->address ?? 'NA',
                        'building' => 'NA',
                        'apartment' => 'NA',
                        'floor' => 'NA',
                        'postal_code' => 'NA',
                        'state' => 'NA',
                    ],
                ];

                // Process payment first - if this fails, don't create order
                $paymentResponse = $gateway->processPayment($paymentData);

                if (! $paymentResponse->success) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => $paymentResponse->message,
                    ], 400);
                }
            }

            // Create checkout record
            $checkout = Checkout::create([
                'cart_id' => $cart->id,
                'clinic_user_id' => $user->id,
                'clinic_id' => $user->clinic_id,
                'payment_method' => $gatewayName === 'cod' ? 0 : 1,
                'subtotal' => $cart->subtotal,
                'shipping' => $cart->shipping,
                'tax' => $cart->tax,
                'discount' => $cart->discount,
                'total' => $cart->total,
            ]);

            $orderNumber = $orderNumber ?? Order::generateOrderNumber();

            // Create order
            $order = Order::create([
                'clinic_user_id' => $user->id,
                'clinic_id' => $user->clinic_id,
                'number' => $orderNumber,
                'status' => 'pending',
                'total' => $cart->total,
                'shipping' => $cart->shipping,
                'tax' => $cart->tax,
                'discount' => $cart->discount,
                'payment_method' => $gatewayName === 'cod' ? 0 : 1,
                'payment_status' => 'pending',
                'payment_gateway' => $gatewayName,
            ]);

            // Create order items from cart items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'supplier_id' => $cartItem->supplier_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'status' => 'pending',
                ]);

                // update product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }
            // Create per-supplier summary rows for this order
            $itemsBySupplier = $order->items()->get()->groupBy('supplier_id');
            foreach ($itemsBySupplier as $supplierId => $items) {
                if (!$supplierId) {
                    continue;
                }
                $subtotal = $items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
                OrderSupplier::create([
                    'order_id' => $order->id,
                    'supplier_id' => $supplierId,
                    'subtotal' => $subtotal,
                    'status' => 'pending',
                ]);
            }

            // Link checkout to order
            $checkout->update(['order_id' => $order->id]);

            // Process payment for COD or get payment response for online gateways
            if ($isOnlinePayment) {
                // Payment already processed above, use the response
                // Update order with transaction ID if available
                if ($paymentResponse->transactionId) {
                    $order->update(['transaction_id' => $paymentResponse->transactionId]);
                }

                // Store order ID in session for return handler
                session()->put('payment_order_id', $order->id);
            } else {
                // For COD, process payment now
                $nameParts = explode(' ', $user->name ?? 'Customer', 2);
                $firstName = $nameParts[0] ?? 'Customer';
                $lastName = $nameParts[1] ?? 'Name';

                $paymentData = [
                    'amount' => $cart->total,
                    'order_id' => $order->id,
                    'order_number' => $orderNumber,
                    'currency' => 'EGP',
                    'customer' => [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $user->email ?? 'customer@example.com',
                        'phone' => $user->phone ?? '01000000000',
                        'city' => 'Cairo',
                        'country' => 'EG',
                        'street' => $user->clinic->address ?? 'NA',
                        'building' => 'NA',
                        'apartment' => 'NA',
                        'floor' => 'NA',
                        'postal_code' => 'NA',
                        'state' => 'NA',
                    ],
                ];

                $paymentResponse = $gateway->processPayment($paymentData);

                if (! $paymentResponse->success) {
                    throw new \Exception($paymentResponse->message);
                }

                // Mark checkout as paid for COD
                $checkout->update(['status' => 'paid']);
            }

            // Clear cart items only for COD (online payments will clear after payment confirmation)
            if ($gatewayName === 'cod') {
                $cart->items()->delete();
                $cart->calculateTotals();
            }

            DB::commit();

            $order->load(['clinic', 'items.supplier.supplierUsers']);

            $itemsBySupplier = $order->items->groupBy('supplier_id');
            foreach ($itemsBySupplier as $supplierId => $items) {
                $supplier = $items->first()->supplier;
                if (! $supplier) {
                    continue;
                }

                $supplierTotal = $items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });

                foreach ($supplier->supplierUsers as $supplierUser) {
                    $supplierUser->notify(new NewOrderNotification($order, $supplierTotal));
                }
            }

            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new NewOrderPlacedNotification($order));
            }

            // Return response with redirect URL if available (for online payments)
            $redirectUrl = $paymentResponse->redirectUrl ?? route('checkout.success', ['order' => $order->id]);

            Log::info('Payment return response', [
                'redirect_url' => $redirectUrl,
                'order_number' => $orderNumber,
                'order_id' => $order->id,
                'requires_payment' => ! empty($paymentResponse->redirectUrl),
            ]);

            return response()->json([
                'success' => true,
                'message' => $paymentResponse->message,
                'order_number' => $orderNumber,
                'order_id' => $order->id,
                'redirect_url' => $redirectUrl,
                'requires_payment' => ! empty($paymentResponse->redirectUrl),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display order success page.
     */
    public function success($orderId)
    {
        $user = Auth::guard('clinic')->user();
        $order = Order::where('id', $orderId)
            ->where('clinic_user_id', $user->id)
            ->with(['items.product', 'items.supplier'])
            ->firstOrFail();

        return view('frontend.pages.checkout.success', compact('order'));
    }

    /**
     * Display order failed page.
     */
    public function failed()
    {
        return view('frontend.pages.checkout.failed');
    }


}
