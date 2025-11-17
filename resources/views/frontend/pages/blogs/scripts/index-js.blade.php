@push('scripts')
<script>
// AJAX Filter functionality for Blogs
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('search');
	const heroSearch = document.getElementById('heroSearch');
	const categorySelect = document.getElementById('category');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const blogGrid = document.getElementById('blogGrid');
	const resultsCount = document.getElementById('resultsCount');
	const paginationContainer = document.getElementById('paginationContainer');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');
	// const gridViewMobileBtn = document.getElementById('gridViewMobile');
	// const listViewMobileBtn = document.getElementById('listViewMobile');
	const loadingSpinner = document.getElementById('loadingSpinner');

	let filterTimeout;
	let currentPage = 1;

	function filterBlogs(page = 1) {
		// Clear existing timeout
		clearTimeout(filterTimeout);
		currentPage = page;

		// Show loading spinner
		if (loadingSpinner) loadingSpinner.classList.remove('hidden');
		blogGrid.style.opacity = '0.5';
		blogGrid.style.pointerEvents = 'none';

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
			if (sortSelect.value) formData.append('sort',
				sortSelect.value);

			// Add page number
			formData.append('page', page);

			// Make AJAX request
			fetch('{{ route("blogs.filter") }}', {
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
					if (loadingSpinner)
						loadingSpinner
						.classList
						.add(
							'hidden'
						);
					blogGrid.style
						.opacity =
						'1';
					blogGrid.style
						.pointerEvents =
						'auto';

					if (data.success &&
						data
						.html !==
						'') {
						blogGrid.innerHTML =
							data
							.html;

						console.log(
							data
						);
						// Update pagination
						if (
							paginationContainer) {
							paginationContainer
								.innerHTML =
								data
								.pagination ||
								'';
							// Re-attach pagination click handlers
							attachPaginationHandlers
								();
						}

						resultsCount
							.textContent =
							data
							.count;

						// Scroll to top of blog section
						blogGrid.scrollIntoView({
							behavior: 'smooth',
							block: 'start'
						});
					} else {
						blogGrid.innerHTML =
							'<div class="col-span-full text-center py-8 text-gray-500">No articles found</div>';
						if (
							paginationContainer
						)
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
					if (loadingSpinner)
						loadingSpinner
						.classList
						.add(
							'hidden'
						);
					blogGrid.style
						.opacity =
						'1';
					blogGrid.style
						.pointerEvents =
						'auto';
					blogGrid.innerHTML =
						'<div class="col-span-full text-center py-8 text-red-500">Error loading articles</div>';
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
								'page'
							)
						) ||
						1;
				} catch (e) {
					// Fallback: try to extract page from href string
					const match =
						href
						.match(
							/[?&]page=(\d+)/
						);
					if (
						match
					) {
						page = parseInt(match[
							1
						]);
					}
				}
				filterBlogs(page);
			});
		});
	}

	// Initial attachment of pagination handlers
	if (paginationContainer) {
		attachPaginationHandlers();
	}

	// Event listeners
	searchInput.addEventListener('input', () => filterBlogs(1));
	heroSearch.addEventListener('input',
		() => filterBlogs(1));
	categorySelect.addEventListener('change', () => filterBlogs(1));
	sortSelect
		.addEventListener('change', () => filterBlogs(1));

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		heroSearch.value = '';
		categorySelect.value = '';
		sortSelect.value = 'newest';
		filterBlogs(1);
	});

	// View toggle
	function setGridView() {
		blogGrid.className =
			'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation';
		gridViewBtn.className =
			'p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg';
		listViewBtn.className =
			'p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300';
		// gridViewMobileBtn.className = 'p-2 bg-gradient-primary text-white rounded-lg';
		// listViewMobileBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded-lg';
	}

	function setListView() {
		blogGrid.className = 'grid grid-cols-1 gap-8 stagger-animation';
		listViewBtn.className =
			'p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg';
		gridViewBtn.className =
			'p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300';
		// listViewMobileBtn.className = 'p-2 bg-gradient-primary text-white rounded-lg';
		// gridViewMobileBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded-lg';
	}

	gridViewBtn.addEventListener('click', setGridView);
	listViewBtn.addEventListener('click',
		setListView);
	// gridViewMobileBtn.addEventListener('click', setGridView);
	// listViewMobileBtn.addEventListener('click', setListView);

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
	categorySelect.addEventListener(
		'change', updateActiveFilters);
	sortSelect.addEventListener('change',
		updateActiveFilters);
});
</script>
@endpush