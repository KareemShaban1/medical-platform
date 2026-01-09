@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Dashboard'))

@push('styles')
    <style>
        /* Dashboard Styles */
        .dashboard-welcome {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        .dashboard-welcome h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .dashboard-welcome p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card .stat-icon.primary {
            background: rgba(102, 126, 234, 0.15);
            color: #667eea;
        }

        .stat-card .stat-icon.success {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .stat-card .stat-icon.warning {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .stat-card .stat-icon.danger {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .stat-card .stat-icon.info {
            background: rgba(23, 162, 184, 0.15);
            color: #17a2b8;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
        }

        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .stat-card .stat-change {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .stat-card .stat-change.positive {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .stat-card .stat-change.negative {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .quick-action-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-decoration: none;
            display: block;
            height: 100%;
        }

        .quick-action-card:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        }

        .quick-action-card .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin: 0 auto 0.75rem;
            color: white;
        }

        .quick-action-card .action-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .quick-action-card .action-desc {
            color: #6c757d;
            font-size: 0.75rem;
            margin: 0;
        }

        .dashboard-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .dashboard-card .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dashboard-card .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: #2c3e50;
        }

        .dashboard-card .card-body {
            padding: 1.5rem;
        }

        .subscription-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .subscription-badge.active {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .subscription-badge.expired {
            background: #dc3545;
            color: white;
        }

        .appointment-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .appointment-item:hover {
            background: #f8f9fa;
        }

        .appointment-item .avatar {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 1rem;
        }

        .appointment-item .details {
            flex: 1;
        }

        .appointment-item .name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.125rem;
        }

        .appointment-item .meta {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .appointment-item .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .status-confirmed {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-completed {
            background: rgba(23, 162, 184, 0.15);
            color: #17a2b8;
        }

        .status-cancelled {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .chart-container {
            position: relative;
            height: 250px;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Announcement -->
        @if (isset($announcement) && $announcement)
            <div class="alert alert-info d-flex justify-content-between align-items-start mb-4" role="alert"
                id="dashboard-announcement" data-id="{{ $announcement->id }}">
                <div>
                    <div class="fw-bold"><i class="fas fa-bullhorn me-2"></i>{{ $announcement->title }}</div>
                    @if ($announcement->body)
                        <div class="mt-1">{!! nl2br(e($announcement->body)) !!}</div>
                    @endif
                    @if ($announcement->link_url)
                        <div class="mt-2"><a href="{{ $announcement->link_url }}" target="_blank"
                                class="btn btn-sm btn-primary">{{ __('Open Link') }}</a></div>
                    @endif
                </div>
                <button type="button" class="btn-close" aria-label="Close" id="dismiss-announcement"></button>
            </div>
        @endif

        <!-- Welcome Section -->
        <div class="dashboard-welcome">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>{{ __('Welcome back') }}, {{ auth('clinic')->user()->name }}! 👋</h2>
                    <p>{{ __('Here\'s what\'s happening at') }} {{ $clinic->name }} {{ __('today') }}.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    @if ($subscription)
                        <span class="subscription-badge active">
                            <i class="fas fa-crown me-1"></i>{{ $subscription->plan->name ?? __('Active Plan') }}
                        </span>
                    @else
                        <a href="{{ route('clinic.subscriptions.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-rocket me-1"></i>{{ __('Upgrade Plan') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <div class="stat-value">{{ $totalAppointmentsToday }}</div>
                            <div class="stat-label">{{ __('Appointments Today') }}</div>
                        </div>
                        <div class="text-end">
                            <small class="text-success d-block"><i class="fas fa-check-circle"></i>
                                {{ $confirmedAppointmentsToday }} {{ __('confirmed') }}</small>
                            <small class="text-warning"><i class="fas fa-clock"></i> {{ $pendingAppointmentsToday }}
                                {{ __('pending') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <div class="stat-value">{{ $totalPatients }}</div>
                            <div class="stat-label">{{ __('Total Patients') }}</div>
                        </div>
                        @if ($patientsGrowth != 0)
                            <span class="stat-change {{ $patientsGrowth > 0 ? 'positive' : 'negative' }}">
                                <i class="fas fa-{{ $patientsGrowth > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ abs($patientsGrowth) }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <div class="stat-value">{{ $totalDoctors }}</div>
                            <div class="stat-label">{{ __('Doctors') }}</div>
                        </div>
                        <small class="text-success"><i class="fas fa-check-circle"></i> {{ $activeDoctors }}
                            {{ __('active') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <div class="stat-value">{{ number_format($expensesThisMonth, 0) }}</div>
                            <div class="stat-label">{{ __('Expenses This Month') }}</div>
                        </div>
                        <small class="text-muted">{{ __('EGP') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-bolt me-2 text-warning"></i>{{ __('Quick Actions') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($quickActions as $action)
                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <a href="{{ route($action['route']) }}" class="quick-action-card">
                                <div class="action-icon bg-{{ $action['color'] }}">
                                    <i class="{{ $action['icon'] }}"></i>
                                </div>
                                <div class="action-title">{{ $action['title'] }}</div>
                                <p class="action-desc">{{ $action['description'] }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Weekly Appointments Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line me-2 text-primary"></i>{{ __('Weekly Appointments') }}</h5>
                        <span class="badge bg-primary">{{ $totalAppointmentsMonth }} {{ __('this month') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="weeklyAppointmentsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Status Distribution -->
            <div class="col-xl-4 col-lg-5">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-pie me-2 text-info"></i>{{ __('Appointment Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="appointmentStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Upcoming Appointments -->
            <div class="col-xl-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-alt me-2 text-success"></i>{{ __('Upcoming Appointments') }}</h5>
                        <a href="{{ route('clinic.appointments.index') }}"
                            class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                    <div class="card-body p-0">
                        @if ($upcomingAppointments->count() > 0)
                            @foreach ($upcomingAppointments as $appointment)
                                <div class="appointment-item">
                                    <div class="avatar">
                                        {{ strtoupper(substr($appointment->patient->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div class="details">
                                        <div class="name">{{ $appointment->patient->name ?? __('Unknown Patient') }}
                                        </div>
                                        <div class="meta">
                                            <i
                                                class="far fa-clock me-1"></i>{{ optional($appointment->period)->date ? \Carbon\Carbon::parse($appointment->period->date)->format('M d, Y') : __('N/A') }}
                                            @if ($appointment->doctorProfile)
                                                · <i
                                                    class="fas fa-user-md me-1"></i>{{ $appointment->doctorProfile->name ?? '' }}
                                            @endif
                                        </div>
                                    </div>
                                    <span
                                        class="status-badge status-{{ $appointment->status }}">{{ __(ucfirst($appointment->status)) }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>{{ __('No upcoming appointments') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Patients -->
            <div class="col-xl-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-user-plus me-2 text-info"></i>{{ __('Recent Patients') }}</h5>
                        <a href="{{ route('clinic.patients.index') }}"
                            class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                    <div class="card-body p-0">
                        @if ($recentPatients->count() > 0)
                            @foreach ($recentPatients as $patient)
                                <div class="appointment-item">
                                    <div class="avatar" style="background: linear-gradient(135deg, #17a2b8, #20c997);">
                                        {{ strtoupper(substr($patient->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div class="details">
                                        <div class="name">{{ $patient->name }}</div>
                                        <div class="meta">
                                            @if ($patient->phone)
                                                <i class="fas fa-phone me-1"></i>{{ $patient->phone }}
                                            @endif
                                            · {{ __('Joined') }} {{ $patient->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>{{ __('No patients yet') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="stat-value">{{ $totalStaff }}</div>
                    <div class="stat-label">{{ __('Staff Members') }}</div>
                    <small class="text-success"><i class="fas fa-check"></i> {{ $activeStaff }}
                        {{ __('active') }}</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon danger">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-value">{{ $activePurchaseRequests }}</div>
                    <div class="stat-label">{{ __('Active Requests') }}</div>
                    <small class="text-warning"><i class="fas fa-handshake"></i> {{ $pendingOffers }}
                        {{ __('with offers') }}</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value">{{ $totalAppointmentsMonth }}</div>
                    <div class="stat-label">{{ __('Appointments This Month') }}</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-value">{{ $newPatientsThisMonth }}</div>
                    <div class="stat-label">{{ __('New Patients This Month') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            // Dismiss Announcement
            $('#dismiss-announcement').on('click', function() {
                var id = $('#dashboard-announcement').data('id');
                $.ajax({
                    url: "{{ route('clinic.announcements.dismiss', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    complete: function() {
                        $('#dashboard-announcement').remove();
                    }
                });
            });

            // Weekly Appointments Chart
            const weeklyData = @json($weeklyAppointments);
            new Chart(document.getElementById('weeklyAppointmentsChart'), {
                type: 'bar',
                data: {
                    labels: weeklyData.map(d => d.date),
                    datasets: [{
                        label: '{{ __('Appointments') }}',
                        data: weeklyData.map(d => d.count),
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Appointment Status Chart
            const statusData = @json($appointmentStatusData);
            const statusLabels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1));
            const statusValues = Object.values(statusData);
            const statusColors = {
                'pending': '#ffc107',
                'confirmed': '#28a745',
                'completed': '#17a2b8',
                'cancelled': '#dc3545'
            };

            if (statusValues.length > 0) {
                new Chart(document.getElementById('appointmentStatusChart'), {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusValues,
                            backgroundColor: Object.keys(statusData).map(s => statusColors[s] ||
                                '#6c757d'),
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
@endpush
