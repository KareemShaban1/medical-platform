@extends('frontend.layouts.app')

@section('title', __('Patient Dashboard'))

@section('content')
	<div class="min-h-screen flex items-center justify-center bg-gray-50 py-10 px-4">
		<div class="w-full max-w-4xl">
			<div class="bg-white shadow-2xl rounded-2xl overflow-hidden">

				<!-- Header -->
				<div
					class="flex justify-between items-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4">
					<h2 class="text-xl font-semibold">{{ __('Patient Dashboard') }}</h2>
					<div class="flex items-center gap-3">
						<a href="{{ route('user.lab-orders.index') }}"
							class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-medium transition">
							<i class="fas fa-vials"></i> {{ __('My Lab Results') }}
						</a>

						<form method="POST" action="{{ route('user.logout') }}"
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
							👋 {{ __('Hello :name', ['name' => $patient->name]) }}
						</h1>
						<p class="text-gray-500">
							{{ __('Welcome to your patient dashboard') }}
						</p>
					</div>

					<!-- Feature Cards -->
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
						@php
							$features = [
								[
									'icon' => 'fa-user',
									'color' => 'text-blue-500',
									'title' =>
										__('Profile Information'),
									'desc' => __('View and update your
												profile'),
									'route' => route('user.profile.index')
								],
								[
									'icon' => 'fa-calendar',
									'color' => 'text-green-500',
									'title' =>
										__('Appointments'),
									'desc' => __('Manage your appointments'),
									'route'
									=> route('user.appointments.my')
								],
								[
									'icon' => 'fa-file-medical',
									'color' => 'text-sky-500',
									'title' =>
										__('Medical Records'),
									'desc' => __('Access your medical history'),
									'route' => route('user.medical-records.index')
								],
								[
									'icon' => 'fa-pills',
									'color' => 'text-yellow-500',
									'title' =>
										__('Prescriptions'),
									'desc' => __('View your prescriptions'),
									'route'
									=> route('user.prescriptions.index')
								],
								// Lab Results card with same style (clickable)
								[
									'icon' => 'fa-vials',
									'color' => 'text-purple-500',
									'title' =>
										__('Lab Results'),
									'desc' => __('View and download your results'),
									'route' => route('user.lab-orders.index')
								],
							// Tickets card
							[
								'icon' => 'fa-ticket-alt',
								'color' => 'text-red-500',
								'title' =>
									__('Manage Tickets'),
								'desc' => __('View and manage your support tickets'),
								'route' => route('user.tickets.index')
							],
							];
						@endphp

						@foreach($features as $feature)
							@if(isset($feature['route']))
								<a href="{{ $feature['route'] }}"
									class="bg-gray-100 hover:bg-gray-200 transition rounded-xl shadow p-6 text-center block">
									<i class="fas {{ $feature['icon'] }} {{ $feature['color'] }} text-4xl mb-3"></i>
									<h5 class="text-lg font-semibold text-gray-800">
										{{ $feature['title'] }}
									</h5>
									<p class="text-sm text-gray-500">
										{{ $feature['desc'] }}
									</p>
								</a>
							@else
								<div class="bg-gray-100 hover:bg-gray-200 transition rounded-xl shadow p-6 text-center">
									<i class="fas {{ $feature['icon'] }} {{ $feature['color'] }} text-4xl mb-3"></i>
									<h5 class="text-lg font-semibold text-gray-800">
										{{ $feature['title'] }}
									</h5>
									<p class="text-sm text-gray-500">
										{{ $feature['desc'] }}
									</p>
								</div>
							@endif
						@endforeach
					</div>

					<hr class="my-8 border-gray-300">

					<!-- Patient Info -->
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div>
							<h6 class="text-lg font-semibold text-gray-800 mb-2">
								{{ __('Patient Information') }}
							</h6>
							<ul class="space-y-1 text-gray-600">
								<li><strong>{{ __('Name') }}:</strong>
									{{ $patient->name }}</li>
								<li><strong>{{ __('Email') }}:</strong>
									{{ $patient->email ?? __('Not provided') }}
								</li>
								<li><strong>{{ __('Phone') }}:</strong>
									{{ $patient->phone }}</li>
							</ul>
						</div>

						<div>
							<h6 class="text-lg font-semibold text-gray-800 mb-2">
								{{ __('Account Status') }}
							</h6>
							<ul class="space-y-1 text-gray-600">
								<li>
									<strong>{{ __('Account Type') }}:</strong>
									@if($patient->isRegistered())
										<span
											class="ml-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
											{{ __('Registered User') }}
										</span>
									@else
										<span
											class="ml-2 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
											{{ __('Clinic Created') }}
										</span>
									@endif
								</li>
								<li><strong>{{ __('Member Since') }}:</strong>
									{{ $patient->created_at->format('F Y') }}
								</li>
							</ul>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
@endsection