@extends('backend.dashboards.admin.layouts.app')
@section('title' , __('Blog Categories Trash'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#blogCategoriesModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Add Blog Category') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Blog Categories Trash') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="blog-categories-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name English') }}</th>
                                <th>{{ __('Name Arabic') }}</th>
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
    let trashTable = $('#blog-categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.blog-categories.trash.data") }}',
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name_en',
                name: 'name_en'
            },
            {
                data: 'name_ar',
                name: 'name_ar'
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
                title: 'Blog Categories Data',
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
                    url: '{{ route("admin.blog-categories.restore", ":id") }}'.replace(':id', id),
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
                    url: '{{ route("admin.blog-categories.force-delete", ":id") }}'.replace(':id', id),
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