@extends('frontend.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Success Message -->
        <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-500 rounded-lg p-8 text-center mb-6">
            <svg class="w-20 h-20 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h1 class="text-3xl font-bold text-green-700 dark:text-green-400 mb-2">Order Placed Successfully!</h1>
            <p class="text-gray-700 dark:text-gray-300">Thank you for your order. We'll process it shortly.</p>
        </div>

        <!-- Order Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Order Details</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Order Number</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $order->number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Order Date</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Payment Method</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $order->payment_method == 0 ? 'Cash on Delivery' : 'Online Payment' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Order Status</p>
                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                        {{ $order->status == 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                        {{ $order->status == 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <!-- Order Items -->
            <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Items Ordered</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex items-center gap-4 py-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                    <div class="w-16 h-16 flex-shrink-0">
                        @if($item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded">
                        @else
                        <div class="w-full h-full bg-gray-200 dark:bg-gray-700 rounded"></div>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $item->product->name }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 dark:text-white">${{ number_format($item->price * $item->quantity, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="mt-6 pt-6 border-t border-gray-300 dark:border-gray-600">
                <div class="space-y-2">
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Subtotal:</span>
                        <span>${{ number_format($order->total - $order->shipping - $order->tax + $order->discount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Shipping:</span>
                        <span>${{ number_format($order->shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Tax:</span>
                        <span>${{ number_format($order->tax, 2) }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Discount:</span>
                        <span>-${{ number_format($order->discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-300 dark:border-gray-600">
                        <span>Total:</span>
                        <span>${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <a href="{{ route('products') }}" class="flex-1 text-center px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800">
                Continue Shopping
            </a>
            <a href="{{ route('home') }}" class="flex-1 text-center px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                Go to Home
            </a>
        </div>
    </div>
</div>
@endsection
