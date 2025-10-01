<!DOCTYPE html>
<html lang="en">

<head>
    <title>Patient Registration</title>
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
                            <h3>{{ __('Join Our Healthcare Community') }}</h3>
                            <p>{{ __('Create your patient account and get access to quality healthcare services') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="col-lg-7">
                    <div class="form-content">
                        <form method="POST" action="{{ route('register') }}" id="patientRegistrationForm">
                            @csrf

                            <div class="login-header">
                                <h4>{{ __('Patient Registration') }}</h4>
                                <p>{{ __('Please fill in your information to create an account') }}</p>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label required">{{ __('Full Name') }}</label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" required>
                                        <div class="validation-feedback" id="name_feedback"></div>
                                        @error('name')
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
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label required">{{ __('Phone') }}</label>
                                        <input type="text" name="phone" id="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}" required>
                                        <div class="validation-feedback" id="phone_feedback"></div>
                                        @error('phone')
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
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label required">{{ __('Confirm Password') }}</label>
                                        <div class="input-group position-relative">
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                style="padding-right: 45px; border-radius: 0.375rem;" required>
                                            <button type="button" id="passwordConfirmToggle"
                                                style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%); border: none; background: transparent; color: #6c757d; z-index: 1000; padding: 4px; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                                                <i class="fa fa-eye" style="font-size: 14px;"></i>
                                            </button>
                                        </div>
                                        <div class="validation-feedback" id="password_confirmation_feedback"></div>
                                        @error('password_confirmation')
                                        <div class="validation-feedback invalid">
                                            <i class="fa fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        {{ __('I agree to the') }}
                                        <a href="#" target="_blank">{{ __('Terms of Service') }}</a>
                                        {{ __('and') }}
                                        <a href="#" target="_blank">{{ __('Privacy Policy') }}</a>
                                    </label>
                                </div>
                                <div class="validation-feedback" id="terms_feedback"></div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" id="registerBtn">
                                {{ __('Create Account') }}
                                <i class="fa fa-check"></i>
                            </button>

                            <div class="divider">
                                <span>{{ __('or') }}</span>
                            </div>

                            <div class="register-link">
                                <p>{{ __("Already have an account?") }}
                                    <a href="{{ route('login') }}">{{ __('Sign in here') }}</a>
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
            $('#passwordToggle, #passwordConfirmToggle').on('click', function() {
                const isPasswordField = $(this).attr('id') === 'passwordToggle';
                const passwordField = isPasswordField ? $('#password') : $('#password_confirmation');
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
            $('#patientRegistrationForm').on('submit', function(e) {
                let isValid = true;

                // Clear previous feedback
                $('.validation-feedback').removeClass('invalid valid').text('');
                $('.form-control').removeClass('is-invalid is-valid');

                // Name validation
                const name = $('#name').val().trim();
                if (!name) {
                    showFieldError('name', '{{ __("Full name is required") }}');
                    isValid = false;
                } else if (name.length < 2) {
                    showFieldError('name', '{{ __("Name must be at least 2 characters") }}');
                    isValid = false;
                }

                // Email validation
                const email = $('#email').val().trim();
                if (!email) {
                    showFieldError('email', '{{ __("Email is required") }}');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showFieldError('email', '{{ __("Please enter a valid email address") }}');
                    isValid = false;
                }

                // Phone validation
                const phone = $('#phone').val().trim();
                if (!phone) {
                    showFieldError('phone', '{{ __("Phone number is required") }}');
                    isValid = false;
                } else if (phone.length < 10) {
                    showFieldError('phone', '{{ __("Please enter a valid phone number") }}');
                    isValid = false;
                }

                // Password validation
                const password = $('#password').val();
                if (!password) {
                    showFieldError('password', '{{ __("Password is required") }}');
                    isValid = false;
                } else if (password.length < 8) {
                    showFieldError('password', '{{ __("Password must be at least 8 characters") }}');
                    isValid = false;
                }

                // Password confirmation validation
                const passwordConfirmation = $('#password_confirmation').val();
                if (!passwordConfirmation) {
                    showFieldError('password_confirmation', '{{ __("Password confirmation is required") }}');
                    isValid = false;
                } else if (password !== passwordConfirmation) {
                    showFieldError('password_confirmation', '{{ __("Passwords do not match") }}');
                    isValid = false;
                }

                // Terms validation
                const terms = $('#terms').is(':checked');
                if (!terms) {
                    showFieldError('terms', '{{ __("You must agree to the terms and conditions") }}');
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
