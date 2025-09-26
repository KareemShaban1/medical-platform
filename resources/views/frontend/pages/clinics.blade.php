@extends('frontend.layouts.app')

@section('content')
<!-- Page Header -->
<div class="bg-white shadow-sm">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<h1 class="text-3xl font-bold text-gray-900">Medical Clinics</h1>
		<p class="text-gray-600 mt-2">Find the best medical clinics near you</p>
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
							placeholder="Search clinics..."
							class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<i
							class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
					</div>
				</div>

				<!-- Specialization Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
					<select id="specialization"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="">All Specializations</option>
						<option value="general">General Practice</option>
						<option value="cardiology">Cardiology</option>
						<option value="dermatology">Dermatology</option>
						<option value="orthopedics">Orthopedics</option>
						<option value="pediatrics">Pediatrics</option>
						<option value="neurology">Neurology</option>
						<option value="oncology">Oncology</option>
					</select>
				</div>

				<!-- Location Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Location</label>
					<select id="location"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="">All Locations</option>
						<option value="downtown">Downtown</option>
						<option value="suburbs">Suburbs</option>
						<option value="north">North Side</option>
						<option value="south">South Side</option>
						<option value="east">East Side</option>
						<option value="west">West Side</option>
					</select>
				</div>

				<!-- Rating Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Minimum
						Rating</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="rating-all" name="rating"
								value="" class="mr-2">
							<label for="rating-all" class="text-sm">Any
								Rating</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="rating-4" name="rating"
								value="4" class="mr-2">
							<label for="rating-4" class="text-sm">4+
								Stars</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="rating-3" name="rating"
								value="3" class="mr-2">
							<label for="rating-3" class="text-sm">3+
								Stars</label>
						</div>
					</div>
				</div>

				<!-- Insurance Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Insurance
						Accepted</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="checkbox" id="insurance-yes"
								class="mr-2">
							<label for="insurance-yes" class="text-sm">Accepts
								Insurance</label>
						</div>
					</div>
				</div>

				<!-- Sort Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Sort
						By</label>
					<select id="sort"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="name">Name A-Z</option>
						<option value="rating">Highest Rated</option>
						<option value="distance">Nearest First</option>
						<option value="newest">Newest First</option>
					</select>
				</div>

				<!-- Clear Filters -->
				<button id="clearFilters"
					class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition">
					Clear All Filters
				</button>
			</div>
		</div>

		<!-- Clinics Grid -->
		<div class="lg:w-3/4">
			<!-- Results Header -->
			<div class="flex justify-between items-center mb-6">
				<div>
					<span class="text-gray-600">Showing <span id="resultsCount">16</span>
						clinics</span>
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

			<!-- Clinics Grid -->
			<div id="clinicsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@for($i = 1; $i <= 16; $i++) <div
					class="clinic-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
					data-specialization="general" data-location="downtown"
					data-rating="{{ rand(3, 5) }}" data-name="Clinic {{ $i }}">
					<div class="h-48 bg-gray-200 flex items-center justify-center">
						<i class="fas fa-hospital text-4xl text-gray-400"></i>
					</div>
					<div class="p-4">
						<h3 class="font-semibold text-lg mb-2">Clinic
							{{ $i }}
						</h3>
						<p class="text-gray-600 text-sm mb-2">Specialized
							medical services</p>
						<div class="flex items-center text-sm text-gray-500 mb-3">
							<i class="fas fa-map-marker-alt mr-2"></i>
							<span>Location {{ $i }}</span>
						</div>
						<div class="flex items-center mb-3">
							<div class="flex text-yellow-400">
								@for($j = 1; $j <= 5; $j++) <i
									class="fas fa-star">
									</i>
									@endfor
							</div>
							<span class="text-sm text-gray-500 ml-2">({{ rand(20, 150) }}
								reviews)</span>
						</div>
						<div class="flex items-center text-sm text-gray-500 mb-3">
							<i class="fas fa-clock mr-2"></i>
							<span>Open 24/7</span>
						</div>
						<button
							class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">View
							Details</button>
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
	const specializationSelect = document.getElementById('specialization');
	const locationSelect = document.getElementById('location');
	const ratingRadios = document.querySelectorAll('input[name="rating"]');
	const insuranceCheckbox = document.getElementById('insurance-yes');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const clinicsGrid = document.getElementById('clinicsGrid');
	const resultsCount = document.getElementById('resultsCount');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');

	let clinics = Array.from(document.querySelectorAll('.clinic-card'));

	function filterClinics() {
		const searchTerm = searchInput.value.toLowerCase();
		const selectedSpecialization = specializationSelect.value;
		const selectedLocation = locationSelect.value;
		const selectedRating = document.querySelector('input[name="rating"]:checked')
			?.value;
		const insuranceOnly = insuranceCheckbox.checked;
		const sortBy = sortSelect.value;

		let filteredClinics = clinics.filter(clinic => {
			const name = clinic.dataset.name.toLowerCase();
			const specialization = clinic.dataset
				.specialization;
			const location = clinic.dataset.location;
			const rating = parseFloat(clinic.dataset.rating);

			// Search filter
			if (searchTerm && !name.includes(searchTerm))
				return false;

			// Specialization filter
			if (selectedSpecialization && specialization !==
				selectedSpecialization) return false;

			// Location filter
			if (selectedLocation && location !==
				selectedLocation) return false;

			// Rating filter
			if (selectedRating && rating < parseFloat(
					selectedRating))
				return false;

			return true;
		});

		// Sort clinics
		filteredClinics.sort((a, b) => {
			switch (sortBy) {
				case 'name':
					return a.dataset.name
						.localeCompare(b
							.dataset
							.name);
				case 'rating':
					return parseFloat(b.dataset
							.rating
						) -
						parseFloat(a
							.dataset
							.rating);
				case 'distance':
					return Math.random() -
						0.5; // Simulate distance sorting
				default:
					return 0;
			}
		});

		// Update results
		clinicsGrid.innerHTML = '';
		filteredClinics.forEach(clinic => clinicsGrid.appendChild(clinic));
		resultsCount.textContent = filteredClinics.length;
	}

	// Event listeners
	searchInput.addEventListener('input', filterClinics);
	specializationSelect.addEventListener('change', filterClinics);
	locationSelect.addEventListener('change', filterClinics);
	ratingRadios.forEach(radio => radio.addEventListener('change', filterClinics));
	insuranceCheckbox.addEventListener('change', filterClinics);
	sortSelect.addEventListener('change', filterClinics);

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		specializationSelect.value = '';
		locationSelect.value = '';
		ratingRadios.forEach(radio => radio.checked = false);
		insuranceCheckbox.checked = false;
		sortSelect.value = 'name';
		filterClinics();
	});

	// View toggle
	gridViewBtn.addEventListener('click', function() {
		clinicsGrid.className =
			'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
		gridViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		listViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});

	listViewBtn.addEventListener('click', function() {
		clinicsGrid.className = 'grid grid-cols-1 gap-6';
		listViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		gridViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});
});
</script>
@endauth