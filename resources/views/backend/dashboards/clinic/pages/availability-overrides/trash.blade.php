@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Availability Overrides Trash'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.availability-overrides.index') }}" class="btn btn-primary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Trash') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="overrides-trash-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Doctor') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Time Range') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Note') }}</th>
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
    let trashTable = $('#overrides-trash-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("clinic.availability-overrides.trash.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'doctor_name', name: 'doctor_name' },
            { data: 'date', name: 'date' },
            { data: 'time_range', name: 'time_range' },
            { data: 'type', name: 'type' },
            { data: 'note', name: 'note' },
            { data: 'trash_action', name: 'trash_action', orderable: false, searchable: false },
        ],
        order: [[0, 'desc']],
        dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
        pageLength: 10,
        responsive: true,
        language: languages[language],
        buttons: [
            { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'excel', text: 'Excel', title: 'Overrides Trash Data', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'copy', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
        ],
        drawCallback: function() {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });

    // Restore Override
    function restore(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to restore this override?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, restore it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("clinic.availability-overrides.restore", ":id") }}'.replace(':id', id),
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        trashTable.ajax.reload();
                        Swal.fire('Restored!', response.message, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
                    }
                });
            }
        });
    }

    // Force Delete Override
    function forceDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the override. You won't be able to revert this!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete permanently!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("clinic.availability-overrides.force-delete", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        trashTable.ajax.reload();
                        Swal.fire('Deleted!', response.message, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush

