@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Offer Invoice'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">{{ __('Offer Invoice') }} #{{ $offer->id }}</h4>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="window.history.back()">{{ __('Back') }}</button>
                    <button class="btn btn-primary" onclick="window.print()"><i class="mdi mdi-printer me-1"></i>{{ __('Print Invoice') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body" id="invoice-area">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h3 class="mb-1">{{ $clinic->name ?? __('Clinic') }}</h3>
                            <div class="text-muted">{{ $clinic->email ?? '' }}</div>
                            <div class="text-muted">{{ $clinic->phone ?? '' }}</div>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-1">{{ __('Invoice') }}</h5>
                            <div class="text-muted">{{ __('Offer Number:') }} #{{ $offer->id }}</div>
                            <div class="text-muted">{{ __('Request Number:') }} #{{ $requestModel->id }}</div>
                            <div class="text-muted">{{ __('Accepted At:') }} {{ $offer->updated_at->format('Y-m-d H:i') }}</div>
                            <div>
                                @if($offer->status === \App\Models\Offer::STATUS_ACCEPTED)
                                    <span class="badge bg-success">{{ __('Accepted') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-2">{{ __('Bill To (Clinic)') }}</h5>
                            <div>{{ $clinic->name }}</div>
                            @if(!empty($clinic->address))<div>{{ $clinic->address }}</div>@endif
                            @if(!empty($clinic->phone))<div>{{ $clinic->phone }}</div>@endif
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5 class="mb-2">{{ __('Supplier') }}</h5>
                            <div>{{ $supplier->name }}</div>
                            @if(!empty($supplier->email))<div>{{ $supplier->email }}</div>@endif
                            @if(!empty($supplier->phone))<div>{{ $supplier->phone }}</div>@endif
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50%">{{ __('Description') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Unit Price') }}</th>
                                    <th>{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>{{ __('Purchase Request') }} #{{ $requestModel->id }}</strong>
                                        <div class="small text-muted mt-1">{{ $requestModel->description }}</div>
                                    </td>
                                    <td>{{ number_format($requestModel->quantity) }}</td>
                                    <td>
                                        @php
                                            $qty = max(1, (int) $requestModel->quantity);
                                            $unit = $offer->final_price / $qty;
                                        @endphp
                                        {{ number_format($unit, 2) }}
                                    </td>
                                    <td>{{ number_format($offer->final_price, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                @php
                                    $totalAmount = $offer->price
                                        - ($offer->discount ?? 0)
                                        + ($offer->shipping ?? 0)
                                        + ($offer->tax ?? 0);
                                @endphp
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Subtotal') }}</span>
                                    <span>{{ number_format($offer->price, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Discount') }}</span>
                                    <span>{{ $offer->discount ? number_format($offer->discount, 2) : '0.00' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Shipping') }}</span>
                                    <span>{{ number_format($offer->shipping ?? 0, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Tax') }}</span>
                                    <span>{{ number_format($offer->tax ?? 0, 2) }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>{{ __('Total') }}</span>
                                    <span>{{ number_format($totalAmount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="mb-2">{{ __('Terms & Conditions') }}</h6>
                        <div class="small">{{ $offer->terms }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #invoice-area, #invoice-area * { visibility: visible; }
    #invoice-area { position: absolute; left: 0; top: 0; width: 100%; }
    .page-title-box, .btn { display: none !important; }
}
</style>
@endsection
