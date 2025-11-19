@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Subscriptions Analytics'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Subscriptions') }}
                    </a>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-chart-timeline-variant text-primary"></i> {{ __('Subscriptions Analytics Dashboard') }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.subscriptions.analytics') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">
                                    <i class="mdi mdi-calendar-start"></i> {{ __('Start Date') }}
                                </label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">
                                    <i class="mdi mdi-calendar-end"></i> {{ __('End Date') }}
                                </label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="mdi mdi-filter"></i> {{ __('Apply Filters') }}
                                </button>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('today')">{{ __('Today') }}</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('week')">{{ __('This Week') }}</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('month')">{{ __('This Month') }}</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('last_month')">{{ __('Last Month') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-currency-usd widget-icon bg-success-lighten text-success"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">{{ __('Total Revenue') }}</h5>
                    <h3 class="mt-3 mb-1">{{ number_format($analytics['total_revenue'], 2) }} {{ __('EGP') }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-success fw-bold">{{ $analytics['total_count'] }}</span>
                        <span class="ms-1">{{ __('subscriptions in range') }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-account-multiple-outline widget-icon bg-primary-lighten text-primary"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">{{ __('Active Subscriptions') }}</h5>
                    <h3 class="mt-3 mb-1">{{ $analytics['status_counts']['active'] }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-muted">{{ __('Expired') }}: {{ $analytics['status_counts']['expired'] }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-chart-bar widget-icon bg-info-lighten text-info"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">{{ __('Avg Revenue per Subscription') }}</h5>
                    <h3 class="mt-3 mb-1">{{ number_format($analytics['average_revenue'], 2) }} {{ __('EGP') }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-muted">{{ __('Pending') }}: {{ $analytics['status_counts']['pending'] }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-close-octagon widget-icon bg-danger-lighten text-danger"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">{{ __('Canceled Subscriptions') }}</h5>
                    <h3 class="mt-3 mb-1">{{ $analytics['status_counts']['canceled'] }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-muted">{{ __('Total') }}: {{ $analytics['total_count'] }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-line"></i> {{ __('Subscriptions & Revenue Trend') }}
                    </h4>
                    <div style="position: relative; height: 380px;">
                        <canvas id="subscriptionsTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-donut"></i> {{ __('By Plan Type') }}
                    </h4>
                    <div style="position: relative; height: 220px;">
                        <canvas id="planTypePieChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-shield-check"></i> {{ __('By Status') }}
                    </h4>
                    <div style="position: relative; height: 220px;">
                        <canvas id="statusDonutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Plans & Entities -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-star-outline"></i> {{ __('Top Plans') }}
                    </h4>
                    @if($analytics['top_plans']->count())
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Plan') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Level') }}</th>
                                    <th>{{ __('Subscriptions') }}</th>
                                    <th>{{ __('Revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['top_plans'] as $planName => $info)
                                <tr>
                                    <td>{{ $planName }}</td>
                                    <td>{{ ucfirst($info['plan_type'] ?? '-') }}</td>
                                    <td>{{ ucfirst($info['level'] ?? '-') }}</td>
                                    <td>{{ $info['count'] }}</td>
                                    <td>{{ number_format($info['revenue'], 2) }} {{ __('EGP') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted mb-0">{{ __('No subscriptions found in this range.') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-account-star-outline"></i> {{ __('Top Subscribers') }}
                    </h4>
                    @if($analytics['top_entities']->count())
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Entity') }}</th>
                                    <th>{{ __('Plan Type') }}</th>
                                    <th>{{ __('Subscriptions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['top_entities'] as $entityName => $info)
                                <tr>
                                    <td>{{ $entityName }}</td>
                                    <td>{{ ucfirst($info['plan_type'] ?? '-') }}</td>
                                    <td>{{ $info['count'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted mb-0">{{ __('No subscriptions found in this range.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Subscriptions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-history"></i> {{ __('Recent Subscriptions') }}
                    </h4>
                    @if($analytics['recent_subscriptions']->count())
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Entity') }}</th>
                                    <th>{{ __('Plan') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Price') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['recent_subscriptions'] as $sub)
                                <tr>
                                    <td>{{ $sub->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @php
                                            $type = class_basename($sub->subscribable_type);
                                            $name = $sub->subscribable->name ?? '-';
                                        @endphp
                                        {{ $name }} ({{ $type }})
                                    </td>
                                    <td>{{ $sub->plan->name ?? '-' }}</td>
                                    <td>{{ ucfirst($sub->plan->plan_type ?? '-') }}</td>
                                    <td>{{ ucfirst($sub->status) }}</td>
                                    <td>{{ number_format($sub->plan->price ?? 0, 2) }} {{ __('EGP') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-clipboard-alert-outline text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">{{ __('No subscriptions found in this range') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function setDateRange(range) {
    const today = new Date();
    let startDate, endDate;

    switch(range) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'week':
            const startOfWeek = new Date();
            startOfWeek.setDate(today.getDate() - today.getDay());
            startDate = startOfWeek.toISOString().split('T')[0];
            endDate = new Date().toISOString().split('T')[0];
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
        case 'last_month':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), today.getMonth(), 0).toISOString().split('T')[0];
            break;
    }

    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    document.getElementById('filterForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const subsByDate = @json($analytics['subscriptions_by_date']);
    const revenueByDate = @json($analytics['revenue_by_date']);
    const byPlanType = @json($analytics['subscriptions_by_plan_type']);
    const statusCounts = @json($analytics['status_counts']);

    const dates = Object.keys(subsByDate).sort();
    const subsCounts = dates.map(d => subsByDate[d]);
    const revenueCounts = dates.map(d => revenueByDate[d] || 0);

    const trendEl = document.getElementById('subscriptionsTrendChart');
    if (trendEl && dates.length) {
        new Chart(trendEl, {
            type: 'line',
            data: {
                labels: dates.map(date => new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
                datasets: [
                    {
                        label: '{{ __("Subscriptions") }}',
                        data: subsCounts,
                        borderColor: '#5b69bc',
                        backgroundColor: 'rgba(91, 105, 188, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y',
                    },
                    {
                        label: '{{ __("Revenue") }}',
                        data: revenueCounts,
                        borderColor: '#10c469',
                        backgroundColor: 'rgba(16, 196, 105, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.4,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: { display: true, text: '{{ __("Subscriptions") }}' }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: '{{ __("Revenue (EGP)") }}' }
                    }
                }
            }
        });
    }

    // Plan type pie
    const planTypeLabels = Object.keys(byPlanType);
    const planTypeData = planTypeLabels.map(k => byPlanType[k]);
    const planTypeEl = document.getElementById('planTypePieChart');
    if (planTypeEl && planTypeLabels.length) {
        new Chart(planTypeEl, {
            type: 'doughnut',
            data: {
                labels: planTypeLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
                datasets: [{
                    data: planTypeData,
                    backgroundColor: ['#5b69bc', '#10c469', '#ff5b5b', '#35b8e0', '#f9c851'],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true } },
                }
            }
        });
    }

    // Status donut
    const statusLabels = Object.keys(statusCounts);
    const statusData = statusLabels.map(k => statusCounts[k]);
    const statusEl = document.getElementById('statusDonutChart');
    if (statusEl && statusLabels.length) {
        new Chart(statusEl, {
            type: 'doughnut',
            data: {
                labels: statusLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#10c469', '#ff5b5b', '#f9c851', '#6c757d'],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true } },
                }
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.widget-flat { transition: transform 0.2s, box-shadow 0.2s; }
.widget-flat:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
.widget-icon { height: 48px; width: 48px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 24px; }
.card { border: none; box-shadow: 0 0 35px 0 rgba(154,161,171,.15); border-radius: 8px; }
.row > div { animation: fadeIn 0.4s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(15px);} to { opacity: 1; transform: translateY(0);} }
</style>
@endpush

