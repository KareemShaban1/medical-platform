@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Clinic User Details'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ __('Clinic User Details') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.clinic-users') }}">{{ __('Clinic Users') }}</a></li>
                        <li class="breadcrumb-item active">{{ $clinicUser->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Clinic User Information -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-cog"></i> {{ __('Clinic User Information') }}</h5>
                    <button type="button" class="btn btn-sm btn-light" onclick="openChangePasswordModal({{ $clinicUser->id }}, 'clinic_user', '{{ str_replace("'", "\\'", $clinicUser->name) }}')">
                        <i class="fas fa-lock"></i> {{ __('Change Password') }}
                    </button>
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
                            <th>{{ __('Role') }}:</th>
                            <td>
                                @if($clinicUser->has_clinic)
                                    <span class="badge badge-danger">{{ __('Clinic Admin') }}</span>
                                @elseif($clinicUser->doctorProfile)
                                    <span class="badge badge-primary">{{ __('Doctor') }}</span>
                                @else
                                    <span class="badge badge-info">{{ __('Staff') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Account Status') }}:</th>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($clinicUser->is_active)
                                        <span class="badge badge-success mr-2">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-secondary mr-2">{{ __('Inactive') }}</span>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-primary toggle-account-status"
                                            data-user-id="{{ $clinicUser->id }}"
                                            data-user-type="clinic_user"
                                            data-current-status="{{ $clinicUser->is_active ? 1 : 0 }}">
                                        <i class="fas fa-toggle-on"></i> {{ $clinicUser->is_active ? __('Deactivate') : __('Activate') }}
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
            <!-- Associated Clinic -->
            @if($clinicUser->clinic)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-hospital"></i> {{ __('Associated Clinic') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            @if($clinicUser->clinic->getFirstMediaUrl('clinic_images'))
                                <img src="{{ $clinicUser->clinic->getFirstMediaUrl('clinic_images') }}" alt="{{ $clinicUser->clinic->name }}" class="img-fluid rounded" style="max-width: 100px;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                                    <i class="fas fa-hospital fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h5>{{ $clinicUser->clinic->name }}</h5>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th width="30%">{{ __('Email') }}:</th>
                                    <td>{{ $clinicUser->clinic->clinic_email ?? __('N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Phone') }}:</th>
                                    <td>{{ $clinicUser->clinic->phone ?? __('N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address') }}:</th>
                                    <td>{{ $clinicUser->clinic->address ?? __('N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('City') }}:</th>
                                    <td>{{ $clinicUser->clinic->city->name ?? __('N/A') }}</td>
                                </tr>
                            </table>
                            <div class="mt-3">
                                <a href="{{ route('admin.users-management.clinic-details', $clinicUser->clinic->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> {{ __('View Clinic Details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Doctor Profile (if applicable) -->
            @if($clinicUser->doctorProfile)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-md"></i> {{ __('Doctor Profile') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="30%">{{ __('Speciality') }}:</th>
                            <td>
                                @if($clinicUser->doctorProfile->speciality)
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
                            <th>{{ __('Status') }}:</th>
                            <td>
                                @if($clinicUser->doctorProfile->status == 'approved')
                                    <span class="badge badge-success">{{ __('Approved') }}</span>
                                @elseif($clinicUser->doctorProfile->status == 'pending')
                                    <span class="badge badge-warning">{{ __('Pending') }}</span>
                                @elseif($clinicUser->doctorProfile->status == 'rejected')
                                    <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($clinicUser->doctorProfile->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Bio') }}:</th>
                            <td>{{ $clinicUser->doctorProfile->bio ?? __('N/A') }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="{{ route('admin.users-management.doctor-profile-details', $clinicUser->doctorProfile->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> {{ __('View Full Doctor Profile') }}
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Doctor's Patients -->
            @if($clinicUser->doctorProfile->patients->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-users"></i> {{ __('Patients') }} ({{ $clinicUser->doctorProfile->patients->count() }})</h5>
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
                                @foreach($clinicUser->doctorProfile->patients as $patient)
                                <tr>
                                    <td>{{ $patient->id }}</td>
                                    <td>{{ $patient->name }}</td>
                                    <td>{{ $patient->email }}</td>
                                    <td>{{ $patient->phone ?? __('N/A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.users-management.patient-details', $patient->id) }}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
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
