@extends('backend.dashboards.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ __('Trash Suppliers') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="suppliers-trash-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Supplier Users') }}</th>
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
        let trashTable = $('#suppliers-trash-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.suppliers.trash.data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'supplier_users', name: 'supplier_users', searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            pageLength: 10,
            responsive: true,
            language: languages[language],
            buttons: [{
                extend: 'print',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            },
            {
                extend: 'excel',
                text: 'Excel',
                title: 'Trash Suppliers',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            },
            {
                extend: 'copy',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            }
            ]
        });

        function restore(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to restore this supplier?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.suppliers.restore", ":id") }}'.replace(':id', id),
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
                text: "This will permanently delete the supplier and all related data!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete permanently!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.suppliers.force-delete", ":id") }}'.replace(':id', id),
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