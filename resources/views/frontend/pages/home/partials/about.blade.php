<!-- Features -->
<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
	<div class="mb-14 text-center">
		<span class="py-1 px-4 bg-primary-gradient rounded-full text-xs font-medium text-white text-center">
			{{ __('about') }}
		</span>
		<h2 class="text-4xl text-center font-bold text-gray-900 py-5 font-tajawal">
			{{ __('about us') }}
		</h2>
		<!-- <p class="text-lg font-normal text-gray-500 max-w-md md:max-w-2xl mx-auto font-tajawal">
			{{ __('about us description') }}
		</p> -->
	</div>
	<!-- Grid -->
	<div class="lg:grid lg:grid-cols-12 lg:gap-16 lg:items-center" style="direction: ltr;">
		<div class="lg:col-span-7">
			<!-- Grid -->
			<div class="grid grid-cols-12 gap-2 sm:gap-6 items-center lg:-translate-x-10">
				<div class="col-span-4">
					<img class="rounded-xl"
						src="{{ asset('frontend/images/about-1.jpg') }}"
						alt="Features Image">
				</div>
				<!-- End Col -->

				<div class="col-span-3">
					<img class="rounded-xl"
						src="{{ asset('frontend/images/about-2.jpg') }}"
						alt="Features Image">
				</div>
				<!-- End Col -->

				<div class="col-span-5">
					<img class="rounded-xl"
						src="{{ asset('frontend/images/about-3.jpg') }}"
						alt="Features Image">
				</div>
				<!-- End Col -->
			</div>
			<!-- End Grid -->
		</div>
		<!-- End Col -->

		<div class="mt-5 sm:mt-10 lg:mt-0 lg:col-span-5">
			<div class="space-y-6 sm:space-y-8">
				<!-- Title -->
				<div class="space-y-2 md:space-y-4">
					<h2 class="font-bold text-3xl lg:text-4xl text-gray-800 ">
						{{ __('about us description 1') }}
					</h2>
					<p class="text-gray-500">
						{{ __('about us description 2') }}
					</p>
				</div>
				<!-- End Title -->

				<!-- List -->
				<ul class="space-y-2 sm:space-y-4">

					<x-frontend.about-item item="{{ __('about item 1') }}" />
					<x-frontend.about-item item="{{ __('about item 2') }}" />
					<x-frontend.about-item item="{{ __('about item 3') }}" />
					<x-frontend.about-item item="{{ __('about item 4') }}" />
					<x-frontend.about-item item="{{ __('about item 5') }}" />
					<x-frontend.about-item item="{{ __('about item 6') }}" />
					<x-frontend.about-item item="{{ __('about item 7') }}" />


				</ul>
				<!-- End List -->
			</div>
		</div>
		<!-- End Col -->
	</div>
	<!-- End Grid -->
</div>
<!-- End Features -->
