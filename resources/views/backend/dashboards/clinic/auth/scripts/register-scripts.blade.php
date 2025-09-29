<script src="{{asset('auth/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<script src="{{asset('auth/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{asset('auth/js/main.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
		// Show server-side validation errors as toastr
		@if($errors->any())
		@foreach($errors->all() as $error)
		toastr.error("{{ $error }}");
		@endforeach
		@endif
		@if(session('success'))
		toastr.success("{{ session('success') }}");
		@endif
		@if(session('error'))
		toastr.error("{{ session('error') }}");
		@endif

		$(document).ready(function() {
			let currentStep = 1;
			let step1Valid = false;
			let step2Valid = false;
			let clinicId = null;

			// Timer variables
			let otpTimer = null;
			let resendTimer = null;

			// Validation rules
			const validators = {
				clinic_name: {
					required: true,
					minLength: 2,
					message: 'Clinic name must be at least 2 characters'
				},
				phone: {
					required: true,
					pattern: /^01[0-2|5]\d{8}$/,
					message: 'Please enter a valid 11-digit Egyptian phone number'
				},
				address: {
					required: true,
					minLength: 10,
					message: 'Address must be at least 10 characters'
				},
				user_name: {
					required: true,
					minLength: 2,
					message: 'User name must be at least 2 characters'
				},
				user_email: {
					required: true,
					pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
					message: 'Please enter a valid email address'
				},
				password: {
					required: true,
					minLength: 8,
					message: 'Password must be at least 8 characters'
				},
				password_confirmation: {
					required: true,
					match: 'password',
					message: 'Passwords do not match'
				}
			};

			// Real-time validation function
			function validateField(fieldName, value) {
				const validator = validators[fieldName];
				if (!validator) return {
					valid: true
				};

				// Required check
				if (validator.required && (!value || value.trim() === '')) {
					return {
						valid: false,
						message: 'This field is required'
					};
				}

				// Skip other validations if field is empty and not required
				if (!value || value.trim() === '') {
					return {
						valid: true
					};
				}

				// Min length check
				if (validator.minLength && value.length < validator.minLength) {
					return {
						valid: false,
						message: validator.message
					};
				}

				// Pattern check
				if (validator.pattern && !validator.pattern.test(value)) {
					return {
						valid: false,
						message: validator.message
					};
				}

				// Match check (for password confirmation)
				if (validator.match) {
					const matchValue = $(`#${validator.match}`).val();
					if (value !== matchValue) {
						return {
							valid: false,
							message: validator.message
						};
					}
				}

				return {
					valid: true
				};
			}

			// Update field validation
			function updateFieldValidation(fieldName, isValid, message = '') {
				const field = $(`#${fieldName}`);
				const feedback = $(`#${fieldName}_feedback`);

				field.removeClass('is-valid is-invalid');
				feedback.removeClass('valid invalid').empty();

				if (isValid) {
					field.addClass('is-valid');
					feedback.addClass('valid').html(
						'<i class="fa fa-check-circle"></i> Valid'
					);
				} else {
					field.addClass('is-invalid');
					feedback.addClass('invalid').html(
						`<i class="fa fa-exclamation-circle"></i> ${message}`
					);
				}
			}

			// Clear all field errors
			function clearAllFieldErrors() {
				$('input, textarea').each(function() {
					const fieldName = $(this).attr('id');
					if (fieldName) {
						updateFieldValidation(fieldName, true, '');
						$(this).removeClass('is-valid is-invalid');
						$(`#${fieldName}_feedback`).removeClass('valid invalid').empty();
					}
				});
			}

			// Track which fields have server errors to prevent frontend override
			let serverErrorFields = {};

			// Display server validation errors
			function displayServerErrors(errors) {
				// Clear any existing errors first
				clearAllFieldErrors();

				// Reset server error tracking
				serverErrorFields = {};

				let firstErrorField = null;

				// Display each field error
				Object.keys(errors).forEach(function(fieldName) {
					const messages = errors[fieldName];
					if (messages && messages.length > 0) {
						// Mark this field as having server error
						serverErrorFields[fieldName] = messages[0];

						// Show the first error message for each field
						updateFieldValidation(fieldName, false, messages[0]);

						// Track first error field for scrolling
						if (!firstErrorField) {
							firstErrorField = fieldName;
						}
					}
				});

				// Show only one toastr for the first error (not per field)
				if (firstErrorField) {
					const firstErrorMessage = serverErrorFields[firstErrorField];
					toastr.error(firstErrorMessage);

					// Scroll to first error field
					const firstErrorElement = $(`#${firstErrorField}`);
					if (firstErrorElement.length) {
						$('html, body').animate({
							scrollTop: firstErrorElement.offset().top - 100
						}, 500);

						// Focus the field
						firstErrorElement.focus();
					}
				}
			}

			// Check if step is valid
			function checkStepValidity(step) {
				let isValid = true;
				const stepFields = step === 1 ? ['clinic_name', 'phone', 'address'] : [
					'user_name', 'user_email', 'password',
					'password_confirmation'
				];

				stepFields.forEach(fieldName => {
					const value = $(`#${fieldName}`).val();
					const validation = validateField(
						fieldName, value);
					if (!validation.valid) {
						isValid = false;
					}
				});

				return isValid;
			}

			// Update step indicators
			function updateStepIndicators() {
				$('.step-dot').each(function(index) {
					const stepNumber = index + 1;
					$(this).removeClass('active completed');

					if (stepNumber === currentStep) {
						$(this).addClass('active');
					} else if (stepNumber < currentStep) {
						$(this).addClass('completed');
					}
				});
			}

			// Real-time validation on input
			$('input, textarea').on('input blur', function() {
				const fieldName = $(this).attr('id');
				const value = $(this).val();

				// Don't override server errors until user types something new
				if (serverErrorFields[fieldName]) {
					// If user started typing, clear the server error
					if (value.length > 0 && $(this).data('user-typed')) {
						delete serverErrorFields[fieldName];
					} else {
						// Keep showing server error
						return;
					}
				}

				const validation = validateField(fieldName, value);
				updateFieldValidation(fieldName, validation.valid, validation.message);

				// Update step validity
				if (currentStep === 1) {
					step1Valid = checkStepValidity(1);
					$('#nextBtn').prop('disabled', !step1Valid);
				} else if (currentStep === 2) {
					step2Valid = checkStepValidity(2);
					$('#submitBtn').prop('disabled', !step2Valid);
				}
			});

			// Track when user starts typing to clear server errors
			$('input, textarea').on('input', function() {
				$(this).data('user-typed', true);
			});

			// File upload handling
			$('#images').on('change', function() {
				const files = this.files;
				const label = $('.file-input-label');
				const feedback = $('#images_feedback');

				if (files.length > 0) {
					label.addClass('has-files');
					label.find('.file-text').text(
						`${files.length} file(s) selected`
					);

					// Validate file sizes
					let validFiles = true;
					Array.from(files).forEach(file => {
						if (file.size >
							10 *
							1024 *
							1024
						) { // 10MB
							validFiles =
								false;
						}
					});

					if (validFiles) {
						updateFieldValidation(
							'images',
							true);
					} else {
						updateFieldValidation(
							'images',
							false,
							'File size must be less than 10MB'
						);
					}
				} else {
					label.removeClass('has-files');
					label.find('.file-text').text(
						'Click to upload images or drag and drop'
					);
					feedback.removeClass('valid invalid')
						.empty();
				}
			});

			// Next step button
			$('.next-step').click(function() {
				if (step1Valid) {
					currentStep = 2;
					$('.step-1').removeClass('active');
					$('.step-2').addClass('active');
					updateStepIndicators();

					// Check step 2 validity
					step2Valid = checkStepValidity(2);
					$('#submitBtn').prop('disabled', !
						step2Valid);
				} else {
					toastr.error(
						'Please fill in all required fields correctly before proceeding.'
					);

					// Scroll to first invalid field
					const firstInvalid = $(
						'.step-1 .is-invalid'
					).first();
					if (firstInvalid.length) {
						$('html, body').animate({
							scrollTop: firstInvalid
								.offset()
								.top -
								100
						}, 500);
					}
				}
			});

			// Previous step button
			$('.prev-step').click(function() {
				currentStep = 1;
				$('.step-2').removeClass('active');
				$('.step-1').addClass('active');
				updateStepIndicators();
			});

			// Form submission with AJAX
			$('#clinicRegistrationForm').submit(function(e) {
				e.preventDefault();

				if (!step2Valid) {
					toastr.error('Please fill in all required fields correctly before submitting.');
					return;
				}

				const formData = new FormData(this);
				const submitBtn = $('#submitBtn');
				const originalText = submitBtn.html();

				// Show loading state
				submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending verification code...');

				$.ajax({
					url: $(this).attr('action'),
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function(response) {
						if (response.success) {
							toastr.success(response.message);
							clinicId = response.clinic_id;
							$('#clinic_id').val(clinicId);

							// Move to step 3
							currentStep = 3;
							$('.step-2').removeClass('active');
							$('.step-3').addClass('active');
							updateStepIndicators();

							// Start timers
							startOtpTimer();
							startResendTimer();
						} else {
							toastr.error(response.message);
						}
					},
					error: function(xhr) {
						if (xhr.status === 422) {
							const response = xhr.responseJSON;
							if (response.errors) {
								// Display field-specific errors
								displayServerErrors(response.errors);

								// Show general message if provided
								if (response.message && response.message !== 'Validation failed. Please check the form and try again.') {
									toastr.error(response.message);
								}
							} else if (response.message) {
								toastr.error(response.message);
							}
						} else {
							toastr.error('Registration failed. Please try again.');
						}
					},
					complete: function() {
						submitBtn.prop('disabled', false).html(originalText);
					}
				});
			});

			// OTP Input handling
			$('.otp-input').on('input', function() {
				let value = $(this).val();
				if (value.length === 1) {
					$(this).next('.otp-input').focus();
				}
				validateOtpInputs();
			});

			$('.otp-input').on('keydown', function(e) {
				if (e.key === 'Backspace' && $(this).val() === '') {
					$(this).prev('.otp-input').focus();
				}
			});

			function validateOtpInputs() {
				let otp = '';
				$('.otp-input').each(function() {
					otp += $(this).val();
				});

				$('#verifyOtpBtn').prop('disabled', otp.length !== 6);
				return otp;
			}

			// OTP Verification
			$('#verifyOtpBtn').on('click', function() {
				const otp = validateOtpInputs();
				if (otp.length !== 6) {
					toastr.error('Please enter the complete 6-digit code.');
					return;
				}

				const btn = $(this);
				const originalText = btn.html();
				btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

				$.ajax({
					url: '{{ route("clinic.verify-otp") }}',
					type: 'POST',
					data: {
						clinic_id: clinicId,
						otp: otp,
						_token: $('meta[name="csrf-token"]').attr('content')
					},
					success: function(response) {
						if (response.success) {
							toastr.success(response.message);
							setTimeout(() => {
								window.location.href = response.redirect_url;
							}, 2000);
						} else {
							toastr.error(response.message);
							console.log(response.message);
						}
					},
					error: function(xhr) {
						if (xhr.status === 422 && xhr.responseJSON?.errors) {
							displayServerErrors(xhr.responseJSON.errors);
						} else {
							toastr.error(xhr.responseJSON?.message || 'Verification failed.');
							console.log(xhr.responseJSON?.message || 'Verification failed.');
						}
					},
					complete: function() {
						btn.prop('disabled', false).html(originalText);
					}
				});
			});

			// Resend OTP
			$('#resendBtn').on('click', function() {
				const btn = $(this);
				btn.prop('disabled', true);

				$.ajax({
					url: '{{ route("clinic.resend-otp") }}',
					type: 'POST',
					data: {
						clinic_id: clinicId,
						_token: $('meta[name="csrf-token"]').attr('content')
					},
					success: function(response) {
						if (response.success) {
							toastr.success(response.message);
							startOtpTimer(); // Reset OTP timer
							startResendTimer(); // Reset resend timer
						} else {
							toastr.error(response.message);
						}
					},
					error: function(xhr) {
						if (xhr.status === 422 && xhr.responseJSON?.errors) {
							displayServerErrors(xhr.responseJSON.errors);
						} else {
							toastr.error(xhr.responseJSON?.message || 'Failed to resend OTP.');
						}
					}
				});
			});

			// Back to step 2
			$('#backToStep2').on('click', function() {
				currentStep = 2;
				$('.step-3').removeClass('active');
				$('.step-2').addClass('active');
				updateStepIndicators();
				clearTimers();
			});

			// Timer functions
			function startOtpTimer() {
				let timeLeft = 300; // 5 minutes
				clearInterval(otpTimer);

				otpTimer = setInterval(function() {
					const minutes = Math.floor(timeLeft / 60);
					const seconds = timeLeft % 60;
					$('#timer').text(String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0'));

					if (timeLeft <= 0) {
						clearInterval(otpTimer);
						$('#timer').text('00:00');
						toastr.error('OTP has expired. Please request a new one.');
						$('.otp-input').prop('disabled', true);
						$('#verifyOtpBtn').prop('disabled', true);
					}
					timeLeft--;
				}, 1000);
			}

			function startResendTimer() {
				let timeLeft = 60; // 1 minute - simplified
				clearInterval(resendTimer);
				$('#resendBtn').prop('disabled', true);

				resendTimer = setInterval(function() {
					$('#resendTimer').text(timeLeft);

					if (timeLeft <= 0) {
						clearInterval(resendTimer);
						$('#resendBtn').prop('disabled', false);
						$('#resendTimer').text('0');
					}
					timeLeft--;
				}, 1000);
			}

			function clearTimers() {
				clearInterval(otpTimer);
				clearInterval(resendTimer);
			}

			// Initialize step indicators
			updateStepIndicators();
		});
	</script>
