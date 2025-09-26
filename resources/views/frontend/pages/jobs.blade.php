@extends('frontend.layouts.app')

@section('content')
<!-- Page Header -->
<div class="bg-white shadow-sm">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<h1 class="text-3xl font-bold text-gray-900">Medical Jobs</h1>
		<p class="text-gray-600 mt-2">Find your next career opportunity in healthcare</p>
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
						<input type="text" id="search" placeholder="Search jobs..."
							class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<i
							class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
					</div>
				</div>

				<!-- Job Type Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Job
						Type</label>
					<select id="jobType"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="">All Job Types</option>
						<option value="full-time">Full Time</option>
						<option value="part-time">Part Time</option>
						<option value="contract">Contract</option>
						<option value="temporary">Temporary</option>
						<option value="internship">Internship</option>
					</select>
				</div>

				<!-- Specialization Filter -->
				<div class="mb-6">
					<label
						class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
					<select id="specialization"
						class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="">All Specializations</option>
						<option value="nursing">Nursing</option>
						<option value="physician">Physician</option>
						<option value="technician">Medical Technician
						</option>
						<option value="therapist">Therapist</option>
						<option value="administrative">Administrative
						</option>
						<option value="pharmacy">Pharmacy</option>
					</select>
				</div>

				<!-- Experience Level Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Experience
						Level</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="exp-all" name="experience"
								value="" class="mr-2">
							<label for="exp-all" class="text-sm">Any
								Experience</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="exp-entry"
								name="experience" value="entry"
								class="mr-2">
							<label for="exp-entry" class="text-sm">Entry
								Level</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="exp-mid" name="experience"
								value="mid" class="mr-2">
							<label for="exp-mid" class="text-sm">Mid
								Level</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="exp-senior"
								name="experience" value="senior"
								class="mr-2">
							<label for="exp-senior" class="text-sm">Senior
								Level</label>
						</div>
					</div>
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
						<option value="remote">Remote</option>
						<option value="hybrid">Hybrid</option>
					</select>
				</div>

				<!-- Salary Range Filter -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-2">Salary
						Range</label>
					<div class="space-y-2">
						<div class="flex items-center">
							<input type="radio" id="salary-all" name="salary"
								value="" class="mr-2">
							<label for="salary-all" class="text-sm">Any
								Salary</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="salary-30-50"
								name="salary" value="30-50"
								class="mr-2">
							<label for="salary-30-50" class="text-sm">$30k -
								$50k</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="salary-50-80"
								name="salary" value="50-80"
								class="mr-2">
							<label for="salary-50-80" class="text-sm">$50k -
								$80k</label>
						</div>
						<div class="flex items-center">
							<input type="radio" id="salary-80+" name="salary"
								value="80+" class="mr-2">
							<label for="salary-80+"
								class="text-sm">$80k+</label>
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
						<option value="salary">Highest Salary</option>
						<option value="title">Job Title A-Z</option>
						<option value="company">Company A-Z</option>
					</select>
				</div>

				<!-- Clear Filters -->
				<button id="clearFilters"
					class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition">
					Clear All Filters
				</button>
			</div>
		</div>

		<!-- Jobs Grid -->
		<div class="lg:w-3/4">
			<!-- Results Header -->
			<div class="flex justify-between items-center mb-6">
				<div>
					<span class="text-gray-600">Showing <span id="resultsCount">18</span>
						jobs</span>
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

			<!-- Jobs Grid -->
			<div id="jobsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@for($i = 1; $i <= 18; $i++) <div
					class="job-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
					data-job-type="full-time" data-specialization="nursing"
					data-experience="mid" data-location="downtown"
					data-salary="{{ rand(40, 120) }}"
					data-name="Medical Position {{ $i }}">
					<div class="p-4">
						<div class="flex items-start justify-between mb-3">
							<div>
								<h3 class="font-semibold text-lg mb-1">
									Medical Position
									{{ $i }}
								</h3>
								<p class="text-gray-600 text-sm">
									Healthcare Company
									{{ $i }}
								</p>
							</div>
							<span
								class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Full
								Time</span>
						</div>

						<div class="space-y-2 mb-4">
							<div
								class="flex items-center text-sm text-gray-500">
								<i class="fas fa-briefcase mr-2"></i>
								<span>Healthcare</span>
							</div>
							<div
								class="flex items-center text-sm text-gray-500">
								<i
									class="fas fa-map-marker-alt mr-2"></i>
								<span>Location {{ $i }}</span>
							</div>
							<div
								class="flex items-center text-sm text-gray-500">
								<i class="fas fa-dollar-sign mr-2"></i>
								<span>${{ rand(40, 120) }}k/year</span>
							</div>
							<div
								class="flex items-center text-sm text-gray-500">
								<i class="fas fa-clock mr-2"></i>
								<span>Posted {{ rand(1, 7) }}
									days ago</span>
							</div>
						</div>

						<p class="text-gray-600 text-sm mb-4">We are
							looking for a qualified medical
							professional to join our team...</p>

						<div class="flex justify-between items-center">
							<button
								class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
								Apply Now
							</button>
							<button class="text-gray-500 hover:text-gray-700">
								<i class="fas fa-heart"></i>
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
	const jobTypeSelect = document.getElementById('jobType');
	const specializationSelect = document.getElementById('specialization');
	const experienceRadios = document.querySelectorAll('input[name="experience"]');
	const locationSelect = document.getElementById('location');
	const salaryRadios = document.querySelectorAll('input[name="salary"]');
	const sortSelect = document.getElementById('sort');
	const clearFiltersBtn = document.getElementById('clearFilters');
	const jobsGrid = document.getElementById('jobsGrid');
	const resultsCount = document.getElementById('resultsCount');
	const gridViewBtn = document.getElementById('gridView');
	const listViewBtn = document.getElementById('listView');

	let jobs = Array.from(document.querySelectorAll('.job-card'));

	function filterJobs() {
		const searchTerm = searchInput.value.toLowerCase();
		const selectedJobType = jobTypeSelect.value;
		const selectedSpecialization = specializationSelect.value;
		const selectedExperience = document.querySelector(
			'input[name="experience"]:checked')?.value;
		const selectedLocation = locationSelect.value;
		const selectedSalary = document.querySelector('input[name="salary"]:checked')
			?.value;
		const sortBy = sortSelect.value;

		let filteredJobs = jobs.filter(job => {
			const name = job.dataset.name.toLowerCase();
			const jobType = job.dataset.jobType;
			const specialization = job.dataset
				.specialization;
			const experience = job.dataset.experience;
			const location = job.dataset.location;
			const salary = parseInt(job.dataset.salary);

			// Search filter
			if (searchTerm && !name.includes(searchTerm))
				return false;

			// Job type filter
			if (selectedJobType && jobType !==
				selectedJobType) return false;

			// Specialization filter
			if (selectedSpecialization && specialization !==
				selectedSpecialization) return false;

			// Experience filter
			if (selectedExperience && experience !==
				selectedExperience) return false;

			// Location filter
			if (selectedLocation && location !==
				selectedLocation) return false;

			// Salary filter
			if (selectedSalary) {
				const [min, max] = selectedSalary
					.split('-').map(s => s ===
						'+' ? Infinity :
						parseInt(s));
				if (salary < min || (max !==
						Infinity &&
						salary > max))
					return false;
			}

			return true;
		});

		// Sort jobs
		filteredJobs.sort((a, b) => {
			switch (sortBy) {
				case 'newest':
					return Math.random() -
						0.5; // Simulate newest sorting
				case 'salary':
					return parseInt(b.dataset
							.salary
						) -
						parseInt(a.dataset
							.salary);
				case 'title':
					return a.dataset.name
						.localeCompare(b
							.dataset
							.name);
				case 'company':
					return Math.random() -
						0.5; // Simulate company sorting
				default:
					return 0;
			}
		});

		// Update results
		jobsGrid.innerHTML = '';
		filteredJobs.forEach(job => jobsGrid.appendChild(job));
		resultsCount.textContent = filteredJobs.length;
	}

	// Event listeners
	searchInput.addEventListener('input', filterJobs);
	jobTypeSelect.addEventListener('change', filterJobs);
	specializationSelect.addEventListener('change', filterJobs);
	experienceRadios.forEach(radio => radio.addEventListener('change', filterJobs));
	locationSelect.addEventListener('change', filterJobs);
	salaryRadios.forEach(radio => radio.addEventListener('change', filterJobs));
	sortSelect.addEventListener('change', filterJobs);

	clearFiltersBtn.addEventListener('click', function() {
		searchInput.value = '';
		jobTypeSelect.value = '';
		specializationSelect.value = '';
		experienceRadios.forEach(radio => radio.checked = false);
		locationSelect.value = '';
		salaryRadios.forEach(radio => radio.checked = false);
		sortSelect.value = 'newest';
		filterJobs();
	});

	// View toggle
	gridViewBtn.addEventListener('click', function() {
		jobsGrid.className =
			'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
		gridViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		listViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});

	listViewBtn.addEventListener('click', function() {
		jobsGrid.className = 'grid grid-cols-1 gap-6';
		listViewBtn.className =
			'p-2 bg-blue-600 text-white rounded';
		gridViewBtn.className =
			'p-2 bg-gray-200 text-gray-600 rounded';
	});
});
</script>

@endpush