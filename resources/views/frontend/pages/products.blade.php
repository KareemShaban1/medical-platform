@extends('frontend.layouts.app')
@section('content')
<!-- Page Header -->
<div class="bg-white shadow-sm">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<h1 class="text-3xl font-bold text-gray-900">Medical Products</h1>
		<p class="text-gray-600 mt-2">Find the best medical products for your needs</p>
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
						<option value="medications">Medications</option>
						<option value="equipment">Medical Equipment
						</option>
						<option value="supplies">Medical Supplies</option>
						<option value="diagnostic">Diagnostic Tools
						</option>
						<option value="surgical">Surgical Instruments
						</option>
						<option value="therapeutic">Therapeutic Devices
						</option>
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
					<button id="gridView" class="p-2 bg-blue-600 text-white rounded">
						<i class="fas fa-th"></i>
					</button>
					<button id="listView" class="p-2 bg-gray-200 text-gray-600 rounded">
						<i class="fas fa-list"></i>
					</button>
				</div>
			</div>

			<!-- Products Grid -->
			<div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@for($i = 1; $i <= 24; $i++) <div
					class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
					data-category="medications" data-price="{{ rand(20, 300) }}"
					data-name="Product {{ $i }}">
					<div class="h-48 bg-gray-200 flex items-center justify-center">
						<i class="fas fa-pills text-4xl text-gray-400"></i>
					</div>
					<div class="p-4">
						<h3 class="font-semibold text-lg mb-2">Product
							{{ $i }}
						</h3>
						<p class="text-gray-600 text-sm mb-3">High-quality
							medical product</p>
						<div class="flex items-center mb-3">
							<div class="flex text-yellow-400">
								@for($j = 1; $j <= 5; $j++) <i
									class="fas fa-star">
									</i>
									@endfor
							</div>
							<span
								class="text-sm text-gray-500 ml-2">({{ rand(10, 100) }})</span>
						</div>
						<div class="flex justify-between items-center">
							<span
								class="text-xl font-bold text-blue-600">${{ rand(20, 300) }}</span>
							<button
								class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
								Add to Cart
							</button>
						</div>
					</div>
			</div>
			@endfor
		</div>

		<!-- Pagination -->
		<div class="mt-8 flex justify-center">
			<nav class="flex items-center space-x-2">
				<button
					class="px-3 py-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">Previous</button>
				<button class="px-3 py-2 bg-blue-600 text-white rounded">1</button>
				<button
					class="px-3 py-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">2</button>
				<button
					class="px-3 py-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">3</button>
				<button
					class="px-3 py-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">Next</button>
			</nav>
		</div>
	</div>
</div>
</div>

@endsection

@push('scripts')
<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('search');
	const categorySelect = document.getElementById('category');
	const priceRadios = document.querySelectorAll('input[name="price"]');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const productsGrid = document.getElementById('productsGrid');
	const resultsCount = document.getElementById('resultsCount');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');

	let products = Array.from(document.querySelectorAll('.product-card'));

	function filterProducts() {
		const searchTerm = searchInput.value.toLowerCase();
		const selectedCategory = categorySelect.value;
		const selectedPrice = document.querySelector('input[name="price"]:checked')
			?.value;
		const sortBy = sortSelect.value;

		let filteredProducts = products.filter(product => {
			const name = product.dataset.name.toLowerCase();
			const category = product.dataset.category;
			const price = parseInt(product.dataset.price);

			// Search filter
			if (searchTerm && !name.includes(searchTerm))
				return false;

			// Category filter
			if (selectedCategory && category !==
				selectedCategory) return false;

			// Price filter
			if (selectedPrice) {
				const [min, max] = selectedPrice.split(
					'-').map(p => p ===
					'+' ? Infinity :
					parseInt(p));
				if (price < min || (max !== Infinity &&
						price > max))
					return false;
			}

			return true;
		});

		// Sort products
		filteredProducts.sort((a, b) => {
			switch (sortBy) {
				case 'name':
					return a.dataset.name
						.localeCompare(b
							.dataset
							.name);
				case 'name-desc':
					return b.dataset.name
						.localeCompare(a
							.dataset
							.name);
				case 'price':
					return parseInt(a.dataset
							.price) -
						parseInt(b.dataset
							.price);
				case 'price-desc':
					return parseInt(b.dataset
							.price) -
						parseInt(a.dataset
							.price);
				default:
					return 0;
			}
		});

		// Update results
		productsGrid.innerHTML = '';
		filteredProducts.forEach(product => productsGrid.appendChild(product));
		resultsCount.textContent = filteredProducts.length;
	}

	// Event listeners
	searchInput.addEventListener('input', filterProducts);
	categorySelect.addEventListener('change', filterProducts);
	priceRadios.forEach(radio => radio.addEventListener('change', filterProducts));
	sortSelect.addEventListener('change', filterProducts);

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		categorySelect.value = '';
		priceRadios.forEach(radio => radio.checked = false);
		sortSelect.value = 'name';
		filterProducts();
	});

	// View toggle
	gridViewBtn.addEventListener('click', function() {
		productsGrid.className =
			'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
		gridViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		listViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});

	listViewBtn.addEventListener('click', function() {
		productsGrid.className = 'grid grid-cols-1 gap-6';
		listViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		gridViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});
});
</script>
@endpush
