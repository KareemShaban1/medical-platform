@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Affiliate Profile'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Affiliate Profile') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Affiliate Details') }}</h5>
                    <div class="mb-2">
                        <div class="text-muted">{{ __('Name') }}</div>
                        <div class="fw-bold">{{ $owner?->name ?? __('Unknown') }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted">{{ __('Email') }}</div>
                        <div class="fw-bold">{{ $owner?->email ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted">{{ __('Phone') }}</div>
                        <div class="fw-bold">{{ $owner?->phone ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted">{{ __('Type') }}</div>
                        <div class="fw-bold">
                            {{ $affiliateCode->affiliateable_type === 'App\\Models\\ClinicUser' ? __('Clinic User') : __('Affiliate User') }}
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted">{{ __('Code') }}</div>
                        <div class="fw-bold text-primary">{{ $affiliateCode->code }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted">{{ __('Balance') }}</div>
                        <div class="fw-bold">{{ number_format($affiliateCode->balance, 2) }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted">{{ __('Total Earned') }}</div>
                        <div class="fw-bold">{{ number_format($affiliateCode->total_earned, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Payout Profile') }}</h5>
                    @if($payoutProfile)
                        <div class="mb-2">
                            <div class="text-muted">{{ __('Payment Method') }}</div>
                            <div class="fw-bold">{{ $payoutProfile->payout_method }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-muted">{{ __('Payment Details') }}</div>
                            <div class="fw-bold" style="white-space: pre-wrap;">{{ $payoutProfile->payout_details }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-muted">{{ __('Notes') }}</div>
                            <div class="fw-bold" style="white-space: pre-wrap;">{{ $payoutProfile->notes ?? '-' }}</div>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('No payout profile saved yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Subscription Transactions') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Subscription') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Commission') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>#{{ $transaction->subscription_id }}</td>
                                        <td>{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ number_format($transaction->commission_amount, 2) }}</td>
                                        <td>{{ number_format($transaction->discount_amount, 2) }}</td>
                                        <td>{{ $transaction->created_at?->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            {{ __('No transactions found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $transactions->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Payout Requests') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Payment Method') }}</th>
                                    <th>{{ __('Payment Details') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Requested At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payoutRequests as $payout)
                                    <tr>
                                        <td>{{ number_format($payout->amount, 2) }}</td>
                                        <td>{{ $payout->payout_method }}</td>
                                        <td style="white-space: pre-wrap; min-width: 180px;">{{ $payout->payout_details }}</td>
                                        <td style="white-space: pre-wrap; min-width: 160px;">{{ $payout->notes ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $payout->status === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($payout->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $payout->created_at?->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            {{ __('No payout requests found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $payoutRequests->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
