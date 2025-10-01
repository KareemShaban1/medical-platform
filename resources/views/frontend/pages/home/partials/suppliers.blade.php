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
			<div class="suppliers-swiper-button-next"></div>
			<div class="suppliers-swiper-button-prev"></div>
		</div>

	</div>
</section>