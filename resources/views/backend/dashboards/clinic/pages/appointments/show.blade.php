@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Appointment Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.appointments.analytics', $appointment->doctor_profile_id) }}" class="btn btn-info me-2">
                        <i class="mdi mdi-chart-line"></i> {{ __('Doctor Analytics') }}
                    </a>
                    <a href="{{ route('clinic.appointments.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Appointment Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Appointment Information') }}</h5>

                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 200px;">{{ __('ID') }}</th>
                                    <td>{{ $appointment->id }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Doctor') }}</th>
                                    <td>{{ $appointment->doctorProfile->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Patient') }}</th>
                                    <td>{{ $appointment->patient->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Date') }}</th>
                                    <td>{{ $appointment->period ? $appointment->period->date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Time') }}</th>
                                    <td>{{ $appointment->period ? $appointment->period->start_time . ' - ' . $appointment->period->end_time : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Slot Number') }}</th>
                                    <td>{{ $appointment->slot_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Status') }}</th>
                                    <td>{!! $appointment->status_badge !!}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Visit Type') }}</th>
                                    <td>{{ $appointment->visit_type_label }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Cost Amount') }}</th>
                                    <td>{{ $appointment->cost_amount ? number_format($appointment->cost_amount, 2) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Payment Status') }}</th>
                                    <td>
                                        <span class="badge bg-{{ $appointment->payment_status === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($appointment->payment_status ?? 'pending') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Booked At') }}</th>
                                    <td>{{ $appointment->booked_at ? $appointment->booked_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Notes') }}</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Patient Notes') }}</label>
                        <p class="text-muted">{{ $appointment->patient_notes ?: __('No notes') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Doctor Notes') }}</label>
                        <p class="text-muted">{{ $appointment->doctor_notes ?: __('No notes') }}</p>
                    </div>

                    @if($appointment->cancellation_reason)
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Cancellation Reason') }}</label>
                        <p class="text-muted">{{ $appointment->cancellation_reason }}</p>
                    </div>
                    @endif

                    @if($appointment->cancelled_at)
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Cancelled At') }}</label>
                        <p class="text-muted">{{ $appointment->cancelled_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Actions') }}</h5>

                    <div class="d-flex gap-2 flex-wrap">
                        @if($appointment->status === 'pending' || $appointment->status === 'confirmed')
                        <button class="btn btn-danger" onclick="cancelAppointment({{ $appointment->id }})">
                            <i class="mdi mdi-cancel"></i> {{ __('Cancel Appointment') }}
                        </button>
                        @endif

                        @if($appointment->status === 'pending')
                        <button class="btn btn-success" onclick="confirmAppointment({{ $appointment->id }})">
                            <i class="mdi mdi-check"></i> {{ __('Confirm Appointment') }}
                        </button>
                        @endif

                        @if($appointment->status === 'confirmed')
                        <button class="btn btn-primary" onclick="completeAppointment({{ $appointment->id }})">
                            <i class="mdi mdi-check-all"></i> {{ __('Mark as Completed') }}
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmAppointment(id) {
    Swal.fire({
        title: '{{ __("Confirm Appointment?") }}',
        text: '{{ __("This will confirm the appointment") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes, confirm") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.appointments.confirm", ":id") }}'.replace(':id', id),
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    Swal.fire('{{ __("Success") }}', response.message, 'success');
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message || '{{ __("An error occurred") }}', 'error');
                }
            });
        }
    });
}

function cancelAppointment(id) {
    Swal.fire({
        title: '{{ __("Cancel Appointment?") }}',
        input: 'textarea',
        inputLabel: '{{ __("Cancellation Reason (Optional)") }}',
        inputPlaceholder: '{{ __("Enter reason...") }}',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes, cancel") }}',
        cancelButtonText: '{{ __("No") }}',
        confirmButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.appointments.cancel", ":id") }}'.replace(':id', id),
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { reason: result.value },
                success: function(response) {
                    Swal.fire('{{ __("Success") }}', response.message, 'success');
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message || '{{ __("An error occurred") }}', 'error');
                }
            });
        }
    });
}

function completeAppointment(id) {
    // Update the appointment status to completed
    Swal.fire({
        title: '{{ __("Mark as Completed?") }}',
        text: '{{ __("This will mark the appointment as completed") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes, complete") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.appointments.update", ":id") }}'.replace(':id', id),
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { status: 'completed' },
                success: function(response) {
                    Swal.fire('{{ __("Success") }}', response.message, 'success');
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message || '{{ __("An error occurred") }}', 'error');
                }
            });
        }
    });
}
</script>
@endpush

