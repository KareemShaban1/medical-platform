@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Affiliate Program'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Affiliate Program') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Your Affiliate Code') }}</h5>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <input type="text" id="affiliate-code" class="form-control" value="{{ $code?->code }}" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="copyAffiliateCode()">
                            <i class="mdi mdi-content-copy"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-muted">{{ __('Discount') }}</div>
                            <div class="fw-bold">{{ number_format($discountPercent, 2) }}%</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-muted">{{ __('Commission') }}</div>
                            <div class="fw-bold">{{ number_format($commissionPercent, 2) }}%</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted">{{ __('Balance') }}</div>
                            <div class="fw-bold">{{ number_format($code?->balance ?? 0, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted">{{ __('Total Earned') }}</div>
                            <div class="fw-bold">{{ number_format($code?->total_earned ?? 0, 2) }}</div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        {{ __('Share your code with subscribers to earn commission on each paid subscription.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Recent Earnings') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-nowrap align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Subscription') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Commission') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>#{{ $transaction->subscription_id }}</td>
                                        <td>{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ number_format($transaction->commission_amount, 2) }}</td>
                                        <td>{{ $transaction->created_at?->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            {{ __('No earnings yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyAffiliateCode() {
    const input = document.getElementById('affiliate-code');
    if (!input) return;
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    toastr.success('{{ __('Affiliate code copied!') }}');
}
</script>
@endpush
