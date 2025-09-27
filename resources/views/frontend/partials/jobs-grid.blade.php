@forelse($jobs as $job)
<div class="job-card card overflow-hidden"
	data-job-type="{{ $job->job_type ?? 'full-time' }}"
	data-specialization="{{ $job->specialization ?? 'nursing' }}"
	data-experience="{{ $job->experience_level ?? 'mid' }}"
	data-location="{{ $job->location }}"
	data-salary="{{ $job->salary }}"
	data-name="{{ $job->title }}">
	<div class="p-4">
		<div class="flex items-start justify-between mb-3">
			<div>
				<h3 class="font-semibold text-lg mb-1">
					{{ $job->title }}
				</h3>
				<p class="text-gray-600 text-sm">
					{{ $job->clinic->name ?? 'Healthcare Company' }}
				</p>
			</div>
			
		</div>

		<div class="space-y-2 mb-4">
			<!-- <div class="flex items-center text-sm text-gray-500">
				<i class="fas fa-briefcase mr-2"></i>
				<span>{{ ucfirst($job->specialization ?? 'Healthcare') }}</span>
			</div> -->
			<span class="badge badge-success">
				{{ ucfirst($job->job_type ?? 'Full Time') }}
			</span>
			<div class="flex items-center text-sm text-gray-500">
				<i class="fas fa-map-marker-alt mr-2"></i>
				<span>{{ $job->location }}</span>
			</div>
			<div class="flex items-center text-sm text-gray-500">
				<i class="fas fa-dollar-sign mr-2"></i>
				<span>${{ number_format($job->salary / 1000) }}k/year</span>
			</div>
			<div class="flex items-center text-sm text-gray-500">
				<i class="fas fa-clock mr-2"></i>
				<span>Posted {{ $job->created_at->diffForHumans() }}</span>
			</div>
		</div>

		

		<div class="flex justify-between items-center">
			<button class="btn-primary">
				Apply Now
			</button>
			<!-- <button class="text-gray-500 hover:text-gray-700">
				<i class="fas fa-heart"></i>
			</button> -->
		</div>
	</div>
</div>
@empty
<div class="col-span-full text-center py-12">
	<div class="text-gray-500">
		<i class="fas fa-search text-4xl mb-4"></i>
		<h3 class="text-lg font-semibold mb-2">No jobs found</h3>
		<p>Try adjusting your search criteria or filters.</p>
	</div>
</div>
@endforelse
