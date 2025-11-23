@forelse($jobs as $job)
<div class="job-card card overflow-hidden" data-job-type="{{ $job->type ?? 'full-time' }}"
	data-experience="{{ $job->experience_level ?? 'mid' }}" data-name="{{ $job->title }}">
	<div class="p-4">
		<div class="flex items-start justify-between mb-3">
			<div>
				<h3 class="font-semibold text-lg mb-1 line-clamp-1 px-1">

					<a href="{{ route('jobs.show', $job->id) }}">{{ $job->title }}</a>
				</h3>
				<p class="text-gray-600 text-sm line-clamp-1">
					{{ __('clinic') }} : {{ $job->clinic->name }}
				</p>
			</div>

		</div>

		<div class="space-y-2 mb-4">

			<span class="badge badge-success">
				{{ ucfirst($job->type ?? 'Full Time') }}
			</span>
			<div class="flex items-center text-sm text-gray-500">
				<i class="fas fa-map-marker-alt mx-2"></i>
				<span>{{ $job->location ?? '' }}</span>
			</div>
			<div class="flex items-center text-sm text-gray-500">
				<i class="fas fa-dollar-sign mx-2"></i>
				<span>{{ $job->salary ? __('EGP') . ' ' . number_format($job->salary, 2) : __('Not specified') }}</span>
			</div>
			<div class="flex items-center text-sm text-gray-500">
				<i class="fas fa-clock mx-2"></i>
				<span>{{ __('posted') }} {{ $job->created_at->diffForHumans() }}</span>
			</div>
		</div>



		<div class="flex justify-between items-center">
			<a href="{{ route('jobs.application', $job->id) }}" class="btn-primary rounded-lg">
				{{ __('apply now') }}
			</a>
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
		<h3 class="text-lg font-semibold mb-2">{{ __('no jobs found') }}</h3>
		<p>{{ __('try adjusting your search criteria or filters') }}</p>
	</div>
</div>
@endforelse
