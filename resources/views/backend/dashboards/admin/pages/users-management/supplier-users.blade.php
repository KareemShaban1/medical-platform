@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Supplier Users Management'))

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ __('Supplier Users Management') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Supplier Users') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-users"></i> {{ __('All Supplier Users') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="supplier-users-table" class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Supplier') }}</th>
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
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#supplier-users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users-management.supplier-users.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'supplier_name', name: 'supplier.name' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{ __("Loading...") }}</span>',
                    search: '{{ __("Search") }}:',
                    lengthMenu: '{{ __("Show") }} _MENU_ {{ __("entries") }}',
                    info: '{{ __("Showing") }} _START_ {{ __("to") }} _END_ {{ __("of") }} _TOTAL_ {{ __("entries") }}',
                    infoEmpty: '{{ __("Showing 0 to 0 of 0 entries") }}',
                    infoFiltered: '({{ __("filtered from") }} _MAX_ {{ __("total entries") }})',
                    paginate: {
                        first: '{{ __("First") }}',
                        last: '{{ __("Last") }}',
                        next: '{{ __("Next") }}',
                        previous: '{{ __("Previous") }}'
                    },
                    emptyTable: '{{ __("No data available in table") }}'
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> {{ __("Copy") }}',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> {{ __("Excel") }}',
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> {{ __("PDF") }}',
                        className: 'btn btn-sm btn-danger'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> {{ __("Print") }}',
                        className: 'btn btn-sm btn-info'
                    }
                ]
            });
        });

        function deleteSupplierUser(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.users-management.supplier-users.destroy", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            $('#supplier-users-table').DataTable().ajax.reload();
                            Swal.fire('Deleted!', response.message, 'success');
                        },
                        error: function (xhr) {
                            Swal.fire('Error!', 'Something went wrong', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush