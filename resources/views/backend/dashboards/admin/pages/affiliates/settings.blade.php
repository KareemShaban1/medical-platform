@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Affiliate Settings'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Affiliate Settings') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ url('admin/affiliates/settings') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Default Discount %') }}</label>
                            <input type="number" name="default_discount_percent" class="form-control" step="0.01" min="0" max="100"
                                value="{{ $settings?->default_discount_percent ?? 5 }}">
                            <small class="text-muted">{{ __('Applied to all affiliate codes unless overridden.') }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Default Commission %') }}</label>
                            <input type="number" name="default_commission_percent" class="form-control" step="0.01" min="0" max="100"
                                value="{{ $settings?->default_commission_percent ?? 5 }}">
                            <small class="text-muted">{{ __('Affiliate balance increases by this percent of subscription value.') }}</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> {{ __('Save Settings') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Notes') }}</h5>
                    <ul class="mb-0">
                        <li>{{ __('Discounts apply only for new subscriptions (not upgrades).') }}</li>
                        <li>{{ __('Free plans are excluded from discounts and commissions.') }}</li>
                        <li>{{ __('Admins can override per affiliate code from the list page.') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
