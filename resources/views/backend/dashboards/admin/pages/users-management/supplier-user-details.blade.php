@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Supplier User Details'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ __('Supplier User Details') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.supplier-users') }}">{{ __('Supplier Users') }}</a></li>
                        <li class="breadcrumb-item active">{{ $supplierUser->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier User Information -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-user"></i> {{ __('User Information') }}</h5>
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
                            <td>{{ $supplierUser->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Name') }}:</th>
                            <td>{{ $supplierUser->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}:</th>
                            <td>{{ $supplierUser->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Phone') }}:</th>
                            <td>{{ $supplierUser->phone ?? __('N/A') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}:</th>
                            <td>
                                @if($supplierUser->status)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Registered At') }}:</th>
                            <td>{{ $supplierUser->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Associated Supplier -->
            @if($supplierUser->supplier)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-truck"></i> {{ __('Associated Supplier') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            @if($supplierUser->supplier->getFirstMediaUrl('supplier_images'))
                                <img src="{{ $supplierUser->supplier->getFirstMediaUrl('supplier_images') }}" alt="{{ $supplierUser->supplier->name }}" class="img-fluid rounded" style="max-width: 100px;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                                    <i class="fas fa-truck fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h5>{{ $supplierUser->supplier->name }}</h5>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th width="30%">{{ __('Phone') }}:</th>
                                    <td>{{ $supplierUser->supplier->phone ?? __('N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address') }}:</th>
                                    <td>{{ $supplierUser->supplier->address ?? __('N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}:</th>
                                    <td>
                                        @if($supplierUser->supplier->status)
                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Is Allowed') }}:</th>
                                    <td>
                                        @if($supplierUser->supplier->is_allowed)
                                            <span class="badge badge-success">{{ __('Allowed') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('Not Allowed') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <div class="mt-3">
                                <a href="{{ route('admin.users-management.supplier-details', $supplierUser->supplier->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> {{ __('View Supplier Details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-0">{{ __('This user is not associated with any supplier.') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
