<!DOCTYPE html>
<html lang="en">

<head>
	<title>Register Clinic</title>
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
							<i class="fa fa-hospital-o"></i>
							<h3>{{ __('Clinic Registration') }}</h3>
							<p>{{ __('Join our medical platform and provide quality healthcare services') }}
							</p>
						</div>
					</div>
				</div>

				<!-- Form Section -->
				<div class="col-lg-7">
					<div class="form-content">
						<form method="POST" action="{{ Route('clinic.register-clinic') }}"
							id="clinicRegistrationForm">
							@csrf

							<!-- Step Indicator -->
							<div class="step-indicator">
								<div class="step-dot active"
									data-step="1"></div>
								<div class="step-dot" data-step="2">
								</div>
								<div class="step-dot" data-step="3">
								</div>
							</div>

							<div id="stepper">
								<!-- Step 1: Clinic Details -->
								<div class="step step-1 active">
									<div class="step-header">
										<h4>{{ __('Step 1: Clinic Information') }}
										</h4>
										<p>{{ __('Please provide your clinic details') }}
										</p>
									</div>

									<div class="row">
										<div
											class="col-md-6">
											<div
												class="form-group">
												<label
													class="form-label required">{{ __('Clinic Name') }}</label>
												<input type="text"
													name="clinic_name"
													id="clinic_name"
													class="form-control @error('clinic_name') is-invalid @enderror"
													value="{{ old('clinic_name') }}"
													required>
												<div class="validation-feedback"
													id="clinic_name_feedback">
												</div>
												@error('clinic_name')
												<div
													class="validation-feedback invalid">
													<i
														class="fa fa-exclamation-circle"></i>
													{{ $message }}
												</div>
												@enderror
											</div>
										</div>
										<div
											class="col-md-6">
											<div
												class="form-group">
												<label
													class="form-label required">{{ __('Phone') }}</label>
												<input type="text"
													name="phone"
													id="phone"
													autocomplete="false"
													class="form-control @error('phone') is-invalid @enderror"
													value="{{ old('phone') }}"
													required>
												<div class="validation-feedback"
													id="phone_feedback">
												</div>
												@error('phone')
												<div
													class="validation-feedback invalid">
													<i
														class="fa fa-exclamation-circle"></i>
													{{ $message }}
												</div>
												@enderror
											</div>
										</div>
									</div>

									<div class="form-group">
										<label
											class="form-label required">{{ __('Address') }}</label>
										<textarea name="address"
											id="address"
											rows="3"
											class="form-control @error('address') is-invalid @enderror"
											required>{{ old('address') }}</textarea>
										<div class="validation-feedback"
											id="address_feedback">
										</div>
										@error('address')
										<div
											class="validation-feedback invalid">
											<i
												class="fa fa-exclamation-circle"></i>
											{{ $message }}
										</div>
										@enderror
									</div>

									<div class="form-group">
										<label
											class="form-label">{{ __('Clinic Images') }}</label>
										<div
											class="file-input-wrapper">
											<input type="file"
												name="images[]"
												id="images"
												multiple
												accept="image/*">
											<label for="images"
												class="file-input-label">
												<i
													class="fa fa-cloud-upload"></i>
												<span
													class="file-text">{{ __('Click to upload images or drag and drop') }}</span>
												<small
													class="file-info">{{ __('PNG, JPG, GIF up to 10MB each') }}</small>
											</label>
										</div>
										<div class="progress-bar"
											id="upload-progress"
											style="display: none;">
											<div class="progress-fill"
												id="progress-fill">
											</div>
										</div>
										<div class="validation-feedback"
											id="images_feedback">
										</div>
										@error('images')
										<div
											class="validation-feedback invalid">
											<i
												class="fa fa-exclamation-circle"></i>
											{{ $message }}
										</div>
										@enderror
									</div>

									<div class="text-end">
										<button type="button"
											class="btn btn-primary next-step"
											id="nextBtn"
											disabled>
											{{ __('Next') }}
											<i
												class="fa fa-arrow-right"></i>
										</button>
									</div>
								</div>

								<!-- Step 2: User Details -->
								<div class="step step-2">
									<div class="step-header">
										<h4>{{ __('Step 2: User Information') }}
										</h4>
										<p>{{ __('Create your admin account for the clinic') }}
										</p>
									</div>

									<div class="row">
										<div
											class="col-md-6">
											<div
												class="form-group">
												<label
													class="form-label required">{{ __('User Name') }}</label>
												<input type="text"
													name="user_name"
													id="user_name"
													class="form-control @error('user_name') is-invalid @enderror"
													value="{{ old('user_name') }}"
													required>
												<div class="validation-feedback"
													id="user_name_feedback">
												</div>
												@error('user_name')
												<div
													class="validation-feedback invalid">
													<i
														class="fa fa-exclamation-circle"></i>
													{{ $message }}
												</div>
												@enderror
											</div>
										</div>
										<div
											class="col-md-6">
											<div
												class="form-group">
												<label
													class="form-label required">{{ __('User Email') }}</label>
												<input type="email"
													name="user_email"
													id="user_email"
													class="form-control @error('user_email') is-invalid @enderror"
													value="{{ old('user_email') }}"
													required>
												<div class="validation-feedback"
													id="user_email_feedback">
												</div>
												@error('user_email')
												<div
													class="validation-feedback invalid">
													<i
														class="fa fa-exclamation-circle"></i>
													{{ $message }}
												</div>
												@enderror
											</div>
										</div>
									</div>

									<div class="row">
										<div
											class="col-md-6">
											<div
												class="form-group">
												<label
													class="form-label required">{{ __('Password') }}</label>
												<input type="password"
													name="password"
													id="password"
													class="form-control @error('password') is-invalid @enderror"
													required>
												<div class="validation-feedback"
													id="password_feedback">
												</div>
												@error('password')
												<div
													class="validation-feedback invalid">
													<i
														class="fa fa-exclamation-circle"></i>
													{{ $message }}
												</div>
												@enderror
											</div>
										</div>
										<div
											class="col-md-6">
											<div
												class="form-group">
												<label
													class="form-label required">{{ __('Confirm Password') }}</label>
												<input type="password"
													name="password_confirmation"
													id="password_confirmation"
													class="form-control @error('password_confirmation') is-invalid @enderror"
													required>
												<div class="validation-feedback"
													id="password_confirmation_feedback">
												</div>
												@error('password_confirmation')
												<div
													class="validation-feedback invalid">
													<i
														class="fa fa-exclamation-circle"></i>
													{{ $message }}
												</div>
												@enderror
											</div>
										</div>
									</div>

									<div
										class="d-flex justify-content-between">
										<button type="button"
											class="btn btn-secondary prev-step">
											<i
												class="fa fa-arrow-left"></i>
											{{ __('Back') }}
										</button>
										<button type="submit"
											class="btn btn-success"
											id="submitBtn"
											disabled>
											{{ __('Register Clinic') }}
											<i
												class="fa fa-check"></i>
										</button>
									</div>
								</div>

								<!-- Step 3: OTP Verification -->
								<div class="step step-3">
									<div class="step-header">
										<h4>{{ __('Step 3: Email Verification') }}</h4>
										<p>{{ __('Please enter the 6-digit code sent to your email') }}</p>
									</div>

									<input type="hidden" id="clinic_id" name="clinic_id">

									<div class="form-group text-center">
										<label class="form-label required">{{ __('Verification Code') }}</label>
										<div class="otp-input-container">
											<input type="text" class="otp-input" maxlength="1" id="otp1">
											<input type="text" class="otp-input" maxlength="1" id="otp2">
											<input type="text" class="otp-input" maxlength="1" id="otp3">
											<input type="text" class="otp-input" maxlength="1" id="otp4">
											<input type="text" class="otp-input" maxlength="1" id="otp5">
											<input type="text" class="otp-input" maxlength="1" id="otp6">
										</div>
										<div class="validation-feedback" id="otp_feedback"></div>
									</div>

									<div class="form-group text-center">
										<div class="timer-container">
											<p>{{ __('Code expires in') }}: <span id="timer">05:00</span></p>
										</div>
										<button type="button" class="btn btn-link" id="resendBtn" disabled>
											{{ __('Resend Code') }} (<span id="resendTimer">60</span>s)
										</button>
									</div>

									<div class="d-flex justify-content-between">
										<button type="button" class="btn btn-secondary" id="backToStep2">
											<i class="fa fa-arrow-left"></i>
											{{ __('Back') }}
										</button>
										<button type="button" class="btn btn-success" id="verifyOtpBtn" disabled>
											{{ __('Verify & Complete Registration') }}
											<i class="fa fa-check"></i>
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>


	@include('backend.dashboards.clinic.auth.scripts.register-scripts')


</body>

</html>
