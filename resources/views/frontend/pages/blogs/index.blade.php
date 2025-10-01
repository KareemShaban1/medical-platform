@extends('frontend.layouts.app')
@section('content')

<!-- Hero Section with Advanced Animations -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden bg-gradient-primary">
	<!-- Animated Background Elements -->
	<div class="absolute inset-0">
		<div class="absolute top-20 left-10 w-32 h-32 bg-white/10 rounded-full animate-pulse"></div>
		<div class="absolute top-40 right-20 w-24 h-24 bg-white/5 rounded-full animate-bounce"></div>
		<div class="absolute bottom-32 left-1/4 w-16 h-16 bg-white/15 rounded-full animate-ping"></div>
		<div class="absolute top-1/3 right-1/3 w-12 h-12 bg-white/20 rounded-full animate-pulse"></div>
		<div class="absolute bottom-20 right-10 w-20 h-20 bg-white/8 rounded-full animate-bounce"></div>
		<div class="absolute top-1/2 left-1/2 w-8 h-8 bg-white/25 rounded-full animate-ping"></div>
	</div>

	<div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
		<h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-up">
			<span class="bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
				Medical Blog
			</span>
		</h1>
		<p class="text-xl md:text-2xl mb-8 animate-fade-in-up animation-delay-200 opacity-90">
			Stay updated with the latest medical news and insights
		</p>

		<!-- Interactive Search Bar -->
		<div class="max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
			<div class="relative group">
				<input type="text" id="heroSearch" placeholder="Search for articles..."
					class="w-full px-6 py-4 pl-14 pr-6 text-gray-900 rounded-full shadow-2xl focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-300 group-hover:scale-105">
				<i
					class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
				<!-- <button
					class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gradient-primary text-white px-6 py-2 rounded-full hover:scale-105 transition-transform duration-300">
					Search
				</button> -->
			</div>
		</div>
	</div>
</section>

<!-- Horizontal Filters Section -->
<section class="py-8 bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<!-- Filter Toggle Button -->
		<div class="flex items-center justify-between mb-6">
			<div class="flex items-center space-x-4">
				<button id="toggleFilters"
					class="group flex items-center space-x-3 px-6 py-3 bg-gradient-primary text-white rounded-xl hover:scale-105 transition-all duration-300 shadow-lg">
					<i class="fas fa-filter text-lg"></i>
					<span class="font-semibold">Filters</span>
					<i id="filterChevron"
						class="fas fa-chevron-down transition-transform duration-300"></i>
				</button>

				<!-- Active Filters Count -->
				<div id="activeFiltersCount"
					class="hidden bg-primary text-white px-3 py-1 rounded-full text-sm font-semibold">
					<span id="filterCount">0</span> filters
				</div>
			</div>

			<!-- Results Count -->
			<div class="flex items-center space-x-4">
				<div class="bg-gray-100 rounded-xl px-4 py-2">
					<span class="text-gray-600">Showing <span id="resultsCount"
							class="font-bold text-primary">{{ $blogPosts->total() }}</span>
						articles</span>
				</div>

				<!-- View Toggle -->
				<div class="hidden md:flex items-center space-x-2">
					<span class="text-sm text-gray-500">View:</span>
					<button id="gridView"
						class="p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg">
						<i class="fas fa-th"></i>
					</button>
					<button id="listView"
						class="p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300">
						<i class="fas fa-list"></i>
					</button>
				</div>
			</div>
		</div>

		<!-- Collapsible Filters Panel -->
		<div id="filtersPanel" class="hidden bg-gray-50 rounded-2xl p-6 shadow-lg border border-gray-200">
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
				<!-- Search Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">Search
						Articles</label>
					<div class="relative">
						<input type="text" id="search"
							placeholder="Search articles..."
							class="form-input w-full pl-10 pr-4 py-3 group-hover:scale-105 transition-transform duration-300">
						<i
							class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
					</div>
				</div>

				<!-- Category Filter -->
				<div class="group">
					<label
						class="block text-sm font-semibold text-gray-700 mb-3">Category</label>
					<div class="relative">
						<select id="category"
							class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="">All Categories</option>
							@foreach($categories as $category)
							<option value="{{ $category->id }}">
								{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}
							</option>
							@endforeach
						</select>
						<i
							class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
					</div>
				</div>
				<!-- Sort Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">Sort
						By</label>
					<div class="relative">
						<select id="sort"
							class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="newest">Newest First</option>
							<option value="oldest">Oldest First</option>
							<option value="title">Title A-Z</option>
						</select>
						<i
							class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
					</div>
				</div>

				<!-- Clear Filters Button -->
				<div class="group flex items-end">
					<button id="clearFilters" class="btn-secondary w-full group">
						<i
							class="fas fa-refresh mr-2 group-hover:rotate-180 transition-transform duration-500"></i>
						Clear All Filters
					</button>
				</div>
			</div>

		
		</div>
	</div>
</section>

<!-- Blog Posts Grid Section -->
<section class="py-12 bg-gray-50 relative overflow-hidden">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<!-- Loading Spinner -->
		<div id="loadingSpinner" class="hidden text-center py-12">
			<div class="inline-flex items-center space-x-3">
				<div class="spinner"></div>
				<span class="text-gray-600 font-medium">Loading articles...</span>
			</div>
		</div>

		<!-- Blog Posts Grid with Advanced Animations -->
		<div id="blogGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation">
			@include('frontend.pages.blogs.partials.blog-grid', ['blogPosts' => $blogPosts])
		</div>

		<!-- Enhanced Pagination -->
		<x-pagination :paginator="$blogPosts" container-class="mt-12" :show-info="true" :max-pages="7"
			:show-first-last="false" />
	</div>
</section>

@endsection

@include('frontend.pages.blogs.scripts.index-js')