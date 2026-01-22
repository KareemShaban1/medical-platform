@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Standalone Doctor Details'))

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ __('Standalone Doctor Details') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.users-management.standalone-doctors') }}">{{ __('Standalone Doctors') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $clinicUser->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Information -->
        <div class="row mb-4">
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-md"></i> {{ __('Doctor Information') }}</h5>
                        <button type="button" class="btn btn-sm btn-light"
                            onclick="openChangePasswordModal({{ $clinicUser->id }}, 'clinic_user', '{{ str_replace("'", "\\'", $clinicUser->name) }}')">
                            <i class="fas fa-lock"></i> {{ __('Change Password') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                style="width: 150px; height: 150px;">
                                <i class="fas fa-user-md fa-5x text-info"></i>
                            </div>
                        </div>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">{{ __('ID') }}:</th>
                                <td>{{ $clinicUser->id }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Name') }}:</th>
                                <td>{{ $clinicUser->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Email') }}:</th>
                                <td>{{ $clinicUser->email }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Phone') }}:</th>
                                <td>{{ $clinicUser->phone ?? __('N/A') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Type') }}:</th>
                                <td>
                                    <span class="badge badge-info">{{ __('Standalone Doctor') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Account Status') }}:</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($clinicUser->status)
                                            <span class="badge badge-success mr-2">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-secondary mr-2">{{ __('Inactive') }}</span>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-primary toggle-account-status"
                                            data-user-id="{{ $clinicUser->id }}" data-user-type="clinic_user"
                                            data-current-status="{{ $clinicUser->status ? 1 : 0 }}">
                                            <i class="fas fa-toggle-on"></i>
                                            {{ $clinicUser->status ? __('Deactivate') : __('Activate') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Registered At') }}:</th>
                                <td>{{ $clinicUser->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Standalone Notice -->
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>{{ __('Standalone Doctor') }}:</strong>
                    {{ __('This doctor is not associated with any clinic and operates independently.') }}
                </div>

                <!-- Doctor Profile (if applicable) -->
                @if ($clinicUser->doctorProfile)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-id-card"></i> {{ __('Doctor Profile') }}</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">{{ __('Speciality') }}:</th>
                                    <td>
                                        @if ($clinicUser->doctorProfile->speciality)
                                            {{ $clinicUser->doctorProfile->speciality->name_en }}
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Years of Experience') }}:</th>
                                    <td>{{ $clinicUser->doctorProfile->years_experience ?? 0 }} {{ __('years') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Approval Status') }}:</th>
                                    <td>
                                        @if ($clinicUser->doctorProfile->status == 'approved')
                                            <span class="badge badge-success">{{ __('Approved') }}</span>
                                        @elseif($clinicUser->doctorProfile->status == 'pending')
                                            <span class="badge badge-warning">{{ __('Pending') }}</span>
                                        @elseif($clinicUser->doctorProfile->status == 'rejected')
                                            <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                        @else
                                            <span
                                                class="badge badge-secondary">{{ ucfirst($clinicUser->doctorProfile->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Bio') }}:</th>
                                    <td>{{ $clinicUser->doctorProfile->bio ?? __('N/A') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <a href="{{ route('admin.users-management.doctor-profile-details', $clinicUser->doctorProfile->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> {{ __('View Full Doctor Profile') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Doctor's Patients -->
                    @if ($clinicUser->doctorProfile->patients->count() > 0)
                        <div class="card shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-users"></i> {{ __('Patients') }}
                                    ({{ $clinicUser->doctorProfile->patients->count() }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th>{{ __('Phone') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clinicUser->doctorProfile->patients as $patient)
                                                <tr>
                                                    <td>{{ $patient->id }}</td>
                                                    <td>{{ $patient->name }}</td>
                                                    <td>{{ $patient->email }}</td>
                                                    <td>{{ $patient->phone ?? __('N/A') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.users-management.patient-details', $patient->id) }}"
                                                            class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> {{ __('No Doctor Profile') }}
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <p class="text-muted mb-0">{{ __('This user does not have a doctor profile created yet.') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Include Password Change Modal -->
    @include('backend.dashboards.admin.components.change-password-modal')

    <script>
        $(document).ready(function() {
            // Toggle account status
            $('.toggle-account-status').click(function() {
                const btn = $(this);
                const userId = btn.data('user-id');
                const userType = btn.data('user-type');
                const currentStatus = btn.data('current-status');
                const newStatus = currentStatus ? 0 : 1;
                const action = newStatus ? '{{ __('activate') }}' : '{{ __('deactivate') }}';

                if (!confirm('{{ __('Are you sure you want to') }} ' + action +
                        ' {{ __('this account?') }}')) {
                    return;
                }

                btn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> {{ __('Processing...') }}');

                $.ajax({
                    url: '{{ route('admin.users-management.toggle-status') }}',
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
                            badge.removeClass('badge-secondary').addClass('badge-success').text(
                                '{{ __('Active') }}');
                            btn.html(
                            '<i class="fas fa-toggle-on"></i> {{ __('Deactivate') }}');
                        } else {
                            badge.removeClass('badge-success').addClass('badge-secondary').text(
                                '{{ __('Inactive') }}');
                            btn.html('<i class="fas fa-toggle-on"></i> {{ __('Activate') }}');
                        }
                        btn.data('current-status', newStatus);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Success') }}',
                                text: response.message ||
                                    '{{ __('Account status updated successfully') }}',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ __('An error occurred') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('{{ __('Error') }}', errorMessage, 'error');
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
