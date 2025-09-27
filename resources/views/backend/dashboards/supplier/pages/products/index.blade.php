@extends('backend.dashboards.supplier.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('supplier.products.trash') }}" class="btn btn-warning me-2">
                        <i class="mdi mdi-delete"></i> {{ __('Trash') }}
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#productsModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Add Product') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Products') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="products-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Images') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('SKU') }}</th>
                                <th>{{ __('Price Before') }}</th>
                                <th>{{ __('Price After') }}</th>
                                <th>{{ __('Stock') }}</th>
                                <th>{{ __('Categories') }}</th>
                                <th>{{ __('Approval') }}</th>
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
<div class="modal fade" id="productsModal" tabindex="-1" role="dialog" aria-labelledby="productsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productsModalLabel">{{ __('Add Product') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productsForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="productsId">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="name_ar"
                                class="form-label">{{ __('Name (Arabic)') }}</label>
                            <input type="text" class="form-control"
                                id="name_ar" name="name_ar" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="name_en"
                                class="form-label">{{ __('Name (English)') }}</label>
                            <input type="text" class="form-control"
                                id="name_en" name="name_en" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="description_ar"
                                class="form-label">{{ __('Description (Arabic)') }}</label>
                            <textarea class="form-control" id="description_ar"
                                name="description_ar" rows="3" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="description_en"
                                class="form-label">{{ __('Description (English)') }}</label>
                            <textarea class="form-control" id="description_en"
                                name="description_en" rows="3" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="sku"
                                class="form-label">{{ __('SKU') }}</label>
                            <input type="text" class="form-control"
                                id="sku" name="sku" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="price_before"
                                class="form-label">{{ __('Price Before') }}</label>
                            <input type="number" class="form-control" step="0.01"
                                id="price_before" name="price_before" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="price_after"
                                class="form-label">{{ __('Price After') }}</label>
                            <input type="number" class="form-control" step="0.01"
                                id="price_after" name="price_after" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="discount_value"
                                class="form-label">{{ __('Discount Value') }}</label>
                            <input type="number" class="form-control" step="0.01"
                                id="discount_value" name="discount_value">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="stock"
                                class="form-label">{{ __('Stock') }}</label>
                            <input type="number" class="form-control"
                                id="stock" name="stock" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="categories"
                                class="form-label">{{ __('Categories') }}</label>
                            <select class="form-control select2" id="categories"
                                name="categories[]" multiple required>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="attachment"
                                class="form-label">{{ __('Product Images') }}</label>
                            <input type="file" class="form-control"
                                id="attachment" name="attachment[]" multiple accept="image/*" required>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">{{ __('Select multiple images (JPEG, PNG, JPG, GIF, SVG, WEBP)') }}</small>

                            <!-- Existing Images Container -->
                            <div id="existingImages" class="mt-3" style="display: none;">
                                <label class="form-label">{{ __('Current Images') }}</label>
                                <div id="currentImagesContainer" class="row">
                                    <!-- Existing images will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="status"
                                    id="statusHidden" value="1">
                                <input type="checkbox"
                                    class="form-check-input"
                                    id="statusToggle" checked>
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
    let table = $('#products-table').DataTable({
        ajax: '{{ route("supplier.products.data") }}',
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'images',
                name: 'images',
                orderable: false,
                searchable: false
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'sku',
                name: 'sku'
            },
            {
                data: 'price_before',
                name: 'price_before'
            },
            {
                data: 'price_after',
                name: 'price_after'
            },
            {
                data: 'stock',
                name: 'stock'
            },
            {
                data: 'categories',
                name: 'categories',
                orderable: false,
                searchable: false
            },
            {
                data: 'approval_status',
                name: 'approval_status'
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
                    columns: [0, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'excel',
                text: 'Excel',
                title: 'Products Data',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'copy',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7, 8]
                }
            },
        ],
        drawCallback: function() {
            $('.dataTables_paginate > .pagination').addClass(
                'pagination-rounded');
        }
    });

    function loadCategories() {
        $.get('{{ route("supplier.products.categories") }}', function(data) {
            console.log(data);

            $('#categories').empty();
            data.forEach(function(category) {
                $('#categories').append(
                    '<option value="' + category.id + '">' + category.name + '</option>'
                );
            });

            $('#categories').select2({
                dropdownParent: $('#productsModal')
            });
        });
    }

    // Load categories for edit with selection
    function loadCategoriesForEdit(selectedCategories) {
        $.get('{{ route("supplier.products.categories") }}', function(data) {
            $('#categories').empty();
            data.forEach(function(category) {
                $('#categories').append(
                    '<option value="' + category.id + '">' + category.name + '</option>'
                );
            });

            $('#categories').select2({
                dropdownParent: $('#productsModal')
            });

            // Select the categories after select2 is initialized
            if (selectedCategories && selectedCategories.length > 0) {
                let categoryIds = selectedCategories.map(cat => cat.id);
                $('#categories').val(categoryIds).trigger('change');
            }
        });
    }

    // Load existing images for edit
    function loadExistingImages(attachments) {
        if (attachments && attachments.length > 0) {
            $('#existingImages').show();
            $('#currentImagesContainer').empty();

            attachments.forEach(function(attachment, index) {
                const parts = attachment.split("/");

                const folderId = parts[parts.length - 2];
                let imageHtml = `
                                        <div class="col-3 mb-2 position-relative" id="image-${folderId}">
                                                  <img src="${attachment}" alt="Product Image"
                                                       class="img-fluid rounded"
                                                       style="width: 100%; height: 80px; object-fit: cover;">
                                                  <button type="button" class="btn btn-sm btn-danger position-absolute"
                                                          style="top: 5px; right: 5px; padding: 2px 6px;"
                                                          onclick="removeExistingImage(${folderId})"
                                                          title="Remove Image">
                                                            <i class="fa fa-times"></i>
                                                  </button>
                                        </div>
                              `;
                $('#currentImagesContainer').append(imageHtml);
            });
        } else {
            $('#existingImages').hide();
        }
    }

    // Remove existing image
    function removeExistingImage(folderId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This image will be removed from the product!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (!$('#removedImages').length) {
                    $('#productsForm').append('<div id="removedImages"></div>');
                }
                $('#removedImages').append(`<input type="hidden" name="removed_images[]" value="${folderId}">`);

                $(`#image-${folderId}`).remove();

                if ($('#currentImagesContainer').children().length === 0) {
                    $('#existingImages').hide();
                }
            }
        });
    }

    // Reset form
    function resetForm() {
        $('#productsForm')[0].reset();
        $('#productsForm').attr('action', '{{ route("supplier.products.store") }}');
        $('#productsId').val('');
        $('#productsModal .modal-title').text('{{ __("Add Product") }}');
        $('#attachment').prop('required', true);
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#existingImages').hide();
        $('#currentImagesContainer').empty();
        loadCategories();
    }

    $('#productsForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#productsId').val();
        let url = id ?
            '{{ route("supplier.products.update", ":id") }}'.replace(':id', id) :
            '{{ route("supplier.products.store") }}';
        let method = id ? 'PUT' : 'POST';

        let formData = new FormData(this);

        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#productsModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', response.message,
                    'success');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON
                        .errors || {};
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
                                        '#productsForm'
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
    function editProduct(id) {
        $.get('{{ route("supplier.products.index") }}/' + id, function(data) {
            $('#productsId').val(data.id);
            $('#name_en').val(data.name_en);
            $('#name_ar').val(data.name_ar);
            $('#description_en').val(data.description_en);
            $('#description_ar').val(data.description_ar);
            $('#sku').val(data.sku);
            $('#price_before').val(data.price_before);
            $('#price_after').val(data.price_after);
            $('#discount_value').val(data.discount_value);
            $('#stock').val(data.stock);
            $('#statusToggle').prop('checked', data.status);

            loadCategoriesForEdit(data.categories);

            // Load existing images
            loadExistingImages(data.images);

            $('#attachment').prop('required', false);
            $('#productsForm').attr('action',
                '{{ route("supplier.products.update", ":id") }}'.replace(
                    ':id', id));
            $('#productsModal .modal-title').text('{{ __("Edit Product") }}');
            $('#productsModal').modal('show');
        });
    }


    // Delete
    function deleteProduct(id) {
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
                    url: '{{ route("supplier.products.destroy", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Deleted!', response.message, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Something went wrong', 'error');
                    }
                });
            }
        });
    }

    $('#statusToggle').on('change', function() {
        $('#statusHidden').val($(this).is(':checked') ? 1 : 0);
    });

    // Handle status toggle in DataTable
    $(document).on('change', '.toggle-status', function() {
        let productId = $(this).data('id');
        let isChecked = $(this).is(':checked');

        $.ajax({
            url: '{{ route("supplier.products.toggle.status", ":id") }}'.replace(':id', productId),
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Update the badge text and class
                let $label = $(this).siblings('label');
                let $badge = $label.find('.badge');

                if (isChecked) {
                    $badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                } else {
                    $badge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                }

                Swal.fire('Success', response.message, 'success');
            }.bind(this),
            error: function(xhr) {
                // Revert the toggle state
                $(this).prop('checked', !isChecked);
                Swal.fire('Error', 'Something went wrong', 'error');
            }.bind(this)
        });
    });

    // Initialize categories on page load
    $(document).ready(function() {
        loadCategories();
    });
</script>
@endpush
