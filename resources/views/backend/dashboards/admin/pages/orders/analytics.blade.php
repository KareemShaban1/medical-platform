@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Orders Analytics'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
                        </a>
                    </div>
                    <h4 class="page-title">
                        <i class="mdi mdi-chart-box-multiple-outline text-primary"></i> {{ __('Orders & Suppliers Analytics') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.orders.analytics') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">
                                        <i class="mdi mdi-calendar-start"></i> {{ __('Start Date') }}
                                    </label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="{{ $startDate }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">
                                        <i class="mdi mdi-calendar-end"></i> {{ __('End Date') }}
                                    </label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ $endDate }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="supplier_id" class="form-label">
                                        <i class="mdi mdi-truck-delivery"></i> {{ __('Filter by Supplier') }}
                                    </label>
                                    <select class="form-select" id="supplier_id" name="supplier_id">
                                        <option value="">{{ __('All Suppliers') }}</option>
                                        @foreach($benefitedSuppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ (string)$supplierId === (string)$supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
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
                        <h5 class="text-muted fw-normal mt-0">{{ __('Total Items') }}</h5>
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
                        <h5 class="text-muted fw-normal mt-0">{{ __('Total Revenue (Orders Total)') }}</h5>
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

        @if($analytics['selected_supplier'] && $analytics['selected_supplier_analytics'])
            <div class="row">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">
                                        <i class="mdi mdi-truck-delivery-outline text-primary"></i>
                                        {{ __('Selected Supplier Overview') }}:
                                        <strong>{{ $analytics['selected_supplier']->name }}</strong>
                                    </h4>
                                    <p class="text-muted mb-0">
                                        {{ __('Orders, items and revenue for this supplier in the selected period.') }}
                                    </p>
                                </div>
                                <div class="d-flex gap-4">
                                    <div>
                                        <h5 class="mb-0">{{ $analytics['selected_supplier_analytics']['orders_count'] }}</h5>
                                        <small class="text-muted">{{ __('Orders') }}</small>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $analytics['selected_supplier_analytics']['items_count'] }}</h5>
                                        <small class="text-muted">{{ __('Items') }}</small>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">
                                            {{ number_format($analytics['selected_supplier_analytics']['revenue'], 2) }}
                                            {{ __('EGP') }}
                                        </h5>
                                        <small class="text-muted">{{ __('Supplier Revenue') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="mdi mdi-chart-timeline-variant"></i> {{ __('Orders & Revenue Over Time') }}
                        </h4>
                        <div style="position: relative; height: 360px;">
                            <canvas id="ordersRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="mdi mdi-chart-donut"></i> {{ __('Status & Payment Overview') }}
                        </h4>
                        <h6 class="text-muted mb-2">{{ __('Order Status') }}</h6>
                        @php
                            $statusMap = [
                                'pending' => __('Pending'),
                                'processing' => __('Processing'),
                                'delivering' => __('Delivering'),
                                'completed' => __('Completed'),
                                'cancelled' => __('Cancelled'),
                                'refunded' => __('Refunded'),
                            ];
                        @endphp
                        <ul class="list-unstyled mb-3">
                            @foreach($statusMap as $key => $label)
                                <li class="d-flex justify-content-between align-items-center mb-1">
                                    <span>{{ $label }}</span>
                                    <span class="badge bg-light text-dark">
                                        {{ $analytics['status_counts']->get($key, 0) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <h6 class="text-muted mb-2">{{ __('Payment Status') }}</h6>
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
                            <small>{{ __('Amount associated with orders that are not yet paid.') }}</small>
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
                            <i class="mdi mdi-truck-delivery"></i> {{ __('Suppliers Performance (Benefited Suppliers)') }}
                        </h4>
                        @if($analytics['suppliers_summary']->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-centered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Supplier') }}</th>
                                            <th>{{ __('Orders') }}</th>
                                            <th>{{ __('Items') }}</th>
                                            <th>{{ __('Revenue') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['suppliers_summary'] as $supplier)
                                            <tr>
                                                <td>{{ $supplier['supplier_name'] }}</td>
                                                <td>{{ $supplier['orders_count'] }}</td>
                                                <td>{{ $supplier['items_count'] }}</td>
                                                <td>{{ number_format($supplier['revenue'], 2) }} {{ __('EGP') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('No supplier activity found for this period.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="mdi mdi-hospital-building"></i> {{ __('Top Clinics') }}
                        </h4>
                        @if($analytics['clinics_summary']->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-centered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Clinic') }}</th>
                                            <th>{{ __('Orders') }}</th>
                                            <th>{{ __('Revenue') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['clinics_summary'] as $clinic)
                                            <tr>
                                                <td>{{ $clinic['clinic_name'] }}</td>
                                                <td>{{ $clinic['orders_count'] }}</td>
                                                <td>{{ number_format($clinic['revenue'], 2) }} {{ __('EGP') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('No clinic data for this period.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
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
                                            <th>{{ __('Suppliers Count') }}</th>
                                            <th>{{ __('Items') }}</th>
                                            <th>{{ __('Total') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Payment') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['recent_orders'] as $order)
                                            <tr>
                                                <td>{{ $order->number }}</td>
                                                <td>{{ $order->clinic->name ?? 'N/A' }}</td>
                                                <td>{{ $order->items->pluck('supplier_id')->unique()->count() }}</td>
                                                <td>{{ $order->items->sum('quantity') }}</td>
                                                <td>{{ number_format($order->total, 2) }} {{ __('EGP') }}</td>
                                                <td><span class="badge bg-light text-dark">{{ ucfirst($order->status) }}</span></td>
                                                <td><span class="badge bg-light text-dark">{{ ucfirst($order->payment_status) }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('No recent orders found.') }}</p>
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
            @if($analytics['orders_by_date']->isNotEmpty())
            const ctx = document.getElementById('ordersRevenueChart');
            if (ctx && window.Chart) {
                const labels = {!! json_encode($analytics['orders_by_date']->keys()->values()) !!};
                const ordersData = {!! json_encode($analytics['orders_by_date']->values()->values()) !!};
                const revenueData = {!! json_encode($analytics['revenue_by_date']->values()->map(fn($v) => round($v, 2))->values()) !!};

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                type: 'bar',
                                label: '{{ __("Orders") }}',
                                data: ordersData,
                                backgroundColor: 'rgba(53, 184, 224, 0.5)',
                                borderColor: '#35b8e0',
                                borderWidth: 1,
                                yAxisID: 'y',
                            },
                            {
                                type: 'line',
                                label: '{{ __("Revenue") }}',
                                data: revenueData,
                                borderColor: '#5b69bc',
                                backgroundColor: 'rgba(91,105,188,0.15)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                yAxisID: 'y1',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        stacked: false,
                        scales: {
                            y: {
                                type: 'linear',
                                position: 'left',
                                beginAtZero: true,
                                ticks: { precision: 0 },
                                title: {
                                    display: true,
                                    text: '{{ __("Orders") }}'
                                }
                            },
                            y1: {
                                type: 'linear',
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false,
                                },
                                title: {
                                    display: true,
                                    text: '{{ __("Revenue (EGP)") }}'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
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

