<section class="py-16  bg-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<x-frontend.section-header title="Medical Suppliers"
			description="Trusted suppliers for medical equipment" button-text="View All Suppliers"
			button-url="{{ route('suppliers') }}" button-icon="fas fa-truck"
			button-color="bg-green-600 hover:bg-green-700" />

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
								<a
									href="{{ route('suppliers.show', $supplier->id) }}">
									{{ $supplier->name }}
								</a>
							</h3>

							<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-map-marker-alt mr-2"></i>
			<span>{{ $supplier->address ?? 'Location not specified' }}</span>
		</div>
		<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-phone mr-2"></i>
			<span>{{ $supplier->phone ?? 'Contact not available' }}</span>
		</div>
							
							
							<a href="{{ route('suppliers.show', $supplier->id) }}"
								class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700 transition">Contact
								Supplier</a>
						</div>
					</div>
				</div>
				@endforeach
			</div>
			<!-- <div class="swiper-pagination"></div> -->
			<div class="suppliers-swiper-button-next"></div>
			<div class="suppliers-swiper-button-prev"></div>
		</div>

	</div>
</section>