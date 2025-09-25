<!DOCTYPE html>
<html lang="en">

<head>
	<title>Clinic Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="icon" type="image/png" href="images/icons/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="{{asset('login/vendor/bootstrap/css/bootstrap.min.css')}}">
	<link rel="stylesheet" type="text/css"
		href="{{asset('login/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
	<link rel="stylesheet" type="text/css"
		href="{{asset('login/fonts/iconic/css/material-design-iconic-font.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('login/vendor/animate/animate.css')}}">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

	@if (app()->getLocale() == 'ar')
	<link rel="stylesheet" type="text/css" href="{{asset('login/css/ar_style.css')}}">
	@else
	<link rel="stylesheet" type="text/css" href="{{asset('login/css/en_style.css')}}">
	@endif
</head>

<body>
	<div class="form-container">
		<div class="form-wrapper">
			<div class="row g-0">
				<!-- Image Section -->
				<div class="col-lg-5">
					<div class="form-image">
						<div class="form-image-content">
							<i class="fa fa-hospital-o"></i>
							<h3>{{ __('Welcome Back') }}</h3>
							<p>{{ __('Sign in to your clinic account and manage your medical services') }}</p>
						</div>
					</div>
				</div>

				<!-- Form Section -->
				<div class="col-lg-7">
					<div class="form-content">
						<form method="POST" action="{{ Route('login') }}" id="clinicLoginForm">
							@csrf

							<div class="login-header">
								<h4>{{ __('Clinic Login') }}</h4>
								<p>{{ __('Please enter your credentials to continue') }}</p>
							</div>

							<div class="form-group">
								<label class="form-label required">{{ __('Email') }}</label>
								<input type="email" name="email" id="email"
									class="form-control @error('email') is-invalid @enderror"
									value="{{ old('email') }}" required>
								<div class="validation-feedback" id="email_feedback"></div>
								@error('email')
								<div class="validation-feedback invalid">
									<i class="fa fa-exclamation-circle"></i>
									{{ $message }}
								</div>
								@enderror
							</div>

							<div class="form-group">
								<label class="form-label required">{{ __('Password') }}</label>
								<div class="input-group position-relative">
									<input type="password" name="password" id="password"
										class="form-control @error('password') is-invalid @enderror"
										style="padding-right: 45px; border-radius: 0.375rem;" required>
									<button type="button" class="position-absolute" id="passwordToggle"
										style="top: 50%; right: 12px; transform: translateY(-50%); border: none; background: transparent; color: #6c757d; z-index: 1000; padding: 4px; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
										<i class="fa fa-eye" style="font-size: 14px;"></i>
									</button>
								</div>
								<div class="validation-feedback" id="password_feedback"></div>
								@error('password')
								<div class="validation-feedback invalid">
									<i class="fa fa-exclamation-circle"></i>
									{{ $message }}
								</div>
								@enderror
							</div>

							<div class="form-group">
								<div class="d-flex justify-content-between align-items-center">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="remember" name="remember">
										<label class="form-check-label" for="remember">
											{{ __('Remember me') }}
										</label>
									</div>
									<a href="#" class="forgot-password-link">
										{{ __('Forgot Password?') }}
									</a>
								</div>
							</div>

							<button type="submit" class="btn btn-primary w-100" id="loginBtn">
								{{ __('Sign In') }}
								<i class="fa fa-sign-in"></i>
							</button>

							<div class="text-center mt-4">
								<p class="register-link">
									{{ __("Don't have an account?") }}
									<a href="{{ route('clinic.register-clinic') }}">{{ __('Register here') }}</a>
								</p>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>


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
			// Password toggle functionality with enhanced styling
			$('#passwordToggle').on('click', function() {
				const passwordField = $('#password');
				const icon = $(this).find('i');

				if (passwordField.attr('type') === 'password') {
					passwordField.attr('type', 'text');
					icon.removeClass('fa-eye').addClass('fa-eye-slash');
					$(this).css('color', '#007bff');
				} else {
					passwordField.attr('type', 'password');
					icon.removeClass('fa-eye-slash').addClass('fa-eye');
					$(this).css('color', '#6c757d');
				}
			});

			// Add hover effects for password toggle
			$('#passwordToggle').hover(
				function() {
					$(this).css('color', '#007bff');
					$(this).css('cursor', 'pointer');
				},
				function() {
					if ($('#password').attr('type') === 'password') {
						$(this).css('color', '#6c757d');
					}
				}
			);

			// Real-time validation
			function validateField(fieldName, value) {
				let isValid = true;
				let message = '';

				if (fieldName === 'email') {
					const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
					if (!value || value.trim() === '') {
						isValid = false;
						message = 'Email is required';
					} else if (!emailPattern.test(value)) {
						isValid = false;
						message = 'Please enter a valid email address';
					}
				} else if (fieldName === 'password') {
					if (!value || value.trim() === '') {
						isValid = false;
						message = 'Password is required';
					} else if (value.length < 6) {
						isValid = false;
						message = 'Password must be at least 6 characters';
					}
				}

				return { valid: isValid, message: message };
			}

			function updateFieldValidation(fieldName, isValid, message = '') {
				const field = $(`#${fieldName}`);
				const feedback = $(`#${fieldName}_feedback`);

				field.removeClass('is-valid is-invalid');
				feedback.removeClass('valid invalid').empty();

				if (isValid) {
					field.addClass('is-valid');
					feedback.addClass('valid').html('<i class="fa fa-check-circle"></i> Valid');
				} else if (message) {
					field.addClass('is-invalid');
					feedback.addClass('invalid').html(`<i class="fa fa-exclamation-circle"></i> ${message}`);
				}
			}

			// Server error handling
			let serverErrorFields = {};

			function displayServerErrors(errors) {
				// Clear any existing errors first
				$('input').removeClass('is-valid is-invalid');
				$('.validation-feedback').removeClass('valid invalid').empty();

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

				// Show only one toastr for the first error
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

			// Real-time validation on input
			$('input[type="email"], input[type="password"]').on('input blur', function() {
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
			});

			// Track when user starts typing to clear server errors
			$('input[type="email"], input[type="password"]').on('input', function() {
				$(this).data('user-typed', true);
			});

			// Enhanced form submission with AJAX
			$('#clinicLoginForm').on('submit', function(e) {
				e.preventDefault();

				const email = $('#email').val();
				const password = $('#password').val();

				// Validate fields
				const emailValidation = validateField('email', email);
				const passwordValidation = validateField('password', password);

				updateFieldValidation('email', emailValidation.valid, emailValidation.message);
				updateFieldValidation('password', passwordValidation.valid, passwordValidation.message);

				if (!emailValidation.valid || !passwordValidation.valid) {
					toastr.error('Please fill in all required fields correctly.');
					return;
				}

				const submitBtn = $('#loginBtn');
				const originalText = submitBtn.html();
				submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Signing in...');

				// AJAX form submission
				$.ajax({
					url: $(this).attr('action'),
					type: 'POST',
					data: {
						email: email,
						password: password,
						remember: $('#remember').is(':checked'),
						_token: $('meta[name="csrf-token"]').attr('content')
					},
					success: function(response) {
						// Login successful - redirect to intended location
						toastr.success('Login successful! Redirecting...');
						setTimeout(() => {
							window.location.href = '/clinic/dashboard';
						}, 1500);
					},
					error: function(xhr) {
						if (xhr.status === 422) {
							// Validation errors from backend
							const response = xhr.responseJSON;
							if (response.errors) {
								displayServerErrors(response.errors);
							} else if (response.message) {
								toastr.error(response.message);
							}
						} else if (xhr.status === 401) {
							// Authentication failed
							toastr.error('Invalid email or password. Please try again.');
							updateFieldValidation('password', false, 'Invalid credentials');
						} else if (xhr.status === 419) {
							// CSRF token mismatch
							toastr.error('Session expired. Please refresh the page and try again.');
							setTimeout(() => {
								window.location.reload();
							}, 2000);
						} else {
							// Other errors
							toastr.error('Login failed. Please try again.');
						}
					},
					complete: function() {
						submitBtn.prop('disabled', false).html(originalText);
					}
				});
			});
		});
	</script>

</body>

</html>
