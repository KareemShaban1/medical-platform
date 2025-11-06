<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\PaymentGateway;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Checkout;
use App\Models\Order;
use App\Models\OrderItem;
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
     * Handle payment gateway return (user redirect after payment)
     */
    public function paymentReturn(Request $request, string $gateway)
    {
        try {
            // Get all request data (query params or POST)
            $paymentData = $request->all();


            // For Paymob, get order ID from query or session
            // $orderId = $request->get('order_id') ?? session()->get('payment_order_id');
            $orderNumber = $request->get('merchant_order_id') ?? session()->get('payment_order_number');


            Log::info('Payment return order id', [
                'order_number' => $orderNumber,
            ]);

            if (! $orderNumber) {
                return redirect()->route('checkout.failed')
                    ->with('error', 'Order not found');
            }

                $order = Order::where('number', $orderNumber)->first();

            Log::info('Payment return order', [
                'order' => $order,
            ]);

            if (! $order) {
                return redirect()->route('checkout.failed')
                    ->with('error', 'Order not found');
            }

            // Verify payment with gateway
            $gatewayInstance = $this->paymentGatewayManager->gateway($gateway);

            // For Paymob, check if payment was successful
            $success = $request->get('success') === 'true' || $request->get('success') === true;

            if ($gateway === 'paymob') {
                // Paymob returns data in query params after payment
                // Check for various possible success indicators
                $success = $request->get('success') === 'true'
                    || $request->get('success') === true
                    || $request->get('success') === '1'
                    || $request->get('txn_response_code') === 'APPROVED';

                // Get transaction ID from various possible fields
                $transactionId = $request->get('id')
                    ?? $request->get('transaction_id')
                    ?? $request->get('obj.id')
                    ?? null;

                // Log the request for debugging
                Log::info('Paymob return request', [
                    'all_params' => $request->all(),
                    'success' => $success,
                    'transaction_id' => $transactionId,
                    'order_number' => $orderNumber,
                ]);

                // Try to verify payment if we have the data
                try {
                    $paymentResponse = $gatewayInstance->verifyPayment($paymentData);

                    if ($paymentResponse->success) {
                        $order->update([
                            'payment_status' => 'paid',
                            'transaction_id' => $paymentResponse->transactionId ?? $transactionId,
                        ]);

                        // Clear cart after successful payment confirmation
                        $cart = Cart::where('clinic_user_id', $order->clinic_user_id)
                            ->where('clinic_id', $order->clinic_id)
                            ->first();
                        if ($cart) {
                            $cart->items()->delete();
                            $cart->calculateTotals();
                        }

                        session()->forget('payment_order_id');

                        return redirect()->route('checkout.success', ['order' => $order->id])
                            ->with('success', 'Payment processed successfully');
                    }
                } catch (\Exception $e) {
                    Log::warning('Paymob verification failed, but checking success flag', [
                        'error' => $e->getMessage(),
                        'success_flag' => $success,
                    ]);
                }

                // If verification failed but we have success flag, still update order
                if ($success && $transactionId) {
                    $order->update([
                        'payment_status' => 'paid',
                        'transaction_id' => $transactionId,
                    ]);

                    // Clear cart after successful payment confirmation
                    $cart = Cart::where('clinic_user_id', $order->clinic_user_id)
                        ->where('clinic_id', $order->clinic_id)
                        ->first();
                    if ($cart) {
                        $cart->items()->delete();
                        $cart->calculateTotals();
                    }

                    session()->forget('payment_order_id');

                    return redirect()->route('checkout.success', ['order' => $order->id])
                        ->with('success', 'Payment processed successfully');
                }

                // If payment failed, restore stock and mark order as failed
                $order->update([
                    'payment_status' => 'failed',
                ]);

                // Restore stock for failed payment
                foreach ($order->items as $orderItem) {
                    $product = $orderItem->product;
                    if ($product) {
                        $product->increment('stock', $orderItem->quantity);
                    }
                }

                session()->forget('payment_order_id');

                return redirect()->route('checkout.failed')
                    ->with('error', 'Payment failed. Please try again.');
            }

            // For other gateways, use verifyPayment
            $paymentResponse = $gatewayInstance->verifyPayment($paymentData);

            if ($paymentResponse->success) {
                $order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $paymentResponse->transactionId,
                ]);

                return redirect()->route('checkout.success', ['order' => $order->id])
                    ->with('success', 'Payment processed successfully');
            }

            $order->update([
                'payment_status' => 'failed',
            ]);

            return redirect()->route('checkout.failed')
                ->with('error', $paymentResponse->message ?? 'Payment failed');

        } catch (\Exception $e) {
            Log::error('Payment return error: '.$e->getMessage(), [
                'gateway' => $gateway,
                'request' => $request->all(),
            ]);

            return redirect()->route('checkout.failed')
                ->with('error', 'Payment verification failed');
        }
    }

    /**
     * Handle payment gateway callback/webhook
     */
    public function paymentCallback(Request $request, string $gateway)
    {
        try {
            $gatewayInstance = $this->paymentGatewayManager->gateway($gateway);
            $paymentResponse = $gatewayInstance->verifyPayment($request->all());

            // Find order by transaction ID or order number
            $order = null;
            if ($paymentResponse->transactionId) {
                $order = Order::where('transaction_id', $paymentResponse->transactionId)
                    ->orWhere('payment_gateway', $gateway)
                    ->first();
            }

            if ($order) {
                if ($paymentResponse->success) {
                    $order->update([
                        'payment_status' => 'paid',
                    ]);
                } else {
                    $order->update([
                        'payment_status' => 'failed',
                    ]);
                }
            }

            // For Paymob, return success response
            if ($gateway === 'paymob') {
                return response()->json(['success' => true]);
            }

            // Redirect to success page if payment successful
            if ($paymentResponse->success && $order) {
                return redirect()->route('checkout.success', ['order' => $order->id])
                    ->with('success', 'Payment processed successfully');
            }

            return redirect()->route('checkout.index')
                ->with('error', $paymentResponse->message);
        } catch (\Exception $e) {
            Log::error('Payment callback error: '.$e->getMessage(), [
                'gateway' => $gateway,
                'request' => $request->all(),
            ]);

            return redirect()->route('checkout.index')
                ->with('error', 'Payment verification failed');
        }
    }
}