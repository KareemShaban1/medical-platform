@push('scripts')
<script>
// AJAX Filter functionality
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('search');
	const heroSearch = document.getElementById('heroSearch');
	const categorySelect = document.getElementById('category');
	const priceRadios = document.querySelectorAll('input[name="price"]');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const productsGrid = document.getElementById('productsGrid');
	const resultsCount = document.getElementById('resultsCount');
	const paginationContainer = document.getElementById('paginationContainer');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');

	let filterTimeout;

	function filterProducts() {
		// Clear existing timeout
		clearTimeout(filterTimeout);

		// Add loading state
		productsGrid.innerHTML =
			'<div class="col-span-full flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';

		// Set timeout for search input to avoid too many requests
		filterTimeout = setTimeout(() => {
			const formData = new FormData();

			// Get filter values
			if (searchInput.value) formData.append('search',
				searchInput.value);
			if (heroSearch.value) formData.append('search',
				heroSearch.value);
			if (categorySelect.value) formData.append(
				'category', categorySelect
				.value);

			const selectedPrice = document.querySelector(
				'input[name="price"]:checked');
			if (selectedPrice) formData.append('price',
				selectedPrice.value);

			if (sortSelect.value) formData.append('sort',
				sortSelect.value);

			// Make AJAX request
			fetch('{{ route("products.filter") }}', {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'X-CSRF-TOKEN': document
							.querySelector(
								'meta[name="csrf-token"]'
							)
							.getAttribute(
								'content'
							)
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.html !==
						'') {
						productsGrid
							.innerHTML =
							data
							.html;
						paginationContainer
							.innerHTML =
							data
							.pagination;
						resultsCount
							.textContent =
							data
							.count;
					} else {
						productsGrid
							.innerHTML =
							'<div class="col-span-full text-center py-8 text-gray-500">No products found</div>';
						paginationContainer
							.innerHTML =
							'';
						resultsCount
							.textContent =
							'0';
					}
				})
				.catch(error => {
					console.error('Error:',
						error
					);
					productsGrid
						.innerHTML =
						'<div class="col-span-full text-center py-8 text-red-500">Error loading products</div>';
				});
		}, searchInput === document.activeElement ? 500 : 0);
	}

	// Event listeners
	searchInput.addEventListener('input', filterProducts);
	heroSearch.addEventListener('input', filterProducts);
	categorySelect.addEventListener('change', filterProducts);
	priceRadios.forEach(radio => radio.addEventListener('change', filterProducts));
	sortSelect.addEventListener('change', filterProducts);

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		heroSearch.value = '';
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
			'p-2 bg-gradient-primary text-white rounded';
		listViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});

	listViewBtn.addEventListener('click', function() {
		productsGrid.className = 'grid grid-cols-1 gap-6';
		listViewBtn.className =
			'p-2 bg-gradient-primary text-white rounded';
		gridViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});


	// Collapsible filter panel toggle
	const toggleFilters = document.getElementById('toggleFilters');
	const filtersPanel = document.getElementById('filtersPanel');
	const filterChevron = document.getElementById('filterChevron');
	const activeFiltersCount = document.getElementById('activeFiltersCount');
	const filterCount = document.getElementById('filterCount');

	let isFiltersOpen = false;
	let activeFilterCount = 0;

	// Toggle filters panel
	if (toggleFilters && filtersPanel) {
		toggleFilters.addEventListener('click', function() {
			isFiltersOpen = !isFiltersOpen;

			if (isFiltersOpen) {
				filtersPanel.classList.remove('hidden');
				filterChevron.style.transform =
					'rotate(180deg)';
				toggleFilters.classList.add(
					'bg-primary-dark');
			} else {
				filtersPanel.classList.add('hidden');
				filterChevron.style.transform =
					'rotate(0deg)';
				toggleFilters.classList.remove(
					'bg-primary-dark');
			}
		});
	}

	// Hero search functionality
	// const heroSearch = document.getElementById('heroSearch');
	const mainSearch = document.getElementById('search');

	if (mainSearch) {
		mainSearch.addEventListener('input', function() {
			mainSearch.value = this.value;
			updateActiveFilters();
		});
	}

	// Update active filters count
	function updateActiveFilters() {
		activeFilterCount = 0;

		if (searchInput.value) activeFilterCount++;
		if (categorySelect.value) activeFilterCount++;
		if (document.querySelector('input[name="price"]:checked') && document
			.querySelector('input[name="price"]:checked').value)
			activeFilterCount++;
		if (sortSelect.value && sortSelect.value !== 'newest') activeFilterCount++;

		if (activeFilterCount > 0) {
			activeFiltersCount.classList.remove('hidden');
			filterCount.textContent = activeFilterCount;
		} else {
			activeFiltersCount.classList.add('hidden');
		}
	}

	// Add updateActiveFilters to all filter change events
	searchInput.addEventListener('input', updateActiveFilters);
	categorySelect.addEventListener('change', updateActiveFilters);
	priceRadios.forEach(radio => radio.addEventListener('change', updateActiveFilters));
	sortSelect.addEventListener('change', updateActiveFilters);


});
</script>
@endpush
