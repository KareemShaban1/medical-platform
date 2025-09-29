@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Create Purchase Request'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('clinic.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('clinic.requests.index') }}">{{ __('Purchase Requests') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Create Request') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Create Purchase Request') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="createRequestForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_ids" class="form-label">{{ __('Categories') }} <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="category_ids" name="category_ids[]" multiple required   >
                                        @foreach($categories as $category)
                                            <option value="{{ $category['id'] }}">
                                                {{ $category['name'] }}
                                                @if($category['suppliers_count'] > 0)
                                                    ({{ $category['suppliers_count'] }} {{ __('suppliers') }})
                                                @else
                                                    ({{ __('No suppliers') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">{{ __('Select one or more categories. Hold Ctrl/Cmd to select multiple.') }}</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="timeline" class="form-label">{{ __('Timeline (Optional)') }}</label>
                                    <input type="date" class="form-control" id="timeline" name="timeline" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <small class="form-text text-muted">{{ __('When do you need this delivered?') }}</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required
                                placeholder="{{ __('Describe what you need in detail...') }}"></textarea>
                            <small class="form-text text-muted">{{ __('Minimum 10 characters, maximum 2000 characters') }}</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="preferred_specs" class="form-label">{{ __('Preferred Specifications (Optional)') }}</label>
                            <textarea class="form-control" id="preferred_specs" name="preferred_specs" rows="3"
                                placeholder="{{ __('Any specific requirements, brands, models, or technical specifications...') }}"></textarea>
                            <small class="form-text text-muted">{{ __('Maximum 1000 characters') }}</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="attachments" class="form-label">{{ __('Attachments (Optional)') }}</label>
                            <input type="file" class="form-control" id="attachments" name="attachments[]" multiple
                                accept=".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx">
                            <small class="form-text text-muted">
                                {{ __('You can upload up to 5 files. Supported formats: JPEG, PNG, GIF, PDF, DOC, DOCX. Max 5MB per file.') }}
                            </small>
                            <div class="invalid-feedback"></div>

                            <!-- File preview area -->
                            <div id="file-preview" class="mt-2"></div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information me-2"></i>
                                    <strong>{{ __('Note:') }}</strong> {{ __('Your request will be sent to all suppliers who specialize in the selected category. They will be able to submit offers, and you can review and accept the best one.') }}
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('clinic.requests.index') }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="mdi mdi-send me-1"></i> {{ __('Create Request') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2();
    // File preview functionality
    $('#attachments').on('change', function() {
        const files = this.files;
        const previewContainer = $('#file-preview');
        previewContainer.empty();

        if (files.length > 5) {
            Swal.fire('{{ __("Error!") }}', '{{ __("You can upload maximum 5 files.") }}', 'error');
            this.value = '';
            return;
        }

        Array.from(files).forEach((file, index) => {
            if (file.size > 5 * 1024 * 1024) { // 5MB
                Swal.fire('{{ __("Error!") }}', `{{ __("File") }} "${file.name}" {{ __("exceeds 5MB limit.") }}`, 'error');
                this.value = '';
                previewContainer.empty();
                return;
            }

            const fileItem = $(`
                <div class="d-flex align-items-center border rounded p-2 mb-2">
                    <i class="mdi mdi-file-document me-2"></i>
                    <span class="flex-grow-1">${file.name}</span>
                    <small class="text-muted me-2">${(file.size / 1024).toFixed(1)} KB</small>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
            `);
            previewContainer.append(fileItem);
        });
    });

    // Form submission
    $('#createRequestForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();

        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> {{ __("Creating...") }}');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('clinic.requests.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: '{{ __("Success!") }}',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: '{{ __("OK") }}'
                    }).then(() => {
                        window.location.href = "{{ route('clinic.requests.index') }}";
                    });
                } else {
                    Swal.fire('{{ __("Error!") }}', response.message, 'error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        const input = $(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });

                    Swal.fire('{{ __("Validation Error!") }}', '{{ __("Please check the form and try again.") }}', 'error');
                } else {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
                }
            },
            complete: function() {
                // Restore button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

function removeFile(index) {
    const fileInput = document.getElementById('attachments');
    const dt = new DataTransfer();
    const files = fileInput.files;

    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }

    fileInput.files = dt.files;
    $(fileInput).trigger('change');
}

// Character counter for description
$('#description').on('input', function() {
    const maxLength = 2000;
    const currentLength = $(this).val().length;
    const remaining = maxLength - currentLength;

    let counterText = `${currentLength}/${maxLength} {{ __('characters') }}`;
    if (remaining < 100) {
        counterText = `<span class="text-warning">${counterText}</span>`;
    }
    if (remaining < 0) {
        counterText = `<span class="text-danger">${counterText}</span>`;
    }

    $(this).siblings('.form-text').html(counterText);
});

// Character counter for preferred specs
$('#preferred_specs').on('input', function() {
    const maxLength = 1000;
    const currentLength = $(this).val().length;
    const remaining = maxLength - currentLength;

    let counterText = `${currentLength}/${maxLength} {{ __('characters') }}`;
    if (remaining < 50) {
        counterText = `<span class="text-warning">${counterText}</span>`;
    }
    if (remaining < 0) {
        counterText = `<span class="text-danger">${counterText}</span>`;
    }

    $(this).siblings('.form-text').html(counterText);
});
</script>
@endpush

@push('styles')
<style>
.file-preview-item {
    transition: all 0.3s ease;
}

.file-preview-item:hover {
    background-color: #f8f9fa;
}

.character-counter {
    font-size: 0.875rem;
}

.form-control:focus {
    border-color: #727cf5;
    box-shadow: 0 0 0 0.2rem rgba(114, 124, 245, 0.25);
}

.alert-info {
    border-left: 4px solid #17a2b8;
}
</style>
@endpush
