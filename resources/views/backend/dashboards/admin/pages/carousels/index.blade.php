@extends('backend.dashboards.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addCarouselModal">
                            <i class="mdi mdi-plus"></i> {{ __('Add Image') }}
                        </button>
                    </div>
                    <h4 class="page-title">{{ __('Carousels') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Homepage Carousel Images') }}</h5>
                        <p class="text-muted mb-0 mt-1">
                            {{ __('Drag and drop to reorder. At least one image is required.') }}</p>
                    </div>
                    <div class="card-body">
                        @if ($carousels->isEmpty())
                            <div class="text-center py-5">
                                <i class="mdi mdi-image-multiple-outline text-muted" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">{{ __('No carousel images yet') }}</h5>
                                <p class="text-muted">{{ __('Click "Add Image" to upload your first carousel image') }}</p>
                            </div>
                        @else
                            <div class="row" id="carousel-sortable">
                                @foreach ($carousels as $carousel)
                                    <div class="col-md-4 col-lg-3 mb-4" data-id="{{ $carousel->id }}">
                                        <div
                                            class="card h-100 carousel-card {{ !$carousel->is_active ? 'opacity-50' : '' }}">
                                            <div class="position-relative">
                                                @if ($carousel->image_url)
                                                    <img src="{{ $carousel->image_url }}" class="card-img-top"
                                                        alt="{{ $carousel->title }}"
                                                        style="height: 180px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                                        style="height: 180px;">
                                                        <i class="mdi mdi-image-outline text-muted"
                                                            style="font-size: 3rem;"></i>
                                                    </div>
                                                @endif
                                                <div class="position-absolute top-0 start-0 m-2">
                                                    <span
                                                        class="badge {{ $carousel->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $carousel->is_active ? __('Active') : __('Inactive') }}
                                                    </span>
                                                </div>
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-dark cursor-move"
                                                        title="{{ __('Drag to reorder') }}">
                                                        <i class="mdi mdi-drag"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h6 class="card-title mb-1">{{ $carousel->title ?? __('Untitled') }}</h6>
                                                @if ($carousel->description)
                                                    <p class="card-text text-muted small text-truncate">
                                                        {{ $carousel->description }}</p>
                                                @endif
                                            </div>
                                            <div
                                                class="card-footer bg-transparent border-top-0 d-flex justify-content-between">
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input toggle-status"
                                                        data-id="{{ $carousel->id }}"
                                                        {{ $carousel->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label small">{{ __('Active') }}</label>
                                                </div>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-primary edit-carousel"
                                                        data-id="{{ $carousel->id }}" data-title="{{ $carousel->title }}"
                                                        data-description="{{ $carousel->description }}"
                                                        data-image="{{ $carousel->image_url }}">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger delete-carousel"
                                                        data-id="{{ $carousel->id }}"
                                                        {{ $carousels->count() <= 1 ? 'disabled' : '' }}
                                                        title="{{ $carousels->count() <= 1 ? __('Cannot delete last image') : __('Delete') }}">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Carousel Modal -->
    <div class="modal fade" id="addCarouselModal" tabindex="-1" aria-labelledby="addCarouselModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCarouselModalLabel">{{ __('Add Carousel Image') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addCarouselForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addTitle" class="form-label">{{ __('Title') }} <small
                                    class="text-muted">({{ __('Optional') }})</small></label>
                            <input type="text" class="form-control" id="addTitle" name="title"
                                placeholder="{{ __('Enter title') }}">
                        </div>
                        <div class="mb-3">
                            <label for="addDescription" class="form-label">{{ __('Description') }} <small
                                    class="text-muted">({{ __('Optional') }})</small></label>
                            <textarea class="form-control" id="addDescription" name="description" rows="2"
                                placeholder="{{ __('Enter description') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="addImage" class="form-label">{{ __('Image') }} <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="addImage" name="image" accept="image/*"
                                required>
                            <div class="form-text">{{ __('Recommended size: 800x600 pixels. Max 5MB.') }}</div>
                        </div>
                        <div id="addImagePreview" class="mb-3 d-none">
                            <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="addIsActive" name="is_active"
                                value="1" checked>
                            <label class="form-check-label" for="addIsActive">{{ __('Active') }}</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Upload') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Carousel Modal -->
    <div class="modal fade" id="editCarouselModal" tabindex="-1" aria-labelledby="editCarouselModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCarouselModalLabel">{{ __('Edit Carousel Image') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCarouselForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editCarouselId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editTitle" class="form-label">{{ __('Title') }} <small
                                    class="text-muted">({{ __('Optional') }})</small></label>
                            <input type="text" class="form-control" id="editTitle" name="title"
                                placeholder="{{ __('Enter title') }}">
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">{{ __('Description') }} <small
                                    class="text-muted">({{ __('Optional') }})</small></label>
                            <textarea class="form-control" id="editDescription" name="description" rows="2"
                                placeholder="{{ __('Enter description') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Current Image') }}</label>
                            <div id="editCurrentImage" class="mb-2">
                                <img src="" alt="Current" class="img-fluid rounded" style="max-height: 150px;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editImage" class="form-label">{{ __('New Image') }} <small
                                    class="text-muted">({{ __('Optional - leave empty to keep current') }})</small></label>
                            <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                        </div>
                        <div id="editImagePreview" class="mb-3 d-none">
                            <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="editIsActive" name="is_active"
                                value="1">
                            <label class="form-check-label" for="editIsActive">{{ __('Active') }}</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .carousel-card {
            transition: all 0.3s ease;
        }

        .carousel-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .cursor-move {
            cursor: move;
        }

        .sortable-ghost {
            opacity: 0.4;
        }

        .sortable-chosen {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Initialize Sortable
        const sortableEl = document.getElementById('carousel-sortable');
        if (sortableEl) {
            new Sortable(sortableEl, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                handle: '.cursor-move',
                onEnd: function(evt) {
                    const order = Array.from(sortableEl.children).map(el => el.dataset.id);

                    $.ajax({
                        url: '{{ route('admin.carousels.update-order') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            order: order
                        },
                        success: function(response) {
                            toastr.success(response.message);
                        },
                        error: function() {
                            toastr.error('{{ __('Failed to update order') }}');
                        }
                    });
                }
            });
        }

        // Image preview for add form
        $('#addImage').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#addImagePreview').removeClass('d-none').find('img').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Image preview for edit form
        $('#editImage').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#editImagePreview').removeClass('d-none').find('img').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Add carousel form
        $('#addCarouselForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: '{{ route('admin.carousels.store') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#addCarouselModal').modal('hide');
                    Swal.fire('{{ __('Success') }}', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let message = Object.values(errors).flat().join('<br>');
                        Swal.fire('{{ __('Validation Error') }}', message, 'error');
                    } else {
                        Swal.fire('{{ __('Error') }}', '{{ __('Something went wrong') }}', 'error');
                    }
                }
            });
        });

        // Open edit modal
        $(document).on('click', '.edit-carousel', function() {
            const id = $(this).data('id');
            const title = $(this).data('title');
            const description = $(this).data('description');
            const image = $(this).data('image');

            $('#editCarouselId').val(id);
            $('#editTitle').val(title);
            $('#editDescription').val(description);
            $('#editCurrentImage img').attr('src', image);
            $('#editImagePreview').addClass('d-none');
            $('#editImage').val('');

            $('#editCarouselModal').modal('show');
        });

        // Edit carousel form
        $('#editCarouselForm').on('submit', function(e) {
            e.preventDefault();

            const id = $('#editCarouselId').val();
            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: '{{ route('admin.carousels.update', ':id') }}'.replace(':id', id),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#editCarouselModal').modal('hide');
                    Swal.fire('{{ __('Success') }}', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let message = Object.values(errors).flat().join('<br>');
                        Swal.fire('{{ __('Validation Error') }}', message, 'error');
                    } else {
                        Swal.fire('{{ __('Error') }}', '{{ __('Something went wrong') }}', 'error');
                    }
                }
            });
        });

        // Toggle status
        $(document).on('change', '.toggle-status', function() {
            const id = $(this).data('id');
            const checkbox = $(this);

            $.ajax({
                url: '{{ route('admin.carousels.toggle-status', ':id') }}'.replace(':id', id),
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function() {
                    checkbox.prop('checked', !checkbox.is(':checked'));
                    toastr.error('{{ __('Failed to update status') }}');
                }
            });
        });

        // Delete carousel
        $(document).on('click', '.delete-carousel', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('This will permanently delete this carousel image.') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __('Yes, delete it') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.carousels.destroy', ':id') }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire('{{ __('Deleted!') }}', response.message, 'success')
                                .then(() => {
                                    location.reload();
                                });
                        },
                        error: function(xhr) {
                            Swal.fire('{{ __('Error') }}', xhr.responseJSON?.message ||
                                '{{ __('Something went wrong') }}', 'error');
                        }
                    });
                }
            });
        });

        // Reset add form on modal close
        $('#addCarouselModal').on('hidden.bs.modal', function() {
            $('#addCarouselForm')[0].reset();
            $('#addImagePreview').addClass('d-none');
        });
    </script>
@endpush
