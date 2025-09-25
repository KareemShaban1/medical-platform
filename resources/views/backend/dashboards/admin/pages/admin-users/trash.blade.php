@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Admin Users Trash'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Admin Users Trash') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                        <table id="adminUsersTrashTable" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Roles') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Deleted At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
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
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#adminUsersTrashTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.admin-users.trash.data") }}',
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'roles', name: 'roles', orderable: false },
            { data: 'status', name: 'status' },
            { data: 'deleted_at', name: 'deleted_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[5, 'desc']], // Order by deleted_at desc
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/{{ app()->getLocale() === "ar" ? "ar" : "en" }}.json'
        }
    });

    // Restore Admin User
    window.restoreAdminUser = function(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: "{{ __('Do you want to restore this admin user?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, restore it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`/admin/admin-users/restore/${id}`, {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.status === 'success') {
                        table.ajax.reload();
                        Swal.fire('{{ __("Restored!") }}', response.message, 'success');
                    } else {
                        Swal.fire('{{ __("Error!") }}', response.message, 'error');
                    }
                });
            }
        });
    };

    // Force Delete Admin User
    window.forceDeleteAdminUser = function(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: "{{ __('This will permanently delete the admin user. You will not be able to revert this!') }}",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete permanently!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/admin-users/force/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            table.ajax.reload();
                            Swal.fire('{{ __("Deleted!") }}', response.message, 'success');
                        } else {
                            Swal.fire('{{ __("Error!") }}', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ __("Something went wrong") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('{{ __("Error!") }}', errorMessage, 'error');
                    }
                });
            }
        });
    };
});
</script>
@endpush
