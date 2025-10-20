@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Daily Periods Management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-success" id="generatePeriodsBtn">
                        <i class="mdi mdi-autorenew"></i> {{ __('Generate Periods') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Daily Periods Management') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="periods-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Doctor') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('Capacity') }}</th>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#periods-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("clinic.daily-periods.data") }}',
            data: function (d) {
                // Add filters here if needed
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'doctor_name', name: 'doctor_name' },
            { data: 'date', name: 'date' },
            { data: 'time', name: 'time' },
            { data: 'capacity_display', name: 'capacity_display', orderable: false },
            { data: 'is_open', name: 'is_open' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[2, 'desc'], [3, 'asc']],
        dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
        pageLength: 10,
        responsive: true,
        language: languages[language],
        buttons: [
            { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'excel', text: 'Excel', title: 'Daily Periods Data', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'copy', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
        ],
        drawCallback: function() {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });

    // Generate Periods
    $('#generatePeriodsBtn').on('click', function() {
        Swal.fire({
            title: '{{ __("Generate Periods") }}',
            html: `
                <div class="mb-3">
                    <label for="doctor_id" class="form-label">{{ __("Doctor") }}</label>
                    <select id="doctor_id" class="form-select">
                        <option value="">{{ __("Select Doctor") }}</option>
                        @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="days_ahead" class="form-label">{{ __("Days Ahead") }}</label>
                    <input id="days_ahead" class="form-control" type="number" placeholder="{{ __('Days ahead') }}" value="30" min="1" max="90">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __("Generate") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            customClass: {
                popup: 'swal2-custom-popup'
            },
            preConfirm: () => {
                const doctorId = $('#doctor_id').val();
                const daysAhead = $('#days_ahead').val();

                if (!doctorId) {
                    Swal.showValidationMessage('{{ __("Please select a doctor") }}');
                    return false;
                }

                return {
                    doctor_profile_id: doctorId,
                    days_ahead: daysAhead
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: '{{ route("clinic.daily-periods.generate") }}',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: result.value,
                    success: function(response) {
                        Swal.fire('{{ __("Success") }}', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message || '{{ __("An error occurred") }}', 'error');
                    }
                });
            }
        });
    });

    // Toggle Status
    window.toggleStatus = function(id) {
        $.ajax({
            url: `/clinic/daily-periods/${id}/toggle-open`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                Swal.fire('{{ __("Success") }}', response.message, 'success');
                table.ajax.reload();
            },
            error: function(xhr) {
                Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message || '{{ __("An error occurred") }}', 'error');
            }
        });
    };

    // Edit Capacity
    window.editCapacity = function(id, currentCapacity) {
        Swal.fire({
            title: '{{ __("Update Capacity") }}',
            input: 'number',
            inputValue: currentCapacity,
            inputLabel: '{{ __("Capacity") }}',
            inputAttributes: {
                min: 1,
                max: 100,
                step: 1
            },
            showCancelButton: true,
            confirmButtonText: '{{ __("Update") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            inputValidator: (value) => {
                if (!value) {
                    return '{{ __("Please enter a capacity") }}'
                }
                if (value < 1 || value > 100) {
                    return '{{ __("Capacity must be between 1 and 100") }}'
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: `/clinic/daily-periods/${id}/update-capacity`,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { capacity: result.value },
                    success: function(response) {
                        Swal.fire('{{ __("Success") }}', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message || '{{ __("An error occurred") }}', 'error');
                    }
                });
            }
        });
    };
});
</script>

<style>
/* Custom Swal2 styling for better form display */
.swal2-custom-popup {
    width: 500px !important;
}
.swal2-html-container {
    text-align: left !important;
}
.swal2-html-container .form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}
.swal2-html-container .form-select,
.swal2-html-container .form-control {
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}
.swal2-html-container .mb-3 {
    margin-bottom: 1rem;
}
</style>
@endpush
