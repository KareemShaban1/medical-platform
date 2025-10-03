@extends('frontend.layouts.app')


@section('content')

<!-- banners -->
@include('frontend.pages.home.partials.banners')

<!-- Featured Products Section -->
@include('frontend.pages.home.partials.products',['products'=>$products])



<!-- Suppliers Section -->
@include('frontend.pages.home.partials.suppliers',['suppliers'=>$suppliers])


<section class="relative z-10 overflow-hidden bg-indigo-600 py-16 px-8">
	<div class="container">
		<div class="-mx-4 flex flex-wrap items-center">
			<div class="w-full px-4 lg:w-1/2">
				<div class="text-center lg:text-left ">
					<div class="mb-10 lg:mb-0 ">
						<h1
							class="mt-0 mb-3 text-3xl font-bold leading-tight sm:text-4xl sm:leading-tight md:text-[40px] md:leading-tight text-white ">
							{{ __('Welcome to our Medical Platform') }}
						</h1>
						<p
							class="w-full text-base font-medium leading-relaxed sm:text-lg sm:leading-relaxed text-white">
							{{ __('Explore our platform and find the best medical services for you.') }}
						</p>
					</div>
				</div>
			</div>
			<div class="w-full px-4 lg:w-1/2">
				<div class="text-center lg:text-right"><a
						class="font-semibold rounded-lg mx-auto inline-flex items-center justify-center bg-white py-4 px-9 hover:bg-opacity-90"
						href="#">{{ __('Create Your First Appointment') }}</a>
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

<!-- Clinics Section -->
@include('frontend.pages.home.partials.clinics',['clinics'=>$clinics])

<!-- Jobs Section -->
@include('frontend.pages.home.partials.jobs',['jobs'=>$jobs])

<!-- Rental Space Section -->
@include('frontend.pages.home.partials.rental-spaces',['rentalSpaces'=>$rentalSpaces])

<section class="bg-gray-900 w-full">
	<div class="relative lg:flex items-center space-y-16 max-w-7xl mx-auto px-8 py-24 lg:space-y-0 lg:space-x-16">
		<div class="flex-grow space-y-8">
			<div class="space-y-4">
				<h2 class="text-white font-bold text-yellow-200 text-4xl">
					{{ __('what are you waiting for?') }} 🚀
				</h2>

				<p class="text-xl text-white">
					{{ __('Visit the course section to find the best courses for you!') }}
				</p>
			</div>

			<a href="#" target="_blank"
				class="group inline-flex items-center justify-center px-6 text-lg sm:text-xl font-semibold tracking-tight text-white transition rounded-lg h-11 ring-2 ring-inset ring-white hover:bg-yellow-200 hover:text-yellow-800 hover:ring-yellow-200 focus:ring-yellow-200 focus:text-yellow-800 focus:bg-yellow-200 focus:outline-none">
				{{ __('Find Courses') }}
			</a>
		</div>
	</div>
</section>
<!-- Courses Section -->
@include('frontend.pages.home.partials.courses',['courses'=>$courses])

<!-- plans -->
<div class="sm:flex sm:flex-col sm:align-center p-10">
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
                <p class="mt-2 text-base text-slate-700 leading-tight">For new makers who want to fine-tune and test an
                    idea.</p>
                <p class="mt-8">
                    <span class="text-4xl font-bold text-slate-900 tracking-tighter">$0</span>

                    <span class="text-base font-medium text-slate-500">/mo</span>
                </p><a href="/sign-up"
                    class="mt-8 block w-full bg-slate-900 rounded-md py-2 text-sm font-semibold text-white text-center">Join
                    as a Starter</a>
            </div>
            <div class="pt-6 pb-8 px-6">
                <h3 class="text-sm font-bold text-slate-900 tracking-wide uppercase">What's included</h3>
                <ul role="list" class="mt-4 space-y-3">
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">1 landing page included</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">1,000 visits/mo</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">Access to all UI blocks</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">50 conversion actions included</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">5% payment commission</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">Real-time analytics</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border border-slate-200 rounded-lg shadow-sm divide-y divide-slate-200">
            <div class="p-6">
                <h2 class="text-xl leading-6 font-bold text-slate-900">Superior</h2>
                <p class="mt-2 text-base text-slate-700 leading-tight">For creators with multiple ideas who want to
                    efficiently test and refine them.</p>
                <p class="mt-8">
                    <span class="text-4xl font-bold text-slate-900 tracking-tighter">$8</span>

                    <span class="text-base font-medium text-slate-500">/mo</span>
                </p><a href="/sign-up"
                    class="mt-8 block w-full bg-slate-900 rounded-md py-2 text-sm font-semibold text-white text-center">Join
                    as a Superior</a>
            </div>
            <div class="pt-6 pb-8 px-6">
                <h3 class="text-sm font-bold text-slate-900 tracking-wide uppercase">What's included</h3>
                <ul role="list" class="mt-4 space-y-3">
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">All Free features</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">5 landing pages included</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">50,000 visits/mo</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">1,000 conversion actions included</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">1% payment commission</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border border-slate-200 rounded-lg shadow-sm divide-y divide-slate-200">
            <div class="p-6">
                <h2 class="text-xl leading-6 font-bold text-slate-900">Shipper</h2>
                <p class="mt-2 text-base text-slate-700 leading-tight">For productive shippers who want to work more
                    efficiently.</p>
                <p class="mt-8">
                    <span class="text-4xl font-bold text-slate-900 tracking-tighter">$15</span>

                    <span class="text-base font-medium text-slate-500">/mo</span>
                </p><a href="/sign-up"
                    class="mt-8 block w-full bg-slate-900 rounded-md py-2 text-sm font-semibold text-white text-center">Join
                    as a Shipper</a>
            </div>
            <div class="pt-6 pb-8 px-6">
                <h3 class="text-sm font-bold text-slate-900 tracking-wide uppercase">What's included</h3>
                <ul role="list" class="mt-4 space-y-3">
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">All Standard features</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">20 landing pages included</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">200,000 visits/mo</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">5,000 conversion actions included</span>
                    </li>
                    <li class="flex space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-green-400" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        <span class="text-base text-slate-700">No payment commission</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection


@include('frontend.pages.home.scripts.index-js')