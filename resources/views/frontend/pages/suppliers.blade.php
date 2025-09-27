@extends('frontend.layouts.app')

@section('content')
<!-- Page Header -->
<div class="bg-gradient-primary">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<h1 class="text-3xl font-bold text-white">Medical Suppliers</h1>
		<p class="text-white/90 mt-2">Find trusted suppliers for medical equipment and supplies</p>
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
						<input type="text" id="search" placeholder="Search suppliers..."
							class="form-input w-full pl-10 pr-4 py-2">
						<i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
					</div>
				</div>

				

				<!-- Rating Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rating</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="rating-all" name="rating" value="" class="mr-2" {{ !request('rating') ? 'checked' : '' }}>
							<label for="rating-all" class="text-sm">Any Rating</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="rating-5" name="rating" value="5" class="mr-2" {{ request('rating') == '5' ? 'checked' : '' }}>
							<label for="rating-5" class="text-sm">5 Stars</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="rating-4" name="rating" value="4" class="mr-2" {{ request('rating') == '4' ? 'checked' : '' }}>
							<label for="rating-4" class="text-sm">4+ Stars</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="rating-3" name="rating" value="3" class="mr-2" {{ request('rating') == '3' ? 'checked' : '' }}>
							<label for="rating-3" class="text-sm">3+ Stars</label>
						</div>
					</div>
				</div>

			

			

				<!-- Sort Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
					<select id="sort" class="form-input w-full px-3 py-2">
						<option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
						<option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
						<option value="experience" {{ request('sort') == 'experience' ? 'selected' : '' }}>Most Experienced</option>
						<option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
						<option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
					</select>
				</div>

				<!-- Clear Filters -->
				<button id="clearFilters" class="btn-secondary w-full">
					Clear All Filters
				</button>
			</div>
		</div>

		<!-- Suppliers Grid -->
		<div class="lg:w-3/4">
			<!-- Results Header -->
			<div class="flex justify-between items-center mb-6">
				<div>
					<span class="text-gray-600">Showing <span id="resultsCount">{{ $suppliers->total() }}</span> suppliers</span>
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
				<p class="text-gray-500 mt-2">Loading suppliers...</p>
			</div>

			<!-- Suppliers Grid -->
			<div id="suppliersGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@include('frontend.partials.suppliers-grid', ['suppliers' => $suppliers])
			</div>

			<!-- Pagination -->
			<div id="paginationContainer" class="mt-8">
				{{ $suppliers->links() }}
			</div>
		</div>
	</div>
</div>
@endsection

@include('frontend.js-scripts.suppliers')
