@push('scripts')
<script>
// AJAX Filter functionality for Clinics
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('search');
	const heroSearch = document.getElementById('heroSearch');
	const governorateSelect = document.getElementById('governorate_id');
	const citySelect = document.getElementById('city_id');
	const areaSelect = document.getElementById('area_id');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const clinicsGrid = document.getElementById('clinicsGrid');
	const resultsCount = document.getElementById('resultsCount');
	const paginationContainer = document.getElementById('paginationContainer');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');
	// const gridViewMobileBtn = document.getElementById('gridViewMobile');
	// const listViewMobileBtn = document.getElementById('listViewMobile');
	const loadingSpinner = document.getElementById('loadingSpinner');

	let filterTimeout;
	let currentPage = 1;

	function filterClinics(page = 1) {
		// Clear existing timeout
		clearTimeout(filterTimeout);
		currentPage = page;

		// Show loading spinner
		if (loadingSpinner) loadingSpinner.classList.remove('hidden');
		clinicsGrid.style.opacity = '0.5';
		clinicsGrid.style.pointerEvents = 'none';

		// Set timeout for search input to avoid too many requests
		filterTimeout = setTimeout(() => {
			const formData = new FormData();

			// Get filter values
			if (searchInput.value) formData.append('search', searchInput.value);
			if (heroSearch.value) formData.append('search', heroSearch.value);
			if (sortSelect.value) formData.append('sort', sortSelect.value);
			if (governorateSelect.value && governorateSelect.value !== 'all') {
				formData.append('governorate_id', governorateSelect.value);
			}
			if (citySelect.value && citySelect.value !== 'all') {
				formData.append('city_id', citySelect.value);
			}
			if (areaSelect.value && areaSelect.value !== 'all') {
				formData.append('area_id', areaSelect.value);
			}

			// Add page number
			formData.append('page', page);

			// Make AJAX request
			fetch('{{ route("clinics.filter") }}', {
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
					clinicsGrid.style.opacity = '1';
					clinicsGrid.style.pointerEvents = 'auto';

					if (data && data.success && data.html !== '') {
						clinicsGrid.innerHTML = data.html;
						
						// Update pagination
						if (paginationContainer) {
							paginationContainer.innerHTML = data.pagination || '';
							// Re-attach pagination click handlers
							attachPaginationHandlers();
						}
						
						if (resultsCount) {
							resultsCount.textContent = data.count ?? 0;
						}
						
						// Scroll to top of clinics section
						clinicsGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
					} else {
						clinicsGrid.innerHTML = `
                          <div class="col-span-full text-center py-12">
                            <div class="text-gray-500">
                              <i class="fas fa-search text-4xl mb-4"></i>
                              <h3 class="text-lg font-semibold mb-2">{{ __('no clinics found') }}</h3>
                              <p>{{ __('try adjusting your search criteria or filters') }}</p>
                            </div>
                          </div>`;
						if (paginationContainer) paginationContainer.innerHTML = '';
						if (resultsCount) resultsCount.textContent = '0';
					}
				})
				.catch(error => {
					console.error('Error:', error);
					if (loadingSpinner) loadingSpinner.classList.add('hidden');
					clinicsGrid.style.opacity = '1';
					clinicsGrid.style.pointerEvents = 'auto';
					clinicsGrid.innerHTML = `
                      <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                          <i class="fas fa-search text-4xl mb-4"></i>
                          <h3 class="text-lg font-semibold mb-2">{{ __('no clinics found') }}</h3>
                          <p>{{ __('try adjusting your search criteria or filters') }}</p>
                        </div>
                      </div>`;
					if (paginationContainer) paginationContainer.innerHTML = '';
					if (resultsCount) resultsCount.textContent = '0';
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
				filterClinics(page);
			});
		});
	}

	// Initial attachment of pagination handlers
	if (paginationContainer) {
		attachPaginationHandlers();
	}

	// Event listeners
	searchInput.addEventListener('input', () => filterClinics(1));
	heroSearch.addEventListener('input', () => filterClinics(1));
	sortSelect.addEventListener('change', () => filterClinics(1));
	governorateSelect.addEventListener('change', () => filterClinics(1));
	citySelect.addEventListener('change', () => filterClinics(1));
	areaSelect.addEventListener('change', () => filterClinics(1));

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		heroSearch.value = '';
		sortSelect.value = 'name';
		governorateSelect.value = 'all';
		citySelect.value = 'all';
		areaSelect.value = 'all';
		filterClinics(1);
	});

	// View toggle
	function setGridView() {
		clinicsGrid.className =
			'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation';
		gridViewBtn.className =
			'p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg';
		listViewBtn.className =
			'p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300';
		// gridViewMobileBtn.className = 'p-2 bg-gradient-primary text-white rounded-lg';
		// listViewMobileBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded-lg';
	}

	function setListView() {
		clinicsGrid.className = 'grid grid-cols-1 gap-8 stagger-animation';
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

<script>
$(document).ready(function() {

	// Load governorates on page load
	loadGovernorates();

	// Restore old values if form validation failed
	@if(old('governorate_id'))
	setTimeout(function() {
		const oldGovernorateId = '{{ old("governorate_id") }}';
		const oldCityId = '{{ old("city_id") }}';
		const oldAreaId = '{{ old("area_id") }}';

		if (oldGovernorateId) {
			$('#governorate_id').val(oldGovernorateId)
				.trigger('change');

			// Wait for cities to load, then set city
			setTimeout(function() {
				if (oldCityId) {
					$('#city_id')
						.val(
							oldCityId
						)
						.trigger(
							'change'
						);

					// Wait for areas to load, then set area
					setTimeout(function() {
							if (
								oldAreaId
							) {
								$('#area_id')
									.val(
										oldAreaId
									);
							}
						},
						500
					);
				}
			}, 500);
		}
	}, 500);
	@endif

	// Load governorates function
	function loadGovernorates() {
		$.ajax({
			url: '{{ route("getGovernorates") }}',
			type: 'GET',
			success: function(response) {
				const select = $(
					'#governorate_id'
				);
				select.empty();
				select.append(
					"<option value=''>{{ __('Select Governorate ') }}</option>"
				);
				response.forEach(function(
					governorate
				) {
					select.append(
						`<option value="${governorate.id}">${governorate.name}</option>`
					);
				});
			},
			error: function() {
				toastr.error(
					'Failed to load governorates. Please refresh the page.'
				);
			}
		});
	}

	// Load cities function
	function loadCities(governorateId) {
		if (!governorateId) {
			$('#city_id').empty().append(
				"<option value=''>{{ __('Select City ') }}</option>"
			).prop('disabled',
				true);
			$('#area_id').empty().append(
				"<option value=''>{{ __('Select Area ') }}</option>"
			).prop('disabled',
				true);
			return;
		}

		$.ajax({
			url: '{{ route("getCities") }}',
			type: 'GET',
			data: {
				governorate_id: governorateId
			},
			success: function(response) {
				const select = $('#city_id');
				select.empty();
				select.append(
					"<option value=''>{{ __('Select City ') }}</option>"
				);
				response.forEach(function(
					city
				) {
					select.append(
						`<option value="${city.id}">${city.name}</option>`
					);
				});
				select.prop('disabled',
					false);

				// Reset area dropdown
				$('#area_id').empty().append(
					"<option value=''>{{ __('Select Area ') }}</option>"
				).prop(
					'disabled',
					true);
			},
			error: function() {
				toastr.error(
					'Failed to load cities. Please try again.'
				);
			}
		});
	}

	// Load areas function
	function loadAreas(cityId) {
		if (!cityId) {
			$('#area_id').empty().append(
				"<option value=''>{{ __('Select Area ') }}</option>"
			).prop('disabled',
				true);
			return;
		}

		$.ajax({
			url: '{{ route("getAreas") }}',
			type: 'GET',
			data: {
				city_id: cityId
			},
			success: function(response) {
				const select = $('#area_id');
				select.empty();
				select.append(
					"<option value=''>{{ __('Select Area ') }}</option>"
				);
				response.forEach(function(
					area
				) {
					select.append(
						`<option value="${area.id}">${area.name}</option>`
					);
				});
				select.prop('disabled',
					false);
			},
			error: function() {
				toastr.error(
					'Failed to load areas. Please try again.'
				);
			}
		});
	}

	// Handle governorate change
	$('#governorate_id').on('change', function() {
		const governorateId = $(this).val();
		loadCities(governorateId);

	});

	// Handle city change
	$('#city_id').on('change', function() {
		const cityId = $(this).val();
		loadAreas(cityId);

	});

	// Handle area change
	$('#area_id').on('change', function() {
		const areaId = $(this).val();

	});

});
</script>
@endpush
