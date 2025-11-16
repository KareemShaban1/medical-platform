@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Trashed Appointments'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.appointments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Appointments') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Trashed Appointments') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-md-3">
                            <label for="filter_start_date" class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" id="filter_start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_end_date" class="form-label">{{ __('End Date') }}</label>
                            <input type="date" id="filter_end_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <button id="applyFilters" class="btn btn-primary">
                                <i class="fas fa-filter"></i> {{ __('Apply Filters') }}
                            </button>
                            <button id="resetFilters" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> {{ __('Reset') }}
                            </button>
                        </div>
                    </div>
                    <table id="trash-appointments-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Doctor') }}</th>
                                <th>{{ __('Patient') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('Visit Type') }}</th>
                                <th>{{ __('Slot') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Deleted At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ============================================
// DataTable Initialization
// ============================================
let table = $('#trash-appointments-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("clinic.appointments.trash.data") }}',
        data: function (d) {
            d.start_date = $('#filter_start_date').val();
            d.end_date = $('#filter_end_date').val();
        }
    },
    columns: [
        { data: 'id', name: 'id' },
        { data: 'doctor_name', name: 'doctor_name' },
        { data: 'patient_name', name: 'patient_name' },
        { data: 'appointment_date', name: 'appointment_date' },
        { data: 'appointment_time', name: 'appointment_time' },
        { data: 'visit_type', name: 'visit_type' },
        { data: 'slot_number', name: 'slot_number' },
        { data: 'status', name: 'status' },
        { data: 'deleted_at', name: 'deleted_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ],
    order: [[8, 'desc']],
    dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
    pageLength: 10,
    responsive: true,
    language: languages[language],
    buttons: [
        { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] } },
        { extend: 'excel', text: 'Excel', title: 'Trashed Appointments', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] } },
        { extend: 'copy', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] } },
    ],
    drawCallback: function() {
        $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
    }
});

// Apply and reset filters
$('#applyFilters').on('click', function () {
    table.ajax.reload();
});

$('#resetFilters').on('click', function () {
    $('#filter_start_date').val('');
    $('#filter_end_date').val('');
    table.ajax.reload();
});

// ============================================
// RESTORE APPOINTMENT
// ============================================
function restoreAppointment(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("This appointment will be restored") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("Yes, restore it!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.appointments.restore", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload();
                    Swal.fire('{{ __("Restored!") }}', response.message, 'success');
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Failed to restore appointment") }}', 'error');
                }
            });
        }
    });
}

// ============================================
// FORCE DELETE APPOINTMENT
// ============================================
function forceDeleteAppointment(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("This action cannot be undone! The appointment will be permanently deleted.") }}',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '{{ __("Yes, delete forever!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.appointments.force-delete", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload();
                    Swal.fire('{{ __("Deleted!") }}', response.message, 'success');
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Failed to delete appointment") }}', 'error');
                }
            });
        }
    });
}
</script>
@endpush
