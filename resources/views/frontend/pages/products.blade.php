@extends('frontend.layouts.app')
@section('content')
<!-- Page Header -->
<div class="bg-gradient-primary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-white">Medical Products</h1>
        <p class="text-white/90 mt-2">Find the best medical products for your needs</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
	<div class="flex flex-col lg:flex-row gap-8">
		<!-- Filters Sidebar -->
		<div class="lg:w-1/4">
			<div class="bg-white rounded-lg shadow-md p-6">
				<h3 class="text-lg font-semibold mb-4">Filters</h3>

				<!-- Search Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Search</label>
					<div class="relative">
						<input type="text" id="search"
							placeholder="Search products..."
							class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<i
							class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
					</div>
				</div>

				<!-- Category Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Category</label>
					<select id="category"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="">All Categories</option>
						@foreach($categories as $category)
						<option value="{{ $category->id }}">
							{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}
						</option>
						@endforeach

					</select>
				</div>

				<!-- Price Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Price
						Range</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="price-all" name="price"
								value="" class="mr-2">
							<label for="price-all" class="text-sm">All
								Prices</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="price-0-50" name="price"
								value="0-50" class="mr-2">
							<label for="price-0-50" class="text-sm">$0 -
								$50</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="price-50-100" name="price"
								value="50-100" class="mr-2">
							<label for="price-50-100" class="text-sm">$50 -
								$100</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="price-100-200"
								name="price" value="100-200"
								class="mr-2">
							<label for="price-100-200" class="text-sm">$100 -
								$200</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="price-200+" name="price"
								value="200+" class="mr-2">
							<label for="price-200+"
								class="text-sm">$200+</label>
						</div>
					</div>
				</div>

				<!-- Sort Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Sort
						By</label>
					<select id="sort"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="name">Name A-Z</option>
						<option value="name-desc">Name Z-A</option>
						<option value="price">Price Low to High</option>
						<option value="price-desc">Price High to Low
						</option>
						<option value="newest">Newest First</option>
					</select>
				</div>

				<!-- Clear Filters -->
				<button id="clearFilters"
					class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition">
					Clear All Filters
				</button>
			</div>
		</div>

		<!-- Products Grid -->
		<div class="lg:w-3/4">
			<!-- Results Header -->
			<div class="flex justify-between items-center mb-6">
				<div>
					<span class="text-gray-600">Showing <span id="resultsCount">24</span>
						products</span>
				</div>
				<div class="flex items-center space-x-2">
					<span class="text-sm text-gray-600">View:</span>
					<button id="gridView" class="p-2 bg-gradient-primary  text-white rounded">
						<i class="fas fa-th"></i>
					</button>
					<button id="listView" class="p-2 bg-gray-200 text-gray-600 rounded">
						<i class="fas fa-list"></i>
					</button>
				</div>
			</div>

			<!-- Products Grid -->
			<div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@include('frontend.partials.products-grid', ['products' => $products])
			</div>

			<!-- Pagination -->
			<div id="paginationContainer">
				{{ $products->links() }}
			</div>
			
		</div>
	</div>
</div>

@endsection

@include('frontend.js-scripts.products')