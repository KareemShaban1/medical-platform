@push('scripts')
<script>
// AJAX Filter functionality for Jobs
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('search');
	const heroSearch = document.getElementById('heroSearch');
	const jobTypeSelect = document.getElementById('jobType');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const jobsGrid = document.getElementById('jobsGrid');
	const resultsCount = document.getElementById('resultsCount');
	const paginationContainer = document.getElementById('paginationContainer');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');
	// const gridViewMobileBtn = document.getElementById('gridViewMobile');
	// const listViewMobileBtn = document.getElementById('listViewMobile');
	const loadingSpinner = document.getElementById('loadingSpinner');

	let filterTimeout;
	let currentPage = 1;

	function filterJobs(page = 1) {
		// Clear existing timeout
		clearTimeout(filterTimeout);
		currentPage = page;

		// Show loading spinner
		if (loadingSpinner) loadingSpinner.classList.remove('hidden');
		jobsGrid.style.opacity = '0.5';
		jobsGrid.style.pointerEvents = 'none';

		// Set timeout for search input to avoid too many requests
		filterTimeout = setTimeout(() => {
			const formData = new FormData();

			// Get filter values
			if (searchInput.value) formData.append('search', searchInput.value);
			if (heroSearch.value) formData.append('search', heroSearch.value);
			if (jobTypeSelect.value) formData.append('type', jobTypeSelect.value);
			if (sortSelect.value) formData.append('sort', sortSelect.value);

			// Add page number
			formData.append('page', page);

			// Make AJAX request
			fetch('{{ route("jobs.filter") }}', {
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
					if (loadingSpinner) loadingSpinner.classList.add('hidden');
					jobsGrid.style.opacity = '1';
					jobsGrid.style.pointerEvents = 'auto';

					if (data.html !== '') {
						jobsGrid.innerHTML = data.html;
						
						// Update pagination
						if (data.pagination && paginationContainer) {
							paginationContainer.innerHTML = data.pagination;
							// Re-attach pagination click handlers
							attachPaginationHandlers();
						}
						
						resultsCount.textContent = data.count;
						
						// Scroll to top of jobs section
						jobsGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
					} else {
						jobsGrid.innerHTML =
							'<div class="col-span-full text-center py-8 text-gray-500">No jobs found</div>';
						if (paginationContainer) paginationContainer.innerHTML = '';
						resultsCount.textContent = '0';
					}
				})
				.catch(error => {
					console.error('Error:', error);
					if (loadingSpinner) loadingSpinner.classList.add('hidden');
					jobsGrid.style.opacity = '1';
					jobsGrid.style.pointerEvents = 'auto';
					jobsGrid.innerHTML =
						'<div class="col-span-full text-center py-8 text-red-500">Error loading jobs</div>';
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
				const href = this.getAttribute('href');
				if (!href) return;
				
				// Extract page number from URL
				let page = 1;
				try {
					const url = new URL(href, window.location.origin);
					page = parseInt(url.searchParams.get('page')) || 1;
				} catch (e) {
					// Fallback: try to extract page from href string
					const match = href.match(/[?&]page=(\d+)/);
					if (match) {
						page = parseInt(match[1]);
					}
				}
				filterJobs(page);
			});
		});
	}

	// Initial attachment of pagination handlers
	if (paginationContainer) {
		attachPaginationHandlers();
	}

	// Event listeners
	searchInput.addEventListener('input', () => filterJobs(1));
	heroSearch.addEventListener('input', () => filterJobs(1));
	jobTypeSelect.addEventListener('change', () => filterJobs(1));
	sortSelect.addEventListener('change', () => filterJobs(1));

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		heroSearch.value = '';
		jobTypeSelect.value = '';
		sortSelect.value = 'newest';
		filterJobs(1);
	});

	// View toggle
	function setGridView() {
		jobsGrid.className =
			'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation';
		gridViewBtn.className =
			'p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg';
		listViewBtn.className =
			'p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300';
		// gridViewMobileBtn.className = 'p-2 bg-gradient-primary text-white rounded-lg';
		// listViewMobileBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded-lg';
	}

	function setListView() {
		jobsGrid.className = 'grid grid-cols-1 gap-8 stagger-animation';
		listViewBtn.className =
			'p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg';
		gridViewBtn.className =
			'p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300';
		// listViewMobileBtn.className = 'p-2 bg-gradient-primary text-white rounded-lg';
		// gridViewMobileBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded-lg';
	}

	gridViewBtn.addEventListener('click', setGridView);
	listViewBtn.addEventListener('click', setListView);
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
			this.value = this.value;
			updateActiveFilters();
		});
	}

	// Update active filters count
	function updateActiveFilters() {
		activeFilterCount = 0;

		if (searchInput.value) activeFilterCount++;
		if (jobTypeSelect.value) activeFilterCount++;
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
	jobTypeSelect.addEventListener('change', updateActiveFilters);
	sortSelect.addEventListener('change', updateActiveFilters);
});
</script>
@endpush