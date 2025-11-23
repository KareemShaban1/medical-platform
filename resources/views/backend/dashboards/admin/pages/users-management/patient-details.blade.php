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
                        <li class="breadcrumb-item active">{{ $patient->name }}</li>
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
                        @if($patient->getFirstMediaUrl('patients'))
                            <img src="{{ $patient->getFirstMediaUrl('patients') }}" alt="{{ $patient->name }}" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-5x text-muted"></i>
                            </div>
                        @endif
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
                                                    @if($doctor->getFirstMediaUrl('doctor_profiles'))
                                                        <img src="{{ $doctor->getFirstMediaUrl('doctor_profiles') }}" alt="{{ $doctor->name }}" class="rounded-circle" width="60" height="60">
                                                    @else
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                            <i class="fas fa-user-md fa-2x text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $doctor->name }}</h6>
                                                    <p class="text-muted mb-1"><small>{{ $doctor->speciality }}</small></p>
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
                                                    @if($clinic->getFirstMediaUrl('clinics'))
                                                        <img src="{{ $clinic->getFirstMediaUrl('clinics') }}" alt="{{ $clinic->name }}" class="rounded" width="60" height="60">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                            <i class="fas fa-hospital fa-2x text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $clinic->name }}</h6>
                                                    <p class="text-muted mb-1"><small><i class="fas fa-envelope"></i> {{ $clinic->email }}</small></p>
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
@endsection

@push('styles')
<style>
    .badge-sm {
        font-size: 0.75rem;
    }
</style>
@endpush
