@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.clinics.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Clinics') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Clinic Details') }} - {{ $clinic->name }}</h4>
            </div>
        </div>
    </div>

    <!-- Clinic Information -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Clinic Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th style="width: 150px;">{{ __('Name') }}:</th>
                                            <td>{{ $clinic->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Phone') }}:</th>
                                            <td>{{ $clinic->phone ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Address') }}:</th>
                                            <td>{{ $clinic->address ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Status') }}:</th>
                                            <td>
                                                @if($clinic->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Is Allowed') }}:</th>
                                            <td>
                                                @if($clinic->is_allowed)
                                                    <span class="badge bg-success">{{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('No') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Total Users') }}:</th>
                                            <td>
                                                <span class="badge bg-info">{{ $clinic->clinicUsers->count() }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Created At') }}:</th>
                                            <td>{{ $clinic->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Updated At') }}:</th>
                                            <td>{{ $clinic->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <h6>{{ __('Clinic Images') }}</h6>
                            @if($clinic->images && count($clinic->images) > 0)
                                <div class="row">
                                    @foreach($clinic->images as $image)
                                        <div class="col-6 mb-3">
                                            <img src="{{ $image }}" alt="Clinic Image" class="img-fluid rounded" style="max-height: 150px; object-fit: cover; width: 100%;">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">{{ __('No images available') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clinic Users -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Clinic Users') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="clinic-users-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Doctor Profile') }}</th>
                                    <th>{{ __('Specialties') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize clinic users DataTable
    $('#clinic-users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.clinics.users.data", $clinic->id) }}',
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user_name', name: 'name' },
            { data: 'user_email', name: 'email' },
            { data: 'user_phone', name: 'phone' },
            { data: 'user_status', name: 'status', orderable: false, searchable: false },
            { data: 'doctor_profile_status', name: 'doctor_profile_status', orderable: false, searchable: false },
            { data: 'doctor_specialties', name: 'doctor_specialties', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        responsive: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pageLength: 25
    });
});

// View user details function
function viewUser(userId) {
    // For now, just show an alert. You can implement a detailed view later.
    Swal.fire({
        title: 'User Details',
        text: 'User ID: ' + userId,
        icon: 'info',
        confirmButtonText: 'OK'
    });
}
</script>
@endpush
