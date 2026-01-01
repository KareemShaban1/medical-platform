<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ __('Forgot Password - Supplier') }}</title>
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
                <!-- Image Section -->
                <div class="col-lg-5">
                    <div class="form-image">
                        <div class="form-image-content">
                            <i class="fa fa-lock"></i>
                            <h3>{{ __('Reset Password') }}</h3>
                            <p>{{ __('Enter your email to receive a verification code') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="col-lg-7">
                    <div class="form-content">
                        <!-- Step 1: Email Form -->
                        <div id="step1" class="step-content">
                            <div class="login-header">
                                <h4>{{ __('Forgot Password') }}</h4>
                                <p>{{ __('Enter your registered email address') }}</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">{{ __('Email') }}</label>
                                <input type="email" id="email" class="form-control" required>
                                <div class="validation-feedback" id="email_feedback"></div>
                            </div>

                            <button type="button" class="btn btn-primary w-100" id="sendOtpBtn">
                                {{ __('Send Verification Code') }}
                                <i class="fa fa-paper-plane"></i>
                            </button>

                            <div class="text-center mt-4">
                                <a href="{{ url('/supplier/login') }}" class="btn btn-link">
                                    <i class="fa fa-arrow-left"></i>
                                    {{ __('Back to Login') }}
                                </a>
                            </div>
                        </div>

                        <!-- Step 2: OTP Verification -->
                        <div id="step2" class="step-content" style="display: none;">
                            <div class="login-header">
                                <h4>{{ __('Verify Code') }}</h4>
                                <p>{{ __('Enter the 6-digit code sent to your email') }}</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">{{ __('Verification Code') }}</label>
                                <input type="text" id="otp" class="form-control text-center" maxlength="6"
                                    style="letter-spacing: 8px; font-size: 24px; font-weight: bold;" required>
                                <div class="validation-feedback" id="otp_feedback"></div>
                            </div>

                            <button type="button" class="btn btn-primary w-100" id="verifyOtpBtn">
                                {{ __('Verify Code') }}
                                <i class="fa fa-check"></i>
                            </button>

                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-link" id="resendOtpBtn">
                                    {{ __('Resend Code') }}
                                </button>
                            </div>

                            <div class="text-center mt-2">
                                <button type="button" class="btn btn-link" id="backToStep1">
                                    <i class="fa fa-arrow-left"></i>
                                    {{ __('Change Email') }}
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Reset Password -->
                        <div id="step3" class="step-content" style="display: none;">
                            <div class="login-header">
                                <h4>{{ __('Set New Password') }}</h4>
                                <p>{{ __('Create a strong password for your account') }}</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">{{ __('New Password') }}</label>
                                <div class="input-group position-relative">
                                    <input type="password" id="password" class="form-control"
                                        style="padding-right: 45px; border-radius: 0.375rem;" required>
                                    <button type="button" id="passwordToggle"
                                        style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%); border: none; background: transparent; color: #6c757d; z-index: 1000; padding: 4px; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                                        <i class="fa fa-eye" style="font-size: 14px;"></i>
                                    </button>
                                </div>
                                <div class="validation-feedback" id="password_feedback"></div>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">{{ __('Confirm Password') }}</label>
                                <div class="input-group position-relative">
                                    <input type="password" id="password_confirmation" class="form-control"
                                        style="padding-right: 45px; border-radius: 0.375rem;" required>
                                    <button type="button" id="confirmPasswordToggle"
                                        style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%); border: none; background: transparent; color: #6c757d; z-index: 1000; padding: 4px; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                                        <i class="fa fa-eye" style="font-size: 14px;"></i>
                                    </button>
                                </div>
                                <div class="validation-feedback" id="password_confirmation_feedback"></div>
                            </div>

                            <button type="button" class="btn btn-primary w-100" id="resetPasswordBtn">
                                {{ __('Reset Password') }}
                                <i class="fa fa-save"></i>
                            </button>
                        </div>
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
        let userId = null;
        let resetToken = null;

        $(document).ready(function () {
            // Password toggle functionality
            $('#passwordToggle').on('click', function () {
                togglePassword('#password', $(this).find('i'));
            });

            $('#confirmPasswordToggle').on('click', function () {
                togglePassword('#password_confirmation', $(this).find('i'));
            });

            function togglePassword(fieldSelector, icon) {
                const field = $(fieldSelector);
                if (field.attr('type') === 'password') {
                    field.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    field.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            }

            // Step 1: Send OTP
            $('#sendOtpBtn').on('click', function () {
                const email = $('#email').val().trim();

                if (!email) {
                    showError('email', '{{ __("Email is required") }}');
                    return;
                }

                if (!isValidEmail(email)) {
                    showError('email', '{{ __("Please enter a valid email address") }}');
                    return;
                }

                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Sending...") }}');

                $.ajax({
                    url: '{{ route("supplier.forgot-password.send") }}',
                    type: 'POST',
                    data: {
                        email: email,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            userId = response.user_id;
                            toastr.success(response.message);
                            $('#step1').hide();
                            $('#step2').show();
                        }
                    },
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response.message || '{{ __("An error occurred") }}');
                    },
                    complete: function () {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Step 2: Verify OTP
            $('#verifyOtpBtn').on('click', function () {
                const otp = $('#otp').val().trim();

                if (!otp || otp.length !== 6) {
                    showError('otp', '{{ __("Please enter the 6-digit code") }}');
                    return;
                }

                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Verifying...") }}');

                $.ajax({
                    url: '{{ route("supplier.forgot-password.verify") }}',
                    type: 'POST',
                    data: {
                        user_id: userId,
                        otp: otp,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            resetToken = response.reset_token;
                            toastr.success(response.message);
                            $('#step2').hide();
                            $('#step3').show();
                        }
                    },
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response.message || '{{ __("Invalid code") }}');
                    },
                    complete: function () {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Resend OTP
            $('#resendOtpBtn').on('click', function () {
                const btn = $(this);
                btn.prop('disabled', true).text('{{ __("Sending...") }}');

                $.ajax({
                    url: '{{ route("supplier.forgot-password.resend") }}',
                    type: 'POST',
                    data: {
                        user_id: userId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        toastr.success(response.message);
                    },
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response.message || '{{ __("Failed to resend code") }}');
                    },
                    complete: function () {
                        btn.prop('disabled', false).text('{{ __("Resend Code") }}');
                    }
                });
            });

            // Back to Step 1
            $('#backToStep1').on('click', function () {
                $('#step2').hide();
                $('#step1').show();
                $('#otp').val('');
            });

            // Step 3: Reset Password
            $('#resetPasswordBtn').on('click', function () {
                const password = $('#password').val();
                const passwordConfirmation = $('#password_confirmation').val();

                // Validate password
                if (!password || password.length < 8) {
                    showError('password', '{{ __("Password must be at least 8 characters") }}');
                    return;
                }

                if (password !== passwordConfirmation) {
                    showError('password_confirmation', '{{ __("Passwords do not match") }}');
                    return;
                }

                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Resetting...") }}');

                $.ajax({
                    url: '{{ route("supplier.forgot-password.reset") }}',
                    type: 'POST',
                    data: {
                        reset_token: resetToken,
                        password: password,
                        password_confirmation: passwordConfirmation,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 1500);
                        }
                    },
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response.message || '{{ __("Failed to reset password") }}');
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Helper functions
            function showError(fieldName, message) {
                $(`#${fieldName}`).addClass('is-invalid');
                $(`#${fieldName}_feedback`).addClass('invalid').text(message);
                toastr.error(message);
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Clear errors on input
            $('input').on('input', function () {
                $(this).removeClass('is-invalid');
                $(`#${$(this).attr('id')}_feedback`).removeClass('invalid').text('');
            });
        });
    </script>

</body>

</html>