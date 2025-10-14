@extends('backend.dashboards.admin.layouts.app')
@section('title' , __('Areas Trash'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
               
                <h4 class="page-title">{{ __('Areas Trash') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="areas-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Governorate') }}</th>
                                <th>{{ __('City') }}</th>
                                <th>{{ __('Name') }}</th>
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
    let trashTable = $('#areas-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.areas.trash.data") }}',
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'governorate',
                name: 'governorate'
            },
            {
                data: 'city',
                name: 'city'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'trash_action',
                name: 'trash_action',
                orderable: false,
                searchable: false
            },

        ],
        order: [
            [0, 'desc']
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
        pageLength: 10,
        responsive: true,
        language: languages[language],
        buttons: [{
                extend: 'print',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
            {
                extend: 'excel',
                text: 'Excel',
                title: 'Areas Data',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
            {
                extend: 'copy',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
        ],
        drawCallback: function() {
            $('.dataTables_paginate > .pagination').addClass(
                'pagination-rounded');
        }
    });

  
   // Restore Product
   function restore(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to restore this category?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, restore it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.areas.restore", ":id") }}'.replace(':id', id),
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        trashTable.ajax.reload();
                        Swal.fire('Restored!', response.message, 'success');
                    }
                });
            }
        });
    }

    // Force Delete Product
    function forceDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the category. You won't be able to revert this!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete permanently!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.areas.force-delete", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        trashTable.ajax.reload();
                        Swal.fire('Deleted!', response.message, 'success');
                    }
                });
            }
        });
    }


</script>
@endpush