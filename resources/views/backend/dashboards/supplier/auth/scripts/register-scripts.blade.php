<script src="{{asset('login/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
	<script src="{{asset('login/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
	<script src="{{asset('login/js/main.js')}}"></script>
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

			// Validation rules
			const validators = {
				supplier_name: {
					required: true,
					minLength: 2,
					message: 'Supplier name must be at least 2 characters'
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

			// Check if step is valid
			function checkStepValidity(step) {
				let isValid = true;
				const stepFields = step === 1 ? ['supplier_name', 'phone', 'address'] : [
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
				const validation = validateField(fieldName,
					value);

				updateFieldValidation(fieldName, validation.valid,
					validation.message);

				// Update step validity
				if (currentStep === 1) {
					step1Valid = checkStepValidity(1);
					$('#nextBtn').prop('disabled', !
						step1Valid);
				} else if (currentStep === 2) {
					step2Valid = checkStepValidity(2);
					$('#submitBtn').prop('disabled', !
						step2Valid);
				}
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
			$('#supplierRegistrationForm').submit(function(e) {
				e.preventDefault();

				if (!step2Valid) {
					toastr.error(
						'Please fill in all required fields correctly before submitting.'
					);
					return;
				}

				const formData = new FormData(this);
				const submitBtn = $('#submitBtn');
				const originalText = submitBtn.html();

				// Show loading state
				submitBtn.prop('disabled', true).html(
					'<i class="fa fa-spinner fa-spin"></i> Registering...'
				);

				$.ajax({
					url: $(this).attr(
						'action'
					),
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function(
						response
					) {
						if (response
							.success
						) {
							toastr.success(response
								.message
							);
							$('#supplierRegistrationForm')[0].reset();
							currentStep = 1;
							$('.step-2').removeClass('active');
							$('.step-1').addClass('active');
							updateStepIndicators();
							step1Valid = false;
							step2Valid = false;
							$('#nextBtn').prop('disabled', true);
							$('#submitBtn').prop('disabled', true);
							$('.validation-feedback').removeClass('valid invalid').empty();
							$('.form-control').removeClass('is-valid is-invalid');
							$('.file-input-label').removeClass('has-files').find('.file-text').text('Click to upload images or drag and drop');
						} else {
							toastr.error(response
								.message
							);
						}
					},
					error: function(
						xhr
					) {
						if (xhr.status ===
							422
						) {
							const errors =
								xhr
								.responseJSON
								.errors;
							Object.values(
									errors
								)
								.forEach(function(
									messages
								) {
									messages.forEach(function(
										message
									) {
										toastr.error(
											message
										);
									});
								});
						} else {
							toastr.error(
								'Registration failed. Please try again.'
							);
						}
					},
					complete: function() {
						submitBtn.prop('disabled',
								false
							)
							.html(
								originalText
							);
					}
				});
			});

			// Initialize step indicators
			updateStepIndicators();
		});
	</script>