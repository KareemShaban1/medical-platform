@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Supplier Details'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ __('Supplier Details') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.suppliers') }}">{{ __('Suppliers') }}</a></li>
                        <li class="breadcrumb-item active">{{ $supplier->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Information -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-truck"></i> {{ __('Supplier Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($supplier->getFirstMediaUrl('suppliers'))
                            <img src="{{ $supplier->getFirstMediaUrl('suppliers') }}" alt="{{ $supplier->name }}" class="img-fluid rounded" style="max-height: 200px;">
                        @else
                            <div class="bg-light rounded p-5">
                                <i class="fas fa-warehouse fa-5x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">{{ __('ID') }}:</th>
                            <td>{{ $supplier->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Name') }}:</th>
                            <td>{{ $supplier->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Contact Person') }}:</th>
                            <td>{{ $supplier->contact_person ?? __('N/A') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}:</th>
                            <td>{{ $supplier->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Phone') }}:</th>
                            <td>{{ $supplier->phone ?? __('N/A') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Address') }}:</th>
                            <td>{{ $supplier->address ?? __('N/A') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}:</th>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Created At') }}:</th>
                            <td>{{ $supplier->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Additional Information -->
            @if($supplier->description)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> {{ __('Description') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $supplier->description }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <!-- Supplier Users -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie"></i> {{ __('Supplier Users') }} ({{ $supplier->supplierUsers->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($supplier->supplierUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Joined') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplier->supplierUsers as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($user->getFirstMediaUrl('supplier_users'))
                                                        <img src="{{ $user->getFirstMediaUrl('supplier_users') }}" alt="{{ $user->name }}" class="rounded-circle mr-2" width="30" height="30">
                                                    @else
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 30px; height: 30px;">
                                                            <i class="fas fa-user text-muted" style="font-size: 0.8rem;"></i>
                                                        </div>
                                                    @endif
                                                    {{ $user->name }}
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone ?? __('N/A') }}</td>
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
                        <p class="text-muted mb-0">{{ __('No users found for this supplier.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.users-management.suppliers') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Suppliers') }}
            </a>
        </div>
    </div>
</div>
@endsection
