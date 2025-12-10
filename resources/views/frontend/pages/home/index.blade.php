@extends('frontend.layouts.app')

@section('title' , __('Home'))


@section('content')


<!-- hero2 -->
@include('frontend.pages.home.partials.hero')


<section class="relative overflow-hidden bg-primary-gradient py-16 px-8">
	<div class="container">
		<div class="-mx-4 flex flex-wrap items-center">
			<div class="w-full px-4 lg:w-1/2">
				<div class="text-center lg:text-right ">
					<div class="mb-10 lg:mb-0 ">
						<h1
							class="mt-0 mb-3 text-3xl font-bold leading-tight sm:text-4xl sm:leading-tight md:text-[40px] md:leading-tight text-white ">
							{{ __('welcome to our medical platform') }}
						</h1>
						<p
							class="w-full text-base font-medium leading-relaxed sm:text-lg sm:leading-relaxed text-white">
							{{ __('explore our platform and find the best medical services for you.') }}
						</p>
					</div>
				</div>
			</div>
			<div class="w-full px-4 lg:w-1/2 flex justify-end">
				<div class="text-center lg:text-right"><a
						class="font-semibold rounded-lg mx-auto inline-flex items-center justify-center bg-white py-4 px-9 hover:bg-opacity-90"
						href="{{ route('doctors.index') }}">{{ __('create your first appointment') }}</a>
				</div>
			</div>
		</div>
	</div>
	<span class="absolute top-0 right-0 -z-10">
		<svg width="388" height="250" viewBox="0 0 388 220" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path opacity="0.05"
				d="M203 -28.5L4.87819e-05 250.5L881.5 250.5L881.5 -28.5002L203 -28.5Z"
				fill="url(#paint0_linear_971_6910)"></path>
			<defs>
				<linearGradient id="paint0_linear_971_6910" x1="60.5" y1="111" x2="287" y2="111"
					gradientUnits="userSpaceOnUse">
					<stop offset="0.520507" stop-color="white"></stop>
					<stop offset="1" stop-color="white" stop-opacity="0"></stop>
				</linearGradient>
			</defs>
		</svg></span><span class="absolute top-0 right-0 -z-10"><svg width="324" height="250"
			viewBox="0 0 324 220" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path opacity="0.05"
				d="M203 -28.5L4.87819e-05 250.5L881.5 250.5L881.5 -28.5002L203 -28.5Z"
				fill="url(#paint0_linear_971_6911)"></path>
			<defs>
				<linearGradient id="paint0_linear_971_6911" x1="60.5" y1="111" x2="287" y2="111"
					gradientUnits="userSpaceOnUse">
					<stop offset="0.520507" stop-color="white"></stop>
					<stop offset="1" stop-color="white" stop-opacity="0"></stop>
				</linearGradient>
			</defs>
		</svg></span><span class="absolute top-4 left-4 -z-10"><svg width="43" height="56"
			viewBox="0 0 43 56" fill="none" xmlns="http://www.w3.org/2000/svg">
			<g opacity="0.5">
				<circle cx="40.9984" cy="1.49626" r="1.49626"
					transform="rotate(90 40.9984 1.49626)" fill="white"></circle>
				<circle cx="27.8304" cy="1.49626" r="1.49626"
					transform="rotate(90 27.8304 1.49626)" fill="white"></circle>
				<circle cx="14.6644" cy="1.49626" r="1.49626"
					transform="rotate(90 14.6644 1.49626)" fill="white"></circle>
				<circle cx="1.49642" cy="1.49626" r="1.49626"
					transform="rotate(90 1.49642 1.49626)" fill="white"></circle>
				<circle cx="40.9984" cy="14.6642" r="1.49626"
					transform="rotate(90 40.9984 14.6642)" fill="white"></circle>
				<circle cx="27.8304" cy="14.6642" r="1.49626"
					transform="rotate(90 27.8304 14.6642)" fill="white"></circle>
				<circle cx="14.6644" cy="14.6642" r="1.49626"
					transform="rotate(90 14.6644 14.6642)" fill="white"></circle>
				<circle cx="1.49642" cy="14.6642" r="1.49626"
					transform="rotate(90 1.49642 14.6642)" fill="white"></circle>
				<circle cx="40.9984" cy="27.8302" r="1.49626"
					transform="rotate(90 40.9984 27.8302)" fill="white"></circle>
				<circle cx="27.8304" cy="27.8302" r="1.49626"
					transform="rotate(90 27.8304 27.8302)" fill="white"></circle>
				<circle cx="14.6644" cy="27.8302" r="1.49626"
					transform="rotate(90 14.6644 27.8302)" fill="white"></circle>
				<circle cx="1.49642" cy="27.8302" r="1.49626"
					transform="rotate(90 1.49642 27.8302)" fill="white"></circle>
				<circle cx="40.9984" cy="40.9982" r="1.49626"
					transform="rotate(90 40.9984 40.9982)" fill="white"></circle>
				<circle cx="27.8304" cy="40.9963" r="1.49626"
					transform="rotate(90 27.8304 40.9963)" fill="white"></circle>
				<circle cx="14.6644" cy="40.9982" r="1.49626"
					transform="rotate(90 14.6644 40.9982)" fill="white"></circle>
				<circle cx="1.49642" cy="40.9963" r="1.49626"
					transform="rotate(90 1.49642 40.9963)" fill="white"></circle>
				<circle cx="40.9984" cy="54.1642" r="1.49626"
					transform="rotate(90 40.9984 54.1642)" fill="white"></circle>
				<circle cx="27.8304" cy="54.1642" r="1.49626"
					transform="rotate(90 27.8304 54.1642)" fill="white"></circle>
				<circle cx="14.6644" cy="54.1642" r="1.49626"
					transform="rotate(90 14.6644 54.1642)" fill="white"></circle>
				<circle cx="1.49642" cy="54.1642" r="1.49626"
					transform="rotate(90 1.49642 54.1642)" fill="white"></circle>
			</g>
		</svg>
	</span>
</section>


<!-- about -->
@include('frontend.pages.home.partials.about')

<!-- features -->
@include('frontend.pages.home.partials.features')

<!-- Featured Products Section -->



<!-- Suppliers Section -->




<!-- Clinics Section -->

<!-- Jobs Section -->

<!-- Rental Space Section -->

<section class="bg-primary-gradient w-full">
	<div class="relative lg:flex items-center space-y-16 max-w-7xl mx-auto px-8 py-24 lg:space-y-0 lg:space-x-16">
		<div class="flex-grow space-y-8">
			<div class="space-y-4">
				<h2 class="text-white font-bold text-yellow-200 text-4xl">
					{{ __('what are you waiting for ?') }}
				</h2>

				<p class="text-xl text-white">
					{{ __('if you are a doctor, visit the course section to find the best courses for you!') }}
				</p>
			</div>

			<a href="{{ route('courses') }}"
				class="group inline-flex items-center justify-center px-6 text-lg sm:text-xl font-semibold tracking-tight text-white transition rounded-lg h-11 ring-2 ring-inset ring-white hover:bg-yellow-200 hover:text-yellow-800 hover:ring-yellow-200 focus:ring-yellow-200 focus:text-yellow-800 focus:bg-yellow-200 focus:outline-none">
				{{ __('find courses') }}
			</a>
		</div>
		<!--  image -->
		<div class="w-1/2 flex justify-end items-center">
			<img src="{{ asset('frontend/images/courses.jpg') }}" alt="Course Image"
				class="w-1/2 h-1/2 object-cover rounded-lg">
		</div>

	</div>
</section>
<!-- Courses Section -->

<!-- Registration Section -->
@include('frontend.pages.home.partials.registration-section')

<!-- Subscription Plans Section -->
<section id="subscriptions-plans" class="py-16 bg-gray-50 scroll-mt-20">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		@if(session('error') && session('upgrade_required'))
		<div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md">
			<div class="flex items-start">
				<div class="flex-shrink-0">
					<i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
				</div>
				<div class="ml-3 flex-1">
					<h3 class="text-sm font-semibold text-red-800 mb-2">
						{{ __('Subscription Required') }}
					</h3>
					<p class="text-sm text-red-700 mb-3">
						{{ session('error') }}
					</p>
					@if(session('usage'))
					@php
						$usage = session('usage');
						$used = $usage['used'] ?? 0;
						$limit = $usage['limit'] ?? null;
						$remaining = $usage['remaining'] ?? 0;
						$percentage = $limit ? round(($used / $limit) * 100, 1) : 0;
					@endphp
					<div class="bg-white rounded-md p-3 mb-3 border border-red-200">
						<div class="flex items-center justify-between mb-2">
							<span class="text-sm font-medium text-gray-700">{{ __('Usage') }}</span>
							<span class="text-sm text-gray-600">
								{{ $used }} / {{ $limit ?? __('unlimited') }}
							</span>
						</div>
						@if($limit)
						<div class="w-full bg-gray-200 rounded-full h-2 mb-2">
							<div class="bg-red-500 h-2 rounded-full"
								 style="width: {{ min(100, $percentage) }}%"></div>
						</div>
						<p class="text-xs text-gray-600">
							{{ __('Remaining') }}: {{ $remaining }}
						</p>
						@endif
					</div>
					@endif
				</div>
			</div>
		</div>
		@endif

		<div class="text-center mb-12">
			<h2 class="text-4xl font-bold text-gray-900 mb-4">{{ __('Choose Your Plan') }}</h2>
			<p class="text-xl text-gray-600">{{ __('Select the perfect subscription plan for your needs') }}</p>
		</div>

		<!-- Plan Type Selector -->
		<div class="flex justify-center gap-4 mb-8">
			<button onclick="showPlans('doctor')"
				class="px-6 py-3 rounded-lg font-semibold transition plan-type-btn {{ $planType === 'doctor' ? 'bg-primary-gradient text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
				<i class="fas fa-user-md mr-2"></i>{{ __('doctor plans') }}
			</button>
			<button onclick="showPlans('clinic')"
				class="px-6 py-3 rounded-lg font-semibold transition plan-type-btn {{ $planType === 'clinic' ? 'bg-primary-gradient text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
				<i class="fas fa-hospital mr-2"></i>{{ __('clinic plans') }}
			</button>
			<button onclick="showPlans('supplier')"
				class="px-6 py-3 rounded-lg font-semibold transition plan-type-btn {{ $planType === 'supplier' ? 'bg-primary-gradient text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
				<i class="fas fa-truck mr-2"></i>{{ __('supplier plans') }}
			</button>
		</div>

		<!-- Plans Grid -->
		<div id="plans-container">
			@if($planType === 'doctor')
				@include('frontend.pages.home.partials.plans-grid', ['plans' => $doctorPlans, 'type' => 'doctor', 'currentSubscription' => $currentSubscription, 'availableGateways' => $availableGateways ?? []])
			@elseif($planType === 'clinic')
				@include('frontend.pages.home.partials.plans-grid', ['plans' => $clinicPlans, 'type' => 'clinic', 'currentSubscription' => $currentSubscription, 'availableGateways' => $availableGateways ?? []])
			@else
				@include('frontend.pages.home.partials.plans-grid', ['plans' => $supplierPlans, 'type' => 'supplier', 'currentSubscription' => $currentSubscription, 'availableGateways' => $availableGateways ?? []])
			@endif
		</div>
	</div>
</section>

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

@push('scripts')
<script>
// Scroll to subscriptions section if hash is present
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#subscriptions-plans') {
        setTimeout(() => {
            const element = document.getElementById('subscriptions-plans');
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
});

function showPlans(type) {
    // Update active button
    document.querySelectorAll('.plan-type-btn').forEach(btn => {
        btn.classList.remove('bg-primary-gradient', 'text-white');
        btn.classList.add('bg-white', 'text-gray-700');
    });
    event.target.closest('button').classList.add('bg-primary-gradient', 'text-white');
    event.target.closest('button').classList.remove('bg-white', 'text-gray-700');

    // Load plans via AJAX
    fetch(`{{ route('home') }}?plan_type=${type}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.html) {
            document.getElementById('plans-container').innerHTML = data.html;
        }
    })
    .catch(error => {
        console.error('Error loading plans:', error);
    });
}
</script>
@endpush

<!-- plans -->
<div class="sm:flex sm:flex-col sm:align-center p-10" style="display: none;">
	<div class="relative self-center bg-slate-200 rounded-lg p-0.5 flex">
		<button type="button"
			class="relative w-1/2 rounded-md py-2 text-sm font-medium whitespace-nowrap focus:outline-none sm:w-auto sm:px-8 bg-slate-50 border-slate-50 text-slate-900 shadow-sm">Monthly
			billing
		</button>
		<button type="button"
			class="ml-0.5 relative w-1/2 border rounded-md py-2 text-sm font-medium whitespace-nowrap focus:outline-none sm:w-auto sm:px-8 border-transparent text-slate-900">Yearly
			billing
		</button>
	</div>
	<div
		class="mt-12 space-y-3 sm:mt-16 sm:space-y-0 sm:grid sm:grid-cols-3 sm:gap-6 md:max-w-5xl md:mx-auto xl:grid-cols-3">
		<div class="border border-slate-200 rounded-lg shadow-sm divide-y divide-slate-200">
			<div class="p-6">
				<h2 class="text-xl leading-6 font-bold text-slate-900">Starter</h2>
				<p class="mt-2 text-base text-slate-700 leading-tight">For new makers who want
					to fine-tune and test an
					idea.</p>
				<p class="mt-8">
					<span
						class="text-4xl font-bold text-slate-900 tracking-tighter">{{ __('EGP') }} 0</span>

					<span class="text-base font-medium text-slate-500">/mo</span>
				</p><a href="/sign-up"
					class="mt-8 block w-full bg-slate-900 rounded-md py-2 text-sm font-semibold text-white text-center">Join
					as a Starter</a>
			</div>
			<div class="pt-6 pb-8 px-6">
				<h3 class="text-sm font-bold text-slate-900 tracking-wide uppercase">What's
					included</h3>
				<ul role="list" class="mt-4 space-y-3">
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">1 landing page
							included</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">1,000
							visits/mo</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">Access to all UI
							blocks</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">50 conversion actions
							included</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">5% payment
							commission</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">Real-time
							analytics</span>
					</li>
				</ul>
			</div>
		</div>
		<div class="border border-slate-200 rounded-lg shadow-sm divide-y divide-slate-200">
			<div class="p-6">
				<h2 class="text-xl leading-6 font-bold text-slate-900">Superior</h2>
				<p class="mt-2 text-base text-slate-700 leading-tight">For creators with
					multiple ideas who want to
					efficiently test and refine them.</p>
				<p class="mt-8">
					<span
						class="text-4xl font-bold text-slate-900 tracking-tighter">{{ __('EGP') }} 8</span>

					<span class="text-base font-medium text-slate-500">/mo</span>
				</p><a href="/sign-up"
					class="mt-8 block w-full bg-slate-900 rounded-md py-2 text-sm font-semibold text-white text-center">Join
					as a Superior</a>
			</div>
			<div class="pt-6 pb-8 px-6">
				<h3 class="text-sm font-bold text-slate-900 tracking-wide uppercase">What's
					included</h3>
				<ul role="list" class="mt-4 space-y-3">
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">All Free
							features</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">5 landing pages
							included</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">50,000
							visits/mo</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">1,000 conversion
							actions included</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">1% payment
							commission</span>
					</li>
				</ul>
			</div>
		</div>
		<div class="border border-slate-200 rounded-lg shadow-sm divide-y divide-slate-200">
			<div class="p-6">
				<h2 class="text-xl leading-6 font-bold text-slate-900">Shipper</h2>
				<p class="mt-2 text-base text-slate-700 leading-tight">For productive shippers
					who want to work more
					efficiently.</p>
				<p class="mt-8">
					<span
						class="text-4xl font-bold text-slate-900 tracking-tighter">{{ __('EGP') }} 15</span>

					<span class="text-base font-medium text-slate-500">/mo</span>
				</p><a href="/sign-up"
					class="mt-8 block w-full bg-slate-900 rounded-md py-2 text-sm font-semibold text-white text-center">Join
					as a Shipper</a>
			</div>
			<div class="pt-6 pb-8 px-6">
				<h3 class="text-sm font-bold text-slate-900 tracking-wide uppercase">What's
					included</h3>
				<ul role="list" class="mt-4 space-y-3">
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">All Standard
							features</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">20 landing pages
							included</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">200,000
							visits/mo</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">5,000 conversion
							actions included</span>
					</li>
					<li class="flex space-x-3">
						<svg xmlns="http://www.w3.org/2000/svg"
							class="flex-shrink-0 h-5 w-5 text-green-400"
							width="24" height="24" viewBox="0 0 24 24"
							stroke-width="2" stroke="currentColor" fill="none"
							stroke-linecap="round" stroke-linejoin="round"
							aria-hidden="true">
							<path stroke="none" d="M0 0h24v24H0z" fill="none">
							</path>
							<path d="M5 12l5 5l10 -10"></path>
						</svg>
						<span class="text-base text-slate-700">No payment
							commission</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<!-- contact -->
@include('frontend.pages.home.partials.contact')

@endsection


@include('frontend.pages.home.scripts.index-js')
