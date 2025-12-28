@extends('frontend.layouts.app')

@section('title' , __('Checkout'))
@section('content')
<div class="container mx-auto px-4 py-8">
	<h1 class="text-3xl font-bold mb-6 text-gray-900">{{ __('checkout') }}</h1>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Order Items Review -->
		<div class="lg:col-span-2">
			<div class="bg-white rounded-lg shadow-md p-6 mb-6">
				<h2 class="text-xl font-bold mb-4 text-gray-900">{{ __('order items') }}
				</h2>

				@foreach($cart->items as $item)
				<div
					class="flex items-center gap-4 py-3 border-b border-gray-200 last:border-0">
					<div class="w-16 h-16 flex-shrink-0">
						@if($item->product->image)
						<img src="{{ asset('storage/' . $item->product->image) }}"
							alt="{{ $item->product->name }}"
							class="w-full h-full object-cover rounded">
						@else
						<div class="w-full h-full bg-gray-200 rounded">
						</div>
						@endif
					</div>
					<div class="flex-grow">
						<h3 class="font-semibold text-gray-900">
							{{ $item->product->name }}</h3>
						<p class="text-sm text-gray-600">{{ __('qty') }}:
							{{ $item->quantity }} ×
							{{ __('EGP') }} {{ number_format($item->price, 2) }}</p>
					</div>
					<div class="text-right">
						<p class="font-bold text-gray-900">
							{{ __('EGP') }} {{ number_format($item->total, 2) }}</p>
					</div>
				</div>
				@endforeach
			</div>

			<!-- Payment Method Selection -->
			<div class="bg-white rounded-lg shadow-md p-6">
				<h2 class="text-xl font-bold mb-4 text-gray-900">{{ __('payment method') }}
				</h2>

				<div class="space-y-3">
					@foreach($availableGateways as $gateway)
					<label
						class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
						<input type="radio" name="payment_gateway"
							value="{{ $gateway['name'] }}"
							class="w-4 h-4 text-blue-600"
							{{ $loop->first ? 'checked' : '' }}>
						<div class="ml-3">
							<div class="font-semibold text-gray-900">
								{{ $gateway['display_name'] }}</div>
							<div class="text-sm text-gray-600">
								@if($gateway['name'] === 'cod')
								{{ __('pay when you receive your order') }}
								@else
								{{ __('pay securely online') }}
								@endif
							</div>
						</div>
					</label>
					@endforeach

					<!-- Paymob sub-options: Card or Wallet -->
					<div id="paymob-options" class="mt-3 hidden">
						<div class="flex items-center gap-4">
							<label
								class="flex items-center gap-2 cursor-pointer">
								<input type="radio" name="pay_method"
									value="card"
									class="text-blue-600" checked>
								<span
									class="text-sm text-gray-800">{{ __('card') }}</span>
							</label>
							<label
								class="flex items-center gap-2 cursor-pointer">
								<input type="radio" name="pay_method"
									value="wallet"
									class="text-blue-600">
								<span class="text-sm text-gray-800">{{ __('wallet') }}
								</span>
							</label>
						</div>
						<div id="wallet-phone-wrapper" class="mt-3 hidden">
							<label class="block text-sm text-gray-700 mb-1">{{ __('wallet phone') }}
								({{ __('egypt') }}:
								{{ __('01XXXXXXXXX') }})</label>
							<input type="tel" id="wallet-phone"
								placeholder="01XXXXXXXXX"
								class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" />
							<p class="text-xs text-gray-500 mt-1">
								{{ __('enter the wallet phone number starting with 01') }}
							</p>
						</div>
					</div>
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
						<span>{{ __('EGP') }} {{ number_format($cart->subtotal, 2) }}</span>
					</div>
					<div class="flex justify-between text-gray-700">
						<span>{{ __('shipping') }}:</span>
						<span>{{ __('EGP') }} {{ number_format($cart->shipping, 2) }}</span>
					</div>
					{{-- <div class="flex justify-between text-gray-700">
						<span>{{ __('tax') }}:</span>
						<span>{{ __('EGP') }} {{ number_format($cart->tax, 2) }}</span>
					</div> --}}
					<div class="flex justify-between text-gray-700">
						<span>{{ __('discount') }}:</span>
						<span>-{{ __('EGP') }} {{ number_format($cart->discount, 2) }}</span>
					</div>
					<div
						class="border-t border-gray-300 pt-3 flex justify-between text-xl font-bold text-gray-900">
						<span>{{ __('total') }}:</span>
						<span>{{ __('EGP') }} {{ number_format($cart->total, 2) }}</span>
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
	const paymobOptions = document.getElementById('paymob-options');
	const walletPhoneWrapper = document.getElementById('wallet-phone-wrapper');
	const walletPhoneInput = document.getElementById('wallet-phone');

	function updatePaymobUI() {
		const selectedGateway = document.querySelector(
			'input[name="payment_gateway"]:checked')?.value;
		if (selectedGateway === 'paymob') {
			paymobOptions.classList.remove('hidden');
			const payMethod = document.querySelector(
					'input[name="pay_method"]:checked')?.value ||
				'card';
			if (payMethod === 'wallet') {
				walletPhoneWrapper.classList.remove('hidden');
			} else {
				walletPhoneWrapper.classList.add('hidden');
			}
		} else {
			paymobOptions.classList.add('hidden');
			walletPhoneWrapper.classList.add('hidden');
		}
	}

	// Listen for changes
	document.querySelectorAll('input[name="payment_gateway"]').forEach(el => el
		.addEventListener('change', updatePaymobUI));
	document.addEventListener('change', (e) => {
		if (e.target && e.target.name === 'pay_method')
			updatePaymobUI();
	});

	// Initialize UI
	updatePaymobUI();

	placeOrderBtn.addEventListener('click', function() {
		const paymentGateway = document.querySelector(
				'input[name="payment_gateway"]:checked')
			.value;
		let payMethod = 'card';
		let walletPhone = null;
		if (paymentGateway === 'paymob') {
			payMethod = document.querySelector(
				'input[name="pay_method"]:checked'
			)?.value || 'card';
			if (payMethod === 'wallet') {
				walletPhone = walletPhoneInput.value
					.trim();
				if (!walletPhone) {
					alert(
						'Please enter wallet phone number'
					);
					return;
				}
			}
		}

		placeOrderBtn.disabled = true;
		placeOrderBtn.textContent = 'Processing...';

		fetch('{{ route("checkout.place-order") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': csrfToken
				},
				body: JSON.stringify({
					payment_gateway: paymentGateway,
					pay_method: payMethod,
					wallet_phone: walletPhone
				})
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// If payment requires redirect (online payment), redirect to payment page
					if (data.requires_payment &&
						data
						.redirect_url
					) {
						window.location
							.href =
							data
							.redirect_url;
					} else {
						// Otherwise, go to success page
						window.location
							.href =
							data
							.redirect_url;
					}
				} else {
					alert(data.message);
					placeOrderBtn
						.disabled =
						false;
					placeOrderBtn
						.textContent =
						'Place Order';
				}
			})
			.catch(error => {
				console.error('Error:',
					error);
				alert(
					'An error occurred. Please try again.'
				);
				placeOrderBtn.disabled =
					false;
				placeOrderBtn.textContent =
					'Place Order';
			});
	});
});
</script>
@endpush
@endsection
