@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Orders Analytics'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('supplier.orders.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
                        </a>
                    </div>
                    <h4 class="page-title">
                        <i class="mdi mdi-chart-line text-primary"></i> {{ __('Orders Analytics Dashboard') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('supplier.orders.analytics') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">
                                        <i class="mdi mdi-calendar-start"></i> {{ __('Start Date') }}
                                    </label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="{{ $startDate }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">
                                        <i class="mdi mdi-calendar-end"></i> {{ __('End Date') }}
                                    </label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ $endDate }}">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="mdi mdi-filter"></i> {{ __('Apply Filters') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-cart-outline widget-icon bg-gradient-primary text-white"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0">{{ __('Total Orders') }}</h5>
                        <h3 class="mt-3 mb-3">{{ $analytics['total_orders'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-package-variant widget-icon bg-success-lighten text-success"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0">{{ __('Total Items Sold') }}</h5>
                        <h3 class="mt-3 mb-3">{{ $analytics['total_items'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-currency-usd widget-icon bg-info-lighten text-info"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0">{{ __('Total Revenue') }}</h5>
                        <h3 class="mt-3 mb-3">
                            {{ number_format($analytics['total_revenue'], 2) }} {{ __('EGP') }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-chart-bar widget-icon bg-warning-lighten text-warning"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0">{{ __('Average Order Value') }}</h5>
                        <h3 class="mt-3 mb-3">
                            {{ number_format($analytics['average_order_value'], 2) }} {{ __('EGP') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="mdi mdi-chart-timeline-variant"></i> {{ __('Revenue Trend') }}
                        </h4>
                        <div style="position: relative; height: 350px;">
                            <canvas id="supplierRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="mdi mdi-chart-donut"></i> {{ __('Status Overview') }}
                        </h4>
                        <ul class="list-unstyled mb-0">
                            @php
                                $statusMap = [
                                    'pending' => __('Pending'),
                                    'processing' => __('Processing'),
                                    'delivering' => __('Delivering'),
                                    'completed' => __('Completed'),
                                    'cancelled' => __('Cancelled'),
                                ];
                            @endphp
                            @foreach($statusMap as $key => $label)
                                <li class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $label }}</span>
                                    <span class="badge bg-light text-dark">
                                        {{ $analytics['supplier_status_counts']->get($key, 0) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <hr>
                        <h5 class="mb-2">{{ __('Payment Overview') }}</h5>
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ __('Paid') }}</span>
                            <span class="badge bg-success">
                                {{ $analytics['payment_status_counts']->get('paid', 0) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ __('Pending') }}</span>
                            <span class="badge bg-warning">
                                {{ $analytics['payment_status_counts']->get('pending', 0) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>{{ __('Failed') }}</span>
                            <span class="badge bg-danger">
                                {{ $analytics['payment_status_counts']->get('failed', 0) }}
                            </span>
                        </div>
                        <div class="alert alert-info mb-0">
                            <strong>{{ number_format($analytics['pending_revenue'], 2) }} {{ __('EGP') }}</strong>
                            <br>
                            <small>{{ __('Potential revenue still pending payment') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="mdi mdi-hospital-building"></i> {{ __('Top Clinics') }}
                        </h4>
                        @if($analytics['top_clinics']->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-centered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Clinic') }}</th>
                                            <th>{{ __('Orders') }}</th>
                                            <th>{{ __('Items') }}</th>
                                            <th>{{ __('Revenue') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['top_clinics'] as $clinic)
                                            <tr>
                                                <td>{{ $clinic['clinic_name'] }}</td>
                                                <td>{{ $clinic['orders_count'] }}</td>
                                                <td>{{ $clinic['items_count'] }}</td>
                                                <td>{{ number_format($clinic['revenue'], 2) }} {{ __('EGP') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('No clinic data available for this range.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="mdi mdi-clock-outline"></i> {{ __('Recent Orders') }}
                        </h4>
                        @if($analytics['recent_orders']->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-centered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Order') }}</th>
                                            <th>{{ __('Clinic') }}</th>
                                            <th>{{ __('Total') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['recent_orders'] as $order)
                                            @php
                                                $supplierItems = $order->items;
                                                $orderTotal = $supplierItems->sum(function ($item) {
                                                    return $item->quantity * $item->price;
                                                });
                                                $supplierStatus = optional($order->suppliers->first())->status ?? $order->status;
                                            @endphp
                                            <tr>
                                                <td>{{ $order->number }}</td>
                                                <td>{{ $order->clinic->name ?? 'N/A' }}</td>
                                                <td>{{ number_format($orderTotal, 2) }} {{ __('EGP') }}</td>
                                                <td><span class="badge bg-light text-dark">{{ ucfirst($supplierStatus) }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('No recent orders in this period.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if($analytics['revenue_by_date']->isNotEmpty())
            const revCtx = document.getElementById('supplierRevenueChart');
            if (revCtx && window.Chart) {
                new Chart(revCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($analytics['revenue_by_date']->keys()->values()) !!},
                        datasets: [{
                            label: '{{ __("Revenue") }}',
                            data: {!! json_encode($analytics['revenue_by_date']->values()->map(fn($v) => round($v, 2))->values()) !!},
                            borderColor: '#5b69bc',
                            backgroundColor: 'rgba(91,105,188,0.15)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            }
            @endif
        });
    </script>
@endpush

@push('styles')
    <style>
        .widget-flat {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .widget-flat:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }

        .widget-icon {
            height: 44px;
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 22px;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #5b69bc 0%, #4a5aa9 100%);
        }
    </style>
@endpush

