@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Users Management Overview'))

@section('content')
<div class="container-fluid">
    <!-- Page Header with Gradient -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header-gradient p-4 rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white mb-2">
                            <i class="fas fa-chart-line"></i> {{ __('Users Management Analytics') }}
                        </h2>
                        <p class="text-white-50 mb-0">{{ __('Comprehensive overview of all system users and entities') }}</p>
                    </div>
                    <div class="text-right">
                        <div class="badge badge-light badge-lg">
                            <i class="fas fa-calendar-alt"></i> {{ now()->format('F d, Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Summary -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card metric-card shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small">{{ __('Total Entities') }}</p>
                            <h3 class="mb-0 font-weight-bold">
                                {{ $stats['clinics']['total'] + $stats['patients']['total'] + $stats['doctor_profiles']['total'] + $stats['suppliers']['total'] }}
                            </h3>
                        </div>
                        <div class="metric-icon bg-gradient-primary">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge badge-soft-primary">
                            <i class="fas fa-globe"></i> {{ __('System Wide') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card metric-card shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small">{{ __('Total Users') }}</p>
                            <h3 class="mb-0 font-weight-bold">
                                {{ $stats['clinic_users']['total'] + $stats['supplier_users']['total'] + $stats['patients']['total'] }}
                            </h3>
                        </div>
                        <div class="metric-icon bg-gradient-success">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge badge-soft-success">
                            <i class="fas fa-user-check"></i> {{ __('Active Accounts') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card metric-card shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small">{{ __('Active Clinics') }}</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['clinics']['active'] }}</h3>
                        </div>
                        <div class="metric-icon bg-gradient-info">
                            <i class="fas fa-hospital"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge badge-soft-info">
                            <i class="fas fa-arrow-up"></i> {{ number_format(($stats['clinics']['active'] / max($stats['clinics']['total'], 1)) * 100, 1) }}% {{ __('Active') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card metric-card shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small">{{ __('Approved Doctors') }}</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['doctor_profiles']['approved'] }}</h3>
                        </div>
                        <div class="metric-icon bg-gradient-warning">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge badge-soft-warning">
                            <i class="fas fa-check-circle"></i> {{ number_format(($stats['doctor_profiles']['approved'] / max($stats['doctor_profiles']['total'], 1)) * 100, 1) }}% {{ __('Approved') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Clinics Distribution Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-hospital text-primary"></i> {{ __('Clinics Distribution') }}
                        </h5>
                        <a href="{{ route('admin.users-management.clinics') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> {{ __('View All') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 280px;">
                        <canvas id="clinicsChart"></canvas>
                    </div>
                    <div class="row mt-3 text-center">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h4 class="mb-0 text-primary">{{ $stats['clinics']['total'] }}</h4>
                                <small class="text-muted">{{ __('Total') }}</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h4 class="mb-0 text-success">{{ $stats['clinics']['active'] }}</h4>
                                <small class="text-muted">{{ __('Active') }}</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h4 class="mb-0 text-warning">{{ $stats['clinics']['inactive'] }}</h4>
                                <small class="text-muted">{{ __('Inactive') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Profiles Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user-md text-info"></i> {{ __('Doctor Profiles Analytics') }}
                        </h5>
                        <a href="{{ route('admin.users-management.doctor-profiles') }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-eye"></i> {{ __('View All') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 280px;">
                        <canvas id="doctorsChart"></canvas>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="status-dot bg-success mr-2"></div>
                                <span class="text-muted small">{{ __('Approved:') }} <strong>{{ $stats['doctor_profiles']['approved'] }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="status-dot bg-warning mr-2"></div>
                                <span class="text-muted small">{{ __('Pending:') }} <strong>{{ $stats['doctor_profiles']['pending'] }}</strong></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="status-dot bg-info mr-2"></div>
                                <span class="text-muted small">{{ __('Clinic-Based:') }} <strong>{{ $stats['doctor_profiles']['clinic_based'] }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="status-dot bg-secondary mr-2"></div>
                                <span class="text-muted small">{{ __('Standalone:') }} <strong>{{ $stats['doctor_profiles']['standalone'] }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics Cards -->
    <div class="row mb-4">
        <!-- Clinics Section -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm stats-card h-100">
                <div class="card-body">
                    <div class="stats-header mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-clinic-medical text-primary"></i> {{ __('Clinic Ecosystem') }}
                            </h5>
                            <div class="stats-badge badge-primary">
                                <i class="fas fa-hospital"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stats-body">
                        <div class="stat-item">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="mb-1 text-muted small">{{ __('Total Clinics') }}</p>
                                    <h3 class="mb-0">{{ $stats['clinics']['total'] }}</h3>
                                </div>
                                <div class="progress-circle">
                                    <span class="text-primary font-weight-bold">100%</span>
                                </div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="mb-1 text-muted small">{{ __('Active Clinics') }}</p>
                                    <h4 class="mb-0 text-success">{{ $stats['clinics']['active'] }}</h4>
                                </div>
                                <div class="badge badge-soft-success">
                                    {{ number_format(($stats['clinics']['active'] / max($stats['clinics']['total'], 1)) * 100, 1) }}%
                                </div>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ ($stats['clinics']['active'] / max($stats['clinics']['total'], 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="stat-item mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="mb-1 text-muted small">{{ __('Clinic Users') }}</p>
                                    <h4 class="mb-0 text-info">{{ $stats['clinic_users']['total'] }}</h4>
                                </div>
                                <a href="{{ route('admin.users-management.clinic-users') }}" class="btn btn-sm btn-soft-info">
                                    <i class="fas fa-users"></i> {{ __('View') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 mt-3">
                        <a href="{{ route('admin.users-management.clinics') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-arrow-right"></i> {{ __('Manage All Clinics') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patients Section -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm stats-card h-100">
                <div class="card-body">
                    <div class="stats-header mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-user-injured text-success"></i> {{ __('Patient Database') }}
                            </h5>
                            <div class="stats-badge badge-success">
                                <i class="fas fa-procedures"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stats-body">
                        <div class="text-center mb-4">
                            <div class="patient-icon-large mb-3">
                                <i class="fas fa-users fa-4x text-success opacity-20"></i>
                            </div>
                            <h1 class="display-4 mb-0 text-success">{{ $stats['patients']['total'] }}</h1>
                            <p class="text-muted">{{ __('Total Registered Patients') }}</p>
                        </div>
                        <div class="alert alert-soft-success mb-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x mr-3"></i>
                                <div>
                                    <strong>{{ __('100% Active') }}</strong>
                                    <p class="mb-0 small">{{ __('All registered patients are active users') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 mt-3">
                        <a href="{{ route('admin.users-management.patients') }}" class="btn btn-success btn-block">
                            <i class="fas fa-arrow-right"></i> {{ __('View All Patients') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suppliers Section -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm stats-card h-100">
                <div class="card-body">
                    <div class="stats-header mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-truck text-warning"></i> {{ __('Supplier Network') }}
                            </h5>
                            <div class="stats-badge badge-warning">
                                <i class="fas fa-warehouse"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stats-body">
                        <div class="stat-item">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="mb-1 text-muted small">{{ __('Total Suppliers') }}</p>
                                    <h3 class="mb-0">{{ $stats['suppliers']['total'] }}</h3>
                                </div>
                                <div class="progress-circle">
                                    <span class="text-warning font-weight-bold">100%</span>
                                </div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="mb-1 text-muted small">{{ __('Active Suppliers') }}</p>
                                    <h4 class="mb-0 text-success">{{ $stats['suppliers']['active'] }}</h4>
                                </div>
                                <div class="badge badge-soft-success">
                                    {{ number_format(($stats['suppliers']['active'] / max($stats['suppliers']['total'], 1)) * 100, 1) }}%
                                </div>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ ($stats['suppliers']['active'] / max($stats['suppliers']['total'], 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="stat-item mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="mb-1 text-muted small">{{ __('Supplier Users') }}</p>
                                    <h4 class="mb-0 text-info">{{ $stats['supplier_users']['total'] }}</h4>
                                </div>
                                <a href="{{ route('admin.users-management.supplier-users') }}" class="btn btn-sm btn-soft-info">
                                    <i class="fas fa-users"></i> {{ __('View') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 mt-3">
                        <a href="{{ route('admin.users-management.suppliers') }}" class="btn btn-warning btn-block">
                            <i class="fas fa-arrow-right"></i> {{ __('Manage All Suppliers') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <!-- Quick Actions Grid -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt"></i> {{ __('Quick Access Panel') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.users-management.clinics') }}" class="quick-action-card">
                                <div class="card border-0 h-100 hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="quick-action-icon bg-soft-primary text-primary mb-3">
                                            <i class="fas fa-hospital fa-2x"></i>
                                        </div>
                                        <h6 class="mb-1">{{ __('Manage Clinics') }}</h6>
                                        <p class="text-muted small mb-0">{{ $stats['clinics']['total'] }} {{ __('Total') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.users-management.clinic-users') }}" class="quick-action-card">
                                <div class="card border-0 h-100 hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="quick-action-icon bg-soft-info text-info mb-3">
                                            <i class="fas fa-user-nurse fa-2x"></i>
                                        </div>
                                        <h6 class="mb-1">{{ __('Clinic Users') }}</h6>
                                        <p class="text-muted small mb-0">{{ $stats['clinic_users']['total'] }} {{ __('Users') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.users-management.patients') }}" class="quick-action-card">
                                <div class="card border-0 h-100 hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="quick-action-icon bg-soft-success text-success mb-3">
                                            <i class="fas fa-user-injured fa-2x"></i>
                                        </div>
                                        <h6 class="mb-1">{{ __('Manage Patients') }}</h6>
                                        <p class="text-muted small mb-0">{{ $stats['patients']['total'] }} {{ __('Patients') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.users-management.doctor-profiles') }}" class="quick-action-card">
                                <div class="card border-0 h-100 hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="quick-action-icon bg-soft-info text-info mb-3">
                                            <i class="fas fa-user-md fa-2x"></i>
                                        </div>
                                        <h6 class="mb-1">{{ __('Doctor Profiles') }}</h6>
                                        <p class="text-muted small mb-0">{{ $stats['doctor_profiles']['total'] }} {{ __('Doctors') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.users-management.suppliers') }}" class="quick-action-card">
                                <div class="card border-0 h-100 hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="quick-action-icon bg-soft-warning text-warning mb-3">
                                            <i class="fas fa-truck fa-2x"></i>
                                        </div>
                                        <h6 class="mb-1">{{ __('Manage Suppliers') }}</h6>
                                        <p class="text-muted small mb-0">{{ $stats['suppliers']['total'] }} {{ __('Suppliers') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.users-management.supplier-users') }}" class="quick-action-card">
                                <div class="card border-0 h-100 hover-shadow">
                                    <div class="card-body text-center">
                                        <div class="quick-action-icon bg-soft-warning text-warning mb-3">
                                            <i class="fas fa-user-tie fa-2x"></i>
                                        </div>
                                        <h6 class="mb-1">{{ __('Supplier Users') }}</h6>
                                        <p class="text-muted small mb-0">{{ $stats['supplier_users']['total'] }} {{ __('Users') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 h-100 bg-soft-secondary">
                                <div class="card-body text-center">
                                    <div class="quick-action-icon bg-soft-dark text-dark mb-3">
                                        <i class="fas fa-chart-pie fa-2x"></i>
                                    </div>
                                    <h6 class="mb-1">{{ __('Total Users') }}</h6>
                                    <p class="text-muted small mb-0">
                                        {{ $stats['clinic_users']['total'] + $stats['supplier_users']['total'] + $stats['patients']['total'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 h-100 bg-soft-secondary">
                                <div class="card-body text-center">
                                    <div class="quick-action-icon bg-soft-dark text-dark mb-3">
                                        <i class="fas fa-layer-group fa-2x"></i>
                                    </div>
                                    <h6 class="mb-1">{{ __('System Entities') }}</h6>
                                    <p class="text-muted small mb-0">
                                        {{ $stats['clinics']['total'] + $stats['suppliers']['total'] }}
                                    </p>
                                </div>
                            </div>
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
    /* Modern Page Header */
    .page-header-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    /* Metric Cards */
    .metric-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }

    .metric-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    }

    /* Badge Soft Colors */
    .badge-soft-primary {
        background-color: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .badge-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .badge-soft-info {
        background-color: rgba(23, 162, 184, 0.1);
        color: #17a2b8;
    }

    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .badge-soft-secondary {
        background-color: rgba(108, 117, 125, 0.1);
        color: #6c757d;
    }

    .badge-soft-dark {
        background-color: rgba(52, 58, 64, 0.1);
        color: #343a40;
    }

    /* Stats Cards */
    .stats-card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    }

    .stats-badge {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    .stats-badge.badge-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stats-badge.badge-success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
    }

    .stats-badge.badge-warning {
        background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .progress-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .opacity-20 {
        opacity: 0.2;
    }

    /* Alert Soft */
    .alert-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
        border: 1px solid rgba(40, 167, 69, 0.2);
        color: #28a745;
    }

    /* Quick Action Cards */
    .quick-action-card {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: all 0.3s ease;
    }

    .quick-action-card:hover {
        text-decoration: none;
        color: inherit;
    }

    .hover-shadow {
        transition: all 0.3s ease;
        border-radius: 10px;
    }

    .hover-shadow:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        transform: translateY(-3px);
    }

    .quick-action-icon {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .bg-soft-primary {
        background-color: rgba(102, 126, 234, 0.1);
    }

    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
    }

    .bg-soft-info {
        background-color: rgba(23, 162, 184, 0.1);
    }

    .bg-soft-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .bg-soft-secondary {
        background-color: rgba(108, 117, 125, 0.05);
    }

    .shadow-hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    /* Button Soft */
    .btn-soft-info {
        background-color: rgba(23, 162, 184, 0.1);
        color: #17a2b8;
        border: none;
    }

    .btn-soft-info:hover {
        background-color: rgba(23, 162, 184, 0.2);
        color: #17a2b8;
    }

    /* Card Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: fadeInUp 0.5s ease-out;
    }

    /* Badge Large */
    .badge-lg {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Clinics Distribution Chart
        const clinicsCtx = document.getElementById('clinicsChart');
        if (clinicsCtx) {
            new Chart(clinicsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['{{ __("Active") }}', '{{ __("Inactive") }}'],
                    datasets: [{
                        data: [{{ $stats['clinics']['active'] }}, {{ $stats['clinics']['inactive'] }}],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed;
                                    const total = {{ $stats['clinics']['total'] }};
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    label += ' (' + percentage + '%)';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Doctor Profiles Chart
        const doctorsCtx = document.getElementById('doctorsChart');
        if (doctorsCtx) {
            new Chart(doctorsCtx, {
                type: 'bar',
                data: {
                    labels: ['{{ __("Approved") }}', '{{ __("Pending") }}', '{{ __("Clinic-Based") }}', '{{ __("Standalone") }}'],
                    datasets: [{
                        label: '{{ __("Doctor Statistics") }}',
                        data: [
                            {{ $stats['doctor_profiles']['approved'] }},
                            {{ $stats['doctor_profiles']['pending'] }},
                            {{ $stats['doctor_profiles']['clinic_based'] }},
                            {{ $stats['doctor_profiles']['standalone'] }}
                        ],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(23, 162, 184, 0.7)',
                            'rgba(108, 117, 125, 0.7)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(23, 162, 184, 1)',
                            'rgba(108, 117, 125, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                display: true,
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

