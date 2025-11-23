@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Users Management Overview'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ __('Users Management Overview') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Users Management') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Clinics Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="fas fa-clinic-medical text-primary"></i> {{ __('Clinic Users') }}</h5>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Total Clinics') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['clinics']['total'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-hospital fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.users-management.clinics') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-eye"></i> {{ __('View Details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Active Clinics') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['clinics']['active'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">{{ number_format(($stats['clinics']['active'] / max($stats['clinics']['total'], 1)) * 100, 1) }}% of total</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Inactive Clinics') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['clinics']['inactive'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">{{ number_format(($stats['clinics']['inactive'] / max($stats['clinics']['total'], 1)) * 100, 1) }}% of total</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Total Clinic Users') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['clinic_users']['total'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">{{ __('Including admins & doctors') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patients Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="fas fa-user-injured text-success"></i> {{ __('Patients') }}</h5>
        </div>
        <div class="col-xl-12 col-md-12">
            <div class="card bg-gradient-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Total Patients') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['patients']['total'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-procedures fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.users-management.patients') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-eye"></i> {{ __('View Details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctor Profiles Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="fas fa-user-md text-info"></i> {{ __('Doctor Profiles') }}</h5>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Total Doctors') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['doctor_profiles']['total'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-stethoscope fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.users-management.doctor-profiles') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-eye"></i> {{ __('View Details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Approved') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['doctor_profiles']['approved'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-check-double fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">{{ number_format(($stats['doctor_profiles']['approved'] / max($stats['doctor_profiles']['total'], 1)) * 100, 1) }}% of total</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Pending') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['doctor_profiles']['pending'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">{{ __('Awaiting approval') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Clinic-Based') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['doctor_profiles']['clinic_based'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-building fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">{{ __('Standalone:') }} {{ $stats['doctor_profiles']['standalone'] }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suppliers Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="fas fa-truck text-warning"></i> {{ __('Suppliers') }}</h5>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Total Suppliers') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['suppliers']['total'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-warehouse fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.users-management.suppliers') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-eye"></i> {{ __('View Details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Active Suppliers') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['suppliers']['active'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">{{ number_format(($stats['suppliers']['active'] / max($stats['suppliers']['total'], 1)) * 100, 1) }}% of total</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-secondary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Inactive Suppliers') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['suppliers']['inactive'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-ban fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">{{ number_format(($stats['suppliers']['inactive'] / max($stats['suppliers']['total'], 1)) * 100, 1) }}% of total</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">{{ __('Supplier Users') }}</h6>
                            <h2 class="mb-0 text-white">{{ $stats['supplier_users']['total'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-user-tie fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">{{ __('Total authorized users') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> {{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.users-management.clinics') }}" class="btn btn-outline-primary btn-block mb-2">
                                <i class="fas fa-hospital"></i> {{ __('Manage Clinics') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.users-management.patients') }}" class="btn btn-outline-success btn-block mb-2">
                                <i class="fas fa-user-injured"></i> {{ __('Manage Patients') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.users-management.doctor-profiles') }}" class="btn btn-outline-info btn-block mb-2">
                                <i class="fas fa-user-md"></i> {{ __('Manage Doctors') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.users-management.suppliers') }}" class="btn btn-outline-warning btn-block mb-2">
                                <i class="fas fa-truck"></i> {{ __('Manage Suppliers') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .opacity-50 {
        opacity: 0.5;
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    }
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush
