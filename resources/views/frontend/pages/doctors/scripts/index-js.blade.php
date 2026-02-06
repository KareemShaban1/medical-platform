@push('scripts')
<script>
// AJAX Filter functionality for Doctors
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('search');
	const heroSearch = document.getElementById('heroSearch');
	const sortSelect = document.getElementById('sort');
	const governorateSelect = document.getElementById('governorate_id');
	const citySelect = document.getElementById('city_id');
	const areaSelect = document.getElementById('area_id');
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
			doctorsGrid.innerHTML =
				'<div class="col-span-full flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
		}

		// Set timeout for search input to avoid too many requests
		filterTimeout = setTimeout(() => {
			const formData = new FormData();

			// Get filter values
			const searchValue = searchInput?.value ||
				heroSearch?.value || '';
			if (searchValue) formData.append('search',
				searchValue);

			if (specialitySelect?.value) formData.append(
				'speciality_id',
				specialitySelect.value);
			if (featuredSelect?.value) formData.append(
				'featured', featuredSelect
				.value);
			if (hasClinicSelect?.value !== '') formData
				.append('has_clinic', hasClinicSelect
					?.value || '');
			if (sortSelect?.value) formData.append('sort',
				sortSelect.value);

			if (governorateSelect?.value && governorateSelect.value !== 'all') {
				formData.append('governorate_id', governorateSelect.value);
			}
			if (citySelect?.value && citySelect.value !== 'all') {
				formData.append('city_id', citySelect.value);
			}
			if (areaSelect?.value && areaSelect.value !== 'all') {
				formData.append('area_id', areaSelect.value);
			}

			// Update active filters count
			let activeCount = 0;
			if (searchValue) activeCount++;
			if (specialitySelect?.value) activeCount++;
			if (featuredSelect?.value) activeCount++;
			if (hasClinicSelect?.value !== '') activeCount++;

			if (activeCount > 0) {
				if (activeFiltersCount) {
					activeFiltersCount.classList
						.remove('hidden');
					if (filterCount) filterCount
						.textContent =
						activeCount;
				}
			} else {
				if (activeFiltersCount)
					activeFiltersCount.classList
					.add('hidden');
			}

			// Make AJAX request
			fetch('{{ route("doctors.filter") }}', {
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

					if (data && data
						.success &&
						data
						.html !==
						'') {
						if (
							doctorsGrid
						)
							doctorsGrid
							.innerHTML =
							data
							.html;
						if (paginationContainer &&
							data
							.pagination
						) {
							paginationContainer
								.innerHTML =
								data
								.pagination;
						}
						if (
							resultsCount
						) {
							resultsCount
								.textContent =
								data
								.count ??
								0;
						}
					} else {
						if (
							doctorsGrid
						) {
							doctorsGrid
								.innerHTML = `
									<div class="col-span-full text-center py-12">
										<div class="text-gray-500">
											<i class="fas fa-user-md text-6xl mb-4"></i>
											<h3 class="text-lg font-semibold mb-2">{{ __('no doctors found') }}</h3>
											<p>{{ __('try adjusting your search criteria or filters') }}</p>
										</div>
									</div>`;
						}
						if (
							paginationContainer
						)
							paginationContainer
							.innerHTML =
							'';
						if (
							resultsCount
						)
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
					if (doctorsGrid) {
						doctorsGrid
							.innerHTML = `
								<div class="col-span-full text-center py-12">
									<div class="text-gray-500">
										<i class="fas fa-user-md text-6xl mb-4"></i>
										<h3 class="text-lg font-semibold mb-2">{{ __('no doctors found') }}</h3>
										<p>{{ __('try adjusting your search criteria or filters') }}</p>
									</div>
								</div>`;
					}
					if (
						paginationContainer
					)
						paginationContainer
						.innerHTML =
						'';
					if (resultsCount)
						resultsCount
						.textContent =
						'0';
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
	if (governorateSelect) governorateSelect.addEventListener('change', filterDoctors);
	if (citySelect) citySelect.addEventListener('change', filterDoctors);
	if (areaSelect) areaSelect.addEventListener('change', filterDoctors);
	if (clearFiltersBtn) {
		clearFiltersBtn.addEventListener('click', function() {
			if (searchInput) searchInput.value = '';
			if (heroSearch) heroSearch.value = '';
			if (sortSelect) sortSelect.value = 'featured';
			if (specialitySelect) specialitySelect.value = '';
			if (featuredSelect) featuredSelect.value = '';
			if (hasClinicSelect) hasClinicSelect.value = '';
			if (governorateSelect) governorateSelect.value =
				'';
			if (citySelect) citySelect.value = '';
			if (areaSelect) areaSelect.value = '';
			filterDoctors();
			if (activeFiltersCount) activeFiltersCount
				.classList.add('hidden');
			filterDoctors();
		});
	}

	// View toggle functionality
	if (gridViewBtn && listViewBtn) {
		gridViewBtn.addEventListener('click', function() {
			if (doctorsGrid) {
				doctorsGrid.className =
					'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation';
			}
			gridViewBtn.classList.add('bg-gradient-primary',
				'text-white');
			gridViewBtn.classList.remove('bg-gray-200',
				'text-gray-600');
			listViewBtn.classList.remove(
				'bg-gradient-primary',
				'text-white');
			listViewBtn.classList.add('bg-gray-200',
				'text-gray-600');
		});

		listViewBtn.addEventListener('click', function() {
			if (doctorsGrid) {
				doctorsGrid.className =
					'grid grid-cols-1 gap-6 stagger-animation';
			}
			listViewBtn.classList.add('bg-gradient-primary',
				'text-white');
			listViewBtn.classList.remove('bg-gray-200',
				'text-gray-600');
			gridViewBtn.classList.remove(
				'bg-gradient-primary',
				'text-white');
			gridViewBtn.classList.add('bg-gray-200',
				'text-gray-600');
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
