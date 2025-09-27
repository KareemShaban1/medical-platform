
@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const searchInput = document.getElementById('search');
		const specializationSelect = document.getElementById('specialization');
		const locationSelect = document.getElementById('location');
		const ratingRadios = document.querySelectorAll('input[name="rating"]');
		const statusRadios = document.querySelectorAll('input[name="status"]');
		const sortSelect = document.getElementById('sort');
		const clearFiltersBtn = document.getElementById('clearFilters');
		const clinicsGrid = document.getElementById('clinicsGrid');
		const paginationContainer = document.getElementById('paginationContainer');
		const resultsCount = document.getElementById('resultsCount');
		const loadingSpinner = document.getElementById('loadingSpinner');
		const gridViewBtn = document.getElementById('gridView');
		const listViewBtn = document.getElementById('listView');

		let searchTimeout;

		// Debounced search
		searchInput.addEventListener('input', function() {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(filterClinics, 300);
		});

		// Other filters
		specializationSelect.addEventListener('change', filterClinics);
		locationSelect.addEventListener('change', filterClinics);
		ratingRadios.forEach(radio => radio.addEventListener('change', filterClinics));
		statusRadios.forEach(radio => radio.addEventListener('change', filterClinics));
		sortSelect.addEventListener('change', filterClinics);

		// Clear filters
		clearFiltersBtn.addEventListener('click', function() {
			searchInput.value = '';
			specializationSelect.value = '';
			locationSelect.value = '';
			ratingRadios.forEach(radio => radio.checked = false);
			statusRadios.forEach(radio => radio.checked = false);
			sortSelect.value = 'name';
			filterClinics();
		});

		// View toggle
		gridViewBtn.addEventListener('click', function() {
			clinicsGrid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
			gridViewBtn.className = 'p-2 bg-gradient-primary text-white rounded';
			listViewBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded';
		});

		listViewBtn.addEventListener('click', function() {
			clinicsGrid.className = 'grid grid-cols-1 gap-6';
			listViewBtn.className = 'p-2 bg-gradient-primary text-white rounded';
			gridViewBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded';
		});

		function filterClinics() {
			// Show loading spinner
			loadingSpinner.classList.remove('hidden');
			clinicsGrid.classList.add('hidden');

			// Collect form data
			const formData = new FormData();
			formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
			formData.append('search', searchInput.value);
			formData.append('specialization', specializationSelect.value);
			formData.append('location', locationSelect.value);
			formData.append('rating', document.querySelector('input[name="rating"]:checked')?.value || '');
			formData.append('status', document.querySelector('input[name="status"]:checked')?.value || '');
			formData.append('sort', sortSelect.value);

			// Make AJAX request
			fetch('{{ route("clinics.filter") }}', {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						clinicsGrid.innerHTML = data.html;
						paginationContainer.innerHTML = data.pagination;
						resultsCount.textContent = data.count;
					}
				})
				.catch(error => {
					console.error('Error:', error);
				})
				.finally(() => {
					// Hide loading spinner
					loadingSpinner.classList.add('hidden');
					clinicsGrid.classList.remove('hidden');
				});
		}
	});
</script>
@endpush
