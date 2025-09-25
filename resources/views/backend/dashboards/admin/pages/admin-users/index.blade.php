@extends('backend.dashboards.admin.layouts.app')

{{-- @section('title', __('Admin Users Management')) --}}

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    <a href="{{ route('admin.admin-users.trash') }}" class="btn btn-warning me-2">
                        <i class="mdi mdi-delete"></i> {{ __('Trash') }}
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#adminUserModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Add Admin User') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Admin Users') }}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                        <table id="adminUsersTable" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Roles') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
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

<!-- Create/Edit Admin User Modal -->
<div class="modal fade" id="adminUserModal" tabindex="-1" aria-labelledby="adminUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminUserModalLabel">{{ __('Add Admin User') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adminUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label required">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                                <div class="validation-feedback" id="name_feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label required">{{ __('Email') }}</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                                <div class="validation-feedback" id="email_feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label required">{{ __('Password') }}</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                                <div class="validation-feedback" id="password_feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label required">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                <div class="validation-feedback" id="password_confirmation_feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role" class="form-label required">{{ __('Role') }}</label>
                                <select name="role" id="role" class="form-control select2" required>
                                    <option value="">{{ __('Select Role') }}</option>
                                </select>
                                <div class="validation-feedback" id="role_feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fa fa-save"></i> {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Admin User Modal -->
<div class="modal fade" id="showAdminUserModal" tabindex="-1" aria-labelledby="showAdminUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showAdminUserModalLabel">{{ __('Admin User Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="showAdminUserContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#adminUsersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.admin-users.data") }}',
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'roles', name: 'roles', orderable: false },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/{{ app()->getLocale() === "ar" ? "ar" : "en" }}.json'
        }
    });

    // Load roles for dropdown
    loadRoles();

    // Create Admin User
    window.createAdminUser = function() {
        resetForm();
        $('#adminUserModalLabel').text('{{ __("Add Admin User") }}');
        $('#adminUserForm')[0].reset();
        $('#adminUserModal').modal('show');
    };

    // Edit Admin User
    window.editAdminUser = function(id) {
        $.get(`/admin/admin-users/${id}`, function(data) {
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#role').val(data.roles[0]?.name || '');
            $('#status').val(data.status ? '1' : '0');

            $('#adminUserModalLabel').text('{{ __("Edit Admin User") }}');
            $('#adminUserForm').data('id', id);
            $('#adminUserModal').modal('show');

            // Make password fields optional for edit
            $('#password, #password_confirmation').removeAttr('required');
        });
    };

    // Show Admin User
    window.showAdminUser = function(id) {
        $.get(`/admin/admin-users/${id}`, function(data) {
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>{{ __('Name') }}:</strong> ${data.name}
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('Email') }}:</strong> ${data.email}
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>{{ __('Roles') }}:</strong>
                        <div class="mt-1">
                            ${data.roles.map(role => `<span class="badge bg-primary me-1">${role.name}</span>`).join('')}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('Status') }}:</strong>
                        <span class="badge ${data.status ? 'bg-success' : 'bg-danger'}">
                            ${data.status ? '{{ __("Active") }}' : '{{ __("Inactive") }}'}
                        </span>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>{{ __('Created At') }}:</strong> ${new Date(data.created_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('Updated At') }}:</strong> ${new Date(data.updated_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </div>
                </div>
            `;
            $('#showAdminUserContent').html(content);
            $('#showAdminUserModal').modal('show');
        });
    };

    // Toggle Status
    window.toggleAdminUserStatus = function(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: "{{ __('Do you want to toggle this admin user status?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, toggle it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`/admin/admin-users/toggle-status/${id}`, {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.status === 'success') {
                        table.ajax.reload();
                        Swal.fire('{{ __("Toggled!") }}', response.message, 'success');
                    } else {
                        Swal.fire('{{ __("Error!") }}', response.message, 'error');
                    }
                });
            }
        });
    };

    // Delete Admin User
    window.deleteAdminUser = function(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: "{{ __('You won\'t be able to revert this!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/admin-users/${id}`,
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

    // Form submission
    $('#adminUserForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const id = $(this).data('id');
        const url = id ? `/admin/admin-users/${id}` : '{{ route("admin.admin-users.store") }}';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    $('#adminUserModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('{{ __("Success!") }}', response.message, 'success');
                } else {
                    Swal.fire('{{ __("Error!") }}', response.message, 'error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(field) {
                        $(`#${field}_feedback`).html(`<div class="text-danger">${errors[field][0]}</div>`);
                    });
                } else {
                    Swal.fire('{{ __("Error!") }}', '{{ __("An error occurred. Please try again.") }}', 'error');
                }
            }
        });
    });

    // Load roles function
    function loadRoles() {
        $.get('{{ route("admin.admin-users.roles") }}', function(data) {
            $('#role').empty().append('<option value="">{{ __("Select Role") }}</option>');
            data.forEach(function(role) {
                $('#role').append(`<option value="${role.name}">${role.name}</option>`);
            });
        });
    }

    // Reset form function
    function resetForm() {
        $('#adminUserForm')[0].reset();
        $('#adminUserForm').removeData('id');
        $('.validation-feedback').empty();
        $('#password, #password_confirmation').attr('required', true);
    }
});
</script>
@endpush
