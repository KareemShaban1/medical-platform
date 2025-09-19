@extends('backend.dashboards.supplier.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#rolesModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Add Role') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Roles Management') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="roles-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Permissions Count') }}</th>
                                <th>{{ __('Created At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="rolesModal" tabindex="-1" role="dialog" aria-labelledby="rolesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rolesModalLabel">{{ __('Add Role') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rolesForm" method="POST">
                    @csrf
                    <input type="hidden" id="rolesId">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="name" class="form-label">{{ __('Role Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('Permissions') }}</label>
                            <div class="row">
                                @foreach($permissions as $permission)
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input permission-check" type="checkbox"
                                               name="permissions[]" value="{{ $permission->id }}"
                                               id="permission_{{ $permission->id }}">
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let table = $('#roles-table').DataTable({
    ajax: '{{ route("supplier.roles.data") }}',
    columns: [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'permissions_count', name: 'permissions_count' },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ],
    order: [[0, 'desc']],
    dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
    pageLength: 10,
    responsive: true,
    language: languages[language],
    buttons: [
        {
            extend: 'print',
            exportOptions: {
                columns: [0, 1, 2, 3]
            }
        },
        {
            extend: 'excel',
            text: 'Excel',
            title: 'Roles Data',
            exportOptions: {
                columns: [0, 1, 2, 3]
            }
        },
        {
            extend: 'copy',
            exportOptions: {
                columns: [0, 1, 2, 3]
            }
        },
    ],
    drawCallback: function() {
        $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
    }
});

// Reset form
function resetForm() {
    $('#rolesForm')[0].reset();
    $('#rolesForm').attr('action', '{{ route("supplier.roles.store") }}');
    $('#rolesId').val('');
    $('#rolesModal .modal-title').text('{{ __("Add Role") }}');
    $('.permission-check').prop('checked', false);
}

// Handle Add/Edit Form Submission
$('#rolesForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#rolesId').val();
    let url = id ?
        '{{ route("supplier.roles.update", ":id") }}'.replace(':id', id) :
        '{{ route("supplier.roles.store") }}';
    let method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: $(this).serialize(),
        success: function(response) {
            $('#rolesModal').modal('hide');
            table.ajax.reload();
            Swal.fire('Success', response.message || 'Operation completed successfully', 'success');
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors || {};
                let messages = [];
                Object.keys(errors).forEach(function(key) {
                    messages.push(errors[key][0]);
                    let nameSelector = '[name="' + key + '"]';
                    let $input = $(nameSelector);
                    if ($input.length) {
                        $input.addClass('is-invalid');
                        $input.next('.invalid-feedback').text(errors[key][0]);
                    }
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: messages.join('<br>')
                });
            } else {
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        }
    });
});

// Edit Role
function editRole(id, name, permissions) {
    $('#rolesId').val(id);
    $('#name').val(name);

    // Uncheck all permissions first
    $('.permission-check').prop('checked', false);

    // Check the permissions for this role
    if (permissions && permissions.length > 0) {
        permissions.forEach(function(permissionId) {
            $('#permission_' + permissionId).prop('checked', true);
        });
    }

    $('#rolesForm').attr('action', '{{ route("supplier.roles.update", ":id") }}'.replace(':id', id));
    $('#rolesModal .modal-title').text('{{ __("Edit Role") }}');
    $('#rolesModal').modal('show');
}

// Delete Role
function deleteRole(id) {
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
                url: '{{ route("supplier.roles.index") }}/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    table.ajax.reload();
                    Swal.fire('Deleted!', response.message || 'Role deleted successfully', 'success');
                }
            });
        }
    });
}
</script>
@endpush
