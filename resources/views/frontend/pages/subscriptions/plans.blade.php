@extends('frontend.layouts.app')

@section('title', __('Subscription Plans'))

@push('styles')
<style>
.plan-card {
    transition: all 0.3s ease;
    border: 2px solid #e5e7eb;
}

.plan-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.plan-card.featured {
    border-color: #079184;
    border-width: 3px;
    position: relative;
}

.plan-card.featured::before {
    content: '{{ __('Most Popular') }}';
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    background: #079184;
    color: white;
    padding: 4px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.price-tag {
    font-size: 3rem;
    font-weight: 700;
    color: #079184;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
}

.feature-item i {
    color: #10b981;
}
</style>
@endpush

@section('content')

<!-- Plans Header -->
<section class="bg-primary-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('Choose Your Plan') }}</h1>
        <p class="text-xl">{{ __('Select the perfect plan for your needs') }}</p>
    </div>
</section>

<!-- Plan Type Selector -->
<section class="py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center gap-4">
            <a href="{{ route('subscriptions.plans', ['type' => 'doctor']) }}"
                class="px-6 py-3 rounded-lg font-semibold transition {{ $type === 'doctor' ? 'bg-primary-gradient text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-user-md mr-2"></i>{{ __('Doctor Plans') }}
            </a>
            <a href="{{ route('subscriptions.plans', ['type' => 'clinic']) }}"
                class="px-6 py-3 rounded-lg font-semibold transition {{ $type === 'clinic' ? 'bg-primary-gradient text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-hospital mr-2"></i>{{ __('Clinic Plans') }}
            </a>
            <a href="{{ route('subscriptions.plans', ['type' => 'supplier']) }}"
                class="px-6 py-3 rounded-lg font-semibold transition {{ $type === 'supplier' ? 'bg-primary-gradient text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-truck mr-2"></i>{{ __('Supplier Plans') }}
            </a>
        </div>
    </div>
</section>

<!-- Plans Grid -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
    </div>
</section>

@endsection

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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    auto_renew: false
                })
            })
            .then(res => res.json())
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
                Swal.fire({
                    title: '{{ __('Error') }}',
                    text: '{{ __('Something went wrong. Please try again.') }}',
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

