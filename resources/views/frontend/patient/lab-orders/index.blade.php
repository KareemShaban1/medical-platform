@extends('frontend.layouts.app')

@section('title', __('My Lab Results'))

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
	<div class="max-w-4xl mx-auto">
		<div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
		   <div class="flex justify-between items-center bg-gradient-to-r from-sky-600 to-blue-600 text-white px-6 py-4">
                <h2 class="text-2xl font-bold text-gray-800">{{ __('My Lab Orders') }}</h2>
				<a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 transition">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
                </a>
			</div>
			<div class="p-6">
				<form method="GET" action="{{ route('user.lab-orders.index') }}" class="mb-4">
					<div class="flex flex-wrap items-end gap-3">
						<div>
							<label class="block text-sm text-gray-600 mb-1">{{ __('From') }}</label>
							<input type="date" name="from" value="{{ request('from') }}" class="border rounded-md px-3 py-2">
						</div>
						<div>
							<label class="block text-sm text-gray-600 mb-1">{{ __('To') }}</label>
							<input type="date" name="to" value="{{ request('to') }}" class="border rounded-md px-3 py-2">
						</div>
						<div class="flex gap-2">
							<button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-md">{{ __('Filter') }}</button>
							<a href="{{ route('user.lab-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">{{ __('Clear') }}</a>
						</div>
					</div>
				</form>
				@if($orders->count() === 0)
				<p class="text-gray-600">
					{{ __('No completed lab results yet.') }}</p>
				@else
				<ul class="space-y-4">
					@foreach($orders as $order)
					<li class="p-4 rounded-xl border border-gray-200 bg-gray-50">
						<div class="flex items-center justify-between">
							<div>
								<h3
									class="text-lg font-semibold text-gray-800">
									{{ $order->test_name }}</h3>
								<p class="text-sm text-gray-500">
									{{ __('Clinic') }}:
									{{ $order->clinic?->name }} •
									{{ $order->reviewed_at?->format('Y-m-d') }}
								</p>
							</div>
							<a href="{{ route('user.lab-orders.show', $order->id) }}"
								class="px-3 py-2 rounded-lg bg-sky-600 text-white text-sm">
								{{ __('View') }}
							</a>
						</div>
					</li>
					@endforeach
				</ul>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
