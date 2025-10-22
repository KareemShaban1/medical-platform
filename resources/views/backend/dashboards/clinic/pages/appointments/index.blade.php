@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Appointments'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#appointmentModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Book Appointment') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Appointments') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="appointments-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Doctor') }}</th>
                                <th>{{ __('Patient') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentModalLabel">{{ __('Book Appointment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm" method="POST">
                    @csrf
                    <input type="hidden" id="appointmentId">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="doctor_profile_id" class="form-label">{{ __('Doctor') }}</label>
                            <select class="form-control select2" id="doctor_profile_id" name="doctor_profile_id" required>
                                <option value="">{{ __('Select a Doctor') }}</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="patient_id" class="form-label">{{ __('Patient') }}</label>
                            <select class="form-control select2" id="patient_id" name="patient_id" required>
                                <option value="">{{ __('Select a Patient') }}</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->user->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="appointment_date" class="form-label">{{ __('Date') }}</label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="period_id" class="form-label">{{ __('Time Slot') }}</label>
                            <select class="form-select" id="period_id" name="period_id" required>
                                <option value="">{{ __('Select Date & Doctor First') }}</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="status" class="form-label">{{ __('Status') }}</label>
                            <select class="form-select" id="appointment_status" name="status">
                                <option value="">{{ __('Select Status') }}</option>
                                <option value="confirmed" selected>{{ __('Confirmed') }}</option>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="waiting">{{ __('Waiting') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                                <option value="cancelled">{{ __('Cancelled') }}</option>
                                <option value="expired">{{ __('Expired') }}</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="patient_notes" class="form-label">{{ __('Patient Notes') }}</label>
                            <textarea class="form-control" id="patient_notes" name="patient_notes" rows="2"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="doctor_notes" class="form-label">{{ __('Doctor Notes') }}</label>
                            <textarea class="form-control" id="doctor_notes" name="doctor_notes" rows="2"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let table = $('#appointments-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("clinic.appointments.data") }}',
        data: function (d) {
            // Add filters here if needed
        }
    },
    columns: [
        { data: 'id', name: 'id' },
        { data: 'doctor_name', name: 'doctor_name' },
        { data: 'patient_name', name: 'patient_name' },
        { data: 'appointment_date', name: 'appointment_date' },
        { data: 'appointment_time', name: 'appointment_time' },
        { data: 'status', name: 'status' },
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ],
    order: [[0, 'desc']],
    dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
    pageLength: 10,
    responsive: true,
    language: languages[language],
    buttons: [
        { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
        { extend: 'excel', text: 'Excel', title: 'Appointments Data', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
        { extend: 'copy', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
    ],
    drawCallback: function() {
        $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
    }
});

// Initialize select2
$('.select2').select2({
    dropdownParent: $('#appointmentModal')
});

// Load available periods when doctor and date are selected
$('#doctor_profile_id, #appointment_date').on('change', function() {
    let doctorId = $('#doctor_profile_id').val();
    let date = $('#appointment_date').val();

    if (doctorId && date) {
        $.ajax({
            url: '{{ route("clinic.appointments.available-periods") }}',
            method: 'GET',
            data: { doctor_profile_id: doctorId, date: date },
            success: function(periods) {
                $('#period_id').empty();
                if (periods.length > 0) {
                    $('#period_id').append('<option value="">{{ __("Select a Time Slot") }}</option>');
                    periods.forEach(function(period) {
                        let available = period.capacity - period.booked_count;
                        $('#period_id').append(
                            `<option value="${period.id}">${period.start_time} - ${period.end_time} (${available} slots available)</option>`
                        );
                    });
                } else {
                    $('#period_id').append('<option value="">{{ __("No available slots") }}</option>');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to load available periods', 'error');
            }
        });
    }
});

// Reset form
function resetForm() {
    $('#appointmentForm')[0].reset();
    $('#appointmentForm').attr('action', '{{ route("clinic.appointments.store") }}');
    $('#appointmentId').val('');
    $('#appointmentModal .modal-title').text('{{ __("Book Appointment") }}');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#period_id').empty().append('<option value="">{{ __("Select Date & Doctor First") }}</option>');
}

// Handle Add/Edit Form Submission
$('#appointmentForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#appointmentId').val();
    let url = id ?
        '{{ route("clinic.appointments.update", ":id") }}'.replace(':id', id) :
        '{{ route("clinic.appointments.store") }}';
    let method = id ? 'PUT' : 'POST';

    let formData = new FormData(this);
    if (method === 'PUT') {
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#appointmentModal').modal('hide');
            table.ajax.reload();
            Swal.fire('Success', response.message, 'success');
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors || {};
                let messages = [];
                Object.keys(errors).forEach(function(key) {
                    messages.push(errors[key][0]);
                    let $input = $('[name="' + key + '"]');
                    if ($input.length) {
                        $input.addClass('is-invalid');
                        $input.next('.invalid-feedback').text(errors[key][0]);
                    }
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: messages.join('<br>')
                });
            } else {
                Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
            }
        }
    });
});

// Edit
function editAppointment(id) {
    $.get('{{ route("clinic.appointments.index") }}/' + id, function(data) {
        $('#appointmentId').val(data.id);
        $('#doctor_profile_id').val(data.doctor_profile_id).trigger('change');
        $('#patient_id').val(data.patient_id).trigger('change');
        $('#status').val(data.status);
        $('#patient_notes').val(data.patient_notes);
        $('#doctor_notes').val(data.doctor_notes);

        // Load period data
        if (data.period) {
            $('#appointment_date').val(data.period.date);

            // Trigger change to load periods
            setTimeout(function() {
                $('#period_id').val(data.period_id);
            }, 500);
        }

        $('#appointmentForm').attr('action',
            '{{ route("clinic.appointments.update", ":id") }}'.replace(':id', id));
        $('#appointmentModal .modal-title').text('{{ __("Edit Appointment") }}');
        $('#appointmentModal').modal('show');
    });
}

// View
function viewAppointment(id) {
    window.location.href = '{{ route("clinic.appointments.show", ":id") }}'.replace(':id', id);
}
</script>
@endpush
