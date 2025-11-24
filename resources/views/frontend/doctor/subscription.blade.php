@extends('frontend.layouts.app')

@section('title', __('My Subscription'))

@push('styles')
<style>
.usage-card {
    transition: all 0.3s ease;
}

.usage-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}
</style>
@endpush

@section('content')

<div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Header -->
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-teal-500 to-emerald-500 text-white">
                <div>
                    <p class="text-sm uppercase tracking-wider text-white/80">{{ __('Doctor portal') }}</p>
                    <h2 class="text-2xl font-semibold">{{ __('My Subscription') }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('doctor.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-full text-sm font-medium transition">
                        <i class="fas fa-long-arrow-alt-left"></i>
                        {{ __('Back to dashboard') }}
                    </a>
                </div>
            </div>

            <div class="p-6">
                @if($subscription && $subscription->isActive())
                <!-- Current Subscription -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 border border-blue-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $subscription->plan->name ?? __('Plan') }}</h3>
                            <p class="text-sm text-gray-600">{{ ucfirst($subscription->plan->level ?? '') }} {{ __('Plan') }}</p>
                        </div>
                        <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                            {{ __('Active') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">{{ __('Start Date') }}</p>
                            <p class="font-semibold text-gray-900">{{ $subscription->start_date?->format('M d, Y') ?? '-' }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">{{ __('End Date') }}</p>
                            <p class="font-semibold text-gray-900">
                                {{ $subscription->end_date?->format('M d, Y') ?? __('Lifetime') }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">{{ __('Days Remaining') }}</p>
                            <p class="font-semibold text-gray-900">
                                @if($subscription->end_date)
                                    {{ number_format(now()->diffInDays($subscription->end_date, false), 2) }} {{ __('days') }}
                                @else
                                    {{ __('Lifetime') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('home') }}#subscriptions-plans"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-primary-gradient text-white rounded-lg font-semibold hover:opacity-90 transition">
                            <i class="fas fa-arrow-up"></i>
                            {{ __('Upgrade Plan') }}
                        </a>
                    </div>
                </div>

                <!-- Usage Statistics -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Feature Usage') }}</h3>
                    @php
                        $usages = $subscription->featureUsages()->with('feature')->get();
                    @endphp
                    @if($usages->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($usages as $usage)
                        <div class="usage-card bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-900">{{ $usage->feature->name ?? $usage->feature_code }}</h4>
                                @if($usage->limit_count !== null)
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                    {{ $usage->used_count }} / {{ $usage->limit_count }}
                                </span>
                                @else
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                    {{ __('Unlimited') }}
                                </span>
                                @endif
                            </div>
                            @if($usage->limit_count !== null)
                            <div class="mb-2">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>{{ __('Used') }}</span>
                                    <span>{{ __('Remaining') }}: {{ max(0, $usage->limit_count - $usage->used_count) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    @php
                                        $percentage = min(100, ($usage->used_count / $usage->limit_count) * 100);
                                        $colorClass = $percentage >= 90 ? 'bg-red-500' : ($percentage >= 70 ? 'bg-yellow-500' : 'bg-green-500');
                                    @endphp
                                    <div class="{{ $colorClass }} h-3 rounded-full transition-all"
                                         style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-gray-50 rounded-xl p-8 text-center">
                        <i class="fas fa-chart-line text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600">{{ __('No usage data available') }}</p>
                    </div>
                    @endif
                </div>
                @else
                <!-- No Subscription -->
                <div class="text-center py-12">
                    <i class="fas fa-credit-card text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No Active Subscription') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('Subscribe to a plan to unlock all premium features') }}</p>
                    <a href="{{ route('home') }}#subscriptions-plans"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-primary-gradient text-white rounded-xl font-semibold hover:opacity-90 transition shadow-lg">
                        <i class="fas fa-arrow-up"></i>
                        {{ __('View Plans') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@endpush
