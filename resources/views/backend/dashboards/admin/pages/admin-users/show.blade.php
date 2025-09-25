@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Admin User Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fa fa-user"></i> {{ __('Admin User Details') }}
                        </h3>
                        <div>
                            <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> {{ __('Back to List') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h6>{{ __('Name') }}</h6>
                                    <p class="mb-0">{{ $adminUser->name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h6>{{ __('Email') }}</h6>
                                    <p class="mb-0">{{ $adminUser->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h6>{{ __('Roles') }}</h6>
                                    <div class="mt-2">
                                        @forelse($adminUser->roles as $role)
                                            <span class="badge bg-primary me-1">{{ ucfirst($role->name) }}</span>
                                        @empty
                                            <span class="badge bg-secondary">{{ __('No Role Assigned') }}</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h6>{{ __('Status') }}</h6>
                                    <span class="badge {{ $adminUser->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $adminUser->status ? __('Active') : __('Inactive') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h6>{{ __('Created At') }}</h6>
                                    <p class="mb-0">{{ $adminUser->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h6>{{ __('Updated At') }}</h6>
                                    <p class="mb-0">{{ $adminUser->updated_at->format('Y-m-d H:i:s') }}</p>
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
