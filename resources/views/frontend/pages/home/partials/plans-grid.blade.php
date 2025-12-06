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


@push('scripts')
<script>
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

function subscribeToPlan(planId, planLevel, planPrice, btn) {
	const hasCurrent = currentPlan.hasActive && currentPlan.level && LEVEL_WEIGHTS[currentPlan.level];
	const newRank = LEVEL_WEIGHTS[planLevel] || null;

	if (hasCurrent && newRank !== null) {
		const currentRank = LEVEL_WEIGHTS[currentPlan.level];

		if (newRank < currentRank) {
			Swal.fire({
				title: "{{ __('Not Allowed ') }}",
				text: "{{ __('You cannot subscribe to a lower plan while you have an active subscription.') }}",
				icon: 'warning',
				confirmButtonColor: '#079184',
			});
			return;
		}

		if (newRank > currentRank) {
			const currentPrice = Number(currentPlan.price || 0);
			const targetPrice = Number(planPrice || 0);
			const diff = Math.max(0, targetPrice - currentPrice);

			Swal.fire({
				title: "{{ __('Upgrade Plan ') }}",
				html: `{{ __('Current plan price') }}: <strong>${currentPrice.toFixed(2)} {{ __('EGP') }}</strong><br>` +
					`{{ __('New plan price') }}: <strong>${targetPrice.toFixed(2)} {{ __('EGP') }}</strong><br>` +
					`{{ __('You will pay the difference') }}: <strong>${diff.toFixed(2)} {{ __('EGP') }}</strong>`,
				icon: 'info',
				showCancelButton: true,
				confirmButtonText: "{{ __('Confirm Upgrade ') }}",
				cancelButtonText: "{{ __('Cancel ') }}",
				confirmButtonColor: '#079184',
			}).then((result) => {
				if (result.isConfirmed) {
					showPaymentModal(planId, planPrice, btn);
				}
			});
			return;
		}
	}

	Swal.fire({
		title: "{{ __('Subscribe to Plan ? ') }}",
		text : "{{ __('Are you sure you want to subscribe to this plan ? ') }}",
		icon : 'question',
		showCancelButton: true,
		confirmButtonText: "{{ __('Yes, Subscribe ') }}",
		cancelButtonText: "{{ __('Cancel') }}",
		confirmButtonColor: '#079184',
	}).then((result) => {
		if (result.isConfirmed) {
			showPaymentModal(planId, planPrice, btn);
		}
	});
}

function showPaymentModal(planId, planPrice, btn) {
	// Get gateways from the page
	const availableGateways = @json($availableGateways ?? []);

	if (availableGateways.length === 0) {
		// No gateways available, proceed without payment selection
		performSubscriptionRequest(planId, btn, 'cod');
		return;
	}

	// Build payment options HTML
	let paymentOptionsHtml = '<div class="text-right mt-4">';
	paymentOptionsHtml += '<label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('Select Payment Method ') }}</label>';

	availableGateways.forEach((gateway, index) => {
		const isChecked = index === 0 ? 'checked' : '';
		paymentOptionsHtml += `
            <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition mb-2">
                <input type="radio" name="payment_gateway" value="${gateway.name}" class="w-4 h-4 text-blue-600" ${isChecked}>
                <div class="mx-3 text-right">
                    <div class="font-semibold text-gray-900">${gateway.display_name}</div>
                    
                </div>
            </label>
        `;
	});

	// Add Paymob sub-options
	const hasPaymob = availableGateways.some(g => g.name === 'paymob');
	const defaultGateway = availableGateways.length > 0 ? availableGateways[0].name : 'cod';
	const showPaymobOptions = hasPaymob && defaultGateway === 'paymob';

	if (hasPaymob) {
		paymentOptionsHtml += `
            <div id="paymob-options" class="mt-3" style="${showPaymobOptions ? '' : 'display: none;'}">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Choose Payment Type') }}:</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-blue-100 transition">
                            <input type="radio" name="pay_method" value="card" class="text-blue-600" checked>
                            <span class="text-sm font-medium text-gray-800">
                                <i class="fas fa-credit-card ml-2"></i>{{ __('Credit Card') }}
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
        `;
	}

	paymentOptionsHtml += '</div>';

	Swal.fire({
		title: "{{ __('Select Payment Method ') }}",
		html: paymentOptionsHtml,
		icon: 'info',
		showCancelButton: true,
		confirmButtonText: "{{ __('Proceed ') }}",
		cancelButtonText: "{{ __('Cancel ') }}",
		confirmButtonColor: '#079184',
		didOpen: () => {
			// Use setTimeout to ensure DOM is fully rendered
			setTimeout(() => {
				// Function to toggle Paymob options
				const togglePaymobOptions =
				() => {
						const selectedGateway =
							document
							.querySelector(
								'input[name="payment_gateway"]:checked'
								)
							?.value;
						const paymobOptions =
							document
							.getElementById(
								'paymob-options'
								);
						if (
							paymobOptions) {
							if (selectedGateway ===
								'paymob'
								) {
								paymobOptions
									.classList
									.remove(
										'hidden');
								paymobOptions
									.style
									.display =
									'block';
							} else {
								paymobOptions
									.classList
									.add(
										'hidden');
								paymobOptions
									.style
									.display =
									'none';
							}
						}
					};

				// Function to toggle wallet phone input
				const toggleWalletPhone =
				() => {
						const selectedMethod =
							document
							.querySelector(
								'input[name="pay_method"]:checked'
								)
							?.value;
						const walletWrapper =
							document
							.getElementById(
								'wallet-phone-wrapper'
								);
						if (
							walletWrapper) {
							if (selectedMethod ===
								'wallet'
								) {
								walletWrapper
									.classList
									.remove(
										'hidden');
								walletWrapper
									.style
									.display =
									'block';
							} else {
								walletWrapper
									.classList
									.add(
										'hidden');
								walletWrapper
									.style
									.display =
									'none';
							}
						}
					};

				// Show/hide Paymob options based on selected gateway
				const gatewayRadios = document
					.querySelectorAll(
						'input[name="payment_gateway"]'
						);
				gatewayRadios.forEach(
				radio => {
					radio.addEventListener(
						'change',
						togglePaymobOptions
						);
					// Also trigger on click for immediate feedback
					radio.addEventListener(
						'click',
						togglePaymobOptions
						);
				});

				// Show/hide wallet phone input based on payment method
				const methodRadios = document
					.querySelectorAll(
						'input[name="pay_method"]'
						);
				methodRadios.forEach(
				radio => {
					radio.addEventListener(
						'change',
						toggleWalletPhone
						);
					radio.addEventListener(
						'click',
						toggleWalletPhone
						);
				});

				// Initialize on open (check current selection)
				togglePaymobOptions();
				toggleWalletPhone();
			}, 100);
		}
	}).then((result) => {
		if (result.isConfirmed) {
			const selectedGateway = document.querySelector(
					'input[name="payment_gateway"]:checked')?.value ||
				'cod';
			const payMethod = document.querySelector(
					'input[name="pay_method"]:checked')?.value ||
				'card';
			const walletPhone = document.getElementById('wallet-phone')?.value ||
				'';

			// Validate wallet phone if wallet method is selected
			if (selectedGateway === 'paymob' && payMethod === 'wallet') {
				const phoneRegex = /^01[0-9]{9}$/;
				if (!walletPhone || !phoneRegex.test(walletPhone)) {
					Swal.fire({
						title: "{{ __('Validation Error ') }}",
						text: "{{ __('Please enter a valid Egyptian mobile number(01 XXXXXXXXX)') }}",
						icon: 'error',
						confirmButtonColor: '#079184',
					});
					return;
				}
			}

			performSubscriptionRequest(planId, btn, selectedGateway, payMethod,
				walletPhone);
		}
	});
}

function performSubscriptionRequest(planId, btn, paymentGateway = 'cod', payMethod = 'card', walletPhone = '') {
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
		auto_renew: false
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
					text: data.message || "{{ __('Subscribed successfully ') }}",
					icon: 'success',
					confirmButtonColor: '#079184',
				}).then(() => {
					window.location.reload();
				});
			} else {
				Swal.fire({
					title: "{{ __('Error ') }}",
					text: data.message || "{{ __('Failed to subscribe ') }}",
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
				text: error.message || "{{ __('Something went wrong.Please try again.') }}",
				icon: 'error',
				confirmButtonColor: '#079184',
			});
			if (btn) {
				btn.disabled = false;
				btn.innerHTML = "{{ __('Subscribe Now') }}";
			}
		});
}
</script>
@endpush