@if($plans->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($plans as $plan)
    <div class="plan-card {{ $plan->level === 'advanced' ? 'featured' : '' }} bg-white rounded-2xl p-8 shadow-lg">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
            <div class="price-tag">
                ${{ number_format($plan->price, 2) }}
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
            <button onclick="subscribeToPlan({{ $plan->id }})"
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
function subscribeToPlan(planId) {
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
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Processing...') }}';

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
                // Handle redirects
                if (res.redirected) {
                    throw new Error('Request was redirected');
                }

                // Check if response is JSON
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
                    btn.disabled = false;
                    btn.innerHTML = '{{ __('Subscribe Now') }}';
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
                btn.disabled = false;
                btn.innerHTML = '{{ __('Subscribe Now') }}';
            });
        }
    });
}
</script>
@endpush

