@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const searchInput = document.getElementById('search');
		const categorySelect = document.getElementById('category');
		const levelSelect = document.getElementById('level');
		const durationSelect = document.getElementById('duration');
		const priceSelect = document.getElementById('price');
		const dateRadios = document.querySelectorAll('input[name="date"]');
		const languageSelect = document.getElementById('language');
		const sortSelect = document.getElementById('sort');
		const clearFiltersBtn = document.getElementById('clearFilters');
		const coursesGrid = document.getElementById('coursesGrid');
		const paginationContainer = document.getElementById('paginationContainer');
		const resultsCount = document.getElementById('resultsCount');
		const loadingSpinner = document.getElementById('loadingSpinner');
		const gridViewBtn = document.getElementById('gridView');
		const listViewBtn = document.getElementById('listView');

		let searchTimeout;

		// Debounced search
		searchInput.addEventListener('input', function() {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(filterCourses, 300);
		});

		// Other filters
		categorySelect.addEventListener('change', filterCourses);
		levelSelect.addEventListener('change', filterCourses);
		durationSelect.addEventListener('change', filterCourses);
		priceSelect.addEventListener('change', filterCourses);
		dateRadios.forEach(radio => radio.addEventListener('change', filterCourses));
		languageSelect.addEventListener('change', filterCourses);
		sortSelect.addEventListener('change', filterCourses);

		// Clear filters
		clearFiltersBtn.addEventListener('click', function() {
			searchInput.value = '';
			categorySelect.value = '';
			levelSelect.value = '';
			durationSelect.value = '';
			priceSelect.value = '';
			dateRadios.forEach(radio => radio.checked = false);
			languageSelect.value = '';
			sortSelect.value = 'newest';
			filterCourses();
		});

		// View toggle
		gridViewBtn.addEventListener('click', function() {
			coursesGrid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
			gridViewBtn.className = 'p-2 bg-gradient-primary text-white rounded';
			listViewBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded';
		});

		listViewBtn.addEventListener('click', function() {
			coursesGrid.className = 'grid grid-cols-1 gap-6';
			listViewBtn.className = 'p-2 bg-gradient-primary text-white rounded';
			gridViewBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded';
		});

		function filterCourses() {
			// Show loading spinner
			loadingSpinner.classList.remove('hidden');
			coursesGrid.classList.add('hidden');

			// Collect form data
			const formData = new FormData();
			formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
			formData.append('search', searchInput.value);
			formData.append('category', categorySelect.value);
			formData.append('level', levelSelect.value);
			formData.append('duration', durationSelect.value);
			formData.append('price', priceSelect.value);
			formData.append('date', document.querySelector('input[name="date"]:checked')?.value || '');
			formData.append('language', languageSelect.value);
			formData.append('sort', sortSelect.value);

			// Make AJAX request
			fetch('{{ route("courses.filter") }}', {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						coursesGrid.innerHTML = data.html;
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
					coursesGrid.classList.remove('hidden');
				});
		}
	});
</script>
@endpush