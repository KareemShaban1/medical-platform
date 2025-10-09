<nav class="navbar">
	<div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-2 sm:p-4">
		<a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-3 rtl:space-x-reverse">
			<!-- <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" /> -->
			<span class="navbar-brand text-lg sm:text-xl lg:text-2xl font-semibold whitespace-nowrap">Medical
				Platform</span>
		</a>
		<div class="flex items-center md:order-2 space-x-1 md:space-x-0 rtl:space-x-reverse">
			<button type="button" data-dropdown-toggle="language-dropdown-menu"
				class="inline-flex items-center gap-1 sm:gap-2 font-medium justify-center px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-900 dark:text-white rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
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
			<div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm dark:bg-gray-700"
				id="language-dropdown-menu">
				<ul class="py-2 font-medium" role="none">
					@foreach(LaravelLocalization::getSupportedLocales() as $localeCode =>
					$properties)
					<li>
						<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
							rel="alternate" hreflang="{{ $localeCode }}"
							href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
							{{ $properties['native'] }}
						</a>
					</li>
					@endforeach
				</ul>
			</div>
			<button data-collapse-toggle="navbar-language" type="button"
				class="inline-flex items-center p-2 w-8 h-8 sm:w-10 sm:h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
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
					class="relative inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-600">
					<svg class="w-5 h-5" aria-hidden="true"
						xmlns="http://www.w3.org/2000/svg" fill="none"
						viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round"
							stroke-width="2"
							d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
					</svg>
					<span class="sr-only">View cart</span>
					<!-- Cart count badge -->
					<div id="cart-count-badge"
						class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -end-1 dark:border-gray-900">
						0
					</div>
				</button>

				<!-- Cart Dropdown -->
				<div id="cart-dropdown"
					class="z-50 hidden my-4 w-80 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700">
					<div
						class="px-4 py-3 text-sm text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600">
						<div class="font-semibold">Shopping Cart</div>
					</div>

					<!-- Cart Items -->
					<div id="cart-items-container" class="max-h-96 overflow-y-auto">
						<!-- Empty cart message -->
						<div id="empty-cart-message"
							class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
							Your cart is empty
						</div>

						<!-- Cart items will be loaded here dynamically -->
						<ul id="cart-items-list" class="py-2 hidden">
							<!-- Example item structure (will be populated via JS/backend) -->
						</ul>
					</div>

					<!-- Cart Footer -->
					<div id="cart-footer"
						class="hidden px-4 py-3 bg-gray-50 dark:bg-gray-800">
						<div class="flex justify-between items-center mb-3">
							<span
								class="text-sm font-medium text-gray-900 dark:text-white">Subtotal:</span>
							<span id="cart-subtotal"
								class="text-sm font-bold text-gray-900 dark:text-white">$0.00</span>
						</div>
						<a href="{{ route('cart.index') }}"
							class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
							View Cart
						</a>
					</div>
				</div>
			</div>

			@auth('clinic')
			<div>
				<!-- my account dropdown -->
				<button type="button" data-dropdown-toggle="my-account-dropdown"
					class="inline-flex items-center gap-1 sm:gap-2 font-medium justify-center px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-900 dark:text-white rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
					<i class="fa-solid fa-user-circle"></i>
				</button>

				<!-- Dropdown -->
				<div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm dark:bg-gray-700"
					id="my-account-dropdown">
					<ul class="py-2 font-medium" role="none">

						<li>
							<!-- my orders -->
							<a href="{{ route('profile.orders') }}"
								class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
								{{ __('My Orders') }}
								<i
									class="fa-solid fa-cart-shopping"></i>
							</a>
						</li>
						<!-- logout -->
						<li>
							<form method="POST"
								action="{{ route('clinic.logout') }}">
								@csrf
								<button type="submit"
									class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
									{{ __('Logout') }}
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
				class="flex flex-col font-medium p-2 sm:p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-4 lg:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
				<li>
					<a href="{{ route('home') }}" @if (Route::is('home'))
						class="block mx-8 rtl:mx-8 py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 md:dark:text-[var(--primary-color)] text-sm sm:text-base"
						@else
						class="block mx-8 rtl:mx-8 py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 md:dark:text-gray-500 text-sm sm:text-base"
						@endif aria-current="page">Home</a>
				</li>
				<li>
					<a href="{{ route('products') }}" @if (Route::is('products'))
						class="block py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 md:dark:text-[var(--primary-color)] text-sm sm:text-base"
						@else
						class="block py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 md:dark:text-gray-500 text-sm sm:text-base"
						@endif>Products</a>
				</li>
				<li>
					<a href="{{ route('jobs') }}" @if (Route::is('jobs'))
						class="block py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 md:dark:text-[var(--primary-color)] text-sm sm:text-base"
						@else
						class="block py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 md:dark:text-gray-500 text-sm sm:text-base"
						@endif>Jobs</a>
				</li>
				<li>
					<a href="{{ route('blogs') }}" @if (Route::is('blogs'))
						class="block py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 md:dark:text-[var(--primary-color)] text-sm sm:text-base"
						@else
						class="block py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 md:dark:text-gray-500 text-sm sm:text-base"
						@endif>Blog</a>
				</li>
				<li>
					<a href="{{ route('courses') }}" @if (Route::is('courses'))
						class="block py-2 px-3 rounded-sm md:bg-transparent md:text-[var(--primary-color)] md:p-0 md:dark:text-[var(--primary-color)] text-sm sm:text-base"
						@else
						class="block py-2 px-3 rounded-sm md:bg-transparent md:text-gray-700 md:p-0 md:dark:text-gray-500 text-sm sm:text-base"
						@endif>Courses</a>
				</li>


				<!-- clinic  -->
				<li>
					<button id="clinicLink" data-dropdown-toggle="clinicDropdown"
						@if(Route::is('clinics'))
						class="flex items-center justify-between w-full py-2 px-3 text-[var(--primary-color)] rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-white md:dark:hover:text-blue-500 dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent text-sm sm:text-base"
						@else
						class="flex items-center justify-between w-full py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-white md:dark:hover:text-blue-500 dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent text-sm sm:text-base"
						@endif>
						Clinics
						<svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
							xmlns="http://www.w3.org/2000/svg" fill="none"
							viewBox="0 0 10 6">
							<path stroke="currentColor" stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="m1 1 4 4 4-4" />
						</svg>
					</button>
					<div id="clinicDropdown"
						class="z-20 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-40 sm:w-44 dark:bg-gray-700 dark:divide-gray-600">
						<ul class="py-2 text-sm text-gray-700 dark:text-gray-400"
							aria-labelledby="clinicLink">
							<li>
								<a href="{{ route('clinics') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-xs sm:text-sm">View
									Clinics</a>
							</li>
							<li>
								<a href="{{ route('clinic.register-clinic') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-xs sm:text-sm">Register
									Clinic</a>
							</li>
							<li>
								<a href="{{ url('/clinic/login') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-xs sm:text-sm">Login
									Clinic</a>
							</li>
						</ul>
					</div>
				</li>


				<li>
					<button id="supplierLink" data-dropdown-toggle="supplierDropdown"
						@if(Route::is('suppliers'))
						class="flex items-center justify-between w-full py-2 px-3 text-[var(--primary-color)] rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-white md:dark:hover:text-blue-500 dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent text-sm sm:text-base"
						@else
						class="flex items-center justify-between w-full py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-white md:dark:hover:text-blue-500 dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent text-sm sm:text-base"
						@endif>
						Suppliers
						<svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
							xmlns="http://www.w3.org/2000/svg" fill="none"
							viewBox="0 0 10 6">
							<path stroke="currentColor" stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="m1 1 4 4 4-4" />
						</svg>
					</button>
					<div id="supplierDropdown"
						class="z-20 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-40 sm:w-44 dark:bg-gray-700 dark:divide-gray-600">
						<ul class="py-2 text-sm text-gray-700 dark:text-gray-400"
							aria-labelledby="supplierLink">
							<li>
								<a href="{{ route('suppliers') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-xs sm:text-sm">View
									Suppliers</a>
							</li>
							<li>
								<a href="{{ route('supplier.register-supplier') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-xs sm:text-sm">Register
									Supplier</a>
							</li>
							<li>
								<a href="{{ url('/supplier/login') }}"
									class="block px-3 sm:px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-xs sm:text-sm">Login
									Supplier</a>
							</li>
						</ul>
					</div>
				</li>

				<!-- Patient/User Authentication -->
				<li>
					<button id="patientLink" data-dropdown-toggle="patientDropdown"
						class="flex items-center justify-between w-full py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-white md:dark:hover:text-blue-500 dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent">
						{{ __('Account') }}
						<svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
							xmlns="http://www.w3.org/2000/svg" fill="none"
							viewBox="0 0 10 6">
							<path stroke="currentColor" stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="m1 1 4 4 4-4" />
						</svg>
					</button>
					<div id="patientDropdown"
						class="z-20 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
						<ul class="py-2 text-sm text-gray-700 dark:text-gray-400"
							aria-labelledby="patientLink">
							@auth('patient')
							<li>
								<a href="{{ route('user.dashboard') }}"
									class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Dashboard</a>
							</li>
							<li>
								<a href="{{ route('user.tickets.index') }}"
									class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Manage
									My Tickets</a>
							</li>
							<li>
								<form method="POST"
									action="{{ route('user.logout') }}"
									class="inline w-full">
									@csrf
									<button type="submit"
										class="text-left w-full block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Logout</button>
								</form>
							</li>
							@else
							<li>
								<a href="{{ route('register') }}"
									class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Register</a>
							</li>
							<li>
								<a href="{{ route('login') }}"
									class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Login</a>
							</li>
							@endauth
						</ul>
					</div>
				</li>

			</ul>
		</div>
	</div>
</nav>