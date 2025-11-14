<nav class="navbar">
	<div class="max-w-screen-2xl flex flex-wrap items-center justify-between mx-auto p-2 sm:p-4">
		<a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-3 rtl:space-x-reverse">
			<!-- <span
			class="navbar-brand text-lg sm:text-xl lg:text-2xl font-semibold whitespace-nowrap">{{ __('Teb Plus') }}</span> -->

			<img src="{{asset('frontend/images/logo-teb-plus.png')}}"
				style=" height: 70px; width: 180px;">
		</a>
		<div class=" flex items-center md:order-2 space-x-1 md:space-x-0 rtl:space-x-reverse">
			<button type="button" data-dropdown-toggle="language-dropdown-menu"
				class="inline-flex items-center gap-1 sm:gap-2 font-medium justify-center px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-900 rounded-lg cursor-pointer hover:bg-gray-100">
				@if (App::getLocale() == 'ar')
				<span
					class="hidden sm:inline">{{ LaravelLocalization::getCurrentLocaleName() }}</span>
				<img src="{{ asset('backend/assets/images/flags/eg.png') }}" alt=""
					class="w-4 h-4 sm:w-5 sm:h-5">
				@else
				<span
					class="hidden sm:inline">{{ LaravelLocalization::getCurrentLocaleName() }}</span>
				<img src="{{ asset('backend/assets/images/flags/us.png') }}" alt=""
					class="w-4 h-4 sm:w-5 sm:h-5">
				@endif

			</button>
			<!-- Dropdown -->
			<div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm"
				id="language-dropdown-menu">
				<ul class="py-2 font-medium" role="none">
					@foreach(LaravelLocalization::getSupportedLocales() as $localeCode =>
					$properties)
					<li>
						<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
							rel="alternate" hreflang="{{ $localeCode }}"
							href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
							{{ $properties['native'] }}
						</a>
					</li>
					@endforeach
				</ul>
			</div>
			<button data-collapse-toggle="navbar-language" type="button"
				class="inline-flex items-center p-2 w-8 h-8 sm:w-10 sm:h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
				aria-controls="navbar-language" aria-expanded="false">
				<span class="sr-only">Open main menu</span>
				<svg class="w-4 h-4 sm:w-5 sm:h-5" aria-hidden="true"
					xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
					<path stroke="currentColor" stroke-linecap="round"
						stroke-linejoin="round" stroke-width="2"
						d="M1 1h15M1 7h15M1 13h15" />
				</svg>
			</button>

			<div>
				<!-- cart -->
				<button type="button" data-dropdown-toggle="cart-dropdown"
					class="relative inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">
					<svg class="w-5 h-5" aria-hidden="true"
						xmlns="http://www.w3.org/2000/svg" fill="none"
						viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round"
							stroke-width="2"
							d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
					</svg>
					<span class="sr-only">{{ __('view cart') }}</span>
					<!-- Cart count badge -->
					<div id="cart-count-badge"
						class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-primary-gradient border-2 border-white rounded-full -top-1 -end-1">
						0
					</div>
				</button>

				<!-- Cart Dropdown -->
				<div id="cart-dropdown"
					class="z-50 hidden my-4 w-80 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg">
					<div class="px-4 py-3 text-sm text-gray-900 border-b border-gray-200">
						<div class="font-semibold">
							{{ __('shopping cart') }}</div>
					</div>

					<!-- Cart Items -->
					<div id="cart-items-container" class="max-h-96 overflow-y-auto">
						<!-- Empty cart message -->
						<div id="empty-cart-message"
							class="px-4 py-6 text-center text-sm text-gray-500">
							{{ __('your cart is empty') }}
						</div>

						<!-- Cart items will be loaded here dynamically -->
						<ul id="cart-items-list" class="py-2 hidden">
							<!-- Example item structure (will be populated via JS/backend) -->
						</ul>
					</div>

					<!-- Cart Footer -->
					<div id="cart-footer" class="hidden px-4 py-3 bg-gray-50">
						<div class="flex justify-between items-center mb-3">
							<span
								class="text-sm font-medium text-gray-900">{{ __('subtotal') }}:</span>
							<span id="cart-subtotal"
								class="text-sm font-bold text-gray-900">$0.00</span>
						</div>
						<a href="{{ route('cart.index') }}"
							class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 ">
							{{ __('view cart') }}
						</a>
					</div>
				</div>
			</div>

			@auth('clinic')
			<div>
				<!-- my account dropdown -->
				<button type="button" data-dropdown-toggle="my-account-dropdown"
					class="inline-flex items-center gap-1 sm:gap-2 font-medium justify-center px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-900 rounded-lg cursor-pointer hover:bg-gray-100">
					<i class="fa-solid fa-user-circle"></i>
				</button>

				<!-- Dropdown -->
				<div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm"
					id="my-account-dropdown">
					<ul class="py-2 font-medium" role="none">
						@if(auth('clinic')->user()->has_clinic)
						<!-- Clinic user with clinic -->
						<li>
							<!-- my orders -->
							<a href="{{ route('profile.orders') }}"
								class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
								{{ __('my orders') }}
								<i
									class="fa-solid fa-cart-shopping"></i>
							</a>
						</li>
						@else
						<!-- Standalone doctor -->
						<li>
							<!-- doctor dashboard -->
							<a href="{{ route('doctor.dashboard') }}"
								class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
								{{ __('Dashboard') }}
								<i
									class="fa-solid fa-tachometer-alt"></i>
							</a>
						</li>
						<li>
							<!-- my orders -->
							<a href="{{ route('doctor.orders.index') }}"
								class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
								{{ __('my orders') }}
								<i
									class="fa-solid fa-cart-shopping"></i>
							</a>
						</li>
						@endif
						<!-- logout -->
						<li>
							<form method="POST"
								action="{{ route('clinic.logout') }}">
								@csrf
								<button type="submit"
									class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
									{{ __('logout') }}
								</button>
							</form>
						</li>
					</ul>
				</div>
			</div>
			@endauth
		</div>
		<div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1"
			id="navbar-language">
			<ul
				class="flex flex-col font-medium p-2 sm:p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white">
				<li>
					<a href="{{ route('home') }}" @if (Route::is('home'))
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 text-sm sm:text-base"
						@else
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 text-sm sm:text-base"
						@endif aria-current="page">{{ __('home') }}</a>
				</li>
				<li>
					<a href="{{ route('products') }}" @if (Route::is('products'))
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 text-sm sm:text-base"
						@else
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 text-sm sm:text-base"
						@endif>{{ __('products') }}</a>
				</li>
				<li>
					<a href="{{ route('rental-spaces') }}" @if(Route::is('rental-spaces'))
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 text-sm sm:text-base"
						@else
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 text-sm sm:text-base"
						@endif>{{ __('rental spaces') }}</a>
				</li>
				<li>
					<a href="{{ route('jobs') }}" @if (Route::is('jobs'))
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 text-sm sm:text-base"
						@else
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 text-sm sm:text-base"
						@endif>{{ __('jobs') }}</a>
				</li>
				<li>
					<a href="{{ route('blogs') }}" @if (Route::is('blogs'))
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 text-sm sm:text-base"
						@else
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 text-sm sm:text-base"
						@endif>{{ __('blog') }}</a>
				</li>
				<li>
					<a href="{{ route('courses') }}" @if (Route::is('courses'))
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 text-sm sm:text-base"
						@else
						class="block mx-2 rtl:mx-2 py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 text-sm sm:text-base"
						@endif>{{ __('courses') }}</a>
				</li>


				<!-- clinic  -->
				<li>
					<button id="clinicLink" data-dropdown-toggle="clinicDropdown"
						@if(Route::is('clinics'))
						class="flex items-center justify-between z-100 w-full mx-2 rtl:mx-2 py-2 px-3 text-[var(--primary-color)] rounded-sm  md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto text-sm sm:text-base"
						@else
						class="flex items-center justify-between z-100 w-full mx-2 rtl:mx-2 py-2 px-3 text-gray-900 rounded-sm md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto text-sm sm:text-base"
						@endif>
						{{ __('clinics') }}
						<svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
							xmlns="http://www.w3.org/2000/svg" fill="none"
							viewBox="0 0 10 6">
							<path stroke="currentColor" stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="m1 1 4 4 4-4" />
						</svg>
					</button>
					<div id="clinicDropdown"
						class="hidden z-[9999] absolute font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-40 sm:w-44">
						<ul class="py-2 text-sm text-gray-700"
							aria-labelledby="clinicLink">
							<li>
								<a href="{{ route('clinics') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 text-xs sm:text-sm">{{ __('view clinics') }}</a>
							</li>

							@if(!auth('clinic')->check() &&
							!auth('patient')->check() &&
							!auth('supplier')->check() )
							<li>
								<a href="{{ route('clinic.register-clinic') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 text-xs sm:text-sm">{{ __('register clinic') }}</a>
							</li>
							<li>
								<a href="{{ url('/clinic/login') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100  text-xs sm:text-sm">{{ __('login clinic') }}</a>
							</li>
							@endif

						</ul>
					</div>
				</li>


				<li>
					<button id="supplierLink" data-dropdown-toggle="supplierDropdown"
						@if(Route::is('suppliers'))
						class="flex items-center justify-between w-full mx-2 rtl:mx-2 py-2 px-3 text-[var(--primary-color)] rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto text-sm sm:text-base"
						@else
						class="flex items-center justify-between w-full mx-2 rtl:mx-2 py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto text-sm sm:text-base"
						@endif>
						{{ __('suppliers') }}
						<svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
							xmlns="http://www.w3.org/2000/svg" fill="none"
							viewBox="0 0 10 6">
							<path stroke="currentColor" stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="m1 1 4 4 4-4" />
						</svg>
					</button>
					<div id="supplierDropdown"
						class="hidden z-[9999] absolute font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-40 sm:w-44">
						<ul class="py-2 text-sm text-gray-700"
							aria-labelledby="supplierLink">
							<li>
								<a href="{{ route('suppliers') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 text-xs sm:text-sm">
									{{ __('view suppliers') }}</a>
							</li>
							@if(!auth('clinic')->check() &&
							!auth('patient')->check() &&
							!auth('supplier')->check() )
							<li>
								<a href="{{ route('supplier.register-supplier') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 text-xs sm:text-sm">
									{{ __('register supplier') }}</a>
							</li>
							<li>
								<a href="{{ url('/supplier/login') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 text-xs sm:text-sm">
									{{ __('login supplier') }}
								</a>
							</li>
							@endif
						</ul>
					</div>
				</li>

				<!-- Patient/User Authentication -->
				<li>
					<button id="patientLink" data-dropdown-toggle="patientDropdown"
						class="flex items-center justify-between w-full mx-2 rtl:mx-2 py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto">
						{{ __('patient') }}
						<svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
							xmlns="http://www.w3.org/2000/svg" fill="none"
							viewBox="0 0 10 6">
							<path stroke="currentColor" stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="m1 1 4 4 4-4" />
						</svg>
					</button>
					<div id="patientDropdown"
						class="hidden z-[9999] absolute font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44">
						<ul class="py-2 text-sm text-gray-700"
							aria-labelledby="patientLink">
							@auth('patient')
							<li>
								<a href="{{ route('user.dashboard') }}"
									class="block px-4 py-2 hover:bg-gray-100">{{ __('dashboard') }}</a>
							</li>
							<li>
								<a href="{{ route('user.tickets.index') }}"
									class="block px-4 py-2 hover:bg-gray-100">{{ __('manage my tickets') }}</a>
							</li>
							<li>
								<form method="POST"
									action="{{ route('user.logout') }}"
									class="inline w-full">
									@csrf
									<button type="submit"
										class="text-left w-full block px-4 py-2 hover:bg-gray-100">Logout</button>
								</form>
							</li>
							@else
							@if(!auth('clinic')->check() &&
							!auth('patient')->check() &&
							!auth('supplier')->check() )
							<li>
								<a href="{{ url('/patient/register') }}"
									class="block px-4 py-2 hover:bg-gray-100">{{ __('patient register') }}</a>
							</li>
							<li>
								<a href="{{ url('/patient/login') }}"
									class="block px-4 py-2 hover:bg-gray-100">{{ __('patient login') }}</a>
							</li>
							@endif
							@endauth
						</ul>
					</div>
				</li>

				<!-- Doctor Authentication -->
				@php
				$isDoctor = auth('clinic')->check() &&
				optional(auth('clinic')->user())->clinic_id === null;
				@endphp
				@if(!$isDoctor && !auth('clinic')->check())
				<li>
					<button id="doctorLink" data-dropdown-toggle="doctorDropdown"
						class="flex items-center justify-between w-full mx-2 rtl:mx-2 py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto">
						{{ __('doctor') }}
						<svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
							xmlns="http://www.w3.org/2000/svg" fill="none"
							viewBox="0 0 10 6">
							<path stroke="currentColor" stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="m1 1 4 4 4-4" />
						</svg>
					</button>
					<div id="doctorDropdown"
						class="hidden z-[9999] absolute font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44">
						<ul class="py-2 text-sm text-gray-700"
							aria-labelledby="doctorLink">
							<li>
								<a href="{{ route('doctors.index') }}"
									class="block px-4 py-2 hover:bg-gray-100">{{ __('view doctors') }}</a>
							</li>
							<li>
								<a href="{{ route('doctor.register.show') }}"
									class="block px-4 py-2 hover:bg-gray-100">{{ __('doctor register') }}</a>
							</li>
							<li>
								<a href="{{ url('/clinic/login')}}"
									class="block px-4 py-2 hover:bg-gray-100">{{ __('doctor login') }}</a>
							</li>
						</ul>
					</div>
				</li>
				@elseif($isDoctor)
				<li>
					<button id="doctorLink" data-dropdown-toggle="doctorDropdown"
						class="flex items-center justify-between w-full mx-2 rtl:mx-2 py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto">
						{{ __('doctor') }}
						<svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
							xmlns="http://www.w3.org/2000/svg" fill="none"
							viewBox="0 0 10 6">
							<path stroke="currentColor" stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="m1 1 4 4 4-4" />
						</svg>
					</button>
					<div id="doctorDropdown"
						class="hidden z-[9999] absolute font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44">
						<ul class="py-2 text-sm text-gray-700"
							aria-labelledby="doctorLink">
							<li>
								<a href="{{ route('doctor.dashboard') }}"
									class="block px-4 py-2 hover:bg-gray-100">{{ __('dashboard') }}</a>
							</li>
							<li>
								<a href="{{ route('doctors.index') }}"
									class="block px-4 py-2 hover:bg-gray-100">{{ __('view doctors') }}</a>
							</li>
							<li>
								<form method="POST"
									action="{{ route('clinic.logout') }}"
									class="inline w-full">
									@csrf
									<button type="submit"
										class="block px-4 py-2 hover:bg-gray-100">{{ __('logout') }}</button>
								</form>
							</li>
						</ul>
					</div>
				</li>
				@endif

			</ul>
		</div>
	</div>
</nav>