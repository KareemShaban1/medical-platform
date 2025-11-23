@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Patient Details'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ __('Patient Details') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.patients') }}">{{ __('Patients') }}</a></li>
                        <li class="breadcrumb-item active">{{ $patient->name ?? '' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-injured"></i> {{ __('Patient Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-5x text-muted"></i>
                        </div>
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">{{ __('ID') }}:</th>
                            <td>{{ $patient->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Name') }}:</th>
                            <td>{{ $patient->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}:</th>
                            <td>{{ $patient->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Phone') }}:</th>
                            <td>{{ $patient->phone ?? __('N/A') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Date of Birth') }}:</th>
                            <td>{{ $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : __('N/A') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Gender') }}:</th>
                            <td>{{ $patient->gender ? __(ucfirst($patient->gender)) : __('N/A') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Registered At') }}:</th>
                            <td>{{ $patient->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Authentication Account -->
            @if($patient->user)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-key"></i> {{ __('User Account') }}</h5>
                        <button type="button" class="btn btn-sm btn-light" onclick="openChangePasswordModal({{ $patient->user->id }}, 'user', '{{ str_replace("'", "\\'", $patient->name) }}')">
                            <i class="fas fa-lock"></i> {{ __('Change Password') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <th width="40%">{{ __('Email') }}:</th>
                                <td>{{ $patient->user->email }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Account Status') }}:</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($patient->user->is_active ?? true)
                                            <span class="badge badge-success mr-2">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-secondary mr-2">{{ __('Inactive') }}</span>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-primary toggle-account-status"
                                                data-user-id="{{ $patient->user->id }}"
                                                data-user-type="user"
                                                data-current-status="{{ $patient->user->is_active ?? 1 }}">
                                            <i class="fas fa-toggle-on"></i> {{ ($patient->user->is_active ?? true) ? __('Deactivate') : __('Activate') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Location Information -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> {{ __('Location') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th width="40%">{{ __('Governorate') }}:</th>
                            <td>{{ $patient->governorate->name ?? __('N/A') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('City') }}:</th>
                            <td>{{ $patient->city->name ?? __('N/A') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Address') }}:</th>
                            <td>{{ $patient->address ?? __('N/A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Assigned Doctors -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-md"></i> {{ __('Assigned Doctors') }} ({{ $patient->doctors->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($patient->doctors->count() > 0)
                        <div class="row">
                            @foreach($patient->doctors as $doctor)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @if($doctor->getFirstMediaUrl('profile_photo'))
                                                        <img src="{{ $doctor->getFirstMediaUrl('profile_photo') }}" alt="{{ $doctor->name }}" class="rounded-circle" width="60" height="60">
                                                    @else
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                            <i class="fas fa-user-md fa-2x text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $doctor->name ?? 'N/A' }}</h6>
                                                    <p class="text-muted mb-1">
                                                        <small>
                                                            @if($doctor->speciality)
                                                                {{ $doctor->speciality->name_en }}
                                                            @else
                                                                {{ __('Speciality N/A') }}
                                                            @endif
                                                        </small>
                                                    </p>
                                                    <div>
                                                        @if($doctor->clinic)
                                                            <span class="badge badge-info badge-sm">{{ $doctor->clinic->name }}</span>
                                                        @else
                                                            <span class="badge badge-secondary badge-sm">{{ __('Standalone') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('No doctors assigned to this patient yet.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Associated Clinics -->
            @php
                $clinics = $patient->doctors->map(function($doctor) {
                    return $doctor->clinic;
                })->filter()->unique('id');
            @endphp
            @if($clinics->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-hospital"></i> {{ __('Associated Clinics') }} ({{ $clinics->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($clinics as $clinic)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @if($clinic->getFirstMediaUrl('clinic_images'))
                                                        <img src="{{ $clinic->getFirstMediaUrl('clinic_images') }}" alt="{{ $clinic->name }}" class="rounded" width="60" height="60">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                            <i class="fas fa-hospital fa-2x text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $clinic->name }}</h6>
                                                    <p class="text-muted mb-1"><small><i class="fas fa-envelope"></i> {{ $clinic->clinic_email ?? 'N/A' }}</small></p>
                                                    <p class="text-muted mb-0"><small><i class="fas fa-phone"></i> {{ $clinic->phone }}</small></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.users-management.patients') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Patients') }}
            </a>
        </div>
    </div>
</div>

<!-- Include Password Change Modal -->
@if($patient->user)
    @include('backend.dashboards.admin.components.change-password-modal')
@endif

<!-- OLD MODAL - TO BE REMOVED
<div class="modal fade" id="OLD_changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="fas fa-lock"></i> {{ __('Change Password') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="changePasswordForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ $patient->user->id }}">
                <input type="hidden" name="user_type" value="user">

                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> {{ __('Changing password for') }}: <strong>{{ $patient->user->email }}</strong>
                    </div>

                    <div class="form-group">
                        <label for="new_password">{{ __('New Password') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ __('Minimum 8 characters') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                    </div>

                    <div id="passwordError" class="alert alert-danger d-none"></div>
                    <div id="passwordSuccess" class="alert alert-success d-none"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> {{ __('Change Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const passwordField = $('#new_password');
        const icon = $(this).find('i');

        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Change password form submission
    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();

        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();
        const errorDiv = $('#passwordError');
        const successDiv = $('#passwordSuccess');

        errorDiv.addClass('d-none');
        successDiv.addClass('d-none');

        if (newPassword !== confirmPassword) {
            errorDiv.text('{{ __("Passwords do not match") }}').removeClass('d-none');
            return;
        }

        if (newPassword.length < 8) {
            errorDiv.text('{{ __("Password must be at least 8 characters") }}').removeClass('d-none');
            return;
        }

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}');

        $.ajax({
            url: '{{ route("admin.users-management.change-password") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                successDiv.text(response.message || '{{ __("Password changed successfully") }}').removeClass('d-none');
                $('#changePasswordForm')[0].reset();

                setTimeout(function() {
                    $('#changePasswordModal').modal('hide');
                    successDiv.addClass('d-none');
                }, 2000);
            },
            error: function(xhr) {
                let errorMessage = '{{ __("An error occurred") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                errorDiv.html(errorMessage).removeClass('d-none');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-check"></i> {{ __("Change Password") }}');
            }
        });
    });

    // Toggle account status
    $('.toggle-account-status').click(function() {
        const btn = $(this);
        const userId = btn.data('user-id');
        const userType = btn.data('user-type');
        const currentStatus = btn.data('current-status');
        const newStatus = currentStatus ? 0 : 1;
        const action = newStatus ? '{{ __("activate") }}' : '{{ __("deactivate") }}';

        if (!confirm('{{ __("Are you sure you want to") }} ' + action + ' {{ __("this account?") }}')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}');

        $.ajax({
            url: '{{ route("admin.users-management.toggle-status") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_id: userId,
                user_type: userType,
                status: newStatus
            },
            success: function(response) {
                const badge = btn.closest('td').find('.badge');
                if (newStatus) {
                    badge.removeClass('badge-secondary').addClass('badge-success').text('{{ __("Active") }}');
                    btn.html('<i class="fas fa-toggle-on"></i> {{ __("Deactivate") }}');
                } else {
                    badge.removeClass('badge-success').addClass('badge-secondary').text('{{ __("Inactive") }}');
                    btn.html('<i class="fas fa-toggle-on"></i> {{ __("Activate") }}');
                }
                btn.data('current-status', newStatus);

                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message || '{{ __("Account status updated successfully") }}');
                }
            },
            error: function(xhr) {
                let errorMessage = '{{ __("An error occurred") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });
});
</script>

@endsection

@push('styles')
<style>
    .badge-sm {
        font-size: 0.75rem;
    }
</style>
@endpush
