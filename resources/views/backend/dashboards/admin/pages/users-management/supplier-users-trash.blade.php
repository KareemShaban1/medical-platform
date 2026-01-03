@extends('backend.dashboards.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ __('Trash Supplier Users') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="supplier-users-trash-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Supplier') }}</th>
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
        let trashTable = $('#supplier-users-trash-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.users-management.supplier-users.trash.data") }}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'supplier_name', name: 'supplier.name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            pageLength: 10,
            responsive: true,
            language: languages[language],
            buttons: [{
                extend: 'print',
                exportOptions: { columns: [0, 1, 2] }
            },
            {
                extend: 'excel',
                text: 'Excel',
                title: 'Trash Supplier Users',
                exportOptions: { columns: [0, 1, 2] }
            },
            {
                extend: 'copy',
                exportOptions: { columns: [0, 1, 2] }
            }
            ]
        });

        function restore(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to restore this user?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.users-management.supplier-users.restore", ":id") }}'.replace(':id', id),
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            trashTable.ajax.reload();
                            Swal.fire('Restored!', response.message, 'success');
                        }
                    });
                }
            });
        }

        function forceDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the user!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete permanently!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.users-management.supplier-users.force-delete", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            trashTable.ajax.reload();
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    });
                }
            });
        }
    </script>
@endpush