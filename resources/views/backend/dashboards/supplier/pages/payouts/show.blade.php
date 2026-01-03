@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Payout Request Details'))

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('supplier.payouts.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> {{ __('Back to Payouts') }}
                        </a>
                    </div>
                    <h4 class="page-title">{{ __('Payout Request') }} #{{ $payoutRequest->id }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Request Details -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Request Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="40%">{{ __('Status') }}</th>
                                <td>
                                    <span class="badge {{ $payoutRequest->status_badge_class }}">
                                        {{ $payoutRequest->status_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Amount') }}</th>
                                <td><strong class="text-success fs-5">{{ number_format($payoutRequest->amount, 2) }}
                                        {{ __('EGP') }}</strong></td>
                            </tr>
                            <tr>
                                <th>{{ __('Payment Method') }}</th>
                                <td>{{ $payoutRequest->payout_method }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Payment Details') }}</th>
                                <td>{{ $payoutRequest->payout_details }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Requested At') }}</th>
                                <td>{{ $payoutRequest->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @if ($payoutRequest->paid_at)
                                <tr>
                                    <th>{{ __('Paid At') }}</th>
                                    <td>{{ $payoutRequest->paid_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endif
                            @if ($payoutRequest->supplier_note)
                                <tr>
                                    <th>{{ __('Your Note') }}</th>
                                    <td>{{ $payoutRequest->supplier_note }}</td>
                                </tr>
                            @endif
                            @if ($payoutRequest->admin_note)
                                <tr>
                                    <th>{{ __('Admin Note') }}</th>
                                    <td class="text-info">{{ $payoutRequest->admin_note }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Proof Images -->
                @if ($payoutRequest->status === 'paid' && $payoutRequest->getMedia('payout_proofs')->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Payment Proof') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach ($payoutRequest->getMedia('payout_proofs') as $media)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ $media->getUrl() }}" target="_blank">
                                            <img src="{{ $media->getUrl() }}" class="img-fluid rounded" alt="Proof">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-6">
                <!-- Included Orders -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Included Orders') }}
                            ({{ $payoutRequest->orderSuppliers->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Order #') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payoutRequest->orderSuppliers as $orderSupplier)
                                        <tr>
                                            <td>{{ $orderSupplier->order->number ?? 'N/A' }}</td>
                                            <td>{{ $orderSupplier->created_at->format('Y-m-d') }}</td>
                                            <td>{{ number_format($orderSupplier->pivot->amount, 2) }} {{ __('EGP') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2" class="text-end">{{ __('Total') }}</th>
                                        <th>{{ number_format($payoutRequest->amount, 2) }} {{ __('EGP') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
