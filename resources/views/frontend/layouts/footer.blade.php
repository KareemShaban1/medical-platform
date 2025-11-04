<!-- Footer -->
<footer class="footer bg-primary-gradient">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
			<div>
				<h3 class="text-lg font-semibold mb-4">Medical Platform</h3>
				<p class="text-white-400 mb-4">Your trusted healthcare
					partner
					providing quality medical services and solutions.
				</p>
				<div class="flex space-x-4">
					<a href="#" class="text-gray-400 hover:text-white transition"><i
							class="fab fa-facebook"></i></a>
					<a href="#" class="text-gray-400 hover:text-white transition"><i
							class="fab fa-x"></i></a>
					<a href="#" class="text-gray-400 hover:text-white transition"><i
							class="fab fa-linkedin"></i></a>
					<a href="#" class="text-gray-400 hover:text-white transition"><i
							class="fab fa-instagram"></i></a>
				</div>
			</div>
			<div>
				<h3 class="text-lg font-semibold mb-4">{{ __('quick links') }}</h3>
				<ul class="space-y-2">
					<li><a href="#"
							class="text-gray-400 hover:text-white transition">{{ __('about us') }}</a>
					</li>
					<li><a href="#"
							class="text-gray-400 hover:text-white transition">{{ __('services') }}</a>
					</li>
					<li><a href="{{ route('clinics') }}"
							class="text-gray-400 hover:text-white transition">{{ __('clinics') }}</a>
					</li>
					<li><a href="{{ route('suppliers') }}"
							class="text-gray-400 hover:text-white transition">{{ __('suppliers') }}</a>
					</li>
				</ul>
			</div>
			<div>
				<h3 class="text-lg font-semibold mb-4">{{ __('services') }}</h3>
				<ul class="space-y-2">
					<li><a href="{{ route('products') }}"
							class="text-white-400 hover:text-white transition">{{ __('about item 1') }}</a>
					</li>
					<li><a href="{{ route('jobs') }}"
							class="text-white-400 hover:text-white transition">{{ __('about item 2') }}</a>
					</li>
					<li><a href="#" class="text-white-400 hover:text-white transition">Rental
							Spaces</a></li>
					<li><a href="{{ route('courses') }}"
							class="text-white-400 hover:text-white transition">Medical
							Courses</a></li>
				</ul>
			</div>
			<div>
				<h3 class="text-lg font-semibold mb-4">{{ __('contact info') }}</h3>
				<ul class="space-y-2 text-white-400">
					<li><i class="fas fa-phone mr-2"></i> +00201100435309</li>
					<li><i class="fas fa-envelope mr-2"></i>
						info@tebbplus.com</li>
					<li><i class="fas fa-map-marker-alt mr-2"></i> {{ __('address') }}
					</li>
				</ul>
			</div>
		</div>
		<div class="border-t border-white-800 mt-8 pt-8 text-center text-white-400">
			<p>&copy; 2025 TEBBPLUS Medical Platform. All rights reserved.</p>
		</div>
	</div>
</footer>
