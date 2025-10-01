<!DOCTYPE html>
<html lang="en">

<head>
    <title>Patient Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="{{asset('auth/vendor/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css"
        href="{{asset('auth/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
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
                <!-- Image Section -->
                <div class="col-lg-5">
                    <div class="form-image">
                        <div class="form-image-content">
                            <i class="fa fa-user-md"></i>
                            <h3>{{ __('Welcome Back') }}</h3>
                            <p>{{ __('Sign in to your patient account and manage your healthcare') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="col-lg-7">
                    <div class="form-content">
                        <form method="POST" action="{{ route('login') }}" id="patientLoginForm">
                            @csrf

                            <div class="login-header">
                                <h4>{{ __('Patient Login') }}</h4>
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
                                    <button type="button" id="passwordToggle"
                                        style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%); border: none; background: transparent; color: #6c757d; z-index: 1000; padding: 4px; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
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

                            <div class="form-group d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                                <a href="{{ route('password.request') }}" class="forgot-password">
                                    {{ __('Forgot Password?') }}
                                </a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
                                {{ __('Sign In') }}
                                <i class="fa fa-sign-in"></i>
                            </button>

                            <div class="divider">
                                <span>{{ __('or') }}</span>
                            </div>

                            <div class="register-link">
                                <p>{{ __("Don't have an account?") }}
                                    <a href="{{ route('register') }}">{{ __('Register here') }}</a>
                                </p>
                            </div>

                            <div class="back-to-home">
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

    <!-- Scripts -->
    <script src="{{asset('auth/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
    <script src="{{asset('auth/vendor/bootstrap/js/popper.js')}}"></script>
    <script src="{{asset('auth/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Password toggle functionality
            $('#passwordToggle').on('click', function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Form validation
            $('#patientLoginForm').on('submit', function(e) {
                let isValid = true;

                // Clear previous feedback
                $('.validation-feedback').removeClass('invalid valid').text('');

                // Email validation
                const email = $('#email').val();
                if (!email) {
                    showFieldError('email', '{{ __("Email is required") }}');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showFieldError('email', '{{ __("Please enter a valid email address") }}');
                    isValid = false;
                }

                // Password validation
                const password = $('#password').val();
                if (!password) {
                    showFieldError('password', '{{ __("Password is required") }}');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            function showFieldError(fieldName, message) {
                const feedback = $(`#${fieldName}_feedback`);
                feedback.addClass('invalid').text(message);
                $(`#${fieldName}`).addClass('is-invalid');
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
        });

        // Display toastr notifications for Laravel session messages
        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif

        @if(session('warning'))
            toastr.warning('{{ session('warning') }}');
        @endif

        @if(session('info'))
            toastr.info('{{ session('info') }}');
        @endif
    </script>
</body>

</html>
