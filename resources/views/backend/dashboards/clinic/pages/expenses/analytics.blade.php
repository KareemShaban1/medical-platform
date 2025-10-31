@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Expenses Analytics'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.expenses.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Expenses') }}
                    </a>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-chart-line text-primary"></i> {{ __('Expenses Analytics Dashboard') }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('clinic.expenses.analytics') }}" id="filterForm">
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
                    <h5 class="text-muted fw-normal mt-0">{{ __('Total Amount') }}</h5>
                    <h3 class="mt-3 mb-3">{{ number_format($analytics['total_amount'], 2) }} {{ __('EGP') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-receipt-outline widget-icon bg-primary-lighten text-primary"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">{{ __('Total Records') }}</h5>
                    <h3 class="mt-3 mb-3">{{ $analytics['total_records'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-chart-bar widget-icon bg-info-lighten text-info"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">{{ __('Average Expense') }}</h5>
                    <h3 class="mt-3 mb-3">{{ number_format($analytics['average_amount'], 2) }} {{ __('EGP') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-shape-plus widget-icon bg-warning-lighten text-warning"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0">{{ __('Top Category') }}</h5>
                    <h3 class="mt-3 mb-3">{{ $analytics['top_category'] ?? '-' }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-timeline-variant"></i> {{ __('Expenses Trend') }}
                    </h4>
                    <div style="position: relative; height: 350px;">
                        <canvas id="expensesTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-donut"></i> {{ __('Category Breakdown') }}
                    </h4>
                    <div style="position: relative; height: 350px;">
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Expenses -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-history"></i> {{ __('Recent Expenses') }}
                    </h4>
                    @if($analytics['recent_expenses']->count())
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Supplier') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['recent_expenses'] as $expense)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</td>
                                    <td>{{ $expense->category?->name ?? '-' }}</td>
                                    <td>{{ $expense->supplier?->name ?? '-' }}</td>
                                    <td>{{ number_format($expense->amount, 2) }} {{ __('EGP') }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($expense->notes, 40) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-cash-remove text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">{{ __('No expenses found in this range') }}</p>
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
    const amountByDate = @json($analytics['amount_by_date']);
    const amountByCategory = @json($analytics['amount_by_category']);

    // Trend chart
    const dates = Object.keys(amountByDate).sort();
    const amounts = dates.map(d => amountByDate[d]);
    const trendEl = document.getElementById('expensesTrendChart');
    if (trendEl && dates.length) {
        new Chart(trendEl, {
            type: 'line',
            data: {
                labels: dates.map(date => new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
                datasets: [{
                    label: '{{ __("Amount") }}',
                    data: amounts,
                    borderColor: '#5b69bc',
                    backgroundColor: 'rgba(91, 105, 188, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#5b69bc',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Category pie
    const catLabels = Object.keys(amountByCategory);
    const catData = catLabels.map(k => amountByCategory[k]);
    const pieEl = document.getElementById('categoryPieChart');
    if (pieEl && catLabels.length) {
        new Chart(pieEl, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: ['#5b69bc', '#10c469', '#ff5b5b', '#35b8e0', '#f9c851', '#6c757d', '#f1556c'],
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
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                                const v = ctx.parsed || 0;
                                const p = total>0 ? Math.round((v/total)*100) : 0;
                                return `${ctx.label}: ${v.toLocaleString()} {{ __('EGP') }} (${p}%)`;
                            }
                        }
                    }
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
.row > div { animation: fadeIn 0.5s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px);} to { opacity: 1; transform: translateY(0);} }
</style>
@endpush

