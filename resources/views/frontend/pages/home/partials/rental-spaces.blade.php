<section class="py-16 bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<x-frontend.section-header title="Rental Spaces" description="Medical office spaces for rent"
			button-text="View All Rental Spaces" button-url="#" button-icon="fas fa-building"
			button-color="bg-green-600 hover:bg-green-700" />
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
									class="fas fa-map-marker-alt mr-2"></i>
								<span
									class="truncate">{{ $rentalSpace->location }}</span>
							</div>

							<!-- availability -->
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i class="fas fa-calendar-alt mr-2"></i>
								<span>{{ $rentalSpace->availability->type }}</span>
							</div>

							<!-- pricing -->
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i class="fas fa-dollar-sign mr-2"></i>
								<span>{{ $rentalSpace->pricing->price }}</span>
							</div>


							<div class="flex justify-between items-center">
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
			<div class="rental-spaces-swiper-button-next"></div>
			<div class="rental-spaces-swiper-button-prev"></div>
		</div>

	</div>
</section>