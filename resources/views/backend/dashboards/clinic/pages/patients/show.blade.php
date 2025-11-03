@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<style>
    .patient-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .patient-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        margin: 0 auto 20px;
        border: 4px solid rgba(255, 255, 255, 0.3);
    }

    .stat-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 20px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 50px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    }

    .timeline-item {
        position: relative;
        padding-left: 100px;
        margin-bottom: 30px;
    }

    .timeline-badge {
        position: absolute;
        left: 32px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .timeline-content {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .timeline-content:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        transform: translateX(5px);
    }

    .medical-record-card {
        border-left: 4px solid #28a745;
    }

    .prescription-card {
        border-left: 4px solid #17a2b8;
    }

    .appointment-card {
        border-left: 4px solid #ffc107;
    }

    .lab-order-card {
        border-left: 4px solid #dc3545;
    }

    .prescription-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        border-left: 3px solid #17a2b8;
    }

    .doctor-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-block;
        margin-right: 10px;
    }

    .nav-pills .nav-link {
        border-radius: 10px;
        padding: 12px 24px;
        margin: 0 5px;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
</style>

<div class="container-fluid">
    <!-- Patient Header -->
    <div class="patient-header">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <div class="patient-avatar">
                    <i class="mdi mdi-account"></i>
                </div>
                <h4 class="mb-0">{{ $patient->name }}</h4>
                <p class="mb-0 opacity-75">{{ $patient->phone }}</p>
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-3 col-6 text-center mb-3">
                        <h3 class="mb-0">{{ $stats['total_appointments'] }}</h3>
                        <small class="opacity-75">{{ __('Appointments') }}</small>
                    </div>
                    <div class="col-md-3 col-6 text-center mb-3">
                        <h3 class="mb-0">{{ $stats['total_medical_records'] }}</h3>
                        <small class="opacity-75">{{ __('Medical Records') }}</small>
                    </div>
                    <div class="col-md-3 col-6 text-center mb-3">
                        <h3 class="mb-0">{{ $stats['total_prescriptions'] }}</h3>
                        <small class="opacity-75">{{ __('Prescriptions') }}</small>
                    </div>
                    <div class="col-md-3 col-6 text-center mb-3">
                        <h3 class="mb-0">{{ $stats['total_lab_orders'] }}</h3>
                        <small class="opacity-75">{{ __('Lab Orders') }}</small>
                    </div>
                </div>

                <div class="mt-3">
                    <strong class="d-block mb-2">{{ __('Assigned Doctors') }}:</strong>
                    @forelse($assignedDoctors as $doctor)
                        <span class="doctor-badge">
                            <i class="mdi mdi-doctor"></i> {{ $doctor->name }}
                        </span>
                    @empty
                        <span class="opacity-75">{{ __('No doctors assigned') }}</span>
                    @endforelse
                </div>

                <div class="mt-3">
                    <a href="{{ route('clinic.patients.index') }}" class="btn btn-light btn-sm me-2">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back') }}
                    </a>
                    <button onclick="editPatient({{ $patient->id }})" class="btn btn-light btn-sm me-2">
                        <i class="fa fa-edit"></i> {{ __('Edit') }}
                    </button>
                    <button onclick="deletePatient({{ $patient->id }})" class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills justify-content-center mb-4" id="patientTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button">
                <i class="mdi mdi-view-dashboard"></i> {{ __('Overview') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="timeline-tab" data-bs-toggle="pill" data-bs-target="#timeline" type="button">
                <i class="mdi mdi-timeline-clock"></i> {{ __('Timeline') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="appointments-tab" data-bs-toggle="pill" data-bs-target="#appointments" type="button">
                <i class="mdi mdi-calendar-clock"></i> {{ __('Appointments') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="records-tab" data-bs-toggle="pill" data-bs-target="#records" type="button">
                <i class="mdi mdi-file-document"></i> {{ __('Medical Records') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="prescriptions-tab" data-bs-toggle="pill" data-bs-target="#prescriptions" type="button">
                <i class="mdi mdi-pill"></i> {{ __('Prescriptions') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="labs-tab" data-bs-toggle="pill" data-bs-target="#labs" type="button">
                <i class="mdi mdi-flask"></i> {{ __('Lab Orders') }}
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="patientTabContent">

        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <!-- Patient Information -->
                <div class="col-lg-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="mdi mdi-account-circle text-primary"></i> {{ __('Patient Information') }}
                            </h5>

                            <div class="mb-3">
                                <small class="text-muted d-block">{{ __('Full Name') }}</small>
                                <strong>{{ $patient->name }}</strong>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">{{ __('Phone') }}</small>
                                <strong>{{ $patient->phone }}</strong>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">{{ __('Email') }}</small>
                                <strong>{{ $patient->email ?: __('Not provided') }}</strong>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">{{ __('Patient Type') }}</small>
                                @if($patient->isRegistered())
                                    <span class="badge bg-success">{{ __('Registered User') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('Clinic Created') }}</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">{{ __('Member Since') }}</small>
                                <strong>{{ $patient->created_at->format('M d, Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-lg-8">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="mdi mdi-history text-info"></i> {{ __('Recent Activity') }}
                            </h5>

                            @php
                                // Combine all recent activities
                                $recentActivities = collect();

                                foreach($appointments as $apt) {
                                    $recentActivities->push([
                                        'type' => 'appointment',
                                        'date' => $apt->appointment_date . ' ' . $apt->appointment_time,
                                        'data' => $apt
                                    ]);
                                }

                                foreach($medicalRecords as $record) {
                                    $recentActivities->push([
                                        'type' => 'medical_record',
                                        'date' => $record->created_at,
                                        'data' => $record
                                    ]);
                                }

                                $recentActivities = $recentActivities->sortByDesc('date')->take(5);
                            @endphp

                            @forelse($recentActivities as $activity)
                                <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                                    <div class="me-3">
                                        @if($activity['type'] == 'appointment')
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                <i class="mdi mdi-calendar-clock text-warning"></i>
                                            </div>
                                        @else
                                            <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                <i class="mdi mdi-file-document text-success"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        @if($activity['type'] == 'appointment')
                                            <strong>{{ __('Appointment') }}</strong>
                                            <p class="mb-0 text-muted small">
                                                {{ __('With') }} Dr. {{ $activity['data']->doctorProfile->name }}<br>
                                                <i class="mdi mdi-clock-outline"></i> {{ \Carbon\Carbon::parse($activity['date'])->format('M d, Y - h:i A') }}
                                            </p>
                                        @else
                                            <strong>{{ __('Medical Record Created') }}</strong>
                                            <p class="mb-0 text-muted small">
                                                {{ __('By') }} Dr. {{ $activity['data']->doctor->name }}<br>
                                                <i class="mdi mdi-clock-outline"></i> {{ $activity['data']->created_at->format('M d, Y - h:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="mdi mdi-history mdi-48px opacity-25"></i>
                                    <p>{{ __('No recent activity') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Statistics -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card stat-card text-center bg-warning bg-opacity-10">
                        <div class="card-body">
                            <i class="mdi mdi-calendar-check stat-icon text-warning"></i>
                            <h3 class="mb-0">{{ $stats['completed_appointments'] }}</h3>
                            <small class="text-muted">{{ __('Completed Visits') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card text-center bg-success bg-opacity-10">
                        <div class="card-body">
                            <i class="mdi mdi-file-document-multiple stat-icon text-success"></i>
                            <h3 class="mb-0">{{ $stats['total_medical_records'] }}</h3>
                            <small class="text-muted">{{ __('Medical Records') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card text-center bg-info bg-opacity-10">
                        <div class="card-body">
                            <i class="mdi mdi-pill stat-icon text-info"></i>
                            <h3 class="mb-0">{{ $stats['total_prescriptions'] }}</h3>
                            <small class="text-muted">{{ __('Prescriptions') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card text-center bg-danger bg-opacity-10">
                        <div class="card-body">
                            <i class="mdi mdi-flask stat-icon text-danger"></i>
                            <h3 class="mb-0">{{ $stats['total_lab_orders'] }}</h3>
                            <small class="text-muted">{{ __('Lab Orders') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Tab -->
        <div class="tab-pane fade" id="timeline" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="timeline">
                        @php
                            // Combine all events for timeline
                            $timelineEvents = collect();

                            foreach($medicalRecords as $record) {
                                $timelineEvents->push([
                                    'type' => 'medical_record',
                                    'date' => $record->created_at,
                                    'data' => $record
                                ]);
                            }

                            foreach($prescriptions as $prescription) {
                                $timelineEvents->push([
                                    'type' => 'prescription',
                                    'date' => $prescription->created_at,
                                    'data' => $prescription
                                ]);
                            }

                            foreach($appointments as $appointment) {
                                $timelineEvents->push([
                                    'type' => 'appointment',
                                    'date' => $appointment->appointment_date . ' ' . $appointment->appointment_time,
                                    'data' => $appointment
                                ]);
                            }

                            foreach($labOrders as $labOrder) {
                                $timelineEvents->push([
                                    'type' => 'lab_order',
                                    'date' => $labOrder->created_at,
                                    'data' => $labOrder
                                ]);
                            }

                            $timelineEvents = $timelineEvents->sortByDesc('date');
                        @endphp

                        @forelse($timelineEvents as $event)
                            <div class="timeline-item">
                                @if($event['type'] == 'medical_record')
                                    <div class="timeline-badge bg-success">
                                        <i class="mdi mdi-file-document"></i>
                                    </div>
                                    <div class="timeline-content medical-record-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="mb-0">
                                                <i class="mdi mdi-file-document text-success"></i> {{ __('Medical Record') }}
                                            </h5>
                                            <small class="text-muted">{{ $event['data']->created_at->format('M d, Y - h:i A') }}</small>
                                        </div>
                                        <p class="mb-2">
                                            <strong>{{ __('Doctor') }}:</strong> Dr. {{ $event['data']->doctor->name }}
                                        </p>
                                        @if($event['data']->chief_complaint)
                                            <p class="mb-1"><strong>{{ __('Chief Complaint') }}:</strong> {{ $event['data']->chief_complaint }}</p>
                                        @endif
                                        @if($event['data']->diagnosis)
                                            <p class="mb-1"><strong>{{ __('Diagnosis') }}:</strong> {{ $event['data']->diagnosis }}</p>
                                        @endif
                                        @if($event['data']->treatment)
                                            <p class="mb-1"><strong>{{ __('Treatment') }}:</strong> {{ $event['data']->treatment }}</p>
                                        @endif
                                    </div>

                                @elseif($event['type'] == 'prescription')
                                    <div class="timeline-badge bg-info">
                                        <i class="mdi mdi-pill"></i>
                                    </div>
                                    <div class="timeline-content prescription-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="mb-0">
                                                <i class="mdi mdi-pill text-info"></i> {{ __('Prescription') }}
                                            </h5>
                                            <small class="text-muted">{{ $event['data']->created_at->format('M d, Y - h:i A') }}</small>
                                        </div>
                                        <p class="mb-2">
                                            <strong>{{ __('Doctor') }}:</strong> Dr. {{ $event['data']->doctorProfile->name }}
                                        </p>
                                        <div class="mt-3">
                                            <strong class="d-block mb-2">{{ __('Medications') }}:</strong>
                                            @foreach($event['data']->items as $item)
                                                <div class="prescription-item">
                                                    <strong>{{ $item->drug_name }}</strong>
                                                    @if($item->dose || $item->frequency || $item->duration)
                                                        <div class="small text-muted mt-1">
                                                            @if($item->dose) <span class="me-2"><i class="mdi mdi-medical-bag"></i> {{ $item->dose }}</span> @endif
                                                            @if($item->frequency) <span class="me-2"><i class="mdi mdi-clock-outline"></i> {{ $item->frequency }}</span> @endif
                                                            @if($item->duration) <span><i class="mdi mdi-calendar"></i> {{ $item->duration }}</span> @endif
                                                        </div>
                                                    @endif
                                                    @if($item->notes)
                                                        <small class="text-muted d-block mt-1">{{ __('Note') }}: {{ $item->notes }}</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                @elseif($event['type'] == 'appointment')
                                    <div class="timeline-badge bg-warning">
                                        <i class="mdi mdi-calendar-clock"></i>
                                    </div>
                                    <div class="timeline-content appointment-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="mb-0">
                                                <i class="mdi mdi-calendar-clock text-warning"></i> {{ __('Appointment') }}
                                            </h5>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($event['date'])->format('M d, Y - h:i A') }}</small>
                                        </div>
                                        <p class="mb-2">
                                            <strong>{{ __('Doctor') }}:</strong> Dr. {{ $event['data']->doctorProfile->name }}
                                        </p>
                                        <span class="badge bg-{{ $event['data']->status == 'completed' ? 'success' : ($event['data']->status == 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($event['data']->status) }}
                                        </span>
                                    </div>

                                @else
                                    <div class="timeline-badge bg-danger">
                                        <i class="mdi mdi-flask"></i>
                                    </div>
                                    <div class="timeline-content lab-order-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="mb-0">
                                                <i class="mdi mdi-flask text-danger"></i> {{ __('Lab Order') }}
                                            </h5>
                                            <small class="text-muted">{{ $event['data']->created_at->format('M d, Y - h:i A') }}</small>
                                        </div>
                                        <p class="mb-2">
                                            <strong>{{ __('Test') }}:</strong> {{ $event['data']->test_name }}
                                        </p>
                                        @if($event['data']->doctorProfile)
                                            <p class="mb-2">
                                                <strong>{{ __('Doctor') }}:</strong> Dr. {{ $event['data']->doctorProfile->name }}
                                            </p>
                                        @endif
                                        <span class="badge bg-{{ $event['data']->status == 'completed' ? 'success' : ($event['data']->status == 'received' ? 'info' : 'warning') }}">
                                            {{ ucfirst($event['data']->status) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="mdi mdi-timeline-clock"></i>
                                <h5>{{ __('No Medical History Yet') }}</h5>
                                <p class="text-muted">{{ __('Medical events will appear here as they are created') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Tab -->
        <div class="tab-pane fade" id="appointments" role="tabpanel">
            <div class="row">
                @forelse($appointments as $appointment)
                    <div class="col-md-6 mb-3">
                        <div class="card stat-card appointment-card h-100 position-relative">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">
                                        <i class="mdi mdi-calendar-clock text-warning"></i>
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                    </h5>
                                    <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>

                                <p class="mb-2">
                                    <i class="mdi mdi-clock-outline"></i> <strong>{{ __('Time') }}:</strong>
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </p>

                                <p class="mb-2">
                                    <i class="mdi mdi-doctor"></i> <strong>{{ __('Doctor') }}:</strong>
                                    Dr. {{ $appointment->doctorProfile->name }}
                                </p>

                                @if($appointment->prescription)
                                    <div class="mt-3 pt-3 border-top">
                                        <small class="text-success">
                                            <i class="mdi mdi-pill"></i> {{ __('Prescription attached') }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('clinic.appointments.show', $appointment->id) }}" class="stretched-link" aria-label="{{ __('View appointment details') }}"></a>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="mdi mdi-calendar-remove"></i>
                            <h5>{{ __('No Appointments') }}</h5>
                            <p class="text-muted">{{ __('No appointments found for this patient') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Medical Records Tab -->
        <div class="tab-pane fade" id="records" role="tabpanel">
            <div class="row">
                @forelse($medicalRecords as $record)
                    <div class="col-12 mb-3">
                        <div class="card stat-card medical-record-card position-relative">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">
                                            <i class="mdi mdi-file-document text-success"></i> {{ __('Medical Record') }}
                                        </h5>
                                        <small class="text-muted">{{ $record->created_at->format('M d, Y - h:i A') }}</small>
                                    </div>
                                    <span class="badge bg-primary">
                                        {{ ['Initial Visit', 'Follow-up', 'Consultation'][$record->visit_type] ?? 'Unknown' }}
                                    </span>
                                </div>

                                <p class="mb-3">
                                    <i class="mdi mdi-doctor"></i> <strong>{{ __('Doctor') }}:</strong>
                                    Dr. {{ $record->doctor->name }}
                                </p>

                                <div class="row">
                                    @if($record->chief_complaint)
                                        <div class="col-md-6 mb-3">
                                            <strong class="text-muted d-block mb-1">{{ __('Chief Complaint') }}</strong>
                                            <p>{{ $record->chief_complaint }}</p>
                                        </div>
                                    @endif

                                    @if($record->diagnosis)
                                        <div class="col-md-6 mb-3">
                                            <strong class="text-muted d-block mb-1">{{ __('Diagnosis') }}</strong>
                                            <p>{{ $record->diagnosis }}</p>
                                        </div>
                                    @endif

                                    @if($record->treatment)
                                        <div class="col-md-6 mb-3">
                                            <strong class="text-muted d-block mb-1">{{ __('Treatment') }}</strong>
                                            <p>{{ $record->treatment }}</p>
                                        </div>
                                    @endif

                                    @if($record->notes)
                                        <div class="col-md-6 mb-3">
                                            <strong class="text-muted d-block mb-1">{{ __('Notes') }}</strong>
                                            <p>{{ $record->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if(!empty($record->appointment) && !empty($record->appointment->id))
                                <a href="{{ route('clinic.medical-records.edit', $record->appointment->id) }}" class="stretched-link" aria-label="{{ __('View medical record') }}"></a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="mdi mdi-file-document-remove"></i>
                            <h5>{{ __('No Medical Records') }}</h5>
                            <p class="text-muted">{{ __('No medical records found for this patient') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Prescriptions Tab -->
        <div class="tab-pane fade" id="prescriptions" role="tabpanel">
            <div class="row">
                @forelse($prescriptions as $prescription)
                    <div class="col-lg-6 mb-3">
                        <div class="card stat-card prescription-card h-100 position-relative">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">
                                        <i class="mdi mdi-pill text-info"></i> {{ __('Prescription') }}
                                    </h5>
                                    <small class="text-muted">{{ $prescription->created_at->format('M d, Y') }}</small>
                                </div>

                                <p class="mb-3">
                                    <i class="mdi mdi-doctor"></i> <strong>{{ __('Doctor') }}:</strong>
                                    Dr. {{ $prescription->doctorProfile->name }}
                                </p>

                                @if($prescription->notes)
                                    <div class="alert alert-info mb-3">
                                        <strong>{{ __('Notes') }}:</strong> {{ $prescription->notes }}
                                    </div>
                                @endif

                                <strong class="d-block mb-2">{{ __('Medications') }}:</strong>
                                @foreach($prescription->items as $item)
                                    <div class="prescription-item">
                                        <strong>{{ $item->drug_name }}</strong>
                                        @if($item->dose || $item->frequency || $item->duration)
                                            <div class="small text-muted mt-1">
                                                @if($item->dose) <span class="me-2"><i class="mdi mdi-medical-bag"></i> {{ $item->dose }}</span> @endif
                                                @if($item->frequency) <span class="me-2"><i class="mdi mdi-clock-outline"></i> {{ $item->frequency }}</span> @endif
                                                @if($item->duration) <span><i class="mdi mdi-calendar"></i> {{ $item->duration }}</span> @endif
                                            </div>
                                        @endif
                                        @if($item->notes)
                                            <small class="text-muted d-block mt-1">{{ __('Note') }}: {{ $item->notes }}</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('clinic.prescriptions.show', $prescription->id) }}" class="stretched-link" aria-label="{{ __('View prescription') }}"></a>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="mdi mdi-pill-off"></i>
                            <h5>{{ __('No Prescriptions') }}</h5>
                            <p class="text-muted">{{ __('No prescriptions found for this patient') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Lab Orders Tab -->
        <div class="tab-pane fade" id="labs" role="tabpanel">
            <div class="row">
                @forelse($labOrders as $labOrder)
                    <div class="col-md-6 mb-3">
                        <div class="card stat-card lab-order-card h-100 position-relative">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">
                                            <i class="mdi mdi-flask text-danger"></i> {{ __('Lab Order') }}
                                        </h5>
                                        <small class="text-muted">{{ $labOrder->created_at->format('M d, Y - h:i A') }}</small>
                                    </div>
                                    <span class="badge bg-{{ $labOrder->status == 'completed' ? 'success' : ($labOrder->status == 'received' ? 'info' : 'warning') }}">
                                        {{ ucfirst($labOrder->status) }}
                                    </span>
                                </div>

                                <p class="mb-2">
                                    <i class="mdi mdi-test-tube"></i> <strong>{{ __('Test Name') }}:</strong>
                                    {{ $labOrder->test_name }}
                                </p>

                                @if($labOrder->lab_name)
                                    <p class="mb-2">
                                        <i class="mdi mdi-hospital-building"></i> <strong>{{ __('Lab') }}:</strong>
                                        {{ $labOrder->lab_name }}
                                    </p>
                                @endif

                                @if($labOrder->doctorProfile)
                                    <p class="mb-2">
                                        <i class="mdi mdi-doctor"></i> <strong>{{ __('Doctor') }}:</strong>
                                        Dr. {{ $labOrder->doctorProfile->name }}
                                    </p>
                                @endif

                                @if($labOrder->cost_amount)
                                    <p class="mb-2">
                                        <i class="mdi mdi-cash"></i> <strong>{{ __('Cost') }}:</strong>
                                        {{ number_format($labOrder->cost_amount, 2) }}
                                    </p>
                                @endif

                                @if($labOrder->notes)
                                    <div class="mt-3 pt-3 border-top">
                                        <small class="text-muted">
                                            <strong>{{ __('Notes') }}:</strong> {{ $labOrder->notes }}
                                        </small>
                                    </div>
                                @endif

                                @if($labOrder->result_comment)
                                    <div class="mt-2 alert alert-info mb-0">
                                        <strong>{{ __('Result Comment') }}:</strong> {{ $labOrder->result_comment }}
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('clinic.lab-orders.show', $labOrder->id) }}" class="stretched-link" aria-label="{{ __('View lab order') }}"></a>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="mdi mdi-flask-remove"></i>
                            <h5>{{ __('No Lab Orders') }}</h5>
                            <p class="text-muted">{{ __('No lab orders found for this patient') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Edit Patient Modal (same as before) -->
<div class="modal fade" id="editPatientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Patient') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPatientForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_patient_id" value="{{ $patient->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">{{ __('Full Name') }} *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" value="{{ $patient->name }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">{{ __('Phone') }} *</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone" value="{{ $patient->phone }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" class="form-control" id="edit_email" name="email" value="{{ $patient->email }}">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">{{ __('Password') }}</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                        <small class="text-muted">{{ __('Leave empty to keep current password') }}</small>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#editPatientForm').on('submit', function(e) {
        e.preventDefault();
        var patientId = $('#edit_patient_id').val();

        $.ajax({
            url: "{{ route('clinic.patients.update', ':id') }}".replace(':id', patientId),
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#editPatientModal').modal('hide');
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showFormErrors(xhr.responseJSON.errors);
                } else {
                    toastr.error('{{ __("An error occurred") }}');
                }
            }
        });
    });
});

function editPatient(id) {
    clearFormErrors();
    $('#editPatientModal').modal('show');
}

function deletePatient(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("You will not be able to recover this patient record!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f56565',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __("Yes, delete it!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('clinic.patients.destroy', ':id') }}".replace(':id', id),
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        setTimeout(() => {
                            window.location.href = "{{ route('clinic.patients.index') }}";
                        }, 1000);
                    }
                },
                error: function() {
                    toastr.error('{{ __("An error occurred") }}');
                }
            });
        }
    });
}

function showFormErrors(errors) {
    clearFormErrors();
    $.each(errors, function(field, messages) {
        var input = $('#edit_' + field);
        input.addClass('is-invalid');
        input.next('.invalid-feedback').text(messages[0]);
    });
}

function clearFormErrors() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}
</script>
@endpush
