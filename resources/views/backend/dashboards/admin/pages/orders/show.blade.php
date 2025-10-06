@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Order Details'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">{{ __('Orders') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Order Details') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('Order Details') }} - #{{ $order->number }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Order Items') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Supplier') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Total') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                            <td>
                                                @php
                                                    $statusClass =
                                                        [
                                                            'pending' => 'warning',
                                                            'processing' => 'info',
                                                            'delivering' => 'primary',
                                                            'completed' => 'success',
                                                            'cancelled' => 'danger',
                                                        ][$item->status] ?? 'secondary';
                                                @endphp
                                                <span
                                                    class="badge bg-{{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Suppliers -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Suppliers Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Supplier') }}</th>
                                        <th>{{ __('Subtotal') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->suppliers as $orderSupplier)
                                        <tr>
                                            <td>{{ $orderSupplier->supplier->name ?? 'N/A' }}</td>
                                            <td>${{ number_format($orderSupplier->subtotal, 2) }}</td>
                                            <td>
                                                @php
                                                    $statusClass =
                                                        [
                                                            'pending' => 'warning',
                                                            'processing' => 'info',
                                                            'delivering' => 'primary',
                                                            'completed' => 'success',
                                                            'cancelled' => 'danger',
                                                        ][$orderSupplier->status] ?? 'secondary';
                                                @endphp
                                                <span
                                                    class="badge bg-{{ $statusClass }}">{{ ucfirst($orderSupplier->status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Refunds -->
                @if ($order->refunds->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Refunds') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Supplier') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Reason') }}</th>
                                            <th>{{ __('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->refunds as $refund)
                                            <tr>
                                                <td>{{ $refund->supplier->name ?? 'N/A' }}</td>
                                                <td>${{ number_format($refund->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($refund->refund_type) }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass =
                                                            [
                                                                'pending' => 'warning',
                                                                'approved' => 'success',
                                                                'rejected' => 'danger',
                                                                'processed' => 'primary',
                                                            ][$refund->status] ?? 'secondary';
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $statusClass }}">{{ ucfirst($refund->status) }}</span>
                                                </td>
                                                <td>{{ $refund->reason ?? 'N/A' }}</td>
                                                <td>{{ $refund->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Order Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Order Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>{{ __('Order Number') }}:</strong></td>
                                <td>{{ $order->number }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Status') }}:</strong></td>
                                <td>
                                    @php
                                        $statusClass =
                                            [
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'delivering' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                'refunded' => 'secondary',
                                            ][$order->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Payment Status') }}:</strong></td>
                                <td>
                                    @php
                                        $paymentClass =
                                            [
                                                'pending' => 'warning',
                                                'paid' => 'success',
                                                'failed' => 'danger',
                                            ][$order->payment_status] ?? 'secondary';
                                    @endphp
                                    <span
                                        class="badge bg-{{ $paymentClass }}">{{ ucfirst($order->payment_status) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Payment Method') }}:</strong></td>
                                <td>{{ $order->payment_method == 0 ? 'COD' : 'Online' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Total') }}:</strong></td>
                                <td>${{ number_format($order->total, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Shipping') }}:</strong></td>
                                <td>${{ number_format($order->shipping, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Tax') }}:</strong></td>
                                <td>${{ number_format($order->tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Discount') }}:</strong></td>
                                <td>${{ number_format($order->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Date') }}:</strong></td>
                                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Clinic Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Clinic Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>{{ __('Clinic') }}:</strong></td>
                                <td>{{ $order->clinic->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Ordered By') }}:</strong></td>
                                <td>{{ $order->clinicUser->name ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> {{ __('Back to Orders') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
