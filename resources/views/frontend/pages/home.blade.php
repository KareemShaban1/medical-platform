@extends('frontend.layouts.app')


@section('content')

<!-- Banner Section -->
<section class="relative">
	<div class="swiper bannerSwiper">
		<div class="swiper-wrapper">
			<div class="swiper-slide">
				<div
					class="relative h-96 bg-gradient-to-r from-blue-600 to-blue-800 flex items-center justify-center">
					<div class="text-center text-white">
						<h1 class="text-4xl font-bold mb-4">
							Welcome to Medical Platform
						</h1>
						<p class="text-xl">Your trusted
							healthcare partner</p>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div
					class="relative h-96 bg-gradient-to-r from-green-600 to-green-800 flex items-center justify-center">
					<div class="text-center text-white">
						<h1 class="text-4xl font-bold mb-4">
							Quality Healthcare</h1>
						<p class="text-xl">Professional medical
							services</p>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div
					class="relative h-96 bg-gradient-to-r from-purple-600 to-purple-800 flex items-center justify-center">
					<div class="text-center text-white">
						<h1 class="text-4xl font-bold mb-4">
							Advanced Technology</h1>
						<p class="text-xl">Cutting-edge medical
							solutions</p>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div
					class="relative h-96 bg-gradient-to-r from-red-600 to-red-800 flex items-center justify-center">
					<div class="text-center text-white">
						<h1 class="text-4xl font-bold mb-4">
							Emergency Care</h1>
						<p class="text-xl">24/7 emergency
							services</p>
					</div>
				</div>
			</div>
			<div class="swiper-slide">
				<div
					class="relative h-96 bg-gradient-to-r from-indigo-600 to-indigo-800 flex items-center justify-center">
					<div class="text-center text-white">
						<h1 class="text-4xl font-bold mb-4">
							Specialized Treatment</h1>
						<p class="text-xl">Expert medical
							specialists</p>
					</div>
				</div>
			</div>
		</div>
		<div class="swiper-pagination"></div>
		<div class="swiper-button-next"></div>
		<div class="swiper-button-prev"></div>
	</div>
</section>

<!-- Featured Products Section -->
<section class="py-16 bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex justify-between items-center mb-12">
			<div class="text-center">
				<h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Products
				</h2>
				<p class="text-lg text-gray-600">Discover our top medical products</p>
			</div>
			<div class="text-center mt-8">
				<a href="{{ route('products') }}"
					class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
					View All Products
					<i class="fas fa-arrow-right ml-2"></i>
				</a>
			</div>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
			@for($i = 1; $i <= 12; $i++) <div
				class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
				<div class="h-48 bg-gray-200 flex items-center justify-center">
					<i class="fas fa-pills text-4xl text-gray-400"></i>
				</div>
				<div class="p-4">
					<h3 class="font-semibold text-lg mb-2">Product
						{{ $i }}
					</h3>
					<p class="text-gray-600 text-sm mb-3">High-quality
						medical product</p>
					<div class="flex justify-between items-center">
						<span class="text-xl font-bold text-blue-600">$99.99</span>
						<button
							class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Add
							to Cart</button>
					</div>
				</div>
		</div>
		@endfor
	</div>

	</div>
</section>

<!-- Clinics Section -->
<section class="py-16 bg-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex justify-between items-center mb-12">
		<div class="text-center">
			<h2 class="text-3xl font-bold text-gray-900 mb-4">Our Clinics</h2>
			<p class="text-lg text-gray-600">Professional medical clinics near you</p>
		</div>
		<div class="text-center mt-8">
		<a href="{{ route('clinics') }}"
			class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
			View All Clinics
			<i class="fas fa-arrow-right ml-2"></i>
		</a>
	</div>
		</div>
		<div class="swiper clinicsSwiper">
			<div class="swiper-wrapper">
				@for($i = 1; $i <= 8; $i++) <div class="swiper-slide">
					<div
						class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
						<div
							class="h-48 bg-gray-200 flex items-center justify-center">
							<i
								class="fas fa-hospital text-4xl text-gray-400"></i>
						</div>
						<div class="p-4">
							<h3 class="font-semibold text-lg mb-2">
								Clinic {{ $i }}</h3>
							<p class="text-gray-600 text-sm mb-2">
								Specialized medical services
							</p>
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i
									class="fas fa-map-marker-alt mr-2"></i>
								<span>Location {{ $i }}</span>
							</div>
							<button
								class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">View
								Details</button>
						</div>
					</div>
			</div>
			@endfor
		</div>
		<div class="swiper-pagination"></div>
	</div>
	
	</div>
</section>

<!-- Suppliers Section -->
<section class="py-16 bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex justify-between items-center mb-12">
			<div class="text-center">
				<h2 class="text-3xl font-bold text-gray-900 mb-4">Medical Suppliers
				</h2>
				<p class="text-lg text-gray-600">Trusted suppliers for medical
					equipment</p>
			</div>
			<div class="text-center">
				<a href="{{ route('suppliers') }}"
					class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
					View All Suppliers
					<i class="fas fa-arrow-right ml-2"></i>
				</a>
			</div>
		</div>

		<div class="swiper suppliersSwiper">
			<div class="swiper-wrapper">
				@foreach($suppliers as $supplier)
				<div class="swiper-slide">
					<div
						class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
						<div
							class="h-48 bg-gray-200 flex items-center justify-center">
							<i
								class="fas fa-truck text-4xl text-gray-400"></i>
						</div>
						<div class="p-4">
							<h3 class="font-semibold text-lg mb-2 truncate">
								{{ $supplier->name }}
							</h3>
							<p class="text-gray-600 text-sm mb-2">
								Medical equipment supplier</p>
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i
									class="fas fa-star text-yellow-400 mr-1"></i>
								<span>4.5 ({{ rand(50, 200) }}
									reviews)</span>
							</div>
							<button
								class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">Contact
								Supplier</button>
						</div>
					</div>
				</div>
				@endforeach
			</div>
			<!-- <div class="swiper-pagination"></div> -->
		</div>

	</div>
</section>

<!-- Jobs Section -->
<section class="py-16 bg-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex justify-between items-center mb-12">
			<div class="text-center mb-12">
				<h2 class="text-3xl font-bold text-gray-900 mb-4">Medical Jobs</h2>
				<p class="text-lg text-gray-600">Find your next career opportunity</p>
			</div>
			<div class="text-center mt-8">
				<a href="{{ route('jobs') }}"
					class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition">
					View All Jobs
					<i class="fas fa-arrow-right ml-2"></i>
				</a>
			</div>
		</div>
		<div class="swiper jobsSwiper">
			<div class="swiper-wrapper">
				@foreach($jobs as $job)
				<div class="swiper-slide">
					<div
						class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
						<div class="p-4">
							<!-- line-clamp-2 -->
							<h3 class="font-semibold text-lg mb-2 truncate">
								{{ $job->title }}
							</h3>
							<p class="text-gray-600 text-sm mb-2">
								Full-time position</p>
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i class="fas fa-briefcase mr-2"></i>
								<span>Healthcare</span>
							</div>
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i
									class="fas fa-map-marker-alt mr-2"></i>
								<span>Location {{ $i }}</span>
							</div>
							<button
								class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition">Apply
								Now</button>
						</div>
					</div>
				</div>
				@endforeach
			</div>
			<!-- <div class="swiper-pagination"></div> -->
		</div>

	</div>
</section>

<!-- Rental Space Section -->
<section class="py-16 bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex justify-between items-center mb-12">
			<div class="text-center">
				<h2 class="text-3xl font-bold text-gray-900 mb-4">Rental Spaces</h2>
				<p class="text-lg text-gray-600">Medical office spaces for rent</p>
			</div>
			<div class="text-center mt-8">
				<a href="#"
					class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
					View All Rental Spaces
					<i class="fas fa-arrow-right ml-2"></i>
				</a>
			</div>
		</div>
		<div class="swiper rentalSwiper">
			<div class="swiper-wrapper">
				@foreach($rentalSpaces as $rentalSpace)
				<div class="swiper-slide">
					<div
						class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
						<div
							class="h-48 bg-gray-200 flex items-center justify-center">
							<img src="{{ $rentalSpace->main_image }}" alt="">
						</div>
						<div class="p-4">
							<h3 class="font-semibold text-lg mb-2 truncate">
								{{ $rentalSpace->name }}
							</h3>

							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i
									class="fas fa-map-marker-alt mr-2 truncate"></i>
								<span>{{ $rentalSpace->location }}</span>
							</div>
							<div class="flex justify-between items-center">
								<span
									class="text-lg font-bold text-green-600">${{ $rentalSpace->pricing->price }}/month</span>
								<button
									class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">View
									Details</button>
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
			<!-- <div class="swiper-pagination"></div> -->
		</div>

	</div>
</section>

<!-- Courses Section -->
<section class="py-16 bg-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex justify-between items-center mb-12">
			<div class="text-center">
				<h2 class="text-3xl font-bold text-gray-900 mb-4">Medical Courses</h2>
				<p class="text-lg text-gray-600">Enhance your medical knowledge</p>
			</div>
			<div class="text-center mt-8">
				<a href="{{ route('courses') }}"
					class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition">
					View All Courses
					<i class="fas fa-arrow-right ml-2"></i>
				</a>
			</div>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			@foreach($courses as $course)
			<div
				class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
				<div class="h-48 bg-gray-200 flex items-center justify-center">
					<i class="fas fa-graduation-cap text-4xl text-gray-400"></i>
				</div>
				<div class="p-4">
					<h3 class="font-semibold text-lg mb-2">{{ $course->title }}</h3>
					<p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $course->description }}</p>
					<div class="flex items-center text-sm text-gray-500 mb-3">
						<i class="fas fa-clock mr-2"></i>
						<span>{{ $course->duration }}</span>
					</div>
					<div class="flex justify-between items-center">
						<button
							class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 transition">Enroll
							Now</button>
					</div>
				</div>
			</div>
			@endforeach
		</div>

	</div>
</section>

@endsection


@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.2/flowbite.min.js"></script>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
	// Initialize Banner Swiper
	const bannerSwiper = new Swiper('.bannerSwiper', {
		loop: true,
		autoplay: {
			delay: 3000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
	});

	// Initialize Clinics Swiper
	const clinicsSwiper = new Swiper('.clinicsSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 3,
				spaceBetween: 40,
			},
		},
	});

	// Initialize Suppliers Swiper
	const suppliersSwiper = new Swiper('.suppliersSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 4,
				spaceBetween: 40,
			},
		},
	});

	// Initialize Jobs Swiper
	const jobsSwiper = new Swiper('.jobsSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 4,
				spaceBetween: 40,
			},
		},
	});

	// Initialize Rental Swiper
	const rentalSwiper = new Swiper('.rentalSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 3,
				spaceBetween: 40,
			},
		},
	});
</script>

@endpush