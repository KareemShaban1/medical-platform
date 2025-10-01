@foreach($products as $product)
<div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
	data-category-ids="{{ $product->categories->pluck('id')->implode(',') }}"
	data-categories="{{ $product->categories->map(function($cat) { return app()->getLocale() == 'ar' ? $cat->name_ar : $cat->name_en; })->implode(',') }}"
	data-price="{{ $product->price_after }}" data-name="{{ $product->name }}">
	<div class="h-48 bg-gray-200 flex items-center justify-center">
		<img src="{{ $product->first_image }}" alt="Product Image" class="w-full h-full object-cover">
	</div>
	<div class="p-4">
		<h3 class="font-semibold text-lg mb-2 truncate">
			<a href="{{ route('products.show', $product->id) }}">
				{{ $product->name }}
			</a>
		</h3>
		<p class="text-gray-600 text-sm mb-2 line-clamp-2">
			{{ $product->description }}
		</p>
		@if($product->categories->count() > 0)
		<div class="mb-3">
			<div class="flex flex-wrap gap-1">
				@foreach($product->categories as $category)
				<span
					class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
					{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}
				</span>
				@endforeach
			</div>
		</div>
		@endif
		<div class="flex items-center mb-3">
			<div class="flex text-yellow-400">
				@for($j = 1; $j <= 5; $j++) <i class="fas fa-star"></i>
					@endfor
			</div>
			<span class="text-sm text-gray-500 ml-2">({{ $product->stock }})</span>
		</div>
		<div class="flex justify-between items-center">
			<span class="text-md text-blue-600">${{ $product->price_after }}</span>
			<!-- price before -->
			<span class="text-sm text-red-500 line-through">${{ $product->price_before }}</span>
			<button
				class="bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition">
				Add to Cart
			</button>
		</div>
	</div>
</div>
@endforeach