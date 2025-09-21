@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.users.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Users') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('User Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('User Information') }}</h5>

                            <div class="table-responsive">
                                <table class="table table-borderless table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 50%;">{{ __('Name') }}:</th>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Email') }}:</th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Phone') }}:</th>
                                            <td>{{ $user->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Status') }}:</th>
                                            <td>
                                                @if($user->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('Roles & Permissions') }}</h5>

                            <div class="mb-3">
                                <h6>{{ __('Assigned Role') }}:</h6>
                                @if($user->roles && $user->roles->count() > 0)
                                    <span class="badge bg-info me-1">{{ $user->roles->first()->name }}</span>
                                @else
                                    <span class="text-muted">{{ __('No role assigned') }}</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <h6>{{ __('Clinic') }}:</h6>
                                <p class="text-muted">{{ $user->clinic->name ?? __('No clinic assigned') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Timestamps') }}</h5>

                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">{{ __('Created At') }}:</th>
                                    <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Updated At') }}:</th>
                                    <td>{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
