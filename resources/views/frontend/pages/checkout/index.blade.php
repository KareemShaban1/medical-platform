@extends('frontend.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items Review -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Order Items</h2>
                
                @foreach($cart->items as $item)
                <div class="flex items-center gap-4 py-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                    <div class="w-16 h-16 flex-shrink-0">
                        @if($item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded">
                        @else
                        <div class="w-full h-full bg-gray-200 dark:bg-gray-700 rounded"></div>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->product->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 dark:text-white">${{ number_format($item->total, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Payment Method Selection -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Payment Method</h2>
                
                <div class="space-y-3">
                    <label class="flex items-center p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition">
                        <input type="radio" name="payment_method" value="0" class="w-4 h-4 text-blue-600" checked>
                        <div class="ml-3">
                            <div class="font-semibold text-gray-900 dark:text-white">Cash on Delivery (COD)</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Pay when you receive your order</div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition">
                        <input type="radio" name="payment_method" value="1" class="w-4 h-4 text-blue-600">
                        <div class="ml-3">
                            <div class="font-semibold text-gray-900 dark:text-white">Online Payment</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Pay securely online</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Order Summary</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Subtotal:</span>
                        <span>${{ number_format($cart->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Shipping:</span>
                        <span>${{ number_format($cart->shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Tax (14%):</span>
                        <span>${{ number_format($cart->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Discount:</span>
                        <span>-${{ number_format($cart->discount, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-300 dark:border-gray-600 pt-3 flex justify-between text-xl font-bold text-gray-900 dark:text-white">
                        <span>Total:</span>
                        <span>${{ number_format($cart->total, 2) }}</span>
                    </div>
                </div>

                <button type="button" id="place-order-btn" class="w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700">
                    Place Order
                </button>

                <a href="{{ route('cart.index') }}" class="block w-full text-center px-6 py-3 mt-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    Back to Cart
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const placeOrderBtn = document.getElementById('place-order-btn');

    placeOrderBtn.addEventListener('click', function() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        placeOrderBtn.disabled = true;
        placeOrderBtn.textContent = 'Processing...';

        fetch('{{ route("checkout.place-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                payment_method: paymentMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message);
                placeOrderBtn.disabled = false;
                placeOrderBtn.textContent = 'Place Order';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            placeOrderBtn.disabled = false;
            placeOrderBtn.textContent = 'Place Order';
        });
    });
});
</script>
@endpush
@endsection
