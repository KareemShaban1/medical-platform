@extends('backend.dashboards.affiliate.layouts.app')
@section('title', __('Affiliate Dashboard'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Affiliate Dashboard') }}</h4>
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
                    <div class="mt-3">
                        @if($pendingPayout)
                            <div class="alert alert-warning mb-2">
                                {{ __('You already have a pending payout request.') }}
                            </div>
                            <button class="btn btn-outline-secondary w-100" disabled>
                                {{ __('Request Payout') }}
                            </button>
                        @else
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#affiliatePayoutModal"
                                @disabled(($code?->balance ?? 0) <= 0)>
                                {{ __('Request Payout') }}
                            </button>
                            @if(($code?->balance ?? 0) <= 0)
                                <small class="text-muted d-block mt-2">
                                    {{ __('Your balance is not eligible for payout yet.') }}
                                </small>
                            @endif
                        @endif
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

<div class="modal fade" id="affiliatePayoutModal" tabindex="-1" aria-labelledby="affiliatePayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('affiliate.payouts.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="affiliatePayoutModalLabel">{{ __('Request Payout') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        {{ __('Your payout amount will be your available balance.') }}
                        <strong>{{ number_format($code?->balance ?? 0, 2) }}</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Payment Method') }}</label>
                        <select class="form-select" name="payout_method" required>
                            @php($selectedMethod = old('payout_method', $payoutProfile?->payout_method))
                            <option value="Instapay" @selected($selectedMethod === 'Instapay')>{{ __('Instapay') }}</option>
                            <option value="Mobile Wallet" @selected($selectedMethod === 'Mobile Wallet')>{{ __('Mobile Wallet') }}</option>
                            <option value="IBAN" @selected($selectedMethod === 'IBAN')>{{ __('IBAN') }}</option>
                            <option value="Other" @selected($selectedMethod === 'Other')>{{ __('Other') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Payment Details') }}</label>
                        <textarea class="form-control" name="payout_details" rows="3" required>{{ old('payout_details', $payoutProfile?->payout_details) }}</textarea>
                        <small class="text-muted">{{ __('Enter phone number, wallet ID, IBAN, or any payout details.') }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Notes') }}</label>
                        <textarea class="form-control" name="notes" rows="3">{{ old('notes', $payoutProfile?->notes) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Submit Request') }}</button>
                </div>
            </form>
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
