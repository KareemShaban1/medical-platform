<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">{{ __('Change Password') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="changePasswordForm">
                @csrf
                <input type="hidden" id="change_user_id" name="user_id">
                <input type="hidden" id="change_user_type" name="user_type">

                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span id="passwordChangeInfo"></span>
                    </div>

                    <div class="form-group">
                        <label for="new_password">{{ __('New Password') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ __('Minimum 8 characters') }}</small>
                        <div class="invalid-feedback" id="new_password_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="confirm_password_error"></div>
                    </div>

                    <div id="passwordStrength" class="mt-2">
                        <small class="text-muted">{{ __('Password Strength:') }}</small>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" id="strengthBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small id="strengthText" class="text-muted"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="savePasswordBtn">
                        <i class="fas fa-save"></i> {{ __('Change Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#toggleNewPassword').click(function() {
        const input = $('#new_password');
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#toggleConfirmPassword').click(function() {
        const input = $('#confirm_password');
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password strength meter
    $('#new_password').on('keyup', function() {
        const password = $(this).val();
        let strength = 0;
        let text = '';
        let color = '';

        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]+/)) strength += 25;
        if (password.match(/[A-Z]+/)) strength += 25;
        if (password.match(/[0-9]+/)) strength += 15;
        if (password.match(/[@$!%*?&#]+/)) strength += 10;

        if (strength < 40) {
            text = '{{ __("Weak") }}';
            color = 'bg-danger';
        } else if (strength < 70) {
            text = '{{ __("Medium") }}';
            color = 'bg-warning';
        } else {
            text = '{{ __("Strong") }}';
            color = 'bg-success';
        }

        $('#strengthBar').css('width', strength + '%').removeClass('bg-danger bg-warning bg-success').addClass(color);
        $('#strengthText').text(text);
    });

    // Form submission
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Validate passwords match
        if ($('#new_password').val() !== $('#confirm_password').val()) {
            $('#confirm_password').addClass('is-invalid');
            $('#confirm_password_error').text('{{ __("Passwords do not match") }}');
            return;
        }

        const formData = $(this).serialize();
        const submitBtn = $('#savePasswordBtn');

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("Changing...") }}');

        $.ajax({
            url: '{{ route("admin.users-management.change-password") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#changePasswordModal').modal('hide');
                    $('#changePasswordForm')[0].reset();
                    $('#strengthBar').css('width', '0%');
                    $('#strengthText').text('');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    if (errors.new_password) {
                        $('#new_password').addClass('is-invalid');
                        $('#new_password_error').text(errors.new_password[0]);
                    }

                    if (errors.confirm_password) {
                        $('#confirm_password').addClass('is-invalid');
                        $('#confirm_password_error').text(errors.confirm_password[0]);
                    }
                } else {
                    toastr.error('{{ __("An error occurred. Please try again.") }}');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> {{ __("Change Password") }}');
            }
        });
    });

    // Clear form on modal close
    $('#changePasswordModal').on('hidden.bs.modal', function() {
        $('#changePasswordForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#strengthBar').css('width', '0%');
        $('#strengthText').text('');

        // Reset password visibility
        $('#new_password, #confirm_password').attr('type', 'password');
        $('#toggleNewPassword i, #toggleConfirmPassword i').removeClass('fa-eye-slash').addClass('fa-eye');
    });
});

// Global function to open password change modal
function openChangePasswordModal(userId, userType, userName) {
    $('#change_user_id').val(userId);
    $('#change_user_type').val(userType);

    let userTypeText = '';
    switch(userType) {
        case 'user':
            userTypeText = '{{ __("Patient") }}';
            break;
        case 'clinic_user':
            userTypeText = '{{ __("Clinic User") }}';
            break;
        case 'supplier_user':
            userTypeText = '{{ __("Supplier User") }}';
            break;
        case 'doctor_profile':
            userTypeText = '{{ __("Doctor") }}';
            break;
    }

    $('#passwordChangeInfo').html('{{ __("You are changing the password for") }} <strong>' + userTypeText + ': ' + userName + '</strong>');
    $('#changePasswordModal').modal('show');
}
</script>
