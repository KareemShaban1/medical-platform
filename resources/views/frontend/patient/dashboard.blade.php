@extends('frontend.layouts.app')

@section('title', __('Patient Dashboard'))

@section('content')
<div class="container-fluid py-5" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card shadow-lg">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Patient Dashboard') }}</h4>
                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('user.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to logout?') }}')">
                            <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1>{{ __('Hello :name 👋', ['name' => $patient->name]) }}</h1>
                        <p class="lead">{{ __('Welcome to your patient dashboard') }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-user fa-3x text-primary mb-2"></i>
                                    <h5>{{ __('Profile Information') }}</h5>
                                    <p class="text-muted">{{ __('View and update your profile') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar fa-3x text-success mb-2"></i>
                                    <h5>{{ __('Appointments') }}</h5>
                                    <p class="text-muted">{{ __('Manage your appointments') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-medical fa-3x text-info mb-2"></i>
                                    <h5>{{ __('Medical Records') }}</h5>
                                    <p class="text-muted">{{ __('Access your medical history') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-pills fa-3x text-warning mb-2"></i>
                                    <h5>{{ __('Prescriptions') }}</h5>
                                    <p class="text-muted">{{ __('View your prescriptions') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('Patient Information') }}</h6>
                            <p><strong>{{ __('Name') }}:</strong> {{ $patient->name }}</p>
                            <p><strong>{{ __('Email') }}:</strong> {{ $patient->email ?? __('Not provided') }}</p>
                            <p><strong>{{ __('Phone') }}:</strong> {{ $patient->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('Account Status') }}</h6>
                            <p><strong>{{ __('Account Type') }}:</strong>
                                @if($patient->isRegistered())
                                    <span class="badge bg-success">{{ __('Registered User') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('Clinic Created') }}</span>
                                @endif
                            </p>
                            <p><strong>{{ __('Member Since') }}:</strong> {{ $patient->created_at->format('F Y') }}</p>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
