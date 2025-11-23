@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Clinic Details'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ __('Clinic Details') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.clinics') }}">{{ __('Clinics') }}</a></li>
                        <li class="breadcrumb-item active">{{ $clinic->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Clinic Information -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-hospital"></i> {{ __('Clinic Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($clinic->getFirstMediaUrl('clinic_images'))
                            <img src="{{ $clinic->getFirstMediaUrl('clinic_images') }}" alt="{{ $clinic->name }}" class="img-fluid rounded" style="max-height: 200px;">
                        @else
                            <div class="bg-light rounded p-5">
                                <i class="fas fa-hospital fa-5x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">{{ __('ID') }}:</th>
                            <td>{{ $clinic->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Name') }}:</th>
                            <td>{{ $clinic->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}:</th>
                            <td>{{ $clinic->clinic_email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Phone') }}:</th>
                            <td>{{ $clinic->phone }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Address') }}:</th>
                            <td>{{ $clinic->address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Allowed') }}:</th>
                            <td>
                                @if($clinic->is_allowed)
                                    <span class="badge badge-success">{{ __('Yes') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ __('No') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}:</th>
                            <td>
                                @if($clinic->status)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Created At') }}:</th>
                            <td>{{ $clinic->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Clinic Users -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> {{ __('Clinic Users') }} ({{ $clinic->clinicUsers->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($clinic->clinicUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Role') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Joined') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clinic->clinicUsers as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->has_clinic)
                                                    <span class="badge badge-primary">{{ __('Admin') }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ __('Doctor') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('No users found for this clinic.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Doctor Profiles -->
            @php
                $doctorProfiles = $clinic->clinicUsers->filter(function($user) {
                    return $user->doctorProfile !== null;
                })->map(function($user) {
                    return $user->doctorProfile;
                });
            @endphp
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-md"></i> {{ __('Doctor Profiles') }} ({{ $doctorProfiles->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($doctorProfiles->count() > 0)
                        <div class="row">
                            @foreach($doctorProfiles as $doctor)
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
                                                        @if($doctor->status == 'approved')
                                                            <span class="badge badge-success badge-sm">{{ __('Approved') }}</span>
                                                        @elseif($doctor->status == 'pending')
                                                            <span class="badge badge-warning badge-sm">{{ __('Pending') }}</span>
                                                        @elseif($doctor->status == 'rejected')
                                                            <span class="badge badge-danger badge-sm">{{ __('Rejected') }}</span>
                                                        @else
                                                            <span class="badge badge-secondary badge-sm">{{ ucfirst($doctor->status) }}</span>
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
                        <p class="text-muted mb-0">{{ __('No doctor profiles found for this clinic.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.users-management.clinics') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Clinics') }}
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-sm {
        font-size: 0.75rem;
    }
</style>
@endpush
