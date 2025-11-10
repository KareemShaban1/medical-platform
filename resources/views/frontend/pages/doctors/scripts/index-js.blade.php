@push('scripts')
<script>
	// AJAX Filter functionality for Doctors
	document.addEventListener('DOMContentLoaded', function() {
		const searchInput = document.getElementById('search');
		const heroSearch = document.getElementById('heroSearch');
		const sortSelect = document.getElementById('sort');
		const specialitySelect = document.getElementById('speciality_id');
		const featuredSelect = document.getElementById('featured');
		const hasClinicSelect = document.getElementById('has_clinic');
		const clearFiltersBtn = document.getElementById('clearFilters');
		const doctorsGrid = document.getElementById('doctorsGrid');
		const resultsCount = document.getElementById('resultsCount');
		const paginationContainer = document.getElementById('paginationContainer');
		const gridViewBtn = document.getElementById('gridView');
		const listViewBtn = document.getElementById('listView');
		const loadingSpinner = document.getElementById('loadingSpinner');
		const toggleFiltersBtn = document.getElementById('toggleFilters');
		const filtersPanel = document.getElementById('filtersPanel');
		const filterChevron = document.getElementById('filterChevron');
		const activeFiltersCount = document.getElementById('activeFiltersCount');
		const filterCount = document.getElementById('filterCount');

		let filterTimeout;

		// Toggle Filters Panel
		if (toggleFiltersBtn && filtersPanel) {
			toggleFiltersBtn.addEventListener('click', function() {
				filtersPanel.classList.toggle('hidden');
				filterChevron.classList.toggle('rotate-180');
			});
		}

		function filterDoctors() {
			// Clear existing timeout
			clearTimeout(filterTimeout);

			// Show loading
			if (loadingSpinner) loadingSpinner.classList.remove('hidden');
			if (doctorsGrid) {
				doctorsGrid.innerHTML = '<div class="col-span-full flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
			}

			// Set timeout for search input to avoid too many requests
			filterTimeout = setTimeout(() => {
				const formData = new FormData();

				// Get filter values
				const searchValue = searchInput?.value || heroSearch?.value || '';
				if (searchValue) formData.append('search', searchValue);

				if (specialitySelect?.value) formData.append('speciality_id', specialitySelect.value);
				if (featuredSelect?.value) formData.append('featured', featuredSelect.value);
				if (hasClinicSelect?.value !== '') formData.append('has_clinic', hasClinicSelect?.value || '');
				if (sortSelect?.value) formData.append('sort', sortSelect.value);

				// Update active filters count
				let activeCount = 0;
				if (searchValue) activeCount++;
				if (specialitySelect?.value) activeCount++;
				if (featuredSelect?.value) activeCount++;
				if (hasClinicSelect?.value !== '') activeCount++;

				if (activeCount > 0) {
					if (activeFiltersCount) {
						activeFiltersCount.classList.remove('hidden');
						if (filterCount) filterCount.textContent = activeCount;
					}
				} else {
					if (activeFiltersCount) activeFiltersCount.classList.add('hidden');
				}

				// Make AJAX request
				fetch('{{ route("doctors.filter") }}', {
						method: 'POST',
						body: formData,
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						}
					})
					.then(response => response.json())
					.then(data => {
						if (loadingSpinner) loadingSpinner.classList.add('hidden');

						if (data && data.success && data.html !== '') {
							if (doctorsGrid) doctorsGrid.innerHTML = data.html;
							if (paginationContainer && data.pagination) {
								paginationContainer.innerHTML = data.pagination;
							}
							if (resultsCount) {
								resultsCount.textContent = data.count ?? 0;
							}
						} else {
							if (doctorsGrid) {
								doctorsGrid.innerHTML = `
									<div class="col-span-full text-center py-12">
										<div class="text-gray-500">
											<i class="fas fa-user-md text-6xl mb-4"></i>
											<h3 class="text-lg font-semibold mb-2">{{ __('no doctors found') }}</h3>
											<p>{{ __('try adjusting your search criteria or filters') }}</p>
										</div>
									</div>`;
							}
							if (paginationContainer) paginationContainer.innerHTML = '';
							if (resultsCount) resultsCount.textContent = '0';
						}
					})
					.catch(error => {
						console.error('Error:', error);
						if (loadingSpinner) loadingSpinner.classList.add('hidden');
						if (doctorsGrid) {
							doctorsGrid.innerHTML = `
								<div class="col-span-full text-center py-12">
									<div class="text-gray-500">
										<i class="fas fa-user-md text-6xl mb-4"></i>
										<h3 class="text-lg font-semibold mb-2">{{ __('no doctors found') }}</h3>
										<p>{{ __('try adjusting your search criteria or filters') }}</p>
									</div>
								</div>`;
						}
						if (paginationContainer) paginationContainer.innerHTML = '';
						if (resultsCount) resultsCount.textContent = '0';
					});
			}, searchInput === document.activeElement ? 500 : 0);
		}

		// Event listeners
		if (searchInput) searchInput.addEventListener('input', filterDoctors);
		if (heroSearch) heroSearch.addEventListener('input', filterDoctors);
		if (sortSelect) sortSelect.addEventListener('change', filterDoctors);
		if (specialitySelect) specialitySelect.addEventListener('change', filterDoctors);
		if (featuredSelect) featuredSelect.addEventListener('change', filterDoctors);
		if (hasClinicSelect) hasClinicSelect.addEventListener('change', filterDoctors);

		if (clearFiltersBtn) {
			clearFiltersBtn.addEventListener('click', function() {
				if (searchInput) searchInput.value = '';
				if (heroSearch) heroSearch.value = '';
				if (sortSelect) sortSelect.value = 'featured';
				if (specialitySelect) specialitySelect.value = '';
				if (featuredSelect) featuredSelect.value = '';
				if (hasClinicSelect) hasClinicSelect.value = '';
				if (activeFiltersCount) activeFiltersCount.classList.add('hidden');
				filterDoctors();
			});
		}

		// View toggle functionality
		if (gridViewBtn && listViewBtn) {
			gridViewBtn.addEventListener('click', function() {
				if (doctorsGrid) {
					doctorsGrid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation';
				}
				gridViewBtn.classList.add('bg-gradient-primary', 'text-white');
				gridViewBtn.classList.remove('bg-gray-200', 'text-gray-600');
				listViewBtn.classList.remove('bg-gradient-primary', 'text-white');
				listViewBtn.classList.add('bg-gray-200', 'text-gray-600');
			});

			listViewBtn.addEventListener('click', function() {
				if (doctorsGrid) {
					doctorsGrid.className = 'grid grid-cols-1 gap-6 stagger-animation';
				}
				listViewBtn.classList.add('bg-gradient-primary', 'text-white');
				listViewBtn.classList.remove('bg-gray-200', 'text-gray-600');
				gridViewBtn.classList.remove('bg-gradient-primary', 'text-white');
				gridViewBtn.classList.add('bg-gray-200', 'text-gray-600');
			});
		}
	});
</script>
@endpush

