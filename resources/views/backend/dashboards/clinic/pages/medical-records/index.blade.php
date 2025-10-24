@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Medical Records'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="page-title">{{ __('Medical Records') }}</h4>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table id="medical-records-table" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Appointment Date') }}</th>
                        <th>{{ __('Patient') }}</th>
                        <th>{{ __('Doctor') }}</th>
                        <th>{{ __('Visit Type') }}</th>
                        <th>{{ __('Shared') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>

            </table>


        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
$(function(){
    let table = $('#medical-records-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('clinic.medical-records.data') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'appointment_date', name: 'appointment_date', orderable: false, searchable: false },
            { data: 'patient_name', name: 'patient.name', orderable: false },
            { data: 'doctor_name', name: 'doctor.name', orderable: false },
            { data: 'visit_type_label', name: 'visit_type', orderable: false },
            { data: 'shared_badge', name: 'is_shared_with_patient', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        order: [[0,'desc']],
        language: languages[language],
        drawCallback: function(){
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });
});
</script>
@endpush
