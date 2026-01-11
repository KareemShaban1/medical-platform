@extends('frontend.layouts.app')

@section('title', __('Checkout'))
@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-900">{{ __('checkout') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Items Review -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">{{ __('order items') }}
                    </h2>

                    @foreach ($cart->items as $item)
                        <div class="flex items-center gap-4 py-3 border-b border-gray-200 last:border-0">
                            <div class="w-16 h-16 shrink-0">
                                @if ($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                        alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded">
                                @else
                                    <div class="w-full h-full bg-gray-200 rounded">
                                    </div>
                                @endif
                            </div>
                            <div class="grow">
                                <h3 class="font-semibold text-gray-900">
                                    {{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-600">{{ __('qty') }}:
                                    {{ $item->quantity }} ×
                                    {{ number_format($item->price, 2) }}
                                    {{ __('EGP') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">
                                    {{ number_format($item->total, 2) }}
                                    {{ __('EGP') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Shipping Information -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">
                        {{ __('Shipping Information') }}</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Shipping Address') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea id="shipping_address" name="shipping_address" rows="4" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('Enter your complete shipping address') }}">{{ old('shipping_address') }}</textarea>
                            <p id="shipping_address_error" class="text-sm text-red-600 mt-1 hidden"></p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('Please provide your complete address including street, building, floor, and apartment number') }}
                            </p>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Phone Number') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" required pattern="^01[0-2,5]{1}[0-9]{8}$"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="01XXXXXXXXX" value="{{ old('phone') }}">
                            <p id="phone_error" class="text-sm text-red-600 mt-1 hidden"></p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('Enter your Egyptian mobile number (e.g., 01XXXXXXXXX)') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">{{ __('payment method') }}
                    </h2>

                    <div class="space-y-3">
                        @foreach ($availableGateways as $gateway)
                            <label
                                class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="payment_gateway" value="{{ $gateway['name'] }}"
                                    class="w-4 h-4 text-blue-600" {{ $loop->first ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <div class="font-semibold text-gray-900">
                                        {{ $gateway['name'] === 'paymob' ? __('Online Payment') : $gateway['display_name'] }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        @if ($gateway['name'] === 'cod')
                                            {{ __('pay when you receive your order') }}
                                        @else
                                            {{ __('Pay securely online via Card or Mobile Wallet') }}
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">{{ __('order summary') }}
                    </h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-700">
                            <span>{{ __('subtotal') }}:</span>
                            <span>{{ number_format($cart->subtotal, 2) }} {{ __('EGP') }}
                            </span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>{{ __('shipping') }}:</span>
                            <span> {{ number_format($cart->shipping, 2) }} {{ __('EGP') }}
                            </span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>{{ __('discount') }}:</span>
                            <span> {{ number_format($cart->discount, 2) }} {{ __('EGP') }}</span>
                        </div>
                        <div class="border-t border-gray-300 pt-3 flex justify-between text-xl font-bold text-gray-900">
                            <span>{{ __('total') }}:</span>
                            <span>{{ number_format($cart->total, 2) }} {{ __('EGP') }} </span>
                        </div>
                    </div>

                    <button type="button" id="place-order-btn"
                        class="w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300">
                        {{ __('place order') }}
                    </button>

                    <a href="{{ route('cart.index') }}"
                        class="block w-full text-center px-6 py-3 mt-3 bg-gray-200 text-gray-900 font-semibold rounded-lg hover:bg-gray-300">
                        {{ __('back to cart') }}
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
                const shippingAddressError = document.getElementById('shipping_address_error');
                const phoneError = document.getElementById('phone_error');

                function resetFieldErrors() {
                    shippingAddressError.textContent = '';
                    shippingAddressError.classList.add('hidden');
                    phoneError.textContent = '';
                    phoneError.classList.add('hidden');
                    document.getElementById('shipping_address').classList.remove('border-red-500');
                    document.getElementById('phone').classList.remove('border-red-500');
                }

                placeOrderBtn.addEventListener('click', function() {
                    resetFieldErrors();

                    // Validate shipping information
                    const shippingAddress = document.getElementById('shipping_address').value.trim();
                    const phone = document.getElementById('phone').value.trim();

                    if (!shippingAddress || shippingAddress.length < 10) {
                        shippingAddressError.textContent =
                            '{{ __('Please enter a valid shipping address (at least 10 characters)') }}';
                        shippingAddressError.classList.remove('hidden');
                        document.getElementById('shipping_address').classList.add('border-red-500');
                        document.getElementById('shipping_address').focus();
                        return;
                    }

                    if (!phone || !/^01[0-2,5]{1}[0-9]{8}$/.test(phone)) {
                        phoneError.textContent =
                            '{{ __('Please enter a valid Egyptian mobile number (e.g., 01XXXXXXXXX)') }}';
                        phoneError.classList.remove('hidden');
                        document.getElementById('phone').classList.add('border-red-500');
                        document.getElementById('phone').focus();
                        return;
                    }

                    const paymentGateway = document.querySelector('input[name="payment_gateway"]:checked')
                    .value;

                    placeOrderBtn.disabled = true;
                    placeOrderBtn.textContent = '{{ __('Processing...') }}';

                    fetch('{{ route('checkout.place-order') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                payment_gateway: paymentGateway,
                                shipping_address: shippingAddress,
                                phone: phone
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Redirect to payment page or success page
                                window.location.href = data.redirect_url;
                            } else {
                                if (typeof toast_error === 'function') {
                                    toast_error(data.message);
                                } else {
                                    Swal.fire('{{ __('Error') }}', data.message, 'error');
                                }
                                placeOrderBtn.disabled = false;
                                placeOrderBtn.textContent = '{{ __('Place Order') }}';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            if (typeof toast_error === 'function') {
                                toast_error('{{ __('An error occurred. Please try again.') }}');
                            } else {
                                Swal.fire('{{ __('Error') }}',
                                    '{{ __('An error occurred. Please try again.') }}', 'error');
                            }
                            placeOrderBtn.disabled = false;
                            placeOrderBtn.textContent = '{{ __('Place Order') }}';
                        });
                });
            });
        </script>
    @endpush
@endsection
