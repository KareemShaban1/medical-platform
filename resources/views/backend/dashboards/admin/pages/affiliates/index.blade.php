@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Affiliate Codes'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Affiliate Codes') }}</h4>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Type') }}</label>
                            <select name="type" class="form-select">
                                <option value="all">{{ __('All') }}</option>
                                <option value="clinic" @selected(request('type') === 'clinic')>{{ __('Clinic Users') }}</option>
                                <option value="affiliate" @selected(request('type') === 'affiliate')>{{ __('Affiliate Users') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-filter-variant"></i> {{ __('Filter') }}
                            </button>
                            <a href="{{ route('admin.affiliates.index') }}" class="btn btn-light">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Owner') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Discount %') }}</th>
                                    <th>{{ __('Commission %') }}</th>
                                    <th>{{ __('Balance') }}</th>
                                    <th>{{ __('Total Earned') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($codes as $code)
                                    @php
                                        $owner = $code->affiliateable;
                                        $ownerName = $owner?->name ?? $owner?->email ?? __('Unknown');
                                        $ownerType = $code->affiliateable_type === 'App\\Models\\ClinicUser' ? __('Clinic User') : __('Affiliate User');
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-primary">{{ $code->code }}</span>
                                        </td>
                                        <td>{{ $ownerName }}</td>
                                        <td>{{ $ownerType }}</td>
                                        <td>{{ $code->discount_percent !== null ? number_format($code->discount_percent, 2) : __('Default') }}</td>
                                        <td>{{ $code->commission_percent !== null ? number_format($code->commission_percent, 2) : __('Default') }}</td>
                                        <td>{{ number_format($code->balance, 2) }}</td>
                                        <td>{{ number_format($code->total_earned, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $code->is_active ? 'success' : 'secondary' }}">
                                                {{ $code->is_active ? __('Active') : __('Disabled') }}
                                            </span>
                                        </td>
                                        <td>
                                            @hasPermission('update affiliates')
                                            <form method="POST" action="{{ route('admin.affiliates.update', $code->id) }}" class="d-flex flex-column gap-2">
                                                @csrf
                                                <div class="d-flex gap-2">
                                                    <input type="number" name="discount_percent" class="form-control form-control-sm" step="0.01" min="0" max="100"
                                                        placeholder="{{ __('Discount %') }}" value="{{ $code->discount_percent }}">
                                                    <input type="number" name="commission_percent" class="form-control form-control-sm" step="0.01" min="0" max="100"
                                                        placeholder="{{ __('Commission %') }}" value="{{ $code->commission_percent }}">
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active-{{ $code->id }}" {{ $code->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="active-{{ $code->id }}">{{ __('Active') }}</label>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        {{ __('Save') }}
                                                    </button>
                                                </div>
                                                <small class="text-muted">
                                                    {{ __('Leave discount/commission empty to use default settings.') }}
                                                </small>
                                            </form>
                                            @else
                                            <span class="text-muted">{{ __('No access') }}</span>
                                            @endhasPermission
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            {{ __('No affiliate codes found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $codes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
