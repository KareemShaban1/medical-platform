@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Doctor Profile Details'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ __('Doctor Profile Details') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.doctor-profiles') }}">{{ __('Doctor Profiles') }}</a></li>
                        <li class="breadcrumb-item active">{{ $doctor->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctor Information -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-md"></i> {{ __('Doctor Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($doctor->getFirstMediaUrl('doctor_profiles'))
                            <img src="{{ $doctor->getFirstMediaUrl('doctor_profiles') }}" alt="{{ $doctor->name }}" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                <i class="fas fa-user-md fa-5x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">{{ __('ID') }}:</th>
                            <td>{{ $doctor->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Name') }}:</th>
                            <td>{{ $doctor->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Speciality') }}:</th>
                            <td><span class="badge badge-primary">{{ $doctor->speciality }}</span></td>
                        </tr>
                        <tr>
                            <th>{{ __('Type') }}:</th>
                            <td>
                                @if($doctor->clinic)
                                    <span class="badge badge-info">{{ __('Clinic-Based') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Standalone') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Approval') }}:</th>
                            <td>
                                @if($doctor->is_approved)
                                    <span class="badge badge-success">{{ __('Approved') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ __('Pending') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}:</th>
                            <td>
                                @if($doctor->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Created At') }}:</th>
                            <td>{{ $doctor->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Clinic Information -->
            @if($doctor->clinic)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-hospital"></i> {{ __('Associated Clinic') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if($doctor->clinic->getFirstMediaUrl('clinics'))
                                <img src="{{ $doctor->clinic->getFirstMediaUrl('clinics') }}" alt="{{ $doctor->clinic->name }}" class="img-fluid rounded" style="max-height: 150px;">
                            @else
                                <div class="bg-light rounded p-4">
                                    <i class="fas fa-hospital fa-4x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <table class="table table-sm mb-0">
                            <tr>
                                <th width="40%">{{ __('Name') }}:</th>
                                <td>{{ $doctor->clinic->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Email') }}:</th>
                                <td>{{ $doctor->clinic->email }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Phone') }}:</th>
                                <td>{{ $doctor->clinic->phone }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Status') }}:</th>
                                <td>
                                    @if($doctor->clinic->is_active)
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Authentication Account -->
            @if($doctor->clinicUser)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-key"></i> {{ __('Authentication Account') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <th width="40%">{{ __('Email') }}:</th>
                                <td>{{ $doctor->clinicUser->email }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Name') }}:</th>
                                <td>{{ $doctor->clinicUser->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Status') }}:</th>
                                <td>
                                    @if($doctor->clinicUser->is_active)
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <!-- Doctor Biography -->
            @if($doctor->bio)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-file-alt"></i> {{ __('Biography') }}</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $doctor->bio }}</p>
                    </div>
                </div>
            @endif

            <!-- Patients -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> {{ __('Assigned Patients') }} ({{ $doctor->patients->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($doctor->patients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Location') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doctor->patients as $patient)
                                        <tr>
                                            <td>{{ $patient->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($patient->getFirstMediaUrl('patients'))
                                                        <img src="{{ $patient->getFirstMediaUrl('patients') }}" alt="{{ $patient->name }}" class="rounded-circle mr-2" width="30" height="30">
                                                    @else
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 30px; height: 30px;">
                                                            <i class="fas fa-user text-muted" style="font-size: 0.8rem;"></i>
                                                        </div>
                                                    @endif
                                                    {{ $patient->name }}
                                                </div>
                                            </td>
                                            <td>{{ $patient->email }}</td>
                                            <td>{{ $patient->phone ?? __('N/A') }}</td>
                                            <td>
                                                @if($patient->governorate && $patient->city)
                                                    {{ $patient->governorate->name }}, {{ $patient->city->name }}
                                                @else
                                                    {{ __('N/A') }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('No patients assigned to this doctor yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.users-management.doctor-profiles') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Doctor Profiles') }}
            </a>
        </div>
    </div>
</div>
@endsection
