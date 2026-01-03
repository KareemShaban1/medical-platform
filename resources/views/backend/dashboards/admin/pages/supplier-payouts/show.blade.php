@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Payout Request Details'))

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.supplier-payouts.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> {{ __('Back to List') }}
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Request Details') }}</h5>
                        <span class="badge {{ $payoutRequest->status_badge_class }} fs-6">
                            {{ $payoutRequest->status_label }}
                        </span>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="35%">{{ __('Supplier') }}</th>
                                <td>
                                    <strong>{{ $payoutRequest->supplier->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $payoutRequest->supplier->phone ?? '' }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Amount') }}</th>
                                <td><strong class="text-success fs-4">{{ number_format($payoutRequest->amount, 2) }}
                                        {{ __('EGP') }}</strong></td>
                            </tr>
                            <tr>
                                <th>{{ __('Payment Method') }}</th>
                                <td>{{ $payoutRequest->payout_method }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Payment Details') }}</th>
                                <td>
                                    <div class="p-2 bg-light rounded">
                                        {{ $payoutRequest->payout_details }}
                                    </div>
                                </td>
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
                                <tr>
                                    <th>{{ __('Paid By') }}</th>
                                    <td>{{ $payoutRequest->paidByAdmin->name ?? 'N/A' }}</td>
                                </tr>
                            @endif
                            @if ($payoutRequest->supplier_note)
                                <tr>
                                    <th>{{ __('Supplier Note') }}</th>
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

                <!-- Actions -->
                @if ($payoutRequest->status === 'pending' || $payoutRequest->status === 'approved')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Actions') }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($payoutRequest->status === 'pending')
                                <div class="d-flex gap-2 mb-3">
                                    <form action="{{ route('admin.supplier-payouts.approve', $payoutRequest->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-info">
                                            <i class="mdi mdi-check me-1"></i> {{ __('Approve') }}
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="mdi mdi-close me-1"></i> {{ __('Reject') }}
                                    </button>
                                </div>
                            @endif

                            <form action="{{ route('admin.supplier-payouts.mark-paid', $payoutRequest->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="admin_note" class="form-label">{{ __('Admin Note') }}</label>
                                    <textarea class="form-control" id="admin_note" name="admin_note" rows="2"
                                        placeholder="{{ __('Optional note for the supplier...') }}"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="proof_images" class="form-label">{{ __('Proof Images') }}</label>
                                    <input type="file" class="form-control" id="proof_images" name="proof_images[]"
                                        accept="image/*" multiple>
                                    <div class="form-text">{{ __('Upload payment proof screenshots. Max 4MB each.') }}
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="mdi mdi-cash-check me-1"></i> {{ __('Mark as Paid') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

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
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Order #') }}</th>
                                        <th>{{ __('Clinic') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payoutRequest->orderSuppliers as $orderSupplier)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $orderSupplier->order_id) }}"
                                                    target="_blank">
                                                    {{ $orderSupplier->order->number ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>{{ $orderSupplier->order->clinic->name ?? 'N/A' }}</td>
                                            <td>{{ $orderSupplier->created_at->format('Y-m-d') }}</td>
                                            <td>{{ number_format($orderSupplier->pivot->amount, 2) }} {{ __('EGP') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th colspan="3" class="text-end">{{ __('Total Amount') }}</th>
                                        <th>{{ number_format($payoutRequest->amount, 2) }} {{ __('EGP') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Items Summary -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Order Items Summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-sm">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Qty') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payoutRequest->orderSuppliers as $orderSupplier)
                                        @foreach ($orderSupplier->order->items ?? [] as $item)
                                            <tr>
                                                <td>{{ $item->product->name ?? ($item->product_name ?? 'N/A') }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->price, 2) }}</td>
                                                <td>{{ number_format($item->quantity * $item->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Reject Payout Request') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.supplier-payouts.reject', $payoutRequest->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reject_note" class="form-label">{{ __('Reason for Rejection') }} <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="reject_note" name="admin_note" rows="3" required
                                placeholder="{{ __('Please provide a reason...') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Reject') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
