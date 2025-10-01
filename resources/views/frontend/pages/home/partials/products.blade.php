<section class="py-16 bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<x-frontend.section-header title="Products" description="Discover our top medical products"
			button-text="View All Products" button-url="{{ route('products') }}"
			button-icon="fas fa-pills" button-color="bg-[var(--primary-color)] hover:bg-green-700" />

	<div class="swiper productsSwiper">
		<div class="swiper-wrapper">
			@foreach($products as $product)
			<div class="swiper-slide">
				<div
					class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
					<div class="h-48 bg-gray-200 flex items-center justify-center">
						<i class="fas fa-pills text-4xl text-gray-400"></i>
					</div>
					<div class="p-4">
						<!-- category -->

						<div class="flex flex-wrap gap-1">
							@foreach($product->categories as $category)
							<span
								class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
								{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}
							</span>
							@endforeach
						</div>
						<h3 class="font-semibold text-lg mb-2">
							<a
								href="{{ route('products.show', $product->id) }}">
								{{ $product->name }}
							</a>
						</h3>
						<p class="text-gray-600 text-sm mb-3 truncate">
							{{ $product->description }}
						</p>
						<div class="flex justify-start items-center gap-2">
							<span
								class="text-md text-primary">${{ $product->price_after }}</span>
							<!-- price before -->
							<span
								class="text-md text-red-500 line-through">${{ $product->price_before }}</span>

						</div>
						<button
							class="btn-primary rounded-full mt-2 flex items-center gap-2">Add
							to Cart
							<i class="fas fa-cart-plus"></i>
						</button>
					</div>
				</div>
			</div>
			@endforeach
			<!-- <div class="swiper-pagination"></div> -->
			<div class="products-swiper-button-next"></div>
			<div class="products-swiper-button-prev"></div>
		</div>

	</div>
</div>
</section>