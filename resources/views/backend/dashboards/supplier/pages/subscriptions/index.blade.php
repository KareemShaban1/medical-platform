@extends('backend.dashboards.supplier.layouts.app')
@section('title', __('My Subscription'))

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<h4 class="page-title">{{ __('My Subscription') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		@if($subscription && $subscription->isActive())
		<!-- Current Subscription Card -->
		<div class="col-lg-8">
			<div class="card">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h5 class="card-title mb-0">{{ __('Current Subscription') }}
						</h5>
						<span class="badge bg-success">{{ __('Active') }}</span>
					</div>

					<div class="row mb-4">
						<div class="col-md-6">
							<p class="text-muted mb-1">{{ __('Plan Name') }}
							</p>
							<h4 class="mb-0">
								{{ $subscription->plan->name ?? '-' }}
							</h4>
						</div>
						<div class="col-md-6">
							<p class="text-muted mb-1">{{ __('Plan Level') }}
							</p>
							<h4 class="mb-0">
								{{ ucfirst($subscription->plan->level ?? '-') }}
							</h4>
						</div>
					</div>

					<div class="row mb-4">
						<div class="col-md-6">
							<p class="text-muted mb-1">{{ __('Start Date') }}
							</p>
							<p class="mb-0">
								{{ $subscription->start_date?->format('M d, Y') ?? '-' }}
							</p>
						</div>
						<div class="col-md-6">
							<p class="text-muted mb-1">{{ __('End Date') }}
							</p>
							<p class="mb-0">
								{{ $subscription->end_date?->format('M d, Y') ?? __('Lifetime') }}
								@if($subscription->end_date)
								<span class="badge bg-info ms-2">
									{{ number_format(now()->diffInDays($subscription->end_date, false), 2) }}
									{{ __('days remaining') }}
								</span>
								@endif
							</p>
						</div>
					</div>

					<div class="row mb-4">
						<div class="col-md-6">
							<p class="text-muted mb-1">{{ __('Status') }}</p>
							<p class="mb-0">
								<span
									class="badge bg-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'expired' ? 'danger' : 'warning') }}">
									{{ ucfirst($subscription->status) }}
								</span>
							</p>
						</div>
					</div>

					<div class="d-flex gap-2">
						@hasPermission('subscribe')
						<a href="{{ route('home') }}#subscriptions-plans"
							class="btn btn-primary">
							<i class="mdi mdi-arrow-up"></i>
							{{ __('Upgrade Plan') }}
						</a>
						@endhasPermission
					</div>
				</div>
			</div>
		</div>

		<!-- Usage Statistics -->
		<div class="col-lg-4">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title mb-4">{{ __('Usage Overview') }}</h5>
					@php
					$usages = $subscription->featureUsages()->with('feature')->get();
					@endphp
					@if($usages->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($usages as $usage)
						<div class="list-group-item px-0">
							<div
								class="d-flex justify-content-between align-items-center mb-2">
								<span
									class="fw-semibold">{{ $usage->feature->name ?? $usage->feature_code }}</span>
								@if($usage->limit_count !== null)
								<span class="badge bg-info">
									{{ $usage->used_count }} /
									{{ $usage->limit_count }}
								</span>
								@else
								<span
									class="badge bg-success">{{ __('Unlimited') }}</span>
								@endif
							</div>
							@if($usage->limit_count !== null)
							<div class="progress" style="height: 8px;">
								@php
								$percentage = min(100,
								($usage->used_count /
								$usage->limit_count) * 100);
								@endphp
								<div class="progress-bar {{ $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success') }}"
									role="progressbar"
									style="width: {{ $percentage }}%"
									aria-valuenow="{{ $percentage }}"
									aria-valuemin="0"
									aria-valuemax="100">
								</div>
							</div>
							<small class="text-muted">
								{{ __('Remaining') }}:
								{{ max(0, $usage->limit_count - $usage->used_count) }}
							</small>
							@endif
						</div>
						@endforeach
					</div>
					@else
					<p class="text-muted">{{ __('No usage data available') }}</p>
					@endif
				</div>
			</div>
		</div>
		@else
		<!-- No Subscription -->
		<div class="col-12">
			<div class="card">
				<div class="card-body text-center py-5">
					<i class="mdi mdi-credit-card-off text-muted"
						style="font-size: 64px;"></i>
					<h4 class="mt-3 mb-2">{{ __('No Active Subscription') }}</h4>
					<p class="text-muted mb-4">
						{{ __('Subscribe to a plan to unlock all features') }}</p>
					<a href="{{ route('home') }}#subscriptions-plans"
						class="btn btn-primary">
						<i class="mdi mdi-arrow-up"></i> {{ __('View Plans') }}
					</a>
				</div>
			</div>
		</div>
		@endif
	</div>
</div>
@endsection

@push('scripts')
@endpush
