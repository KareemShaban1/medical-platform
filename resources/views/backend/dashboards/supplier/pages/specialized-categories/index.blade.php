@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Specialized Categories'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('supplier.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Specialized Categories') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Specialized Categories') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-danger mb-2 me-2" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                                <i class="mdi mdi-plus-circle me-2"></i> {{ __('Create New Category') }}
                            </button>
                            <button type="button" class="btn btn-info mb-2" data-bs-toggle="modal" data-bs-target="#attachCategoryModal">
                                <i class="mdi mdi-link me-2"></i> {{ __('Join Existing Category') }}
                            </button>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <button type="button" class="btn btn-success mb-2 me-1" onclick="refreshTable()">
                                    <i class="mdi mdi-refresh"></i> {{ __('Refresh') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="categories-table" class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('English Name') }}</th>
                                    <th>{{ __('Arabic Name') }}</th>
                                    <th>{{ __('Active Requests') }}</th>
                                    <th>{{ __('Total Suppliers') }}</th>
                                    <th>{{ __('Created') }}</th>
                                    <th style="width: 85px;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">{{ __('Create New Specialized Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name_en" class="form-label">{{ __('English Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_en" name="name_en" required>
                    </div>
                    <div class="mb-3">
                        <label for="name_ar" class="form-label">{{ __('Arabic Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Attach to Existing Category Modal -->
<div class="modal fade" id="attachCategoryModal" tabindex="-1" aria-labelledby="attachCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachCategoryModalLabel">{{ __('Join Existing Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="attachCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="available_category_id" class="form-label">{{ __('Available Categories') }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="available_category_id" name="category_id" required>
                            <option value="">{{ __('Loading categories...') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ __('Select a category that you want to specialize in.') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-info">{{ __('Join Category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">{{ __('Edit Specialized Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm">
                <input type="hidden" id="edit_category_id" name="category_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name_en" class="form-label">{{ __('English Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name_en" name="name_en" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_name_ar" class="form-label">{{ __('Arabic Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name_ar" name="name_ar" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier.specialized-categories.data') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name_en', name: 'name_en'},
            {data: 'name_ar', name: 'name_ar'},
            {data: 'requests_count', name: 'requests_count'},
            {data: 'suppliers_count', name: 'suppliers_count'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']],
        responsive: true,
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });

    // Create Category Form
    $('#createCategoryForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('supplier.specialized-categories.store') }}",
            type: 'POST',
            data: $(this).serialize() + "&_token={{ csrf_token() }}",
            success: function(response) {
                if (response.success) {
                    $('#createCategoryModal').modal('hide');
                    $('#createCategoryForm')[0].reset();
                    Swal.fire('{{ __("Success!") }}', response.message, 'success');
                    refreshTable();
                } else {
                    Swal.fire('{{ __("Error!") }}', response.message, 'error');
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = Object.values(errors).flat().join('\n');
                    Swal.fire('{{ __("Validation Error!") }}', errorMessage, 'error');
                } else {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
                }
            }
        });
    });

    // Edit Category Form
    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();

        let categoryId = $('#edit_category_id').val();

        $.ajax({
            url: "{{ route('supplier.specialized-categories.update', ':id') }}".replace(':id', categoryId),
            type: 'PUT',
            data: $(this).serialize() + "&_token={{ csrf_token() }}",
            success: function(response) {
                if (response.success) {
                    $('#editCategoryModal').modal('hide');
                    $('#editCategoryForm')[0].reset();
                    Swal.fire('{{ __("Success!") }}', response.message, 'success');
                    refreshTable();
                } else {
                    Swal.fire('{{ __("Error!") }}', response.message, 'error');
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = Object.values(errors).flat().join('\n');
                    Swal.fire('{{ __("Validation Error!") }}', errorMessage, 'error');
                } else {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
                }
            }
        });
    });

    // Load available categories when attach modal is opened
    $('#attachCategoryModal').on('show.bs.modal', function() {
        loadAvailableCategories();
    });

    // Attach Category Form
    $('#attachCategoryForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('supplier.specialized-categories.attach') }}",
            type: 'POST',
            data: $(this).serialize() + "&_token={{ csrf_token() }}",
            success: function(response) {
                if (response.success) {
                    $('#attachCategoryModal').modal('hide');
                    $('#attachCategoryForm')[0].reset();
                    Swal.fire('{{ __("Success!") }}', response.message, 'success');
                    refreshTable();
                } else {
                    Swal.fire('{{ __("Error!") }}', response.message, 'error');
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = Object.values(errors).flat().join('\n');
                    Swal.fire('{{ __("Validation Error!") }}', errorMessage, 'error');
                } else {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
                }
            }
        });
    });
});

function refreshTable() {
    $('#categories-table').DataTable().ajax.reload();
}

function loadAvailableCategories() {
    const select = $('#available_category_id');
    select.html('<option value="">{{ __("Loading categories...") }}</option>');

    $.ajax({
        url: "{{ route('supplier.specialized-categories.available') }}",
        type: 'GET',
        success: function(response) {
            if (response.success) {
                select.empty();
                if (response.data.length === 0) {
                    select.html('<option value="">{{ __("No categories available") }}</option>');
                } else {
                    select.html('<option value="">{{ __("Select a category") }}</option>');
                    response.data.forEach(function(category) {
                        const locale = '{{ app()->getLocale() }}';
                        const name = locale === 'ar' ? category.name_ar : category.name_en;
                        select.append(`<option value="${category.id}">${name}</option>`);
                    });
                }
            } else {
                select.html('<option value="">{{ __("Error loading categories") }}</option>');
            }
        },
        error: function(xhr) {
            select.html('<option value="">{{ __("Error loading categories") }}</option>');
        }
    });
}

function editCategory(id) {
    $.ajax({
        url: "{{ route('supplier.specialized-categories.show', ':id') }}".replace(':id', id),
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let category = response.data;
                $('#edit_category_id').val(category.id);
                $('#edit_name_en').val(category.name_en);
                $('#edit_name_ar').val(category.name_ar);
                $('#editCategoryModal').modal('show');
            } else {
                Swal.fire('{{ __("Error!") }}', response.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
        }
    });
}

function deleteCategory(id) {
    Swal.fire({
        title: '{{ __("Remove Specialization?") }}',
        text: '{{ __("This will remove your specialization in this category. You can rejoin it later.") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("Yes, remove it!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('supplier.specialized-categories.destroy', ':id') }}".replace(':id', id),
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('{{ __("Removed!") }}', response.message, 'success');
                        refreshTable();
                    } else {
                        Swal.fire('{{ __("Error!") }}', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
                }
            });
        }
    });
}
</script>
@endpush
