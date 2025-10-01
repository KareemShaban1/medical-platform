<section class="py-16 bg-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<x-frontend.section-header title="Medical Jobs" description="Find your next career opportunity"
			button-text="View All Jobs" button-url="{{ route('jobs') }}"
			button-icon="fas fa-briefcase" button-color="bg-purple-600 hover:bg-purple-700" />
		<div class="swiper jobsSwiper">
			<div class="swiper-wrapper">
				@foreach($jobs as $job)
				<div class="swiper-slide">
					<div
						class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
						<div class="p-4">
							<!-- line-clamp-2 -->
							<h3 class="font-semibold text-lg mb-2 truncate">

								<a
									href="{{ route('jobs.show', $job->id) }}">
									{{ $job->title }}
								</a>
							</h3>
							<p class="text-gray-600 text-sm mb-2">
								{{ ucfirst($job->type) }} position</p>
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i class="fas fa-briefcase mr-2"></i>
								<span>{{ $job->clinic->name }}</span>
							</div>
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i
									class="fas fa-map-marker-alt mr-2"></i>
								<span> {{ $job->clinic->address ?? '' }}
								</span>
							</div>
							<a href="{{ route('jobs.application', $job->id) }}"
								class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition">Apply
								Now</a>
						</div>
					</div>
				</div>
				@endforeach
			</div>
			<!-- <div class="swiper-pagination"></div> -->
			<div class="jobs-swiper-button-next"></div>
			<div class="jobs-swiper-button-prev"></div>
		</div>

	</div>
</section>