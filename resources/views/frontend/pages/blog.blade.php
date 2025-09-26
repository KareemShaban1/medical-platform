@extends('frontend.layouts.app')
@section('content')

<!-- Page Header -->
<div class="bg-white shadow-sm">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<h1 class="text-3xl font-bold text-gray-900">Medical Blog</h1>
		<p class="text-gray-600 mt-2">Stay updated with the latest medical news and insights</p>
	</div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
	<div class="flex flex-col lg:flex-row gap-8">
		<!-- Filters Sidebar -->
		<div class="lg:w-1/4">
			<div class="bg-white rounded-lg shadow-md p-6">
				<h3 class="text-lg font-semibold mb-4">Filters</h3>

				<!-- Search Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Search</label>
					<div class="relative">
						<input type="text" id="search"
							placeholder="Search articles..."
							class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<i
							class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
					</div>
				</div>

				<!-- Category Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Category</label>
					<select id="category"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="">All Categories</option>
						<option value="news">Medical News</option>
						<option value="research">Research</option>
						<option value="health-tips">Health Tips</option>
						<option value="technology">Medical Technology
						</option>
						<option value="treatment">Treatment</option>
						<option value="prevention">Prevention</option>
					</select>
				</div>

				<!-- Author Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Author</label>
					<select id="author"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="">All Authors</option>
						<option value="dr-smith">Dr. Smith</option>
						<option value="dr-johnson">Dr. Johnson</option>
						<option value="dr-williams">Dr. Williams</option>
						<option value="medical-team">Medical Team</option>
					</select>
				</div>

				<!-- Date Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Published</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="date-all" name="date"
								value="" class="mr-2">
							<label for="date-all" class="text-sm">All
								Time</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="date-week" name="date"
								value="week" class="mr-2">
							<label for="date-week" class="text-sm">This
								Week</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="date-month" name="date"
								value="month" class="mr-2">
							<label for="date-month" class="text-sm">This
								Month</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="date-year" name="date"
								value="year" class="mr-2">
							<label for="date-year" class="text-sm">This
								Year</label>
						</div>
					</div>
				</div>

				<!-- Tags Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Popular
						Tags</label>
					<div class="flex flex-wrap gap-2">
						<span class="tag bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded cursor-pointer hover:bg-blue-200"
							data-tag="covid">COVID-19</span>
						<span class="tag bg-green-100 text-green-800 text-xs px-2 py-1 rounded cursor-pointer hover:bg-green-200"
							data-tag="vaccine">Vaccine</span>
						<span class="tag bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded cursor-pointer hover:bg-purple-200"
							data-tag="heart">Heart Health</span>
						<span class="tag bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded cursor-pointer hover:bg-yellow-200"
							data-tag="mental">Mental Health</span>
						<span class="tag bg-red-100 text-red-800 text-xs px-2 py-1 rounded cursor-pointer hover:bg-red-200"
							data-tag="cancer">Cancer</span>
					</div>
				</div>

				<!-- Sort Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Sort
						By</label>
					<select id="sort"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="newest">Newest First</option>
						<option value="oldest">Oldest First</option>
						<option value="popular">Most Popular</option>
						<option value="title">Title A-Z</option>
					</select>
				</div>

				<!-- Clear Filters -->
				<button id="clearFilters"
					class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition">
					Clear All Filters
				</button>
			</div>
		</div>

		<!-- Blog Posts Grid -->
		<div class="lg:w-3/4">
			<!-- Results Header -->
			<div class="flex justify-between items-center mb-6">
				<div>
					<span class="text-gray-600">Showing <span id="resultsCount">15</span>
						articles</span>
				</div>
				<div class="flex items-center space-x-2">
					<span class="text-sm text-gray-600">View:</span>
					<button id="gridView" class="p-2 bg-blue-600 text-white rounded">
						<i class="fas fa-th"></i>
					</button>
					<button id="listView" class="p-2 bg-gray-200 text-gray-600 rounded">
						<i class="fas fa-list"></i>
					</button>
				</div>
			</div>

			<!-- Blog Posts Grid -->
			<div id="blogGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@for($i = 1; $i <= 15; $i++) <div
					class="blog-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
					data-category="news" data-author="dr-smith"
					data-date="{{ rand(1, 30) }}" data-name="Medical Article {{ $i }}">
					<div class="h-48 bg-gray-200 flex items-center justify-center">
						<i class="fas fa-newspaper text-4xl text-gray-400"></i>
					</div>
					<div class="p-4">
						<div class="flex items-center text-sm text-gray-500 mb-2">
							<span
								class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">News</span>
							<span>{{ rand(1, 30) }} days ago</span>
						</div>
						<h3 class="font-semibold text-lg mb-2">Medical
							Article {{ $i }}</h3>
						<p class="text-gray-600 text-sm mb-3">Latest
							medical research and health insights for
							professionals and patients...</p>
						<div class="flex items-center justify-between">
							<div
								class="flex items-center text-sm text-gray-500">
								<i class="fas fa-user mr-1"></i>
								<span>Dr. Smith</span>
							</div>
							<div
								class="flex items-center text-sm text-gray-500">
								<i class="fas fa-eye mr-1"></i>
								<span>{{ rand(100, 1000) }}</span>
							</div>
						</div>
					</div>
			</div>
			@endfor
		</div>

		<!-- Pagination -->
		<div class="mt-8 flex justify-center">
			<nav class="flex items-center space-x-2">
				<button
					class="px-3 py-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">Previous</button>
				<button class="px-3 py-2 bg-blue-600 text-white rounded">1</button>
				<button
					class="px-3 py-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">2</button>
				<button
					class="px-3 py-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">Next</button>
			</nav>
		</div>
	</div>
</div>
</div>

@endsection

@push('scripts')
<script>
// Filter functionality
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
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');

	let blogPosts = Array.from(document.querySelectorAll('.blog-card'));
	let selectedTags = [];

	function filterBlogPosts() {
		const searchTerm = searchInput.value.toLowerCase();
		const selectedCategory = categorySelect.value;
		const selectedAuthor = authorSelect.value;
		const selectedDate = document.querySelector('input[name="date"]:checked')
			?.value;
		const sortBy = sortSelect.value;

		let filteredPosts = blogPosts.filter(post => {
			const name = post.dataset.name.toLowerCase();
			const category = post.dataset.category;
			const author = post.dataset.author;
			const date = parseInt(post.dataset.date);

			// Search filter
			if (searchTerm && !name.includes(searchTerm))
				return false;

			// Category filter
			if (selectedCategory && category !==
				selectedCategory) return false;

			// Author filter
			if (selectedAuthor && author !== selectedAuthor)
				return false;

			// Date filter
			if (selectedDate) {
				const daysAgo = parseInt(selectedDate);
				if (date > daysAgo) return false;
			}

			return true;
		});

		// Sort posts
		filteredPosts.sort((a, b) => {
			switch (sortBy) {
				case 'newest':
					return parseInt(a.dataset
							.date) -
						parseInt(b.dataset
							.date);
				case 'oldest':
					return parseInt(b.dataset
							.date) -
						parseInt(a.dataset
							.date);
				case 'popular':
					return Math.random() -
						0.5; // Simulate popularity sorting
				case 'title':
					return a.dataset.name
						.localeCompare(b
							.dataset
							.name);
				default:
					return 0;
			}
		});

		// Update results
		blogGrid.innerHTML = '';
		filteredPosts.forEach(post => blogGrid.appendChild(post));
		resultsCount.textContent = filteredPosts.length;
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
			this.classList.toggle(
				'bg-blue-100'
			);
			this.classList.toggle(
				'bg-blue-200'
			);
			filterBlogPosts();
		});
	});

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		categorySelect.value = '';
		authorSelect.value = '';
		dateRadios.forEach(radio => radio.checked = false);
		sortSelect.value = 'newest';
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