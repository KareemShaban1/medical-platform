<section class="py-16 bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<x-frontend.section-header title="Our Clinics" description="Professional medical clinics near you"
			button-text="View All Clinics" button-url="{{ route('clinics') }}"
			button-icon="fas fa-hospital" button-color="bg-blue-600 hover:bg-blue-700" />
		<div class="swiper clinicsSwiper">
			<div class="swiper-wrapper">
				@foreach($clinics as $clinic)
				<div class="swiper-slide">
					<div
						class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
						<div
							class="h-48 bg-gray-200 flex items-center justify-center">
							<i
								class="fas fa-hospital text-4xl text-gray-400"></i>
						</div>
						<div class="p-4">
							<h3 class="font-semibold text-lg mb-2">
								{{$clinic->name}}
							</h3>
							<p class="text-gray-600 text-sm mb-2">
								{{$clinic->phone ?? ''}}
							</p>
							<div
								class="flex items-center text-sm text-gray-500 mb-3">
								<i
									class="fas fa-map-marker-alt mr-2"></i>
								<span>{{$clinic->address ?? ''}}</span>
							</div>
							<a href="{{route('clinics.show',$clinic->id)}}"
								class="p-2 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">View
								Details</a>
						</div>
					</div>
				</div>
				@endforeach
			</div>
			<!-- <div class="swiper-pagination"></div> -->
			<div class="clinics-swiper-button-next"></div>
			<div class="clinics-swiper-button-prev"></div>
		</div>

	</div>
</section>