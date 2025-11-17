@push('scripts')
<script>
// AJAX Filter functionality for Courses
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('search');
	const heroSearch = document.getElementById('heroSearch');
	const levelSelect = document.getElementById('level');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const coursesGrid = document.getElementById('coursesGrid');
	const resultsCount = document.getElementById('resultsCount');
	const paginationContainer = document.getElementById('paginationContainer');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');
	// const gridViewMobileBtn = document.getElementById('gridViewMobile');
	// const listViewMobileBtn = document.getElementById('listViewMobile');
	const loadingSpinner = document.getElementById('loadingSpinner');

	let filterTimeout;
	let currentPage = 1;

	function filterCourses(page = 1) {
		// Clear existing timeout
		clearTimeout(filterTimeout);
		currentPage = page;

		// Show loading spinner
		if (loadingSpinner) loadingSpinner.classList.remove('hidden');
		coursesGrid.style.opacity = '0.5';
		coursesGrid.style.pointerEvents = 'none';

		// Set timeout for search input to avoid too many requests
		filterTimeout = setTimeout(() => {
			const formData = new FormData();

			// Get filter values
			if (searchInput.value) formData.append('search',
				searchInput.value);
			if (heroSearch.value) formData.append('search',
				heroSearch.value);
			if (levelSelect.value) formData.append('level',
				levelSelect.value);
			if (sortSelect.value) formData.append('sort',
				sortSelect.value);

			// Add page number
			formData.append('page', page);

			// Make AJAX request
			fetch('{{ route("courses.filter") }}', {
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
					coursesGrid.style
						.opacity =
						'1';
					coursesGrid.style
						.pointerEvents =
						'auto';

					if (data.success &&
						data
						.html !==
						'') {
						coursesGrid
							.innerHTML =
							data
							.html;

						// Update pagination
						if (
							paginationContainer
							) {
							paginationContainer
								.innerHTML =
								data
								.pagination ||
								'';
							// Re-attach pagination click handlers
							attachPaginationHandler
								();
						}

						resultsCount
							.textContent =
							data
							.count;

						// Scroll to top of courses section
						coursesGrid
							.scrollIntoView({
								behavior: 'smooth',
								block: 'start'
							});
					} else {
						coursesGrid
							.innerHTML =
							'<div class="col-span-full text-center py-8 text-gray-500">No courses found</div>';
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
					coursesGrid.style
						.opacity =
						'1';
					coursesGrid.style
						.pointerEvents =
						'auto';
					coursesGrid
						.innerHTML =
						'<div class="col-span-full text-center py-8 text-red-500">Error loading courses</div>';
				});
		}, searchInput === document.activeElement ? 500 : 0);
	}

	// Pagination click handler using event delegation (attached once, works for all pagination links)
	let paginationHandlerAttached = false;

	function handlePaginationClick(e) {
		const link = e.target.closest('a[href]');
		if (!link) return;

		// Check if the link is within the pagination container
		const container = document.getElementById('paginationContainer');
		if (!container || !container.contains(link)) return;

		e.preventDefault();
		const href = link.getAttribute('href');
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
		filterCourses(page);
	}

	// Attach pagination handler once using event delegation
	// This will work for all pagination links, even dynamically added ones
	function attachPaginationHandler() {
		if (paginationHandlerAttached) return;

		const container = document.getElementById('paginationContainer');
		if (container) {
			container.addEventListener('click', handlePaginationClick);
			paginationHandlerAttached = true;
		}
	}

	// Try to attach handler immediately
	attachPaginationHandler();

	// Also try after a short delay in case the container isn't ready yet
	setTimeout(attachPaginationHandler, 100);

	// Event listeners
	searchInput.addEventListener('input', () => filterCourses(1));
	heroSearch.addEventListener('input', () => filterCourses(1));
	levelSelect.addEventListener('change', () => filterCourses(1));
	sortSelect.addEventListener('change', () => filterCourses(1));

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		heroSearch.value = '';
		levelSelect.value = '';
		sortSelect.value = 'newest';
		filterCourses(1);
	});

	// View toggle
	function setGridView() {
		coursesGrid.className =
			'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation';
		gridViewBtn.className =
			'p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg';
		listViewBtn.className =
			'p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300';
		// gridViewMobileBtn.className = 'p-2 bg-gradient-primary text-white rounded-lg';
		// listViewMobileBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded-lg';
	}

	function setListView() {
		coursesGrid.className = 'grid grid-cols-1 gap-8 stagger-animation';
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
});
</script>
@endpush
