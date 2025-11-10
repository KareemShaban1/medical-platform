<!DOCTYPE html>
<html lang="en">

<head>
	<title>Doctor Registration</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="images/icons/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="{{asset('auth/vendor/bootstrap/css/bootstrap.min.css')}}">
	<link rel="stylesheet" type="text/css"
		href="{{asset('auth/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
	<link rel="stylesheet" type="text/css"
		href="{{asset('auth/fonts/iconic/css/material-design-iconic-font.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('auth/vendor/animate/animate.css')}}">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
	<meta name="csrf-token" content="{{ csrf_token() }}">

	@if (app()->getLocale() == 'ar')
	<link rel="stylesheet" type="text/css" href="{{asset('auth/css/ar_style.css')}}">
	@else
	<link rel="stylesheet" type="text/css" href="{{asset('auth/css/en_style.css')}}">
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
							<i class="fa fa-user-md"></i>
							<h3>{{ __('Doctor Registration') }}</h3>
							<p>{{ __('Join our medical platform as a standalone doctor') }}
							</p>
						</div>
					</div>
				</div>

				<!-- Form Section -->
				<div class="col-lg-7">
					<div class="form-content">
						<form method="POST"
							action="{{ route('doctor.register') }}"
							id="doctorRegistrationForm">
							@csrf

							<div class="step-header">
								<h4>{{ __('Doctor Registration') }}
								</h4>
								<p>{{ __('Create your doctor account') }}
								</p>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="form-label required">{{ __('User Name') }}</label>
										<input type="text"
											name="user_name"
											id="user_name"
											class="form-control @error('user_name') is-invalid @enderror"
											value="{{ old('user_name') }}"
											required>
										<div class="validation-feedback" id="user_name_feedback">
										</div>
										@error('user_name')
										<div class="validation-feedback invalid">
											<i class="fa fa-exclamation-circle"></i>
											{{ $message }}
										</div>
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="form-label required">{{ __('User Email') }}</label>
										<input type="email"
											name="user_email"
											id="user_email"
											class="form-control @error('user_email') is-invalid @enderror"
											value="{{ old('user_email') }}"
											required>
										<div class="validation-feedback" id="user_email_feedback">
										</div>
										@error('user_email')
										<div class="validation-feedback invalid">
											<i class="fa fa-exclamation-circle"></i>
											{{ $message }}
										</div>
										@enderror
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="form-label required">{{ __('Password') }}</label>
										<input type="password"
											name="password"
											id="password"
											class="form-control @error('password') is-invalid @enderror"
											required>
										<div class="validation-feedback" id="password_feedback">
										</div>
										@error('password')
										<div class="validation-feedback invalid">
											<i class="fa fa-exclamation-circle"></i>
											{{ $message }}
										</div>
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="form-label required">{{ __('Confirm Password') }}</label>
										<input type="password"
											name="password_confirmation"
											id="password_confirmation"
											class="form-control @error('password_confirmation') is-invalid @enderror"
											required>
										<div class="validation-feedback" id="password_confirmation_feedback">
										</div>
										@error('password_confirmation')
										<div class="validation-feedback invalid">
											<i class="fa fa-exclamation-circle"></i>
											{{ $message }}
										</div>
										@enderror
									</div>
								</div>
							</div>

							<div class="d-flex justify-content-end">
								<button type="submit"
									class="btn btn-success"
									id="submitBtn">
									{{ __('Register') }}
									<i class="fa fa-check"></i>
								</button>
							</div>

							<div class="text-center mt-4">
								<p class="register-link">
									{{ __("Already have an account?") }}
									<a href="{{ url('/clinic/login') }}">{{ __('Login here') }}</a>
								</p>
							</div>
                            <div class="back-to-home text-center">
                                <a href="{{ route('home') }}" class="btn btn-link">
                                    <i class="fa fa-arrow-left"></i>
                                    {{ __('Back to Home') }}
                                </a>
                            </div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>


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
			// Real-time validation
			function validateField(fieldName, value) {
				let isValid = true;
				let message = '';

				if (fieldName === 'user_email') {
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
					} else if (value.length < 8) {
						isValid = false;
						message = 'Password must be at least 8 characters';
					}
				} else if (fieldName === 'password_confirmation') {
					const password = $('#password').val();
					if (!value || value.trim() === '') {
						isValid = false;
						message = 'Password confirmation is required';
					} else if (value !== password) {
						isValid = false;
						message = 'Passwords do not match';
					}
				} else if (fieldName === 'user_name') {
					if (!value || value.trim() === '') {
						isValid = false;
						message = 'User name is required';
					} else if (value.length < 2) {
						isValid = false;
						message = 'User name must be at least 2 characters';
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

			// Real-time validation on input
			$('input').on('input blur', function() {
				const fieldName = $(this).attr('id');
				const value = $(this).val();
				const validation = validateField(fieldName, value);
				updateFieldValidation(fieldName, validation.valid, validation.message);
			});

			// Enhanced form submission with AJAX
			$('#doctorRegistrationForm').on('submit', function(e) {
				e.preventDefault();

				const user_name = $('#user_name').val();
				const user_email = $('#user_email').val();
				const password = $('#password').val();
				const password_confirmation = $('#password_confirmation').val();

				// Validate all fields
				const nameValidation = validateField('user_name', user_name);
				const emailValidation = validateField('user_email', user_email);
				const passwordValidation = validateField('password', password);
				const passwordConfirmationValidation = validateField('password_confirmation', password_confirmation);

				updateFieldValidation('user_name', nameValidation.valid, nameValidation.message);
				updateFieldValidation('user_email', emailValidation.valid, emailValidation.message);
				updateFieldValidation('password', passwordValidation.valid, passwordValidation.message);
				updateFieldValidation('password_confirmation', passwordConfirmationValidation.valid, passwordConfirmationValidation.message);

				if (!nameValidation.valid || !emailValidation.valid || !passwordValidation.valid || !passwordConfirmationValidation.valid) {
					toastr.error('Please fill in all required fields correctly.');
					return;
				}

				const submitBtn = $('#submitBtn');
				const originalText = submitBtn.html();
				submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registering...');

				// AJAX form submission
				$.ajax({
					url: $(this).attr('action'),
					type: 'POST',
					data: {
						user_name: user_name,
						user_email: user_email,
						password: password,
						password_confirmation: password_confirmation,
						_token: $('meta[name="csrf-token"]').attr('content')
					},
					success: function(response) {
						if (response.success) {
							toastr.success(response.message);
							setTimeout(() => {
								window.location.href = response.redirect_url || '/';
							}, 1500);
						} else {
							toastr.error(response.message || 'Registration failed');
							submitBtn.prop('disabled', false).html(originalText);
						}
					},
					error: function(xhr) {
						if (xhr.status === 422) {
							const response = xhr.responseJSON;
							if (response.errors) {
								Object.keys(response.errors).forEach(function(fieldName) {
									const messages = response.errors[fieldName];
									if (messages && messages.length > 0) {
										updateFieldValidation(fieldName, false, messages[0]);
									}
								});
								toastr.error(response.message || 'Validation failed');
							} else if (response.message) {
								toastr.error(response.message);
							}
						} else if (xhr.status === 419) {
							toastr.error('Session expired. Please refresh the page and try again.');
							setTimeout(() => {
								window.location.reload();
							}, 2000);
						} else {
							toastr.error('Registration failed. Please try again.');
						}
						submitBtn.prop('disabled', false).html(originalText);
					}
				});
			});
		});
	</script>

</body>

</html>

