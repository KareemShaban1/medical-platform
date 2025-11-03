@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Appointments Analytics'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.appointments.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Appointments') }}
                    </a>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-chart-line text-primary"></i> {{ __('Appointments Analytics Dashboard') }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('clinic.appointments.analytics', $selectedDoctor?->id ?? 0) }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="doctor_id" class="form-label">
                                    <i class="mdi mdi-doctor"></i> {{ __('Select Doctor') }}
                                </label>
                                <select class="form-select" id="doctor_id" name="doctor_id" onchange="changeDoctor()">
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ $selectedDoctor && $selectedDoctor->id == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="mdi mdi-filter"></i> {{ __('Apply Filters') }}
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('today')">
                                        {{ __('Today') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('week')">
                                        {{ __('This Week') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('month')">
                                        {{ __('This Month') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('last_month')">
                                        {{ __('Last Month') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setDateRange('quarter')">
                                        {{ __('This Quarter') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($selectedDoctor)
    <!-- Doctor Info Banner -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white mb-1">
                                <i class="mdi mdi-doctor"></i> {{ $selectedDoctor->name }}
                            </h3>
                            <p class="mb-0 opacity-75">
                                {{ $selectedDoctor->speciality->name ?? __('General Practice') }} •
                                {{ __('Viewing data from') }} {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}
                                {{ __('to') }} {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <h4 class="text-white mb-0">
                                {{ $analytics['total_appointments'] }} {{ __('Appointments') }}
                            </h4>
                            <p class="mb-0 opacity-75">{{ __('In selected period') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row">
        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-check-circle widget-icon bg-success-lighten text-success"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="{{ __('Completed') }}">{{ __('Completed') }}</h5>
                    <h3 class="mt-3 mb-3">{{ $analytics['completed'] }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge bg-success me-1">{{ number_format($analytics['completion_rate'], 1) }}%</span>
                        <span class="text-nowrap">{{ __('Completion Rate') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-calendar-clock widget-icon bg-warning-lighten text-warning"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="{{ __('Confirmed') }}">{{ __('Confirmed') }}</h5>
                    <h3 class="mt-3 mb-3">{{ $analytics['confirmed'] }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">{{ __('Active bookings') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-cash-multiple widget-icon bg-primary-lighten text-primary"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="{{ __('Revenue') }}">{{ __('Total Revenue') }}</h5>
                    <h3 class="mt-3 mb-3">{{ number_format($analytics['total_revenue'], 2) }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-success">
                            <i class="mdi mdi-arrow-up-bold"></i> {{ __('EGP') }}
                        </span>
                        <span class="text-nowrap">{{ $analytics['paid_count'] }} {{ __('paid') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-close-circle widget-icon bg-danger-lighten text-danger"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="{{ __('Cancelled') }}">{{ __('Cancelled') }}</h5>
                    <h3 class="mt-3 mb-3">{{ $analytics['cancelled'] }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge bg-danger me-1">{{ number_format($analytics['cancellation_rate'], 1) }}%</span>
                        <span class="text-nowrap">{{ __('Cancellation Rate') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <!-- Appointments Trend -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-timeline-variant"></i> {{ __('Appointments Trend') }}
                    </h4>
                    <div style="position: relative; height: 350px;">
                        <canvas id="appointmentsTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-donut"></i> {{ __('Status Overview') }}
                    </h4>
                    <div style="position: relative; height: 350px;">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <!-- Visit Types -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-medical-bag"></i> {{ __('Visit Types Distribution') }}
                    </h4>
                    <div class="mt-4">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="mb-0">{{ __('Initial Visit') }}</h5>
                                <h5 class="mb-0 text-primary">{{ $analytics['initial_visits'] }}</h5>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-primary"
                                     style="width: {{ $analytics['total_appointments'] > 0 ? ($analytics['initial_visits'] / $analytics['total_appointments'] * 100) : 0 }}%">
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ $analytics['total_appointments'] > 0 ? number_format(($analytics['initial_visits'] / $analytics['total_appointments'] * 100), 1) : 0 }}%
                            </small>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="mb-0">{{ __('Follow-up') }}</h5>
                                <h5 class="mb-0 text-success">{{ $analytics['follow_ups'] }}</h5>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-success"
                                     style="width: {{ $analytics['total_appointments'] > 0 ? ($analytics['follow_ups'] / $analytics['total_appointments'] * 100) : 0 }}%">
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ $analytics['total_appointments'] > 0 ? number_format(($analytics['follow_ups'] / $analytics['total_appointments'] * 100), 1) : 0 }}%
                            </small>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="mb-0">{{ __('Consultation') }}</h5>
                                <h5 class="mb-0 text-info">{{ $analytics['consultations'] }}</h5>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-info"
                                     style="width: {{ $analytics['total_appointments'] > 0 ? ($analytics['consultations'] / $analytics['total_appointments'] * 100) : 0 }}%">
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ $analytics['total_appointments'] > 0 ? number_format(($analytics['consultations'] / $analytics['total_appointments'] * 100), 1) : 0 }}%
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-currency-usd"></i> {{ __('Revenue Breakdown') }}
                    </h4>
                    <div class="mt-4">
                        <div class="text-center mb-4">
                            <h2 class="text-success mb-1">{{ number_format($analytics['total_revenue'], 2) }} {{ __('EGP') }}</h2>
                            <p class="text-muted mb-0">{{ __('Total Collected') }}</p>
                        </div>

                        <div class="row text-center mb-4">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="mb-1">{{ $analytics['paid_count'] }}</h4>
                                    <p class="text-muted mb-0"><i class="mdi mdi-check-circle text-success"></i> {{ __('Paid') }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-1">{{ $analytics['pending_payment_count'] }}</h4>
                                <p class="text-muted mb-0"><i class="mdi mdi-clock-outline text-warning"></i> {{ __('Pending') }}</p>
                            </div>
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <i class="mdi mdi-alert-circle-outline me-2"></i>
                            <strong>{{ number_format($analytics['pending_revenue'], 2) }} {{ __('EGP') }}</strong>
                            <br>
                            <small>{{ __('Pending Payment Collection') }}</small>
                        </div>

                        @if($analytics['average_cost'] > 0)
                        <div class="text-center">
                            <p class="text-muted mb-1">{{ __('Average Appointment Cost') }}</p>
                            <h4 class="text-primary mb-0">{{ number_format($analytics['average_cost'], 2) }} {{ __('EGP') }}</h4>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-information-outline"></i> {{ __('Quick Statistics') }}
                    </h4>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                            <div>
                                <i class="mdi mdi-calendar-star text-primary fs-3"></i>
                                <span class="ms-2">{{ __('Busiest Day') }}</span>
                            </div>
                            <h5 class="mb-0 text-primary">{{ $analytics['busiest_day'] ?? '-' }}</h5>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                            <div>
                                <i class="mdi mdi-clock-check-outline text-success fs-3"></i>
                                <span class="ms-2">{{ __('Pending') }}</span>
                            </div>
                            <h5 class="mb-0 text-warning">{{ $analytics['pending'] }}</h5>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                            <div>
                                <i class="mdi mdi-seat text-info fs-3"></i>
                                <span class="ms-2">{{ __('Waiting') }}</span>
                            </div>
                            <h5 class="mb-0 text-info">{{ $analytics['waiting'] }}</h5>
                        </div>

                        <div class="alert alert-info mb-0" role="alert">
                            <i class="mdi mdi-lightbulb-on-outline me-2"></i>
                            @if($analytics['completion_rate'] >= 80)
                                <strong>{{ __('Excellent Performance!') }}</strong>
                                <p class="mb-0 small">{{ __('High completion rate indicates efficient service delivery') }}</p>
                            @elseif($analytics['completion_rate'] >= 60)
                                <strong>{{ __('Good Performance') }}</strong>
                                <p class="mb-0 small">{{ __('Room for improvement in completion rate') }}</p>
                            @else
                                <strong>{{ __('Needs Attention') }}</strong>
                                <p class="mb-0 small">{{ __('Consider reviewing appointment workflow') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-clock-outline"></i> {{ __('Recent Appointments') }}
                    </h4>
                    @if($analytics['recent_appointments']->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Time') }}</th>
                                    <th>{{ __('Patient') }}</th>
                                    <th>{{ __('Visit Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Cost') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['recent_appointments'] as $appointment)
                                <tr>
                                    <td>{{ $appointment->period->date->format('M d, Y') }}</td>
                                    <td>{{ $appointment->period->start_time }}</td>
                                    <td>
                                        <strong>{{ $appointment->patient->user->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $appointment->visit_type_label }}</span>
                                    </td>
                                    <td>{!! $appointment->status_badge !!}</td>
                                    <td>
                                        @if($appointment->cost_amount)
                                            {{ number_format($appointment->cost_amount, 2) }} {{ __('EGP') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('clinic.appointments.show', $appointment->id) }}"
                                           class="btn btn-sm btn-info text-white">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-calendar-remove text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">{{ __('No appointments found') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-doctor text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">{{ __('No doctor selected') }}</h4>
                    <p class="text-muted">{{ __('Please select a doctor to view analytics') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Date range quick filters
function setDateRange(range) {
    const today = new Date();
    let startDate, endDate;

    switch(range) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'week':
            const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
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
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), quarter * 3 + 3, 0).toISOString().split('T')[0];
            break;
    }

    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    document.getElementById('filterForm').submit();
}

function changeDoctor() {
    const doctorId = document.getElementById('doctor_id').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    window.location.href = `/clinic/appointments/${doctorId}/analytics?start_date=${startDate}&end_date=${endDate}`;
}

@if($selectedDoctor && $analytics['total_appointments'] > 0)
document.addEventListener('DOMContentLoaded', function() {
    // Prepare data for charts
    const appointmentsByDate = @json($analytics['appointments_by_date']);
    const appointmentsByStatusAndDate = @json($analytics['appointments_by_status_and_date']);

    const dates = Object.keys(appointmentsByDate).sort();
    const counts = dates.map(date => appointmentsByDate[date]);

    // Trend Chart
    const trendCtx = document.getElementById('appointmentsTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: dates.map(date => new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
                datasets: [{
                    label: '{{ __("Appointments") }}',
                    data: counts,
                    borderColor: '#5b69bc',
                    backgroundColor: 'rgba(91, 105, 188, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#5b69bc',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Status Pie Chart
    const statusCtx = document.getElementById('statusPieChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    '{{ __("Completed") }}',
                    '{{ __("Confirmed") }}',
                    '{{ __("Cancelled") }}',
                    '{{ __("Pending") }}',
                    '{{ __("Waiting") }}'
                ],
                datasets: [{
                    data: [
                        {{ $analytics['completed'] }},
                        {{ $analytics['confirmed'] }},
                        {{ $analytics['cancelled'] }},
                        {{ $analytics['pending'] }},
                        {{ $analytics['waiting'] }}
                    ],
                    backgroundColor: [
                        '#10c469',
                        '#ffaa00',
                        '#ff5b5b',
                        '#5b69bc',
                        '#35b8e0'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
@endif
</script>
@endpush

@push('styles')
<style>
.widget-flat {
    transition: transform 0.2s, box-shadow 0.2s;
}

.widget-flat:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.widget-icon {
    height: 48px;
    width: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 24px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #5b69bc 0%, #4a5aa9 100%);
}

.card {
    border: none;
    box-shadow: 0 0 35px 0 rgba(154,161,171,.15);
    border-radius: 8px;
}

.progress {
    background-color: rgba(91, 105, 188, 0.1);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.row > div {
    animation: fadeIn 0.5s ease-in-out;
}
</style>
@endpush

