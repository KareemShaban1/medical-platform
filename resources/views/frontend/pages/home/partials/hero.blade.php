<section class="w-full bg-white py-16">
	<div class="container mx-auto flex flex-col md:flex-row items-center gap-10 px-6">
		<!-- Left: Text Content -->
		<div class="md:w-1/2 space-y-6 text-position">
			<h2 class="text-4xl md:text-4xl font-bold text-gray-900 leading-tight">
				{{ __('banner heading') }}
			</h2>
			<p class="text-gray-600 text-lg leading-relaxed">
				{{ __('banner sub heading 1') }}
				<br>
				{{ __('banner sub heading 2') }}
			</p>


			<div class="flex flex-col sm:flex-row justify-center md:justify-start gap-4">
				<a href="#register-section"
					class="btn-primary text-white px-6 py-3 rounded-full font-semibold shadow-md hover:bg-green-700 transition">
					{{ __('register now') }}

				</a>

				<a href="#"
					class="flex items-center justify-center gap-2 border border-[var(--primary-color)] text-[var(--primary-color)] px-6 py-3 rounded-full font-semibold hover:bg-indigo-50 transition">
					{{ __('watch a demo') }} <i class="fas fa-arrow-left"></i>
				</a>
			</div>

			<div class="pt-6">
				<p class="text-lg font-xl text-gray-500 mb-3">
					{{ __('contact us on social media') }}</p>
				<div class="flex space-x-4">

					<a href="#"
						class="text-[var(--primary-color)] hover:text-black transition text-2xl"><i
							class="fab fa-facebook"></i></a>
					<a href="#"
						class="text-[var(--primary-color)] hover:text-black transition text-2xl"><i
							class="fab fa-x"></i></a>
					<a href="#"
						class="text-[var(--primary-color)] hover:text-black transition text-2xl"><i
							class="fab fa-linkedin"></i></a>
					<a href="#"
						class="text-[var(--primary-color)] hover:text-black transition text-2xl"><i
							class="fab fa-instagram"></i></a>
				</div>
			</div>
		</div>

		<!-- Right: Carousel -->
		<div class="md:w-1/2">
			<div id="default-carousel" class="relative w-full rounded-xl overflow-hidden shadow-lg"
				data-carousel="slide">
				<!-- Carousel wrapper -->
				<div class="relative h-64 md:h-96 overflow-hidden rounded-xl">
					<!-- Item 1 -->
					<div class="duration-700 ease-in-out" data-carousel-item>
						<img src="{{ asset('frontend/images/image-1.jpg') }}"
							class="absolute block w-full h-full object-cover"
							alt="">
					</div>
					<!-- Item 2 -->
					<div class="hidden duration-700 ease-in-out" data-carousel-item>
						<img src="{{ asset('frontend/images/image-2.jpg') }}"
							class="absolute block w-full h-full object-cover"
							alt="">
					</div>
					<!-- Item 3 -->
					<div class="hidden duration-700 ease-in-out" data-carousel-item>
						<img src="{{ asset('frontend/images/image-3.jpg') }}"
							class="absolute block w-full h-full object-cover"
							alt="">
					</div>

					<!-- Item 4 -->
					<div class="hidden duration-700 ease-in-out" data-carousel-item>
						<img src="{{ asset('frontend/images/image-4.jpg') }}"
							class="absolute block w-full h-full object-cover"
							alt="">
					</div>

				</div>

				<!-- Controls -->
				<button type="button" style="z-index: 1000;"
					class="absolute top-0 left-0 z-1000 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
					data-carousel-prev>
					<span
						class="inline-flex items-center justify-center w-8 h-8 bg-white/50 rounded-full group-hover:bg-white shadow-md">
						<svg aria-hidden="true" class="w-5 h-5 text-gray-700"
							fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="M15 19l-7-7 7-7" />
						</svg>
					</span>
				</button>
				<button type="button" style="z-index: 1000;"
					class="absolute top-0 right-0 z-1000 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
					data-carousel-next>
					<span
						class="inline-flex items-center justify-center w-8 h-8 bg-white/50 rounded-full group-hover:bg-white shadow-md">
						<svg aria-hidden="true" class="w-5 h-5 text-gray-700"
							fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round"
								stroke-linejoin="round" stroke-width="2"
								d="M9 5l7 7-7 7" />
						</svg>
					</span>
				</button>
			</div>
		</div>
	</div>
</section>
