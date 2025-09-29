@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Submit Offer'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('supplier.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('supplier.available-requests.index') }}">{{ __('Available Requests') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Submit Offer') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Submit Offer') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">{{ __('Offer Details') }}</h5>

                    <form id="createOfferForm">
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $request->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">{{ __('Price') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price" name="price"
                                               step="0.01" min="0.01" max="999999.99" required
                                               placeholder="0.00">
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount" class="form-label">{{ __('Discount (Optional)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="discount" name="discount"
                                               step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    <small class="form-text text-muted">{{ __('Optional discount amount') }}</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_time" class="form-label">{{ __('Delivery Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="delivery_time" name="delivery_time"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    <small class="form-text text-muted">{{ __('When can you deliver this order?') }}</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Final Price') }}</label>
                                    <div class="form-control-plaintext">
                                        <strong id="final-price">$0.00</strong>
                                        <small class="text-muted d-block">{{ __('Price - Discount') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="terms" class="form-label">{{ __('Terms & Conditions') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="terms" name="terms" rows="4" required
                                placeholder="{{ __('Describe your terms, conditions, warranty, return policy, etc...') }}"></textarea>
                            <small class="form-text text-muted">{{ __('Minimum 10 characters, maximum 1000 characters') }}</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="alert alert-info">
                            <i class="mdi mdi-information me-2"></i>
                            <strong>{{ __('Important:') }}</strong> {{ __('Once submitted, you can only edit your offer while it\'s still pending. Make sure all details are accurate.') }}
                        </div>

                        <div class="text-end">
                            <a href="{{ route('supplier.available-requests.show', $request->id) }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="mdi mdi-paper-plane me-1"></i> {{ __('Submit Offer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Request Summary -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Request Summary') }}</h5>

                    <div class="mb-3">
                        <strong>{{ __('Request ID:') }}</strong>
                        <p class="mb-2">#{{ $request->id }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Clinic:') }}</strong>
                        <p class="mb-2">{{ $request->clinic->name }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Category:') }}</strong>
                        <p class="mb-2">{{ $request->categories->pluck('name')->join(', ') }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Quantity:') }}</strong>
                        <p class="mb-2">{{ number_format($request->quantity) }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Timeline:') }}</strong>
                        <p class="mb-2">
                            @if($request->timeline)
                                {{ $request->timeline->format('M d, Y') }}
                            @else
                                <span class="text-muted">{{ __('Not specified') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Description:') }}</strong>
                        <div class="border rounded p-2 mt-1 bg-light">
                            <small>{{ Str::limit($request->description, 150) }}</small>
                        </div>
                    </div>

                    <a href="{{ route('supplier.available-requests.show', $request->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="mdi mdi-eye me-1"></i> {{ __('View Full Request') }}
                    </a>
                </div>
            </div>

            <!-- Offer Tips -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Offer Tips') }}</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="mdi mdi-lightbulb text-warning me-2"></i>
                            {{ __('Research market prices') }}
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-lightbulb text-warning me-2"></i>
                            {{ __('Be realistic with delivery dates') }}
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-lightbulb text-warning me-2"></i>
                            {{ __('Include warranty information') }}
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-lightbulb text-warning me-2"></i>
                            {{ __('Mention your experience') }}
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-lightbulb text-warning me-2"></i>
                            {{ __('Highlight unique value') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate final price
    function updateFinalPrice() {
        const price = parseFloat($('#price').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        const finalPrice = Math.max(0, price - discount);
        $('#final-price').text('$' + finalPrice.toFixed(2));
    }

    $('#price, #discount').on('input', updateFinalPrice);

    // Validate discount doesn't exceed price
    $('#discount').on('input', function() {
        const price = parseFloat($('#price').val()) || 0;
        const discount = parseFloat($(this).val()) || 0;

        if (discount > price) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('{{ __("Discount cannot exceed the price.") }}');
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('');
        }
    });

    // Form submission
    $('#createOfferForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();

        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> {{ __("Submitting...") }}');

        $.ajax({
            url: "{{ route('supplier.offers.store') }}",
            type: 'POST',
            data: $(this).serialize() + "&_token={{ csrf_token() }}",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: '{{ __("Success!") }}',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: '{{ __("OK") }}'
                    }).then(() => {
                        window.location.href = "{{ route('supplier.offers.index') }}";
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

// Character counter for terms
$('#terms').on('input', function() {
    const maxLength = 1000;
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
</script>
@endpush

@push('styles')
<style>
.card-title {
    color: #6c757d;
    font-weight: 600;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.form-control:focus {
    border-color: #727cf5;
    box-shadow: 0 0 0 0.2rem rgba(114, 124, 245, 0.25);
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

.list-unstyled li {
    padding: 0.25rem 0;
}

#final-price {
    font-size: 1.25rem;
    color: #28a745;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}
</style>
@endpush
