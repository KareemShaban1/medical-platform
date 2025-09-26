@extends('frontend.layouts.app')


@section('content')


<!-- Page Header -->
<div class="bg-white shadow-sm">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<h1 class="text-3xl font-bold text-gray-900">Medical Courses</h1>
		<p class="text-gray-600 mt-2">Enhance your medical knowledge with professional courses</p>
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
							placeholder="Search courses..."
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
						<option value="clinical">Clinical Medicine
						</option>
						<option value="surgery">Surgery</option>
						<option value="nursing">Nursing</option>
						<option value="pharmacy">Pharmacy</option>
						<option value="emergency">Emergency Medicine
						</option>
						<option value="pediatrics">Pediatrics</option>
						<option value="cardiology">Cardiology</option>
					</select>
				</div>

				<!-- Level Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Level</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="level-all" name="level"
								value="" class="mr-2">
							<label for="level-all" class="text-sm">All
								Levels</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="level-beginner"
								name="level" value="beginner"
								class="mr-2">
							<label for="level-beginner"
								class="text-sm">Beginner</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="level-intermediate"
								name="level" value="intermediate"
								class="mr-2">
							<label for="level-intermediate"
								class="text-sm">Intermediate</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="level-advanced"
								name="level" value="advanced"
								class="mr-2">
							<label for="level-advanced"
								class="text-sm">Advanced</label>
						</div>
					</div>
				</div>

				<!-- Duration Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="duration-all"
								name="duration" value="" class="mr-2">
							<label for="duration-all" class="text-sm">Any
								Duration</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="duration-short"
								name="duration" value="short"
								class="mr-2">
							<label for="duration-short" class="text-sm">Short
								(1-4
								weeks)</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="duration-medium"
								name="duration" value="medium"
								class="mr-2">
							<label for="duration-medium"
								class="text-sm">Medium (1-3
								months)</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="duration-long"
								name="duration" value="long"
								class="mr-2">
							<label for="duration-long" class="text-sm">Long
								(3+
								months)</label>
						</div>
					</div>
				</div>

				<!-- Price Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Price
						Range</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="price-all" name="price"
								value="" class="mr-2">
							<label for="price-all" class="text-sm">All
								Prices</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="price-free" name="price"
								value="free" class="mr-2">
							<label for="price-free"
								class="text-sm">Free</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="price-low" name="price"
								value="low" class="mr-2">
							<label for="price-low" class="text-sm">Under
								$100</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="price-medium" name="price"
								value="medium" class="mr-2">
							<label for="price-medium" class="text-sm">$100 -
								$500</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="price-high" name="price"
								value="high" class="mr-2">
							<label for="price-high"
								class="text-sm">$500+</label>
						</div>
					</div>
				</div>

				<!-- Format Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Format</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="checkbox" id="format-online"
								class="mr-2">
							<label for="format-online"
								class="text-sm">Online</label>
						</div>
						<div class="flex items-center">
							<input type="checkbox" id="format-offline"
								class="mr-2">
							<label for="format-offline"
								class="text-sm">Offline</label>
						</div>
						<div class="flex items-center">
							<input type="checkbox" id="format-hybrid"
								class="mr-2">
							<label for="format-hybrid"
								class="text-sm">Hybrid</label>
						</div>
					</div>
				</div>

				<!-- Sort Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Sort
						By</label>
					<select id="sort"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="newest">Newest First</option>
						<option value="popular">Most Popular</option>
						<option value="price-low">Price Low to High
						</option>
						<option value="price-high">Price High to Low
						</option>
						<option value="title">Title A-Z</option>
						<option value="duration">Shortest First</option>
					</select>
				</div>

				<!-- Clear Filters -->
				<button id="clearFilters"
					class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition">
					Clear All Filters
				</button>
			</div>
		</div>

		<!-- Courses Grid -->
		<div class="lg:w-3/4">
			<!-- Results Header -->
			<div class="flex justify-between items-center mb-6">
				<div>
					<span class="text-gray-600">Showing <span id="resultsCount">20</span>
						courses</span>
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

			<!-- Courses Grid -->
			<div id="coursesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@for($i = 1; $i <= 20; $i++) <div
					class="course-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
					data-category="clinical" data-level="intermediate"
					data-duration="{{ rand(2, 12) }}" data-price="{{ rand(0, 800) }}"
					data-name="Medical Course {{ $i }}">
					<div class="h-48 bg-gray-200 flex items-center justify-center">
						<i class="fas fa-graduation-cap text-4xl text-gray-400"></i>
					</div>
					<div class="p-4">
						<div class="flex items-center justify-between mb-2">
							<span
								class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Clinical</span>
							<span class="text-sm text-gray-500">{{ rand(2, 12) }}
								weeks</span>
						</div>
						<h3 class="font-semibold text-lg mb-2">Medical
							Course {{ $i }}</h3>
						<p class="text-gray-600 text-sm mb-3">Professional
							medical training and certification</p>
						<div class="flex items-center mb-3">
							<div class="flex text-yellow-400">
								@for($j = 1; $j <= 5; $j++) <i
									class="fas fa-star">
									</i>
									@endfor
							</div>
							<span class="text-sm text-gray-500 ml-2">({{ rand(50, 300) }}
								reviews)</span>
						</div>
						<div class="flex items-center text-sm text-gray-500 mb-3">
							<i class="fas fa-clock mr-2"></i>
							<span>{{ rand(2, 12) }} weeks</span>
						</div>
						<div class="flex items-center text-sm text-gray-500 mb-3">
							<i class="fas fa-users mr-2"></i>
							<span>{{ rand(100, 1000) }}
								students</span>
						</div>
						<div class="flex justify-between items-center">
							<span class="text-lg font-bold text-orange-600">
								@if(rand(0, 1))
								Free
								@else
								${{ rand(50, 800) }}
								@endif
							</span>
							<button
								class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 transition">
								Enroll Now
							</button>
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
	const levelRadios = document.querySelectorAll('input[name="level"]');
	const durationRadios = document.querySelectorAll('input[name="duration"]');
	const priceRadios = document.querySelectorAll('input[name="price"]');
	const formatCheckboxes = document.querySelectorAll('input[type="checkbox"]');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const coursesGrid = document.getElementById('coursesGrid');
	const resultsCount = document.getElementById('resultsCount');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');

	let courses = Array.from(document.querySelectorAll('.course-card'));

	function filterCourses() {
		const searchTerm = searchInput.value.toLowerCase();
		const selectedCategory = categorySelect.value;
		const selectedLevel = document.querySelector('input[name="level"]:checked')
			?.value;
		const selectedDuration = document.querySelector(
			'input[name="duration"]:checked')?.value;
		const selectedPrice = document.querySelector('input[name="price"]:checked')
			?.value;
		const sortBy = sortSelect.value;

		let filteredCourses = courses.filter(course => {
			const name = course.dataset.name.toLowerCase();
			const category = course.dataset.category;
			const level = course.dataset.level;
			const duration = parseInt(course.dataset
				.duration);
			const price = parseInt(course.dataset.price);

			// Search filter
			if (searchTerm && !name.includes(searchTerm))
				return false;

			// Category filter
			if (selectedCategory && category !==
				selectedCategory) return false;

			// Level filter
			if (selectedLevel && level !== selectedLevel)
				return false;

			// Duration filter
			if (selectedDuration) {
				if (selectedDuration === 'short' &&
					duration > 4) return false;
				if (selectedDuration === 'medium' && (
						duration < 4 ||
						duration > 12))
					return false;
				if (selectedDuration === 'long' &&
					duration < 12) return false;
			}

			// Price filter
			if (selectedPrice) {
				if (selectedPrice === 'free' && price >
					0) return false;
				if (selectedPrice === 'low' && price >=
					100) return false;
				if (selectedPrice === 'medium' && (
						price < 100 ||
						price > 500))
					return false;
				if (selectedPrice === 'high' && price <
					500) return false;
			}

			return true;
		});

		// Sort courses
		filteredCourses.sort((a, b) => {
			switch (sortBy) {
				case 'newest':
					return Math.random() -
						0.5; // Simulate newest sorting
				case 'popular':
					return Math.random() -
						0.5; // Simulate popularity sorting
				case 'price-low':
					return parseInt(a.dataset
							.price) -
						parseInt(b.dataset
							.price);
				case 'price-high':
					return parseInt(b.dataset
							.price) -
						parseInt(a.dataset
							.price);
				case 'title':
					return a.dataset.name
						.localeCompare(b
							.dataset
							.name);
				case 'duration':
					return parseInt(a.dataset
							.duration
						) -
						parseInt(b.dataset
							.duration
						);
				default:
					return 0;
			}
		});

		// Update results
		coursesGrid.innerHTML = '';
		filteredCourses.forEach(course => coursesGrid.appendChild(course));
		resultsCount.textContent = filteredCourses.length;
	}

	// Event listeners
	searchInput.addEventListener('input', filterCourses);
	categorySelect.addEventListener('change', filterCourses);
	levelRadios.forEach(radio => radio.addEventListener('change', filterCourses));
	durationRadios.forEach(radio => radio.addEventListener('change', filterCourses));
	priceRadios.forEach(radio => radio.addEventListener('change', filterCourses));
	formatCheckboxes.forEach(checkbox => checkbox.addEventListener('change', filterCourses));
	sortSelect.addEventListener('change', filterCourses);

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		categorySelect.value = '';
		levelRadios.forEach(radio => radio.checked = false);
		durationRadios.forEach(radio => radio.checked = false);
		priceRadios.forEach(radio => radio.checked = false);
		formatCheckboxes.forEach(checkbox => checkbox.checked =
			false);
		sortSelect.value = 'newest';
		filterCourses();
	});

	// View toggle
	gridViewBtn.addEventListener('click', function() {
		coursesGrid.className =
			'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
		gridViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		listViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});

	listViewBtn.addEventListener('click', function() {
		coursesGrid.className = 'grid grid-cols-1 gap-6';
		listViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		gridViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});
});
</script>

@endpush