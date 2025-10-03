@forelse($courses as $course)
<div class="course-card card overflow-hidden"
	data-level="{{ $course->level ?? 'intermediate' }}" data-name="{{ $course->title }}">
	<div class="h-48 bg-gray-200 flex items-center justify-center">
		@if($course->main_image)
		<img src="{{ $course->main_image }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
		@else
		<i class="fas fa-graduation-cap text-4xl text-gray-400"></i>
		@endif
	</div>
	<div class="p-4">
		<div class="flex items-center justify-between mb-2">
			<span class="badge badge-primary">{{ ucfirst($course->level ?? 'Intermediate') }}</span>
			<!-- <span class="text-sm text-gray-500">{{ $course->duration ?? rand(2, 12) }} weeks</span> -->
		</div>
		<h3 class="font-semibold text-lg mb-2 truncate">{{ $course->title }}</h3>
		<p class="text-gray-600 text-sm mb-3 line-clamp-2">
			{{ Str::limit($course->description, 100) }}
		</p>

		<!-- <div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-clock mr-2"></i>
			<span>{{ $course->duration ?? rand(2, 12) }} weeks duration</span>
		</div> -->
		<div class="flex justify-between items-center">
			<button class="btn-primary">
				Enroll Now
			</button>
		</div>
	</div>
</div>
@empty
<div class="col-span-full text-center py-12">
	<div class="text-gray-500">
		<i class="fas fa-search text-4xl mb-4"></i>
		<h3 class="text-lg font-semibold mb-2">No courses found</h3>
		<p>Try adjusting your search criteria or filters.</p>
	</div>
</div>
@endforelse
