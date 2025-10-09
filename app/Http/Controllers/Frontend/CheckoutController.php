<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Checkout;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
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

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        return view('frontend.pages.checkout.index', compact('cart'));
    }

    /**
     * Process the checkout and create order.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:0,1', // 0 = COD, 1 = Online
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

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty',
                ], 400);
            }

            // validate stock
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                if (!$product || !$product->active()) {
                    throw new \Exception("Product {$product->name} is unavailable or inactive.");
                }

                if ($product->stock < $cartItem->quantity) {
                    throw new \Exception("Not enough stock for {$product->name}.");
                }
            }

            // Create checkout record
            $checkout = Checkout::create([
                'cart_id' => $cart->id,
                'clinic_user_id' => $user->id,
                'clinic_id' => $user->clinic_id,
                'payment_method' => $request->payment_method,
                'subtotal' => $cart->subtotal,
                'shipping' => $cart->shipping,
                'tax' => $cart->tax,
                'discount' => $cart->discount,
                'total' => $cart->total,
            ]);

            $orderNumber = Order::generateOrderNumber();

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
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method == 0 ? 'pending' : 'pending',
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

            // Update checkout status
            if ($request->payment_method == 0) {
                // COD - mark as paid status for checkout
                $checkout->update([
                    'status' => 'paid',
                ]);
            }

            // Clear cart items
            $cart->items()->delete();
            $cart->calculateTotals();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_number' => $orderNumber,
                'order_id' => $order->id,
                'redirect_url' => route('checkout.success', ['order' => $order->id]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage(),
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
}