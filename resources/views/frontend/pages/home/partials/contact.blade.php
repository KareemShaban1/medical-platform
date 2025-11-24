<div class="isolate relative bg-white px-6 py-6 sm:py-6 lg:px-8">
	<!-- <div aria-hidden="true"
		class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
		<div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"
			class="relative left-1/2 -z-10 aspect-1155/678 w-144.5 max-w-none -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-40rem)] sm:w-288.75">
		</div>
	</div> -->
	<div class="mb-14 text-center">
		<span class="py-1 px-4 bg-primary-gradient rounded-full text-xs font-medium text-white text-center">
			{{ __('contact') }}
		</span>
		<h2 class="text-4xl text-center font-bold text-gray-900 py-5 font-tajawal">
			{{ __('contact us') }}
		</h2>
		<p class="text-lg font-normal text-gray-500 max-w-md md:max-w-2xl mx-auto font-tajawal">
			{{ __('provides contact us that are essential for the platform to get in touch with us.') }}
		</p>
	</div>
	<form id="contact-form" action="#" method="POST" class="mx-auto mt-16 max-w-xl sm:mt-20">
		@csrf
		<div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
			<div>
				<label for="first-name"
					class="block text-sm/6 font-semibold text-gray-900">{{ __('first name') }}</label>
				<div class="mt-2.5">
					<input id="first-name" type="text" name="first_name"
						autocomplete="given-name"
						class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600" />
					<span class="text-danger text-sm error-message" id="error-first_name"></span>
				</div>
			</div>
			<div>
				<label for="last-name"
					class="block text-sm/6 font-semibold text-gray-900">{{ __('last name') }}</label>
				<div class="mt-2.5">
					<input id="last-name" type="text" name="last_name"
						autocomplete="family-name"
						class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600" />
					<span class="text-danger text-sm error-message" id="error-last_name"></span>
				</div>
			</div>
			<div class="sm:col-span-2">
				<label for="company"
					class="block text-sm/6 font-semibold text-gray-900">{{ __('company') }}</label>
				<div class="mt-2.5">
					<input id="company" type="text" name="company"
						autocomplete="organization"
						class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600" />
					<span class="text-danger text-sm error-message" id="error-company"></span>
				</div>
			</div>
			<div class="sm:col-span-2">
				<label for="email"
					class="block text-sm/6 font-semibold text-gray-900">{{ __('email') }}</label>
				<div class="mt-2.5">
					<input id="email" type="email" name="email" autocomplete="email"
						class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600" />
					<span class="text-danger text-sm error-message" id="error-email"></span>
				</div>
			</div>
			<div class="sm:col-span-2">
				<label for="phone-number"
					class="block text-sm/6 font-semibold text-gray-900">{{ __('phone number') }}</label>
				<div class="mt-2.5">
					<div
						class="flex rounded-md bg-white outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">

						<input id="phone-number" type="text" name="phone"
							placeholder="123-456-7890"
							class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6" />
					</div>
					<span class="text-danger text-sm error-message" id="error-phone"></span>
				</div>
			</div>
			<div class="sm:col-span-2">
				<label for="message"
					class="block text-sm/6 font-semibold text-gray-900">{{ __('message') }}</label>
				<div class="mt-2.5">
					<textarea id="message" name="message" rows="4"
						class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"></textarea>
					<span class="text-danger text-sm error-message" id="error-message"></span>
				</div>
			</div>
			<div class="sm:col-span-2">
				<div class="flex items-start gap-x-3">
					<div class="flex h-6 items-center">
						<input id="agree-to-policies" type="checkbox"
							name="agree_to_policies"
							value="1"
							class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
					</div>
					<label for="agree-to-policies" class="text-sm leading-6 text-gray-600 cursor-pointer">
						{{ __('by selecting this, you agree to our') }}
						<a href="{{ route('privacy') }}" target="_blank"
							class="font-semibold text-indigo-600 hover:text-indigo-500">
							{{ __('privacy policy') }}</a>.
					</label>
				</div>
				<span class="text-danger text-sm error-message" id="error-agree_to_policies"></span>
			</div>
		</div>
		<div class="mt-10">
			<button type="submit" id="submit-btn"
				class="block w-full rounded-md bg-primary-gradient px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
				{{ __("let's talk") }}</button>
		</div>
	</form>

	<!-- Contact Form JavaScript -->
	<style>
		.border-danger {
			border-color: #dc3545 !important;
			outline-color: #dc3545 !important;
		}
		.error-message {
			display: block;
			margin-top: 0.25rem;
			color: #dc3545;
			font-size: 0.875rem;
		}
		/* Make checkbox cursor pointer */
		#agree-to-policies {
			cursor: pointer;
		}
	</style>
	<script>
		$(document).ready(function() {
			$('#contact-form').on('submit', function(e) {
				e.preventDefault();

				// Clear previous errors
				$('.error-message').text('');
				$('input, textarea').removeClass('border-danger');
				$('.border-danger').removeClass('border-danger');

				// Disable submit button and show loading
				const submitBtn = $('#submit-btn');
				const originalText = submitBtn.html();
				submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("sending") }}...');

				$.ajax({
					url: '{{ route("contact.store") }}',
					method: 'POST',
					data: $(this).serialize(),
					success: function(response) {
						if(response.success) {
							// Show success message
							toastr.success(response.message);

							// Reset form
							$('#contact-form')[0].reset();

							// Reset checkbox toggle visually
							$('#agree-to-policies').prop('checked', false);
						}
					},
					error: function(xhr) {
						if(xhr.status === 422) {
							// Validation errors
							const errors = xhr.responseJSON.errors;
							$.each(errors, function(key, value) {
								// Display error message
								$('#error-' + key).text(value[0]);

								// Add red border to input/textarea
								const input = $('[name="' + key + '"]');
								if(input.length) {
									input.addClass('border-danger');
									// For inputs inside a container (like phone)
									input.closest('div').find('input').addClass('border-danger');
								}
							});
							toastr.error('{{ __("Please fix the errors and try again") }}');
						} else {
							// Server error
							toastr.error(xhr.responseJSON.message || '{{ __("An error occurred. Please try again.") }}');
						}
					},
					complete: function() {
						// Re-enable submit button
						submitBtn.prop('disabled', false).html(originalText);
					}
				});
			});

			// Clear error on input change
			$('#contact-form input, #contact-form textarea, #contact-form select').on('input change', function() {
				const fieldName = $(this).attr('name');
				if(fieldName) {
					$('#error-' + fieldName).text('');
					$(this).removeClass('border-danger');
				}
			});
		});
	</script>
	<span class="absolute top-0 right-0 -z-10">
		<svg width="388" height="250" viewBox="0 0 388 220" fill="none" class="shape"
			xmlns="http://www.w3.org/2000/svg">
			<path opacity="0.05"
				d="M203 -28.5L4.87819e-05 250.5L881.5 250.5L881.5 -28.5002L203 -28.5Z"
				fill="url(#paint0_linear_971_6910)"></path>
			<defs>
				<linearGradient id="paint0_linear_971_6910" x1="60.5" y1="111" x2="287" y2="111"
					gradientUnits="userSpaceOnUse">
					<stop offset="0.520507" stop-color="white"></stop>
					<stop offset="1" stop-color="white" stop-opacity="0"></stop>
				</linearGradient>
			</defs>
		</svg>
	</span>
	<span class="absolute top-0 right-0 -z-10"><svg width="324" height="250" viewBox="0 0 324 220" fill="none"
			xmlns="http://www.w3.org/2000/svg" class="shape">
			<path opacity="0.05"
				d="M203 -28.5L4.87819e-05 250.5L881.5 250.5L881.5 -28.5002L203 -28.5Z"
				fill="url(#paint0_linear_971_6911)"></path>
			<defs>
				<linearGradient id="paint0_linear_971_6911" x1="60.5" y1="111" x2="287" y2="111"
					gradientUnits="userSpaceOnUse">
					<stop offset="0.520507" stop-color="white"></stop>
					<stop offset="1" stop-color="white" stop-opacity="0"></stop>
				</linearGradient>
			</defs>
		</svg></span>
	<span class="absolute top-4 left-4 -z-10">
		<svg width="43" height="56" viewBox="0 0 43 56" fill="none" xmlns="http://www.w3.org/2000/svg"
			class="shape">
			<g opacity="0.5">
				<circle cx="40.9984" cy="1.49626" r="1.49626"
					transform="rotate(90 40.9984 1.49626)" fill="white"></circle>
				<circle cx="27.8304" cy="1.49626" r="1.49626"
					transform="rotate(90 27.8304 1.49626)" fill="white"></circle>
				<circle cx="14.6644" cy="1.49626" r="1.49626"
					transform="rotate(90 14.6644 1.49626)" fill="white"></circle>
				<circle cx="1.49642" cy="1.49626" r="1.49626"
					transform="rotate(90 1.49642 1.49626)" fill="white"></circle>
				<circle cx="40.9984" cy="14.6642" r="1.49626"
					transform="rotate(90 40.9984 14.6642)" fill="white"></circle>
				<circle cx="27.8304" cy="14.6642" r="1.49626"
					transform="rotate(90 27.8304 14.6642)" fill="white"></circle>
				<circle cx="14.6644" cy="14.6642" r="1.49626"
					transform="rotate(90 14.6644 14.6642)" fill="white"></circle>
				<circle cx="1.49642" cy="14.6642" r="1.49626"
					transform="rotate(90 1.49642 14.6642)" fill="white"></circle>
				<circle cx="40.9984" cy="27.8302" r="1.49626"
					transform="rotate(90 40.9984 27.8302)" fill="white"></circle>
				<circle cx="27.8304" cy="27.8302" r="1.49626"
					transform="rotate(90 27.8304 27.8302)" fill="white"></circle>
				<circle cx="14.6644" cy="27.8302" r="1.49626"
					transform="rotate(90 14.6644 27.8302)" fill="white"></circle>
				<circle cx="1.49642" cy="27.8302" r="1.49626"
					transform="rotate(90 1.49642 27.8302)" fill="white"></circle>
				<circle cx="40.9984" cy="40.9982" r="1.49626"
					transform="rotate(90 40.9984 40.9982)" fill="white"></circle>
				<circle cx="27.8304" cy="40.9963" r="1.49626"
					transform="rotate(90 27.8304 40.9963)" fill="white"></circle>
				<circle cx="14.6644" cy="40.9982" r="1.49626"
					transform="rotate(90 14.6644 40.9982)" fill="white"></circle>
				<circle cx="1.49642" cy="40.9963" r="1.49626"
					transform="rotate(90 1.49642 40.9963)" fill="white"></circle>
				<circle cx="40.9984" cy="54.1642" r="1.49626"
					transform="rotate(90 40.9984 54.1642)" fill="white"></circle>
				<circle cx="27.8304" cy="54.1642" r="1.49626"
					transform="rotate(90 27.8304 54.1642)" fill="white"></circle>
				<circle cx="14.6644" cy="54.1642" r="1.49626"
					transform="rotate(90 14.6644 54.1642)" fill="white"></circle>
				<circle cx="1.49642" cy="54.1642" r="1.49626"
					transform="rotate(90 1.49642 54.1642)" fill="white"></circle>
			</g>
		</svg>
	</span>


	<span class="absolute bottom-0 left-0 -z-10 flip-x">
		<svg width="388" height="250" viewBox="0 0 388 220" fill="none" class="shape"
			xmlns="http://www.w3.org/2000/svg">
			<path opacity="0.05"
				d="M203 -28.5L4.87819e-05 250.5L881.5 250.5L881.5 -28.5002L203 -28.5Z"
				fill="url(#paint0_linear_971_6910)"></path>
			<defs>
				<linearGradient id="paint0_linear_971_6910" x1="60.5" y1="111" x2="287" y2="111"
					gradientUnits="userSpaceOnUse">
					<stop offset="0.520507" stop-color="white"></stop>
					<stop offset="1" stop-color="white" stop-opacity="0"></stop>
				</linearGradient>
			</defs>
		</svg>
	</span>
	<span class="absolute bottom-0 left-0 -z-10 flip-x"><svg width="324" height="250" viewBox="0 0 324 220"
			fill="none" xmlns="http://www.w3.org/2000/svg" class="shape">
			<path opacity="0.05"
				d="M203 -28.5L4.87819e-05 250.5L881.5 250.5L881.5 -28.5002L203 -28.5Z"
				fill="url(#paint0_linear_971_6911)"></path>
			<defs>
				<linearGradient id="paint0_linear_971_6911" x1="60.5" y1="111" x2="287" y2="111"
					gradientUnits="userSpaceOnUse">
					<stop offset="0.520507" stop-color="white"></stop>
					<stop offset="1" stop-color="white" stop-opacity="0"></stop>
				</linearGradient>
			</defs>
		</svg>
	</span>
	<span class="absolute bottom-4 right-4 -z-10 flip-x">
		<svg width="43" height="56" viewBox="0 0 43 56" fill="none" xmlns="http://www.w3.org/2000/svg"
			class="shape">
			<g opacity="0.5">
				<circle cx="40.9984" cy="1.49626" r="1.49626"
					transform="rotate(90 40.9984 1.49626)" fill="white"></circle>
				<circle cx="27.8304" cy="1.49626" r="1.49626"
					transform="rotate(90 27.8304 1.49626)" fill="white"></circle>
				<circle cx="14.6644" cy="1.49626" r="1.49626"
					transform="rotate(90 14.6644 1.49626)" fill="white"></circle>
				<circle cx="1.49642" cy="1.49626" r="1.49626"
					transform="rotate(90 1.49642 1.49626)" fill="white"></circle>
				<circle cx="40.9984" cy="14.6642" r="1.49626"
					transform="rotate(90 40.9984 14.6642)" fill="white"></circle>
				<circle cx="27.8304" cy="14.6642" r="1.49626"
					transform="rotate(90 27.8304 14.6642)" fill="white"></circle>
				<circle cx="14.6644" cy="14.6642" r="1.49626"
					transform="rotate(90 14.6644 14.6642)" fill="white"></circle>
				<circle cx="1.49642" cy="14.6642" r="1.49626"
					transform="rotate(90 1.49642 14.6642)" fill="white"></circle>
				<circle cx="40.9984" cy="27.8302" r="1.49626"
					transform="rotate(90 40.9984 27.8302)" fill="white"></circle>
				<circle cx="27.8304" cy="27.8302" r="1.49626"
					transform="rotate(90 27.8304 27.8302)" fill="white"></circle>
				<circle cx="14.6644" cy="27.8302" r="1.49626"
					transform="rotate(90 14.6644 27.8302)" fill="white"></circle>
				<circle cx="1.49642" cy="27.8302" r="1.49626"
					transform="rotate(90 1.49642 27.8302)" fill="white"></circle>
				<circle cx="40.9984" cy="40.9982" r="1.49626"
					transform="rotate(90 40.9984 40.9982)" fill="white"></circle>
				<circle cx="27.8304" cy="40.9963" r="1.49626"
					transform="rotate(90 27.8304 40.9963)" fill="white"></circle>
				<circle cx="14.6644" cy="40.9982" r="1.49626"
					transform="rotate(90 14.6644 40.9982)" fill="white"></circle>
				<circle cx="1.49642" cy="40.9963" r="1.49626"
					transform="rotate(90 1.49642 40.9963)" fill="white"></circle>
				<circle cx="40.9984" cy="54.1642" r="1.49626"
					transform="rotate(90 40.9984 54.1642)" fill="white"></circle>
				<circle cx="27.8304" cy="54.1642" r="1.49626"
					transform="rotate(90 27.8304 54.1642)" fill="white"></circle>
				<circle cx="14.6644" cy="54.1642" r="1.49626"
					transform="rotate(90 14.6644 54.1642)" fill="white"></circle>
				<circle cx="1.49642" cy="54.1642" r="1.49626"
					transform="rotate(90 1.49642 54.1642)" fill="white"></circle>
			</g>
		</svg>
	</span>
</div>
