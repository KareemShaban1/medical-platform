@push('scripts')
<script>
// AJAX Filter functionality
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('search');
	const categorySelect = document.getElementById('category');
	const authorSelect = document.getElementById('author');
	const dateRadios = document.querySelectorAll('input[name="date"]');
	const tags = document.querySelectorAll('.tag');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const blogGrid = document.getElementById('blogGrid');
	const resultsCount = document.getElementById('resultsCount');
	const paginationContainer = document.getElementById('paginationContainer');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');

	let filterTimeout;
	let selectedTags = [];

	function filterBlogPosts() {
		// Clear existing timeout
		clearTimeout(filterTimeout);

		// Add loading state
		blogGrid.innerHTML =
			'<div class="col-span-full flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';

		// Set timeout for search input to avoid too many requests
		filterTimeout = setTimeout(() => {
			const formData = new FormData();

			// Get filter values
			if (searchInput.value) formData.append('search',
				searchInput.value);
			if (categorySelect.value) formData.append(
				'category', categorySelect
				.value);
			if (authorSelect.value) formData.append('author',
				authorSelect.value);

			const selectedDate = document.querySelector(
				'input[name="date"]:checked');
			if (selectedDate && selectedDate.value) formData
				.append('date', selectedDate.value);

			if (selectedTags.length > 0) {
				selectedTags.forEach(tag => formData
					.append('tags[]',
						tag));
			}

			if (sortSelect.value) formData.append('sort',
				sortSelect.value);

			// Make AJAX request
			fetch('{{ route("blog.filter") }}', {
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
					if (data.success) {
						blogGrid.innerHTML =
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
						blogGrid.innerHTML =
							'<div class="col-span-full text-center py-8 text-gray-500">No blog posts found</div>';
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
					blogGrid.innerHTML =
						'<div class="col-span-full text-center py-8 text-red-500">Error loading blog posts</div>';
				});
		}, searchInput === document.activeElement ? 500 : 0);
	}

	// Event listeners
	searchInput.addEventListener('input', filterBlogPosts);
	categorySelect.addEventListener('change', filterBlogPosts);
	authorSelect.addEventListener('change', filterBlogPosts);
	dateRadios.forEach(radio => radio.addEventListener('change', filterBlogPosts));
	sortSelect.addEventListener('change', filterBlogPosts);

	// Tag functionality
	tags.forEach(tag => {
		tag.addEventListener('click', function() {
			const tagValue = this.dataset
				.tag;
			if (selectedTags.includes(
					tagValue
				)) {
				selectedTags =
					selectedTags
					.filter(t =>
						t !==
						tagValue
					);
				this.classList
					.remove(
						'bg-blue-200'
					);
				this.classList.add(
					'bg-blue-100'
				);
			} else {
				selectedTags.push(
					tagValue
				);
				this.classList
					.remove(
						'bg-blue-100'
					);
				this.classList.add(
					'bg-blue-200'
				);
			}
			filterBlogPosts();
		});
	});

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		categorySelect.value = '';
		authorSelect.value = '';
		dateRadios.forEach(radio => radio.checked = false);
		sortSelect.value = 'newest';
		selectedTags = [];
		tags.forEach(tag => {
			tag.classList.remove(
				'bg-blue-200'
			);
			tag.classList.add(
				'bg-blue-100'
			);
		});
		filterBlogPosts();
	});

	// View toggle
	gridViewBtn.addEventListener('click', function() {
		blogGrid.className =
			'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
		gridViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		listViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});

	listViewBtn.addEventListener('click', function() {
		blogGrid.className = 'grid grid-cols-1 gap-6';
		listViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		gridViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});
});
</script>

@endpush