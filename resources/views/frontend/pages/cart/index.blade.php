@extends('frontend.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Shopping Cart</h1>

    @if($cart && $cart->items->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                @foreach($cart->items as $item)
                <div class="cart-item p-4 border-b border-gray-200 dark:border-gray-700" data-item-id="{{ $item->id }}">
                    <div class="flex items-center gap-4">
                        <!-- Product Image -->
                        <div class="w-20 h-20 flex-shrink-0">
                            @if($item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded">
                            @else
                            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            @endif
                        </div>

                        <!-- Product Details -->
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $item->product->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Supplier: {{ $item->supplier->name ?? 'N/A' }}</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">${{ number_format($item->price, 2) }}</p>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-2">
                            <button type="button" class="decrease-qty px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                -
                            </button>
                            <input type="number" class="item-quantity w-16 text-center border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" value="{{ $item->quantity }}" min="1">
                            <button type="button" class="increase-qty px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                +
                            </button>
                        </div>

                        <!-- Item Total -->
                        <div class="text-right min-w-[100px]">
                            <p class="text-lg font-bold text-gray-900 dark:text-white item-total">${{ number_format($item->total, 2) }}</p>
                        </div>

                        <!-- Remove Button -->
                        <button type="button" class="remove-item text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Order Summary</h2>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">${{ number_format($cart->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Shipping:</span>
                        <span id="cart-shipping">${{ number_format($cart->shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Tax (14%):</span>
                        <span id="cart-tax">${{ number_format($cart->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Discount:</span>
                        <span id="cart-discount">-${{ number_format($cart->discount, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-300 dark:border-gray-600 pt-3 flex justify-between text-xl font-bold text-gray-900 dark:text-white">
                        <span>Total:</span>
                        <span id="cart-total">${{ number_format($cart->total, 2) }}</span>
                    </div>
                </div>

                <a href="{{ route('checkout.index') }}" class="block w-full text-center px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Proceed to Checkout
                </a>

                <button type="button" id="clear-cart" class="block w-full text-center px-6 py-3 mt-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300">
                    Clear Cart
                </button>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
        <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Your cart is empty</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Add some products to get started</p>
        <a href="{{ route('products') }}" class="inline-block px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800">
            Browse Products
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Update quantity
    document.querySelectorAll('.increase-qty, .decrease-qty').forEach(button => {
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const itemId = cartItem.dataset.itemId;
            const qtyInput = cartItem.querySelector('.item-quantity');
            let quantity = parseInt(qtyInput.value);

            if (this.classList.contains('increase-qty')) {
                quantity++;
            } else if (this.classList.contains('decrease-qty') && quantity > 1) {
                quantity--;
            }

            qtyInput.value = quantity;
            updateCartItem(itemId, quantity, cartItem);
        });
    });

    // Manual quantity input
    document.querySelectorAll('.item-quantity').forEach(input => {
        input.addEventListener('change', function() {
            const cartItem = this.closest('.cart-item');
            const itemId = cartItem.dataset.itemId;
            let quantity = parseInt(this.value);
            
            if (quantity < 1) quantity = 1;
            this.value = quantity;
            
            updateCartItem(itemId, quantity, cartItem);
        });
    });

    // Remove item
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item?')) {
                const cartItem = this.closest('.cart-item');
                const itemId = cartItem.dataset.itemId;
                removeCartItem(itemId, cartItem);
            }
        });
    });

    // Clear cart
    document.getElementById('clear-cart')?.addEventListener('click', function() {
        if (confirm('Are you sure you want to clear your cart?')) {
            clearCart();
        }
    });

    function updateCartItem(itemId, quantity, cartItem) {
        fetch(`/cart/update/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cartItem.querySelector('.item-total').textContent = '$' + parseFloat(data.item_total).toFixed(2);
                document.getElementById('cart-total').textContent = '$' + parseFloat(data.cart_total).toFixed(2);
                location.reload(); // Reload to update all totals
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function removeCartItem(itemId, cartItem) {
        fetch(`/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cartItem.remove();
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function clearCart() {
        fetch('/cart/clear', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>
@endpush
@endsection
