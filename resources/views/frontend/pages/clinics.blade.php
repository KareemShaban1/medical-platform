@extends('frontend.layouts.app')

@section('content')
<!-- Page Header -->
<div class="bg-gradient-primary">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<h1 class="text-3xl font-bold text-white">Medical Clinics</h1>
		<p class="text-white/90 mt-2">Find the best medical clinics near you</p>
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
						<input type="text" id="search" placeholder="Search clinics..."
							class="form-input w-full pl-10 pr-4 py-2">
						<i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
					</div>
				</div>

				<!-- Specialization Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
					<select id="specialization" class="form-input w-full px-3 py-2">
						<option value="">All Specializations</option>
						@foreach($specializations as $key => $value)
						<option value="{{ $key }}" {{ request('specialization') == $key ? 'selected' : '' }}>
							{{ $value }}
						</option>
						@endforeach
					</select>
				</div>

				<!-- Location Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
					<select id="location" class="form-input w-full px-3 py-2">
						<option value="">All Locations</option>
						@foreach($locations as $key => $value)
						<option value="{{ $key }}" {{ request('location') == $key ? 'selected' : '' }}>
							{{ $value }}
						</option>
						@endforeach
					</select>
				</div>

				<!-- Rating Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rating</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="rating-all" name="rating" value="" class="mr-2" {{ !request('rating') ? 'checked' : '' }}>
							<label for="rating-all" class="text-sm">Any Rating</label>
						</div>
						@foreach($ratings as $key => $value)
						<div class="flex items-center">
							<input type="radio" id="rating-{{ $key }}" name="rating" value="{{ $key }}" class="mr-2" {{ request('rating') == $key ? 'checked' : '' }}>
							<label for="rating-{{ $key }}" class="text-sm">{{ $value }}</label>
						</div>
						@endforeach
					</div>
				</div>

				<!-- Status Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="status-all" name="status" value="" class="mr-2" {{ !request('status') ? 'checked' : '' }}>
							<label for="status-all" class="text-sm">All Status</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="status-open" name="status" value="open" class="mr-2" {{ request('status') == 'open' ? 'checked' : '' }}>
							<label for="status-open" class="text-sm">Open</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="status-closed" name="status" value="closed" class="mr-2" {{ request('status') == 'closed' ? 'checked' : '' }}>
							<label for="status-closed" class="text-sm">Closed</label>
						</div>
					</div>
				</div>

				<!-- Sort Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
					<select id="sort" class="form-input w-full px-3 py-2">
						<option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
						<option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
						<option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
						<option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
						<option value="location" {{ request('sort') == 'location' ? 'selected' : '' }}>Location A-Z</option>
					</select>
				</div>

				<!-- Clear Filters -->
				<button id="clearFilters" class="btn-secondary w-full">
					Clear All Filters
				</button>
			</div>
		</div>

		<!-- Clinics Grid -->
		<div class="lg:w-3/4">
			<!-- Results Header -->
			<div class="flex justify-between items-center mb-6">
				<div>
					<span class="text-gray-600">Showing <span id="resultsCount">{{ $clinics->total() }}</span> clinics</span>
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
				<p class="text-gray-500 mt-2">Loading clinics...</p>
			</div>

			<!-- Clinics Grid -->
			<div id="clinicsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@include('frontend.partials.clinics-grid', ['clinics' => $clinics])
			</div>

			<!-- Pagination -->
			<div id="paginationContainer" class="mt-8">
				{{ $clinics->links() }}
			</div>
		</div>
	</div>
</div>
@endsection

@include('frontend.js-scripts.clinics')
