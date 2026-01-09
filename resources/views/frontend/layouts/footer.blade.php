<!-- Footer -->
<footer class="footer bg-primary-gradient">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
		<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8 items-start">
			<div class="space-y-4">
				<h3 class="text-lg font-semibold mb-4">{{ __('medical platform') }}</h3>
				<p class="text-white-400 mb-4">{{ __('footer tagline') }}</p>
				<div class="flex gap-4 items-center">
					<a href="https://web.facebook.com/tebbplus/" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition" title="Facebook"><i class="fab fa-facebook"></i></a>
					<a href="https://x.com/tebbplus" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition" title="X (Twitter)"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/></svg></a>
					<a href="https://www.linkedin.com/in/tebbplus/" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
					<a href="https://www.instagram.com/tebbplus/" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition" title="Instagram"><i class="fab fa-instagram"></i></a>
				</div>
			</div>
			<div class="space-y-2">
				<h3 class="text-lg font-semibold mb-4">{{ __('quick links') }}</h3>
				<ul class="space-y-2">
					<li><a href="{{ route('about') }}"
							class="text-gray-400 hover:text-white transition">{{ __('legal.about.title') }}</a>
					</li>
					<li><a href="{{ route('clinics') }}"
							class="text-gray-400 hover:text-white transition">{{ __('clinics') }}</a>
					</li>
					<li><a href="{{ route('suppliers') }}"
							class="text-gray-400 hover:text-white transition">{{ __('suppliers') }}</a>
					</li>
				</ul>
			</div>
			<div class="space-y-2">
				<h3 class="text-lg font-semibold mb-4">{{ __('services') }}</h3>
				<ul class="space-y-2">
					<li><a href="{{ route('clinics') }}"
							class="text-white-400 hover:text-white transition">{{ __('about item 1') }}</a>
					</li>
					<li><a href="{{ route('jobs') }}"
							class="text-white-400 hover:text-white transition">{{ __('about item 2') }}</a>
					</li>
					<li><a href="{{ route('rental-spaces') }}"
							class="text-white-400 hover:text-white transition">{{ __('rental spaces') }}</a>
					</li>
					<li><a href="{{ route('courses') }}"
							class="text-white-400 hover:text-white transition">{{ __('medical courses') }}</a>
					</li>
				</ul>
			</div>
			<div class="space-y-2">
				<h3 class="text-lg font-semibold mb-4">{{ __('shipping & returns') }}</h3>
				<ul class="space-y-2">
					<li><a href="{{ route('terms') }}"
							class="text-gray-400 hover:text-white transition">{{ __('legal.terms.title') }}</a>
					</li>
					<li><a href="{{ route('privacy') }}"
							class="text-gray-400 hover:text-white transition">{{ __('legal.privacy.title') }}</a>
					</li>
					<li><a href="{{ route('return-policy') }}"
							class="text-gray-400 hover:text-white transition">{{ __('legal.refund.title') }}</a>
					</li>
					<li><a href="{{ route('shipping-policy') }}"
							class="text-gray-400 hover:text-white transition">{{ __('legal.shipping.title') }}</a>
					</li>
				</ul>
			</div>
			<div class="space-y-2">
				<h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
					<i class="fas fa-headset text-white"></i>
					<span>{{ __('contact info') }}</span>
				</h3>
				<ul class="space-y-2 text-white-400">
					<li><i class="fas fa-phone mr-2"></i> +00201035067680</li>
					<li><i class="fas fa-envelope mr-2"></i>
						info@tebbplus.com</li>
					<li><i class="fas fa-map-marker-alt mr-2"></i> {{ __('address') }}
					</li>
				</ul>
			</div>
		</div>
		<div class="border-t border-white-800 mt-8 pt-8 text-center text-white-400">
			<p>{{ __('copyright', ['year' => date('Y')]) }}</p>
		</div>
	</div>
</footer>
