@if($plans->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
	@foreach($plans as $plan)
	<div
		class="plan-card {{ $plan->level === 'advanced' ? 'featured' : '' }} bg-white rounded-2xl p-8 shadow-lg flex flex-col h-full">
		<div class="text-center mb-6">
			<h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
			<div class="price-tag">
				<span style="font-size: 2.5rem">EGP {{ number_format($plan->price, 2) }}</span>
				@if($plan->duration_in_days)
				<span
					class="text-lg text-gray-500">/{{ round($plan->duration_in_days / 30) }}mo</span>
				@else
				<span class="text-lg text-gray-500">/{{ __('lifetime') }}</span>
				@endif
			</div>
			@if($plan->description)
			<p class="text-gray-600 mt-4">{{ $plan->description }}</p>
			@endif
		</div>

		<ul class="space-y-3 mb-8">
			@foreach($plan->planFeatures as $planFeature)
			@if($planFeature->is_enabled)
			<li class="feature-item">
				<i class="fas fa-check-circle"></i>
				<span>
					{{ $planFeature->feature->name }}
					@if($planFeature->is_limited && $planFeature->value)
					<strong>({{ $planFeature->value }})</strong>
					@elseif(!$planFeature->is_limited)
					<span
						class="inline-flex items-center gap-1 text-green-700 text-sm font-semibold">
						&infin;
					</span>
					@endif
				</span>
			</li>
			@endif
			@endforeach
		</ul>

		<div class="mt-auto">
			@if(Auth::guard('clinic')->check() || Auth::guard('supplier')->check())
			@php
			$isSubscribed = $currentSubscription && $currentSubscription->plan_id === $plan->id &&
			$currentSubscription->isActive();
			@endphp
			@if($isSubscribed)
			<button class="w-full py-3 bg-green-100 text-green-700 rounded-lg font-semibold" disabled>
				<i class="fas fa-check-circle mr-2"></i>{{ __('Current Plan') }}
			</button>
			@else
			<button onclick="subscribeToPlan({{ $plan->id }}, '{{ $plan->level }}', {{ $plan->price }}, this)"
				class="w-full py-3 bg-primary-gradient text-white rounded-lg font-semibold hover:opacity-90 transition">
				{{ __('Subscribe Now') }}
			</button>
			@endif
			@else
			<a href="{{ url('/clinic/login') }}"
				class="block w-full text-center py-3 bg-primary-gradient text-white rounded-lg font-semibold hover:opacity-90 transition">
				{{ __('Login to Subscribe') }}
			</a>
			@endif
		</div>
	</div>
	@endforeach
</div>
@else
<div class="text-center py-12">
	<i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
	<p class="text-xl text-gray-600">{{ __('No plans available at the moment') }}</p>
</div>
@endif

<div id="subscription-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
	<div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4">
		<div class="flex items-center justify-between px-6 py-4 border-b">
			<h3 id="subscription-modal-title" class="text-xl font-bold text-gray-900"></h3>
			<button type="button" id="subscription-modal-close" class="text-gray-400 hover:text-gray-600">
				<i class="fas fa-times"></i>
			</button>
		</div>
		<div class="px-6 py-5 space-y-4">
			<div id="subscription-modal-message" class="text-gray-600"></div>

			<div id="subscription-price-summary" class="hidden rounded-xl border border-gray-200 bg-gray-50 p-4">
				<div class="flex items-center justify-between text-sm text-gray-600">
					<span>{{ __('Current plan price') }}</span>
					<span id="subscription-current-price" class="font-semibold text-gray-900"></span>
				</div>
				<div class="flex items-center justify-between text-sm text-gray-600 mt-2">
					<span>{{ __('New plan price') }}</span>
					<span id="subscription-target-price" class="font-semibold text-gray-900"></span>
				</div>
				<div class="flex items-center justify-between text-sm text-gray-600 mt-2">
					<span>{{ __('You will pay the difference') }}</span>
					<span id="subscription-diff-price" class="font-semibold text-gray-900"></span>
				</div>
				<p id="subscription-no-payment" class="hidden text-sm font-semibold text-emerald-600 mt-2">
					{{ __('No payment required for this upgrade.') }}
				</p>
			</div>

			<div id="subscription-payment-section" class="space-y-3">
				<label class="block text-sm font-semibold text-gray-700">{{ __('Select Payment Method') }}</label>
				<div id="subscription-payment-methods" class="space-y-2"></div>

				<div id="subscription-paymob-options" class="hidden">
					<div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
						<label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Choose Payment Type') }}:</label>
						<div class="flex items-center gap-4">
							<label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-blue-100 transition">
								<input type="radio" name="pay_method" value="card" class="text-blue-600" checked>
								<span class="text-sm font-medium text-gray-800">
									<i class="fas fa-credit-card mr-1"></i>{{ __('Credit Card') }}
								</span>
							</label>
							<label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-blue-100 transition">
								<input type="radio" name="pay_method" value="wallet" class="text-blue-600">
								<span class="text-sm font-medium text-gray-800">
									<i class="fas fa-wallet mr-1"></i>{{ __('Mobile Wallet') }}
								</span>
							</label>
						</div>
					</div>
					<div id="wallet-phone-wrapper" class="hidden">
						<label class="block text-sm text-gray-700 mb-1">{{ __('wallet phone') }} <span class="text-red-500">*</span> ({{ __('egypt') }}: 01XXXXXXXXX)</label>
						<input type="tel" id="wallet-phone" placeholder="01XXXXXXXXX" pattern="^01[0-9]{9}$" maxlength="11" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" required>
						<p class="text-xs text-gray-500 mt-1">{{ __('Enter your mobile wallet number starting with 01') }}</p>
					</div>
				</div>
			</div>

			<div id="subscription-discount-section" class="space-y-2">
				<label class="block text-sm font-semibold text-gray-700">{{ __('Discount Code (optional)') }}</label>
				<input type="text" id="discount-code" placeholder="{{ __('Enter your code') }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
				<p class="text-xs text-gray-500">{{ __('Discount applies on the subscription payment.') }}</p>
			</div>
		</div>
		<div class="flex items-center justify-end gap-3 px-6 py-4 border-t">
			<button type="button" id="subscription-cancel-btn" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
				{{ __('Cancel') }}
			</button>
			<button type="button" id="subscription-confirm-btn" class="px-5 py-2 rounded-lg bg-primary-gradient text-white font-semibold hover:opacity-90 transition">
				{{ __('Continue') }}
			</button>
		</div>
	</div>
</div>

@push('scripts')
<script>
const availableGateways = @json($availableGateways ?? []);

const currentPlan = {
	hasActive: "{{($currentSubscription && $currentSubscription->isActive() &&$currentSubscription->plan) ? 'true' : 'false'}}",
	level: "{{ $currentSubscription && $currentSubscription->isActive() && $currentSubscription->plan ? $currentSubscription->plan->level : null }}",
	price: "{{$currentSubscription && $currentSubscription->isActive() && $currentSubscription->plan ? (float)$currentSubscription->plan->price : 'null'}}",
};


const LEVEL_WEIGHTS = {
	free: 1,
	basic: 2,
	advanced: 3,
	vip: 4
};

const subscriptionState = {
	planId: null,
	planPrice: 0,
	btn: null,
	requiresPayment: true,
};

function getDefaultOnlineGateway() {
	const onlineGateway = availableGateways.find(g => g.name !== 'cod');
	return onlineGateway ? onlineGateway.name : 'paymob';
}

function openSubscriptionModal(options) {
	const modal = document.getElementById('subscription-modal');
	const titleEl = document.getElementById('subscription-modal-title');
	const messageEl = document.getElementById('subscription-modal-message');
	const confirmBtn = document.getElementById('subscription-confirm-btn');
	const paymentSection = document.getElementById('subscription-payment-section');
	const priceSummary = document.getElementById('subscription-price-summary');
	const noPaymentEl = document.getElementById('subscription-no-payment');

	subscriptionState.planId = options.planId || null;
	subscriptionState.planPrice = options.planPrice || 0;
	subscriptionState.btn = options.btn || null;
	subscriptionState.requiresPayment = options.requiresPayment;

	titleEl.textContent = options.title || '';
	messageEl.innerHTML = options.message || '';
	confirmBtn.textContent = options.confirmText || "{{ __('Continue') }}";
	confirmBtn.style.display = options.showConfirm === false ? 'none' : 'inline-flex';

	const discountSection = document.getElementById('subscription-discount-section');
	if (options.showDiscount === false) {
		discountSection?.classList.add('hidden');
	} else {
		discountSection?.classList.remove('hidden');
	}
	document.getElementById('discount-code').value = options.prefillCode || '';

	if (options.showSummary) {
		priceSummary.classList.remove('hidden');
		document.getElementById('subscription-current-price').textContent = options.currentPriceText || '';
		document.getElementById('subscription-target-price').textContent = options.targetPriceText || '';
		document.getElementById('subscription-diff-price').textContent = options.diffPriceText || '';
		if (options.noPaymentRequired) {
			noPaymentEl.classList.remove('hidden');
		} else {
			noPaymentEl.classList.add('hidden');
		}
	} else {
		priceSummary.classList.add('hidden');
		noPaymentEl.classList.add('hidden');
	}

	if (subscriptionState.requiresPayment && availableGateways.length > 0) {
		paymentSection.classList.remove('hidden');
		renderPaymentOptions();
	} else {
		paymentSection.classList.add('hidden');
	}

	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function closeSubscriptionModal() {
	const modal = document.getElementById('subscription-modal');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
}

function renderPaymentOptions() {
	const container = document.getElementById('subscription-payment-methods');
	container.innerHTML = '';

	availableGateways.forEach((gateway, index) => {
		const displayName = gateway.name === 'paymob'
			? "{{ __('Online Payment') }}"
			: gateway.display_name;
		const wrapper = document.createElement('label');
		wrapper.className = 'flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition';
		wrapper.innerHTML = `
			<input type="radio" name="payment_gateway" value="${gateway.name}" class="w-4 h-4 text-blue-600" ${index === 0 ? 'checked' : ''}>
			<div class="mx-3 text-right">
				<div class="font-semibold text-gray-900">${displayName}</div>
			</div>
		`;
		container.appendChild(wrapper);
	});

	togglePaymobOptions();

	document.querySelectorAll('input[name="payment_gateway"]').forEach(radio => {
		radio.addEventListener('change', togglePaymobOptions);
	});

	document.querySelectorAll('input[name="pay_method"]').forEach(radio => {
		radio.addEventListener('change', toggleWalletPhone);
	});
}

function togglePaymobOptions() {
	const selectedGateway = document.querySelector('input[name="payment_gateway"]:checked')?.value;
	const paymobOptions = document.getElementById('subscription-paymob-options');
	if (!paymobOptions) return;

	if (selectedGateway === 'paymob') {
		paymobOptions.classList.remove('hidden');
		toggleWalletPhone();
	} else {
		paymobOptions.classList.add('hidden');
	}
}

function toggleWalletPhone() {
	const selectedMethod = document.querySelector('input[name="pay_method"]:checked')?.value;
	const walletWrapper = document.getElementById('wallet-phone-wrapper');
	if (!walletWrapper) return;
	if (selectedMethod === 'wallet') {
		walletWrapper.classList.remove('hidden');
	} else {
		walletWrapper.classList.add('hidden');
	}
}

function subscribeToPlan(planId, planLevel, planPrice, btn) {
	const planPriceNum = Number(planPrice || 0);
	const isFreePlan = planPriceNum <= 0;

	// For free plans, skip payment modal and directly subscribe (backend will handle as online payment)
	if (isFreePlan) {
		openSubscriptionModal({
			title: "{{ __('Subscribe to Plan') }}",
			message: "{{ __('This plan is free. No payment is required.') }}",
			confirmText: "{{ __('Yes, Subscribe') }}",
			planId,
			planPrice,
			btn,
			requiresPayment: false,
			showDiscount: false,
		});
		return;
	}

	const hasCurrent = currentPlan.hasActive && currentPlan.level && LEVEL_WEIGHTS[currentPlan.level];
	const newRank = LEVEL_WEIGHTS[planLevel] || null;

	if (hasCurrent && newRank !== null) {
		const currentRank = LEVEL_WEIGHTS[currentPlan.level];

		if (newRank < currentRank) {
			openSubscriptionModal({
				title: "{{ __('Not Allowed') }}",
				message: "{{ __('You cannot subscribe to a lower plan while you have an active subscription.') }}",
				showConfirm: false,
				showDiscount: false,
				requiresPayment: false,
			});
			return;
		}

		if (newRank > currentRank) {
			const currentPrice = Number(currentPlan.price || 0);
			const targetPrice = Number(planPrice || 0);
			const diff = Math.max(0, targetPrice - currentPrice);

			// If difference is 0 (e.g., upgrading to free plan), skip payment
			if (diff <= 0) {
				openSubscriptionModal({
					title: "{{ __('Upgrade Plan') }}",
					message: "{{ __('You are upgrading your subscription.') }}",
					confirmText: "{{ __('Confirm Upgrade') }}",
					showSummary: true,
					noPaymentRequired: true,
					currentPriceText: `${currentPrice.toFixed(2)} {{ __('EGP') }}`,
					targetPriceText: `${targetPrice.toFixed(2)} {{ __('EGP') }}`,
					diffPriceText: `${diff.toFixed(2)} {{ __('EGP') }}`,
					planId,
					planPrice,
					btn,
					requiresPayment: false,
				});
				return;
			}

			openSubscriptionModal({
				title: "{{ __('Upgrade Plan') }}",
				message: "{{ __('Review the upgrade details and complete payment.') }}",
				confirmText: "{{ __('Confirm Upgrade') }}",
				showSummary: true,
				noPaymentRequired: false,
				currentPriceText: `${currentPrice.toFixed(2)} {{ __('EGP') }}`,
				targetPriceText: `${targetPrice.toFixed(2)} {{ __('EGP') }}`,
				diffPriceText: `${diff.toFixed(2)} {{ __('EGP') }}`,
				planId,
				planPrice,
				btn,
				requiresPayment: true,
			});
			return;
		}
	}

	openSubscriptionModal({
		title: "{{ __('Subscribe to Plan') }}",
		message: "{{ __('Choose your payment method and apply a discount code if you have one.') }}",
		confirmText: "{{ __('Yes, Subscribe') }}",
		planId,
		planPrice,
		btn,
		requiresPayment: true,
	});
}

function performSubscriptionRequest(planId, btn, paymentGateway = 'cod', payMethod = 'card', walletPhone = '', discountCode = '') {
	if (btn) {
		btn.disabled = true;
		btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Processing...') }}';
	}

	// Get CSRF token from meta tag
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
	if (!csrfToken) {
		Swal.fire({
			title: "{{ __('Error ') }}",
			text: "{{ __('CSRF token not found.Please refresh the page.') }}",
			icon: 'error',
			confirmButtonColor: '#079184',
		});
		if (btn) {
			btn.disabled = false;
			btn.innerHTML = "{{ __('Subscribe Now ') }}";
		}
		return;
	}

	const requestBody = {
		payment_gateway: paymentGateway,
		auto_renew: false,
		affiliate_code: discountCode
	};

	if (paymentGateway === 'paymob') {
		requestBody.pay_method = payMethod;
		if (payMethod === 'wallet' && walletPhone) {
			requestBody.wallet_phone = walletPhone;
		}
	}

	fetch(`{{ route('subscriptions.subscribe', ['planId' => '__PLAN_ID__']) }}`.replace('__PLAN_ID__', planId), {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Accept': 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
				'X-CSRF-TOKEN': csrfToken
			},
			body: JSON.stringify(requestBody),
			credentials: 'same-origin'
		})
		.then(async res => {
			if (res.redirected) {
				throw new Error('Request was redirected');
			}

			const contentType = res.headers.get('content-type');
			if (!contentType || !contentType.includes('application/json')) {
				const text = await res.text();
				throw new Error(text || 'Invalid response format');
			}

			return res.json();
		})
		.then(data => {
			if (data.status === 'success') {
				// If payment redirect is required, redirect to payment page
				if (data.requires_payment && data.redirect_url) {
					window.location.href = data.redirect_url;
					return;
				}

				Swal.fire({
					title: "{{ __('Success!') }}",
					text: data.message ||
						"{{ __('Subscribed successfully ') }}",
					icon: 'success',
					confirmButtonColor: '#079184',
				}).then(() => {
					window.location.reload();
				});
			} else {
				Swal.fire({
					title: "{{ __('Error ') }}",
					text: data.message ||
						"{{ __('Failed to subscribe ') }}",
					icon: 'error',
					confirmButtonColor: '#079184',
				});
				if (btn) {
					btn.disabled = false;
					btn.innerHTML = "{{ __('Subscribe Now ') }}";
				}
			}
		})
		.catch(error => {
			console.error('Subscription error:', error);
			Swal.fire({
				title: "{{ __('Error ') }}",
				text: error.message ||
					"{{ __('Something went wrong.Please try again.') }}",
				icon: 'error',
				confirmButtonColor: '#079184',
			});
			if (btn) {
				btn.disabled = false;
				btn.innerHTML = "{{ __('Subscribe Now') }}";
			}
		});
}

document.addEventListener('click', (event) => {
	if (event.target.closest('#subscription-modal-close') || event.target.closest('#subscription-cancel-btn')) {
		closeSubscriptionModal();
		return;
	}

	if (!event.target.closest('#subscription-confirm-btn')) {
		return;
	}

	const discountCode = document.getElementById('discount-code')?.value || '';

	if (subscriptionState.requiresPayment && availableGateways.length > 0) {
		const selectedGateway = document.querySelector('input[name="payment_gateway"]:checked')?.value || 'cod';
		const payMethod = document.querySelector('input[name="pay_method"]:checked')?.value || 'card';
		const walletPhone = document.getElementById('wallet-phone')?.value || '';

		if (selectedGateway === 'paymob' && payMethod === 'wallet') {
			const phoneRegex = /^01[0-9]{9}$/;
			if (!walletPhone || !phoneRegex.test(walletPhone)) {
				Swal.fire({
					title: "{{ __('Validation Error') }}",
					text: "{{ __('Please enter a valid Egyptian mobile number (01XXXXXXXXX)') }}",
					icon: 'error',
					confirmButtonColor: '#079184',
				});
				return;
			}
		}

		closeSubscriptionModal();
		performSubscriptionRequest(subscriptionState.planId, subscriptionState.btn, selectedGateway, payMethod, walletPhone, discountCode);
		return;
	}

	const fallbackGateway = availableGateways.length ? getDefaultOnlineGateway() : 'cod';
	closeSubscriptionModal();
	performSubscriptionRequest(subscriptionState.planId, subscriptionState.btn, fallbackGateway, 'card', '', discountCode);
});
</script>
@endpush
