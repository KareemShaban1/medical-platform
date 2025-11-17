@push('scripts')
<script>
// AJAX Filter functionality
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('search');
	const heroSearch = document.getElementById('heroSearch');
	const categorySelect = document.getElementById('category');
	const priceRangeSelect = document.getElementById('priceRange');
	const priceRadios = document.querySelectorAll('input[name="price"]');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const productsGrid = document.getElementById('productsGrid');
	const resultsCount = document.getElementById('resultsCount');
	const paginationContainer = document.getElementById('paginationContainer');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');

	let filterTimeout;
	let currentPage = 1;

	function filterProducts(page = 1) {
		// Clear existing timeout
		clearTimeout(filterTimeout);
		currentPage = page;

		// Show loading spinner
		document.getElementById('loadingSpinner').classList.remove('hidden');
		productsGrid.style.opacity = '0.5';
		productsGrid.style.pointerEvents = 'none';

		// Set timeout for search input to avoid too many requests
		filterTimeout = setTimeout(() => {
			const formData = new FormData();

			// Get filter values
			const searchValue = searchInput.value ||
				heroSearch.value;
			if (searchValue) formData.append('search',
				searchValue);

			if (categorySelect.value) formData.append(
				'category', categorySelect
				.value);

			// Handle priceRange select (highest/lowest)
			if (priceRangeSelect && priceRangeSelect.value) {
				formData.append('priceRange',
					priceRangeSelect
					.value);
			}

			// Handle price radio buttons (if they exist)
			const selectedPrice = document.querySelector(
				'input[name="price"]:checked');
			if (selectedPrice) formData.append('price',
				selectedPrice.value);

			if (sortSelect.value) formData.append('sort',
				sortSelect.value);

			// Add page number
			formData.append('page', page);

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
					document.getElementById(
							'loadingSpinner'
							)
						.classList
						.add(
							'hidden');
					productsGrid.style
						.opacity =
						'1';
					productsGrid.style
						.pointerEvents =
						'auto';

					if (data.html !==
						'') {
						productsGrid
							.innerHTML =
							data
							.html;

						// Update pagination
						if (data
							.pagination) {
							paginationContainer
								.innerHTML =
								data
								.pagination;
							// Re-attach pagination click handlers
							attachPaginationHandlers
								();
						}

						resultsCount
							.textContent =
							data
							.count;

						// Scroll to top of products section
						productsGrid
							.scrollIntoView({
								behavior: 'smooth',
								block: 'start'
							});
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
					document.getElementById(
							'loadingSpinner'
							)
						.classList
						.add(
							'hidden');
					productsGrid.style
						.opacity =
						'1';
					productsGrid.style
						.pointerEvents =
						'auto';
					productsGrid
						.innerHTML =
						'<div class="col-span-full text-center py-8 text-red-500">Error loading products</div>';
				});
		}, searchInput === document.activeElement ? 500 : 0);
	}

	// Attach click handlers to pagination links
	function attachPaginationHandlers() {
		if (!paginationContainer) return;

		const paginationLinks = paginationContainer.querySelectorAll('a[href]');
		paginationLinks.forEach(link => {
			// Remove existing listeners to avoid duplicates
			const newLink = link.cloneNode(true);
			link.parentNode.replaceChild(newLink, link);

			newLink.addEventListener('click', function(e) {
				e.preventDefault();
				const href = this
					.getAttribute(
						'href'
						);
				if (!href) return;

				// Extract page number from URL
				let page = 1;
				try {
					const url =
						new URL(href,
							window
							.location
							.origin
							);
					page = parseInt(url.searchParams
							.get(
								'page')
							) ||
						1;
				} catch (e) {
					// Fallback: try to extract page from href string
					const match =
						href
						.match(
							/[?&]page=(\d+)/);
					if (
						match) {
						page = parseInt(match[
							1]);
					}
				}
				filterProducts(
				page);
			});
		});
	}

	// Initial attachment of pagination handlers
	if (paginationContainer) {
		attachPaginationHandlers();
	}

	// Event listeners
	searchInput.addEventListener('input', () => filterProducts(1));
	heroSearch.addEventListener('input', () => filterProducts(1));
	categorySelect.addEventListener('change', () => filterProducts(1));
	if (priceRangeSelect) {
		priceRangeSelect.addEventListener('change', () => filterProducts(1));
	}
	priceRadios.forEach(radio => radio.addEventListener('change', () => filterProducts(1)));
	sortSelect.addEventListener('change', () => filterProducts(1));

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		heroSearch.value = '';
		categorySelect.value = '';
		if (priceRangeSelect) {
			priceRangeSelect.value = '';
		}
		priceRadios.forEach(radio => radio.checked = false);
		sortSelect.value = 'name';
		filterProducts(1);
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

		if (searchInput.value || heroSearch.value) activeFilterCount++;
		if (categorySelect.value) activeFilterCount++;
		if (priceRangeSelect && priceRangeSelect.value) activeFilterCount++;
		if (document.querySelector('input[name="price"]:checked') && document
			.querySelector('input[name="price"]:checked').value)
			activeFilterCount++;
		if (sortSelect.value && sortSelect.value !== 'name') activeFilterCount++;

		if (activeFilterCount > 0) {
			activeFiltersCount.classList.remove('hidden');
			filterCount.textContent = activeFilterCount;
		} else {
			activeFiltersCount.classList.add('hidden');
		}
	}

	// Add updateActiveFilters to all filter change events
	searchInput.addEventListener('input', updateActiveFilters);
	heroSearch.addEventListener('input', updateActiveFilters);
	categorySelect.addEventListener('change', updateActiveFilters);
	if (priceRangeSelect) {
		priceRangeSelect.addEventListener('change', updateActiveFilters);
	}
	priceRadios.forEach(radio => radio.addEventListener('change', updateActiveFilters));
	sortSelect.addEventListener('change', updateActiveFilters);


});
</script>
@endpush
