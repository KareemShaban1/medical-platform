<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.product', 'items.supplier']);
        
        return view('frontend.pages.cart.index', compact('cart'));
    }

    /**
     * Get cart data for AJAX requests (dropdown).
     */
    public function getData()
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.product', 'items.supplier']);
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'N/A',
                    'product_image' => $item->product->image ?? null,
                    'supplier_name' => $item->supplier->name ?? 'N/A',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'tax' => $item->tax,
                    'shipping' => $item->shipping,
                    'total' => $item->total,
                ];
            }),
            'total_items' => $cart->total_items,
            'subtotal' => $cart->subtotal,
            'total' => $cart->total,
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $cart = $this->getOrCreateCart();
            $product = Product::findOrFail($request->product_id);

            // Check if item already exists in cart
            $cartItem = $cart->items()
                ->where('product_id', $request->product_id)
                ->where('supplier_id', $request->supplier_id)
                ->first();

            if ($cartItem) {
                // Update quantity
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
                $cartItem->calculateTotal();
            } else {
                // Create new cart item
                $cartItem = $cart->items()->create([
                    'product_id' => $request->product_id,
                    'supplier_id' => $request->supplier_id,
                    'quantity' => $request->quantity,
                    'price' => $product->price_after,
                    'tax' => $product->tax,
                    'shipping' => $product->shipping,
                ]);
                $cartItem->calculateTotal();
            }

            $cart->calculateTotals();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => $cart->total_items,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $cart = $this->getOrCreateCart();
            $cartItem = $cart->items()->findOrFail($itemId);

            $cartItem->quantity = $request->quantity;
            $cartItem->save();
            $cartItem->calculateTotal();

            $cart->calculateTotals();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'item_total' => $cartItem->total,
                'cart_total' => $cart->total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function remove($itemId)
    {
        try {
            DB::beginTransaction();

            $cart = $this->getOrCreateCart();
            $cartItem = $cart->items()->findOrFail($itemId);
            $cartItem->delete();

            $cart->calculateTotals();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $cart->total_items,
                'cart_total' => $cart->total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        try {
            $cart = $this->getOrCreateCart();
            $cart->items()->delete();
            $cart->calculateTotals();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get or create a cart for the authenticated clinic user.
     */
    private function getOrCreateCart(): Cart
    {
        $user = Auth::guard('clinic')->user();
        
        return Cart::firstOrCreate(
            [
                'clinic_user_id' => $user->id,
                'clinic_id' => $user->clinic_id,
            ],
            [
                'subtotal' => 0,
                'shipping' => 0,
                'tax' => 0,
                'discount' => 0,
                'total' => 0,
            ]
        );
    }
}
