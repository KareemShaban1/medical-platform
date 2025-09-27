@extends('frontend.layouts.app')
@section('content')

<!-- Page Header -->
<div class="bg-gradient-primary">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<h1 class="text-3xl font-bold text-white">Medical Blog</h1>
		<p class="text-white/90 mt-2">Stay updated with the latest medical news and insights</p>
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
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Search</label>
					<div class="relative">
						<input type="text" id="search"
							placeholder="Search articles..."
							class="form-input w-full pl-10 pr-4 py-2">
						<i
							class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
					</div>
				</div>

				<!-- Category Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Category</label>
					<select id="category" class="form-input w-full px-3 py-2">
						<option value="">All Categories</option>
						@foreach($categories as $category)
						<option value="{{ $category->id }}">
							{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}
						</option>
						@endforeach
					</select>
				</div>

				<!-- Author Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Author</label>
					<select id="author" class="form-input w-full px-3 py-2">
						<option value="">All Authors</option>
						@foreach($authors as $author)
						<option value="{{ $author }}">
							{{ ucfirst(str_replace('-', ' ', $author)) }}
						</option>
						@endforeach
					</select>
				</div>

				<!-- Date Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Published</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="date-all" name="date"
								value="" class="mr-2" checked>
							<label for="date-all" class="text-sm">All
								Time</label>
						</div>
						@foreach($dateRanges as $range)
						<div class="flex items-center">
							<input type="radio" id="date-{{ $range }}"
								name="date" value="{{ $range }}"
								class="mr-2">
							<label for="date-{{ $range }}" class="text-sm">
								{{ ucfirst($range) }}
							</label>
						</div>
						@endforeach
					</div>
				</div>

				<!-- Tags Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Popular
						Tags</label>
					<div class="flex flex-wrap gap-2">
						@foreach($tags as $tag)
						<span class="tag bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded cursor-pointer hover:bg-blue-200"
							data-tag="{{ $tag }}">{{ ucfirst($tag) }}</span>
						@endforeach
					</div>
				</div>

				<!-- Sort Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Sort
						By</label>
					<select id="sort" class="form-input w-full px-3 py-2">
						<option value="newest">Newest First</option>
						<option value="oldest">Oldest First</option>
						<option value="popular">Most Popular</option>
						<option value="title">Title A-Z</option>
					</select>
				</div>

				<!-- Clear Filters -->
				<button id="clearFilters" class="btn-secondary w-full">
					Clear All Filters
				</button>
			</div>
		</div>

		<!-- Blog Posts Grid -->
		<div class="lg:w-3/4">
			<!-- Results Header -->
			<div class="flex justify-between items-center mb-6">
				<div>
					<span class="text-gray-600">Showing <span
							id="resultsCount">{{ $blogPosts->total() }}</span>
						articles</span>
				</div>
				<div class="flex items-center space-x-2">
					<span class="text-sm text-gray-600">View:</span>
					<button id="gridView"
						class="p-2 bg-gradient-primary text-white rounded">
						<i class="fas fa-th"></i>
					</button>
					<button id="listView" class="p-2 bg-gray-200 text-gray-600 rounded">
						<i class="fas fa-list"></i>
					</button>
				</div>
			</div>

			<!-- Blog Posts Grid -->
			<div id="blogGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@include('frontend.partials.blog-grid', ['blogPosts' => $blogPosts])
			</div>

			<!-- Pagination -->
			<div id="paginationContainer">
				{{ $blogPosts->links() }}
			</div>
		</div>
	</div>
</div>

@endsection

@include('frontend.js-scripts.blogs')

