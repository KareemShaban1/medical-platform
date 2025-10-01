<section class="py-16 bg-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<x-frontend.section-header title="Medical Courses" description="Enhance your medical knowledge"
			button-text="View All Courses" button-url="{{ route('courses') }}"
			button-icon="fas fa-graduation-cap" button-color="bg-orange-600 hover:bg-orange-700" />


		<div class="swiper coursesSwiper">
			<div class="swiper-wrapper">
				@foreach($courses as $course)
				<div class="swiper-slide">
					<div
						class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
						<div
							class="h-48 bg-gray-200 flex items-center justify-center">
							<i
								class="fas fa-graduation-cap text-4xl text-gray-400"></i>
						</div>
						<div class="p-4">
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<span
									class="badge badge-primary">{{ ucfirst($course->level ?? 'Intermediate') }}</span>

							</div>
							<h3
								class="font-semibold text-md mb-2 line-clamp-2">
								<a
									href="{{ route('courses.show', $course->id) }}">
									{{ $course->title }}
								</a>
							</h3>
							<p
								class="text-gray-600 text-sm mb-3 line-clamp-2">
								{{ $course->description }}
							</p>

							<div class="flex justify-between items-center">
								<button
									class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 transition">Enroll
									Now</button>
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
			<!-- <div class="swiper-pagination"></div> -->
			<div class="courses-swiper-button-next"></div>
			<div class="courses-swiper-button-prev"></div>
		</div>


	</div>
</section>