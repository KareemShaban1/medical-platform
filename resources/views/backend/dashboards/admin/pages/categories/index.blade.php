@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#categoriesModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Add Category') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Categories') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="categories-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name English') }}</th>
                                <th>{{ __('Name Arabic') }}</th>
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

<!-- Modal -->
<div class="modal fade" id="categoriesModal" tabindex="-1" role="dialog" aria-labelledby="categoriesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoriesModalLabel">{{ __('Add Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoriesForm" method="POST">
                    @csrf
                    <input type="hidden" id="categoriesId">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="name_ar"
                                class="form-label">{{ __('Name Arabic') }}</label>
                            <input type="text" class="form-control"
                                id="name_ar" name="name_ar">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="name_en"
                                class="form-label">{{ __('Name English') }}</label>
                            <input type="text" class="form-control"
                                id="name_en" name="name_en">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="status"
                                    id="statusHidden" value="0">
                                <input type="checkbox"
                                    class="form-check-input"
                                    id="statusToggle" value="1">
                                <label class="form-check-label"
                                    for="statusToggle">{{ __('Status') }}</label>

                            </div>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit"
                            class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let table = $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.categories.data") }}',
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
                data: 'status',
                name: 'status'
            },
            {
                data: 'action',
                name: 'action',
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
                title: 'Categories Data',
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

    // Reset form
    function resetForm() {
        $('#categoriesForm')[0].reset();
        $('#categoriesForm').attr('action', '{{ route("admin.categories.store") }}');
        $('#categoriesId').val('');
        $('#categoriesModal .modal-title').text('{{ __("Add Category") }}');
    }

    // Handle Add/Edit Form Submission
    $('#categoriesForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#categoriesId').val();
        let url = id ?
            '{{ route("admin.categories.update", ":id") }}'.replace(':id', id) :
            '{{ route("admin.categories.store") }}';
        let method = id ? 'PUT' : 'POST';

        $('#statusHidden').val($('#statusToggle').is(':checked') ? 1 : 0);


        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#categoriesModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', response.message,
                    'success');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON
                        .errors || {};
                    // show inline feedback and aggregated alert
                    let messages = [];
                    Object.keys(errors).forEach(
                        function(
                            key
                        ) {
                            messages.push(errors[
                                    key
                                ]
                                [
                                    0
                                ]
                            );
                            // find input (supports nested names like items[0][price])
                            let nameSelector =
                                '[name="' +
                                key +
                                '"]';
                            let $input =
                                $(
                                    nameSelector
                                );
                            // fallback for inputs using array syntax:
                            if (!$input
                                .length
                            ) {
                                // try ends-with matching
                                $input = $(
                                        '#categoriesForm'
                                    )
                                    .find('[name^="' +
                                        key +
                                        '"], [name$="' +
                                        key +
                                        '"]'
                                    );
                            }
                            if ($input
                                .length
                            ) {
                                $input.addClass(
                                    'is-invalid'
                                );
                                $input.next(
                                        '.invalid-feedback'
                                    )
                                    .text(errors[
                                            key
                                        ]
                                        [
                                            0
                                        ]
                                    );
                            }
                        });
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors',
                        html: messages
                            .join(
                                '<br>'
                            )
                    });
                } else {
                    Swal.fire('Error', 'Something went wrong',
                        'error');
                }
            }
        });
    });

    // Edit
    function editCategory(id) {
        $.get('{{ route("admin.categories.index") }}/' + id, function(data) {
            $('#categoriesId').val(data.id);
            $('#name_en').val(data.name_en);
            $('#name_ar').val(data.name_ar);
            $('#statusToggle').prop('checked', data.status);

            $('#categoriesForm').attr('action',
                '{{ route("admin.categories.update", ":id") }}'.replace(
                    ':id', id));
            $('#categoriesModal .modal-title').text('{{ __("Edit Category") }}');
            $('#categoriesModal').modal('show');
        });
    }


    // Delete
    function deleteCategory(id) {
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
                    url: '{{ route("admin.categories.index") }}/' +
                        id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            )
                            .attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Deleted!',
                            response
                            .message,
                            'success'
                        );
                    }
                });
            }
        });
    }
</script>
@endpush