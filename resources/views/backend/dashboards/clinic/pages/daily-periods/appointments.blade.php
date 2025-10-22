@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Period Appointments'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.daily-periods.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Periods') }}
                    </a>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-calendar-check text-primary"></i> {{ __('Appointments Queue') }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Period Information Card -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-primary text-white mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h5 class="text-white mb-1">
                                <i class="mdi mdi-doctor"></i> {{ $period->doctorProfile->name }}
                            </h5>
                            <p class="mb-0 opacity-75">{{ $period->doctorProfile->speciality->name ?? 'General' }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-white mb-1">
                                <i class="mdi mdi-calendar"></i> {{ __('Date') }}
                            </h6>
                            <p class="mb-0 fs-5">{{ $period->date->format('l, F j, Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-white mb-1">
                                <i class="mdi mdi-clock-outline"></i> {{ __('Time') }}
                            </h6>
                            <p class="mb-0 fs-5">{{ $period->start_time }} - {{ $period->end_time }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-white mb-1">
                                <i class="mdi mdi-seat"></i> {{ __('Capacity') }}
                            </h6>
                            <p class="mb-0 fs-5">{{ $period->booked_count }} / {{ $period->capacity }} {{ __('Booked') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row">
        <!-- Total Appointments -->
        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-clipboard-list float-end"></i>
                    <h6 class="text-uppercase mt-0">{{ __('Total Appointments') }}</h6>
                    <h2 class="my-2" id="active-users-count">{{ $analytics['total_appointments'] }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="badge bg-{{ $analytics['capacity_percentage'] >= 80 ? 'danger' : 'success' }} me-1">
                            {{ number_format($analytics['capacity_percentage'], 0) }}%
                        </span>
                        <span class="text-nowrap">{{ __('Capacity Used') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Confirmed -->
        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-check-circle text-success float-end fs-2"></i>
                    <h6 class="text-uppercase mt-0">{{ __('Confirmed') }}</h6>
                    <h2 class="my-2 text-success">{{ $analytics['confirmed'] }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">{{ __('Active bookings') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-check-all text-primary float-end fs-2"></i>
                    <h6 class="text-uppercase mt-0">{{ __('Completed') }}</h6>
                    <h2 class="my-2 text-primary">{{ $analytics['completed'] }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">{{ __('Finished visits') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Available Slots -->
        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-seat text-info float-end fs-2"></i>
                    <h6 class="text-uppercase mt-0">{{ __('Available Slots') }}</h6>
                    <h2 class="my-2 text-info">{{ $analytics['available_slots'] }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">{{ __('Open for booking') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Status Distribution -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-donut"></i> {{ __('Status Distribution') }}
                    </h4>
                    <div style="position: relative; height: 300px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visit Type Distribution -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-bar"></i> {{ __('Visit Types') }}
                    </h4>
                    <div class="mt-3">
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">
                                <i class="mdi mdi-circle text-primary"></i> {{ __('Initial Visit') }}
                                <span class="float-end">{{ $visitTypeStats['initial'] }}</span>
                            </p>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $analytics['total_appointments'] > 0 ? ($visitTypeStats['initial'] / $analytics['total_appointments'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">
                                <i class="mdi mdi-circle text-success"></i> {{ __('Follow-up') }}
                                <span class="float-end">{{ $visitTypeStats['follow_up'] }}</span>
                            </p>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $analytics['total_appointments'] > 0 ? ($visitTypeStats['follow_up'] / $analytics['total_appointments'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">
                                <i class="mdi mdi-circle text-info"></i> {{ __('Consultation') }}
                                <span class="float-end">{{ $visitTypeStats['consultation'] }}</span>
                            </p>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: {{ $analytics['total_appointments'] > 0 ? ($visitTypeStats['consultation'] / $analytics['total_appointments'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Statistics -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-cash-multiple"></i> {{ __('Payment Stats') }}
                    </h4>
                    <div class="mt-3">
                        <div class="mb-3">
                            <p class="mb-1 text-muted">{{ __('Total Revenue') }}</p>
                            <h3 class="text-success mb-0">{{ number_format($paymentStats['total_revenue'], 2) }} {{ __('EGP') }}</h3>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 text-muted">{{ __('Pending Revenue') }}</p>
                            <h3 class="text-warning mb-0">{{ number_format($paymentStats['pending_revenue'], 2) }} {{ __('EGP') }}</h3>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <p class="text-muted mb-1">{{ __('Paid') }}</p>
                                <h4 class="text-success mb-0">{{ $paymentStats['paid'] }}</h4>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1">{{ __('Pending') }}</p>
                                <h4 class="text-warning mb-0">{{ $paymentStats['pending'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Queue -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-format-list-numbered"></i> {{ __('Appointments Queue') }}
                    </h4>

                    @if($appointments->isEmpty())
                        <div class="text-center py-5">
                            <i class="mdi mdi-calendar-remove text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">{{ __('No appointments scheduled for this period') }}</h5>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Queue') }}</th>
                                        <th>{{ __('Patient') }}</th>
                                        <th>{{ __('Visit Type') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Cost') }}</th>
                                        <th>{{ __('Payment') }}</th>
                                        <th>{{ __('Booked At') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary fs-5" style="width: 40px; height: 40px; line-height: 28px; border-radius: 50%;">
                                                {{ $appointment->slot_number ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <p class="mb-0 fw-semibold">{{ $appointment->patient->user->name ?? 'N/A' }}</p>
                                                <small class="text-muted">ID: {{ $appointment->patient_id }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $appointment->visit_type_label }}
                                            </span>
                                        </td>
                                        <td>{!! $appointment->status_badge !!}</td>
                                        <td>
                                            @if($appointment->cost_amount)
                                                <span class="fw-semibold">{{ number_format($appointment->cost_amount, 2) }} {{ __('EGP') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($appointment->payment_status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $appointment->booked_at ? $appointment->booked_at->format('M d, H:i') : '-' }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('clinic.appointments.show', $appointment->id) }}"
                                               class="btn btn-sm btn-info text-white"
                                               title="{{ __('View Details') }}">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
document.addEventListener('DOMContentLoaded', function() {
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart');

    if (statusCtx && typeof Chart !== 'undefined') {
        try {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        '{{ __("Confirmed") }}',
                        '{{ __("Pending") }}',
                        '{{ __("Completed") }}',
                        '{{ __("Cancelled") }}',
                        '{{ __("Waiting") }}'
                    ],
                    datasets: [{
                        data: [
                            {{ $analytics['confirmed'] }},
                            {{ $analytics['pending'] }},
                            {{ $analytics['completed'] }},
                            {{ $analytics['cancelled'] }},
                            {{ $analytics['waiting'] }}
                        ],
                        backgroundColor: [
                            '#10c469', // success - confirmed
                            '#ffaa00', // warning - pending
                            '#5b69bc', // primary - completed
                            '#ff5b5b', // danger - cancelled
                            '#35b8e0'  // info - waiting
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
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
        } catch (error) {
            console.error('Error initializing chart:', error);
            // Hide chart container if there's an error
            if (statusCtx.parentElement) {
                statusCtx.parentElement.innerHTML = '<p class="text-muted text-center">Chart could not be loaded</p>';
            }
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.tilebox-one {
    transition: transform 0.2s, box-shadow 0.2s;
}

.tilebox-one:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.card {
    border: none;
    box-shadow: 0 0 35px 0 rgba(154,161,171,.15);
}

.table-hover tbody tr:hover {
    background-color: rgba(91, 105, 188, 0.05);
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

.card {
    animation: fadeIn 0.5s ease-in-out;
}

.row > div {
    animation: fadeIn 0.5s ease-in-out;
}

.row > div:nth-child(2) {
    animation-delay: 0.1s;
}

.row > div:nth-child(3) {
    animation-delay: 0.2s;
}

.row > div:nth-child(4) {
    animation-delay: 0.3s;
}
</style>
@endpush

