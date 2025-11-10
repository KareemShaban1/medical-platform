@extends('frontend.layouts.app')

@section('title', __('Doctor Dashboard'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-10 px-4">
	<div class="w-full max-w-4xl">
		<div class="bg-white shadow-2xl rounded-2xl overflow-hidden">

			<!-- Header -->
			<div
				class="flex justify-between items-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4">
				<h2 class="text-xl font-semibold">{{ __('Doctor Dashboard') }}</h2>
				<div class="flex items-center gap-3">
					<form method="POST" action="{{ route('clinic.logout') }}"
						onsubmit="return confirm('{{ __('Are you sure you want to logout?') }}')">
						@csrf
						<button type="submit"
							class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-medium transition">
							<i class="fas fa-sign-out-alt"></i>
							{{ __('Logout') }}
						</button>
					</form>
				</div>
			</div>

			<!-- Body -->
			<div class="p-8">

				<!-- Greeting -->
				<div class="text-center mb-10">
					<h1 class="text-2xl md:text-3xl font-bold text-gray-800">
						👋 {{ __('Hello :name', ['name' => $doctor->name]) }}
					</h1>
					<p class="text-gray-500">
						{{ __('Welcome to your doctor dashboard') }}</p>
				</div>

				<!-- Feature Cards -->
				<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
					@php
					$features = [
					['icon' => 'fa-user-md', 'color' => 'text-blue-500', 'title' =>
					__('Doctor Profile'), 'desc' => __('Create and manage your doctor profile'), 'route' => route('doctor.profile.index')],
					['icon' => 'fa-shopping-cart', 'color' => 'text-green-500', 'title' =>
					__('My Orders'), 'desc' => __('View your orders from the website'), 'route' => route('doctor.orders.index')],
					['icon' => 'fa-graduation-cap', 'color' => 'text-purple-500', 'title' =>
					__('Courses'), 'desc' => __('Browse available courses'), 'route' => route('courses')],
					];
					@endphp

					@foreach($features as $feature)
					@if(isset($feature['route']))
					<a href="{{ $feature['route'] }}"
						class="bg-gray-100 hover:bg-gray-200 transition rounded-xl shadow p-6 text-center block">
						<i
							class="fas {{ $feature['icon'] }} {{ $feature['color'] }} text-4xl mb-3"></i>
						<h5 class="text-lg font-semibold text-gray-800">
							{{ $feature['title'] }}</h5>
						<p class="text-sm text-gray-500">
							{{ $feature['desc'] }}</p>
					</a>
					@else
					<div
						class="bg-gray-100 hover:bg-gray-200 transition rounded-xl shadow p-6 text-center">
						<i
							class="fas {{ $feature['icon'] }} {{ $feature['color'] }} text-4xl mb-3"></i>
						<h5 class="text-lg font-semibold text-gray-800">
							{{ $feature['title'] }}</h5>
						<p class="text-sm text-gray-500">
							{{ $feature['desc'] }}</p>
					</div>
					@endif
					@endforeach
				</div>

				<hr class="my-8 border-gray-300">

				<!-- Doctor Info -->
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div>
						<h6 class="text-lg font-semibold text-gray-800 mb-2">
							{{ __('Doctor Information') }}
						</h6>
						<ul class="space-y-1 text-gray-600">
							<li><strong>{{ __('Name') }}:</strong>
								{{ $doctor->name }}</li>
							<li><strong>{{ __('Email') }}:</strong>
								{{ $doctor->email ?? __('Not provided') }}
							</li>
							<li><strong>{{ __('Phone') }}:</strong>
								{{ $doctor->phone ?? __('Not provided') }}</li>
						</ul>
					</div>

					<div>
						<h6 class="text-lg font-semibold text-gray-800 mb-2">
							{{ __('Account Status') }}
						</h6>
						<ul class="space-y-1 text-gray-600">
							<li>
								<strong>{{ __('Account Type') }}:</strong>
								<span
									class="ml-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
									{{ __('Standalone Doctor') }}
								</span>
							</li>
							<li><strong>{{ __('Member Since') }}:</strong>
								{{ $doctor->created_at->format('F Y') }}
							</li>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection

