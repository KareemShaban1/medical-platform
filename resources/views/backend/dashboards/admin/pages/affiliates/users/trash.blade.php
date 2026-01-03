@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Trash Affiliate Users'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.affiliates.users.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> {{ __('Back to Affiliate Users') }}
                        </a>
                    </div>
                    <h4 class="page-title">{{ __('Trash Affiliate Users') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="trash-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
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
        let table = $('#trash-table').DataTable({
            ajax: '{{ route("admin.affiliates.users.trash.data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'deleted_at_formatted', name: 'deleted_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[4, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            pageLength: 10,
            responsive: true,
            language: languages[language],
            buttons: [
                { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'excel', text: 'Excel', title: 'Trash Affiliate Users', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'copy', exportOptions: { columns: [0, 1, 2, 3, 4] } }
            ],
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });

        function restoreUser(id) {
            Swal.fire({
                title: '{{ __("Restore User?") }}',
                text: '{{ __("This will restore the affiliate user.") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __("Yes, restore it!") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.affiliates.users.restore", ":id") }}'.replace(':id', id),
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            table.ajax.reload();
                            Swal.fire('{{ __("Restored!") }}', response.message, 'success');
                        },
                        error: function (xhr) {
                            Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong") }}', 'error');
                        }
                    });
                }
            });
        }

        function forceDeleteUser(id) {
            Swal.fire({
                title: '{{ __("Permanently Delete?") }}',
                text: '{{ __("This action cannot be undone. The user will be permanently deleted.") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("Yes, delete permanently!") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.affiliates.users.force-delete", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            table.ajax.reload();
                            Swal.fire('{{ __("Deleted!") }}', response.message, 'success');
                        },
                        error: function (xhr) {
                            Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong") }}', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush