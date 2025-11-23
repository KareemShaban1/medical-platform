@if($plans->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($plans as $plan)
    <div class="plan-card {{ $plan->level === 'advanced' ? 'featured' : '' }} bg-white rounded-2xl p-8 shadow-lg">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
            <div class="price-tag">
                <span style="font-size: 2.5rem">EGP {{ number_format($plan->price, 2) }}</span>
                @if($plan->duration_in_days)
                <span class="text-lg text-gray-500">/{{ round($plan->duration_in_days / 30) }}mo</span>
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
                    @endif
                </span>
            </li>
            @endif
            @endforeach
        </ul>

        @if(Auth::guard('clinic')->check() || Auth::guard('supplier')->check())
            @php
                $isSubscribed = $currentSubscription && $currentSubscription->plan_id === $plan->id && $currentSubscription->isActive();
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
    hasActive: {{ ($currentSubscription && $currentSubscription->isActive() && $currentSubscription->plan) ? 'true' : 'false' }},
    level: "{{ $currentSubscription && $currentSubscription->isActive() && $currentSubscription->plan ? $currentSubscription->plan->level : null }}",
    price: {{ $currentSubscription && $currentSubscription->isActive() && $currentSubscription->plan ? (float) $currentSubscription->plan->price : 'null' }},
};


const LEVEL_WEIGHTS = { free: 1, basic: 2, advanced: 3, vip: 4 };

function subscribeToPlan(planId, planLevel, planPrice, btn) {
    const hasCurrent = currentPlan.hasActive && currentPlan.level && LEVEL_WEIGHTS[currentPlan.level];
    const newRank = LEVEL_WEIGHTS[planLevel] || null;

    if (hasCurrent && newRank !== null) {
        const currentRank = LEVEL_WEIGHTS[currentPlan.level];

        if (newRank < currentRank) {
            Swal.fire({
                title: '{{ __('Not Allowed') }}',
                text: '{{ __('You cannot subscribe to a lower plan while you have an active subscription.') }}',
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
                title: '{{ __('Upgrade Plan') }}',
                html: `{{ __('Current plan price') }}: <strong>${currentPrice.toFixed(2)} {{ __('EGP') }}</strong><br>` +
                      `{{ __('New plan price') }}: <strong>${targetPrice.toFixed(2)} {{ __('EGP') }}</strong><br>` +
                      `{{ __('You will pay the difference') }}: <strong>${diff.toFixed(2)} {{ __('EGP') }}</strong>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '{{ __('Confirm Upgrade') }}',
                cancelButtonText: '{{ __('Cancel') }}',
                confirmButtonColor: '#079184',
            }).then((result) => {
                if (result.isConfirmed) {
                    performSubscriptionRequest(planId, btn);
                }
            });
            return;
        }
    }

    Swal.fire({
        title: '{{ __('Subscribe to Plan?') }}',
        text: '{{ __('Are you sure you want to subscribe to this plan?') }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '{{ __('Yes, Subscribe') }}',
        cancelButtonText: '{{ __('Cancel') }}',
        confirmButtonColor: '#079184',
    }).then((result) => {
        if (result.isConfirmed) {
            performSubscriptionRequest(planId, btn);
        }
    });
}

function performSubscriptionRequest(planId, btn) {
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Processing...') }}';
    }

    fetch(`{{ route('subscriptions.subscribe', ['planId' => '__PLAN_ID__']) }}`.replace('__PLAN_ID__', planId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            auto_renew: false
        }),
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
            Swal.fire({
                title: '{{ __('Success!') }}',
                text: data.message || '{{ __('Subscribed successfully') }}',
                icon: 'success',
                confirmButtonColor: '#079184',
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: '{{ __('Error') }}',
                text: data.message || '{{ __('Failed to subscribe') }}',
                icon: 'error',
                confirmButtonColor: '#079184',
            });
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '{{ __('Subscribe Now') }}';
            }
        }
    })
    .catch(error => {
        console.error('Subscription error:', error);
        Swal.fire({
            title: '{{ __('Error') }}',
            text: error.message || '{{ __('Something went wrong. Please try again.') }}',
            icon: 'error',
            confirmButtonColor: '#079184',
        });
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '{{ __('Subscribe Now') }}';
        }
    });
}
</script>
@endpush
