@push('scripts')
<script>
	// AJAX Filter functionality for Suppliers
	document.addEventListener('DOMContentLoaded', function() {
		const searchInput = document.getElementById('search');
		const heroSearch = document.getElementById('heroSearch');
		const sortSelect = document.getElementById('sort');
		const clearFiltersBtn = document.getElementById('clearFilters');
		const suppliersGrid = document.getElementById('suppliersGrid');
		const resultsCount = document.getElementById('resultsCount');
		const paginationContainer = document.getElementById('paginationContainer');
		const gridViewBtn = document.getElementById('gridView');
		const listViewBtn = document.getElementById('listView');
		// const gridViewMobileBtn = document.getElementById('gridViewMobile');
		// const listViewMobileBtn = document.getElementById('listViewMobile');
		const loadingSpinner = document.getElementById('loadingSpinner');

		let filterTimeout;

		function filterSuppliers() {
			// Clear existing timeout
			clearTimeout(filterTimeout);

			suppliersGrid.innerHTML =
				'<div class="col-span-full flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';

			// Set timeout for search input to avoid too many requests
			filterTimeout = setTimeout(() => {
				const formData = new FormData();

				// Get filter values
				if (searchInput.value) formData.append('search', searchInput.value);
				if (heroSearch.value) formData.append('search', heroSearch.value);
				if (sortSelect.value) formData.append('sort', sortSelect.value);

				// Make AJAX request
				fetch('{{ route("suppliers.filter") }}', {
						method: 'POST',
						body: formData,
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						}
					})
					.then(response => response.json())
					.then(data => {
						if (data.html !=='') {
							suppliersGrid
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
						suppliersGrid
							.innerHTML =
							'<div class="col-span-full text-center py-8 text-gray-500">No suppliers found</div>';
						paginationContainer
							.innerHTML =
							'';
						resultsCount
							.textContent =
							'0';
					}
					})
					.catch(error => {
						console.error('Error:', error);
						suppliersGrid.innerHTML = '<div class="col-span-full text-center py-8 text-red-500">Error loading suppliers</div>';
					});
			}, searchInput === document.activeElement ? 500 : 0);
		}

		// Event listeners
		searchInput.addEventListener('input', filterSuppliers);
		heroSearch.addEventListener('input', filterSuppliers);
		sortSelect.addEventListener('change', filterSuppliers);

		clearFiltersBtn.addEventListener('click', function() {
			searchInput.value = '';
			heroSearch.value = '';
			sortSelect.value = 'name';
			filterSuppliers();
		});

		// View toggle
		function setGridView() {
			suppliersGrid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation';
			gridViewBtn.className = 'p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg';
			listViewBtn.className = 'p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300';
			// gridViewMobileBtn.className = 'p-2 bg-gradient-primary text-white rounded-lg';
			// listViewMobileBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded-lg';
		}

		function setListView() {
			suppliersGrid.className = 'grid grid-cols-1 gap-8 stagger-animation';
			listViewBtn.className = 'p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg';
			gridViewBtn.className = 'p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300';
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
