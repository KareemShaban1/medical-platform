<!DOCTYPE html>
<html lang="en">

<head>
	<title>{{ __('Affiliate Login') }}</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="icon" type="image/png" href="{{asset('frontend/images/favicon/favicon-96x96.png')}}" />
	<link rel="stylesheet" type="text/css" href="{{asset('auth/vendor/bootstrap/css/bootstrap.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('auth/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
	<link rel="stylesheet" type="text/css"
		href="{{asset('auth/fonts/iconic/css/material-design-iconic-font.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('auth/vendor/animate/animate.css')}}">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

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
				<div class="col-lg-5">
					<div class="form-image">
						<div class="form-image-content">
							<i class="fa fa-users"></i>
							<h3>{{ __('Welcome Back') }}</h3>
							<p>{{ __('Sign in to manage your affiliate earnings') }}</p>
						</div>
					</div>
				</div>

				<div class="col-lg-7">
					<div class="form-content">
						<form method="POST" action="{{ Route('login') }}">
							@csrf

							<div class="login-header">
								<h4>{{ __('Affiliate Login') }}</h4>
								<p>{{ __('Please enter your credentials to continue') }}</p>
							</div>

							<div class="form-group">
								<label class="form-label required">{{ __('Email') }}</label>
								<input type="email" name="email"
									class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
									required>
								@error('email')
									<div class="validation-feedback invalid">
										<i class="fa fa-exclamation-circle"></i>
										{{ $message }}
									</div>
								@enderror
							</div>

							<div class="form-group">
								<label class="form-label required">{{ __('Password') }}</label>
								<input type="password" name="password"
									class="form-control @error('password') is-invalid @enderror" required>
								@error('password')
									<div class="validation-feedback invalid">
										<i class="fa fa-exclamation-circle"></i>
										{{ $message }}
									</div>
								@enderror
							</div>

							<div class="form-group">
							<div class="d-flex justify-content-end align-items-center">
								<a href="{{ route('affiliate.forgot-password') }}" class="forgot-password-link">
									{{ __('Forgot Password?') }}
								</a>
							</div>
						</div>

						<button type="submit" class="btn btn-primary w-100">
								{{ __('Sign In') }}
								<i class="fa fa-sign-in"></i>
							</button>

							<div class="text-center mt-4">
								<p class="register-link">
									{{ __("Don't have an account?") }}
									<a href="{{ route('affiliate.register') }}">{{ __('Register here') }}</a>
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
	</script>
</body>

</html>