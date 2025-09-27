@extends('frontend.layouts.app')

@section('content')
<!-- Page Header -->
<div class="bg-gradient-primary">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<h1 class="text-3xl font-bold text-white">Medical Courses</h1>
		<p class="text-white/90 mt-2">Enhance your medical knowledge with professional courses</p>
	</div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
	<div class="flex flex-col lg:flex-row gap-8">
		<!-- Filters Sidebar -->
		<div class="lg:w-1/4">
			<div class="card p-6">
				<h3 class="text-lg font-semibold mb-4">Filters</h3>

				<!-- Search Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
					<div class="relative">
						<input type="text" id="search" placeholder="Search courses..."
							class="form-input w-full pl-10 pr-4 py-2">
						<i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
					</div>
				</div>

				<!-- Category Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
					<select id="category" class="form-input w-full px-3 py-2">
						<option value="">All Categories</option>
						@foreach($categories as $key => $value)
						<option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
							{{ $value }}
						</option>
						@endforeach
					</select>
				</div>

				<!-- Level Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
					<select id="level" class="form-input w-full px-3 py-2">
						<option value="">All Levels</option>
						@foreach($levels as $key => $value)
						<option value="{{ $key }}" {{ request('level') == $key ? 'selected' : '' }}>
							{{ $value }}
						</option>
						@endforeach
					</select>
				</div>

				<!-- Duration Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
					<select id="duration" class="form-input w-full px-3 py-2">
						<option value="">Any Duration</option>
						@foreach($durations as $key => $value)
						<option value="{{ $key }}" {{ request('duration') == $key ? 'selected' : '' }}>
							{{ $value }}
						</option>
						@endforeach
					</select>
				</div>

				<!-- Price Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
					<select id="price" class="form-input w-full px-3 py-2">
						<option value="">Any Price</option>
						@foreach($priceRanges as $key => $value)
						<option value="{{ $key }}" {{ request('price') == $key ? 'selected' : '' }}>
							{{ $value }}
						</option>
						@endforeach
					</select>
				</div>

				<!-- Date Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="date-all" name="date" value="" class="mr-2" {{ !request('date') ? 'checked' : '' }}>
							<label for="date-all" class="text-sm">All Dates</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="date-upcoming" name="date" value="upcoming" class="mr-2" {{ request('date') == 'upcoming' ? 'checked' : '' }}>
							<label for="date-upcoming" class="text-sm">Upcoming</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="date-ongoing" name="date" value="ongoing" class="mr-2" {{ request('date') == 'ongoing' ? 'checked' : '' }}>
							<label for="date-ongoing" class="text-sm">Ongoing</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="date-completed" name="date" value="completed" class="mr-2" {{ request('date') == 'completed' ? 'checked' : '' }}>
							<label for="date-completed" class="text-sm">Completed</label>
						</div>
					</div>
				</div>

				<!-- Language Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
					<select id="language" class="form-input w-full px-3 py-2">
						<option value="">All Languages</option>
						<option value="english" {{ request('language') == 'english' ? 'selected' : '' }}>English</option>
						<option value="arabic" {{ request('language') == 'arabic' ? 'selected' : '' }}>Arabic</option>
						<option value="french" {{ request('language') == 'french' ? 'selected' : '' }}>French</option>
						<option value="spanish" {{ request('language') == 'spanish' ? 'selected' : '' }}>Spanish</option>
					</select>
				</div>

				<!-- Sort Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
					<select id="sort" class="form-input w-full px-3 py-2">
						<option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
						<option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
						<option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
						<option value="duration" {{ request('sort') == 'duration' ? 'selected' : '' }}>Shortest Duration</option>
						<option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
						<option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
						<option value="start_date" {{ request('sort') == 'start_date' ? 'selected' : '' }}>Start Date</option>
					</select>
				</div>

				<!-- Clear Filters -->
				<button id="clearFilters" class="btn-secondary w-full">
					Clear All Filters
				</button>
			</div>
		</div>

		<!-- Courses Grid -->
		<div class="lg:w-3/4">
			<!-- Results Header -->
			<div class="flex justify-between items-center mb-6">
				<div>
					<span class="text-gray-600">Showing <span id="resultsCount">{{ $courses->total() }}</span> courses</span>
				</div>
				<div class="flex items-center space-x-2">
					<span class="text-sm text-gray-600">View:</span>
					<button id="gridView" class="p-2 bg-gradient-primary text-white rounded">
						<i class="fas fa-th"></i>
					</button>
					<button id="listView" class="p-2 bg-gray-200 text-gray-600 rounded">
						<i class="fas fa-list"></i>
					</button>
				</div>
			</div>

			<!-- Loading Spinner -->
			<div id="loadingSpinner" class="hidden text-center py-8">
				<div class="spinner mx-auto"></div>
				<p class="text-gray-500 mt-2">Loading courses...</p>
			</div>

			<!-- Courses Grid -->
			<div id="coursesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@include('frontend.partials.courses-grid', ['courses' => $courses])
			</div>

			<!-- Pagination -->
			<div id="paginationContainer" class="mt-8">
				{{ $courses->links() }}
			</div>
		</div>
	</div>
</div>
@endsection

@include('frontend.js-scripts.courses')

