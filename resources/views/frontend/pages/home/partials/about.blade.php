<!-- Features -->
<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
	<div class="mb-14 text-center">
		<span class="py-1 px-4 bg-primary-gradient rounded-full text-xs font-medium text-white text-center">
			{{ __('about') }}
		</span>
		<h2 class="text-4xl text-center font-bold text-gray-900 py-5 font-tajawal">
			{{ __('about us') }}
		</h2>
		<p class="text-lg font-normal text-gray-500 max-w-md md:max-w-2xl mx-auto font-tajawal">
			{{ __('provides about us that are essential for the platform.') }}
		</p>
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
						{{ __('welcome to our medical platform') }}
					</h2>
					<p class="text-gray-500">
						{{ __('explore our platform and find the best medical services for you.') }}
					</p>
				</div>
				<!-- End Title -->

				<!-- List -->
				<ul class="space-y-2 sm:space-y-4">
					<li class="flex gap-x-3">
						<span
							class="mt-0.5 size-5 flex justify-center items-center rounded-full bg-blue-50 text-blue-600">
							<svg class="shrink-0 size-3.5"
								xmlns="http://www.w3.org/2000/svg"
								width="24" height="24"
								viewBox="0 0 24 24" fill="none"
								stroke="currentColor" stroke-width="2"
								stroke-linecap="round"
								stroke-linejoin="round">
								<polyline points="20 6 9 17 4 12" />
							</svg>
						</span>
						<div class="grow">
							<span class="text-sm sm:text-base text-gray-500">
								{{ __('less routine') }} <span
									class="font-bold">{{ __('more creativity') }}</span>
							</span>
						</div>
					</li>

					<li class="flex gap-x-3">
						<span
							class="mt-0.5 size-5 flex justify-center items-center rounded-full bg-blue-50 text-blue-600">
							<svg class="shrink-0 size-3.5"
								xmlns="http://www.w3.org/2000/svg"
								width="24" height="24"
								viewBox="0 0 24 24" fill="none"
								stroke="currentColor" stroke-width="2"
								stroke-linecap="round"
								stroke-linejoin="round">
								<polyline points="20 6 9 17 4 12" />
							</svg>
						</span>
						<div class="grow">
							<span class="text-sm sm:text-base text-gray-500 ">
								{{ __('hundreds of thousands saved') }}
							</span>
						</div>
					</li>

					<li class="flex gap-x-3">
						<span
							class="mt-0.5 size-5 flex justify-center items-center rounded-full bg-blue-50 text-blue-600">
							<svg class="shrink-0 size-3.5"
								xmlns="http://www.w3.org/2000/svg"
								width="24" height="24"
								viewBox="0 0 24 24" fill="none"
								stroke="currentColor" stroke-width="2"
								stroke-linecap="round"
								stroke-linejoin="round">
								<polyline points="20 6 9 17 4 12" />
							</svg>
						</span>
						<div class="grow">
							<span class="text-sm sm:text-base text-gray-500">
								{{ __('scale budgets') }} <span
									class="font-bold">{{ __('efficiently') }}</span>
							</span>
						</div>
					</li>
				</ul>
				<!-- End List -->
			</div>
		</div>
		<!-- End Col -->
	</div>
	<!-- End Grid -->
</div>
<!-- End Features -->