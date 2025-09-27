@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const searchInput = document.getElementById('search');
		const categorySelect = document.getElementById('category');
		const locationSelect = document.getElementById('location');
		const ratingRadios = document.querySelectorAll('input[name="rating"]');
		const certifiedCheckbox = document.getElementById('certified');
		const isoCheckbox = document.getElementById('iso');
		const fdaCheckbox = document.getElementById('fda');
		const experienceSelect = document.getElementById('experience');
		const sortSelect = document.getElementById('sort');
		const clearFiltersBtn = document.getElementById('clearFilters');
		const suppliersGrid = document.getElementById('suppliersGrid');
		const paginationContainer = document.getElementById('paginationContainer');
		const resultsCount = document.getElementById('resultsCount');
		const loadingSpinner = document.getElementById('loadingSpinner');
		const gridViewBtn = document.getElementById('gridView');
		const listViewBtn = document.getElementById('listView');

		let searchTimeout;

		// Debounced search
		searchInput.addEventListener('input', function() {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(filterSuppliers, 300);
		});

		// Other filters
		ratingRadios.forEach(radio => radio.addEventListener('change', filterSuppliers));
		sortSelect.addEventListener('change', filterSuppliers);

		// Clear filters
		clearFiltersBtn.addEventListener('click', function() {
			searchInput.value = '';
			ratingRadios.forEach(radio => radio.checked = false);
			sortSelect.value = 'name';
			filterSuppliers();
		});

		// View toggle
		gridViewBtn.addEventListener('click', function() {
			suppliersGrid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
			gridViewBtn.className = 'p-2 bg-gradient-primary text-white rounded';
			listViewBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded';
		});

		listViewBtn.addEventListener('click', function() {
			suppliersGrid.className = 'grid grid-cols-1 gap-6';
			listViewBtn.className = 'p-2 bg-gradient-primary text-white rounded';
			gridViewBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded';
		});

		function filterSuppliers() {
			// Show loading spinner
			loadingSpinner.classList.remove('hidden');
			suppliersGrid.classList.add('hidden');

			// Collect form data
			const formData = new FormData();
			formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
			formData.append('search', searchInput.value);
			formData.append('category', categorySelect.value);
			formData.append('location', locationSelect.value);
			formData.append('rating', document.querySelector('input[name="rating"]:checked')?.value || '');
			formData.append('certified', certifiedCheckbox.checked ? '1' : '');
			formData.append('iso', isoCheckbox.checked ? '1' : '');
			formData.append('fda', fdaCheckbox.checked ? '1' : '');
			formData.append('experience', experienceSelect.value);
			formData.append('sort', sortSelect.value);

			// Make AJAX request
			fetch('{{ route("suppliers.filter") }}', {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						suppliersGrid.innerHTML = data.html;
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
					suppliersGrid.classList.remove('hidden');
				});
		}
	});
</script>
@endpush
