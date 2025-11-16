@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="page-title">{{ __('My Orders') }}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($orders->isEmpty())
                        <div class="alert alert-info mb-0">
                            {{ __('You have not placed any orders yet.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Order Number') }}</th>
                                    <th>{{ __('Items') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Payment Status') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th class="text-center">{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->number }}</td>
                                        <td>{{ $order->items->count() }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @php
                                                $status = $order->status;
                                                $statusBadge = match($status) {
                                                    'completed' => 'success',
                                                    'pending' => 'warning',
                                                    'cancelled' => 'danger',
                                                    'processing' => 'info',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusBadge }}">{{ ucfirst($status) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $payStatus = $order->payment_status;
                                                $payBadge = match($payStatus) {
                                                    'paid' => 'success',
                                                    'failed' => 'danger',
                                                    'pending' => 'warning',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $payBadge }}">{{ ucfirst($payStatus) }}</span>
                                        </td>
                                        <td><strong>{{ number_format($order->total, 2) }} EGP</strong></td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    onclick="openOrderModal({{ $order->id }})">
                                                {{ __('View Details') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Order Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="order-details-body">
                <p class="text-muted mb-0">{{ __('Loading order details...') }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openOrderModal(orderId) {
        const modalEl = document.getElementById('orderDetailsModal');
        const bodyEl = document.getElementById('order-details-body');
        const modal = new bootstrap.Modal(modalEl);

        bodyEl.innerHTML = '<p class="text-muted mb-0">{{ __('Loading order details...') }}</p>';
        modal.show();

        fetch('{{ route('clinic.orders.show', ':id') }}'.replace(':id', orderId))
            .then(response => response.json())
            .then(order => {
                if (!order || order.error) {
                    bodyEl.innerHTML = '<div class="alert alert-danger mb-0">{{ __('Failed to load order details.') }}</div>';
                    return;
                }

                let itemsHtml = '';
                if (order.items && order.items.length) {
                    itemsHtml = order.items.map(item => `
                        <tr>
                            <td>${item.product ? item.product.name : '---'}</td>
                            <td>${item.supplier ? item.supplier.name : '-'}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.price).toFixed(2)} EGP</td>
                            <td><strong>${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)} EGP</strong></td>
                        </tr>
                    `).join('');
                } else {
                    itemsHtml = `
                        <tr>
                            <td colspan="5" class="text-center text-muted">{{ __('No items found for this order.') }}</td>
                        </tr>
                    `;
                }

                const shipping = parseFloat(order.shipping || 0);
                const tax = parseFloat(order.tax || 0);
                const discount = parseFloat(order.discount || 0);
                const total = parseFloat(order.total || 0);
                const inferredSubtotal = total - (shipping + tax - discount);

                bodyEl.innerHTML = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="mb-1">{{ __('Order') }} #${order.id}</h5>
                            <p class="mb-0 text-muted">
                                {{ __('Order Number') }}: <strong>${order.number}</strong><br>
                                {{ __('Date') }}: <strong>${new Date(order.created_at).toLocaleString()}</strong><br>
                                {{ __('Status') }}: <strong>${order.status}</strong>
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <p class="mb-1 text-muted">
                                {{ __('Payment Method') }}:
                                <strong>${order.payment_method === 0 ? '{{ __('Cash on Delivery') }}' : '{{ __('Online Payment') }}'}</strong>
                            </p>
                            <p class="mb-1 text-muted">
                                {{ __('Payment Status') }}:
                                <strong>${order.payment_status}</strong>
                            </p>
                            <p class="mb-0 text-muted">
                                {{ __('Payment Gateway') }}:
                                <strong>${order.payment_gateway ?? '-'}</strong>
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Supplier') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <p class="mb-1">
                                {{ __('Subtotal') }}:
                                <strong>${inferredSubtotal.toFixed(2)} EGP</strong>
                            </p>
                            <p class="mb-1">
                                {{ __('Shipping') }}:
                                <strong>${shipping.toFixed(2)} EGP</strong>
                            </p>
                            <p class="mb-1">
                                {{ __('Tax') }}:
                                <strong>${tax.toFixed(2)} EGP</strong>
                            </p>
                            <p class="mb-1">
                                {{ __('Discount') }}:
                                <strong>${discount.toFixed(2)} EGP</strong>
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end d-flex align-items-end justify-content-md-end mt-3 mt-md-0">
                            <h5 class="mb-0">{{ __('Total') }}: <strong>${total.toFixed(2)} EGP</strong></h5>
                        </div>
                    </div>
                `;
            })
            .catch(() => {
                bodyEl.innerHTML = '<div class="alert alert-danger mb-0">{{ __('Failed to load order details.') }}</div>';
            });
    }
</script>
@endpush
@endsection
