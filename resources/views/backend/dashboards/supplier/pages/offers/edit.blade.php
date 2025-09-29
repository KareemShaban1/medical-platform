@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Edit Offer'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('supplier.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('supplier.offers.index') }}">{{ __('My Offers') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Edit Offer') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Edit Offer') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @if($offer->status !== 'pending')
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert me-2"></i>
                            <strong>{{ __('Note:') }}</strong> {{ __('This offer is') }} <strong>{{ ucfirst($offer->status) }}</strong>. {{ __('You can only view the details.') }}
                        </div>
                    @endif

                    <h5 class="card-title mb-4">{{ __('Offer Details') }}</h5>

                    <form id="editOfferForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">{{ __('Price') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price" name="price"
                                               step="0.01" min="0.01" max="999999.99" required
                                               value="{{ $offer->price }}" {{ $offer->status !== 'pending' ? 'readonly' : '' }}>
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
                                               step="0.01" min="0" value="{{ $offer->discount }}" {{ $offer->status !== 'pending' ? 'readonly' : '' }}>
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
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                                           value="{{ $offer->delivery_time->format('Y-m-d') }}" {{ $offer->status !== 'pending' ? 'readonly' : '' }}>
                                    <small class="form-text text-muted">{{ __('When can you deliver this order?') }}</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Final Price') }}</label>
                                    <div class="form-control-plaintext">
                                        <strong id="final-price">${{ number_format($offer->final_price, 2) }}</strong>
                                        <small class="text-muted d-block">{{ __('Price - Discount') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Status') }}</label>
                                    <div class="form-control-plaintext">
                                        @if($offer->status === 'pending')
                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                        @elseif($offer->status === 'accepted')
                                            <span class="badge bg-success">{{ __('Accepted') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Declined') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Submitted') }}</label>
                                    <div class="form-control-plaintext">
                                        {{ $offer->created_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="terms" class="form-label">{{ __('Terms & Conditions') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="terms" name="terms" rows="4" required {{ $offer->status !== 'pending' ? 'readonly' : '' }}>{{ $offer->terms }}</textarea>
                            <small class="form-text text-muted">{{ __('Minimum 10 characters, maximum 1000 characters') }}</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        @if($offer->status === 'pending')
                            <div class="alert alert-info">
                                <i class="mdi mdi-information me-2"></i>
                                <strong>{{ __('Note:') }}</strong> {{ __('You can update your offer while it\'s still pending. Once accepted or declined, no changes are allowed.') }}
                            </div>
                        @elseif($offer->status === 'accepted')
                            <div class="alert alert-success">
                                <i class="mdi mdi-check-circle me-2"></i>
                                <strong>{{ __('Congratulations!') }}</strong> {{ __('Your offer has been accepted. Please prepare to fulfill the order according to your terms.') }}
                            </div>
                        @else
                            <div class="alert alert-secondary">
                                <i class="mdi mdi-information me-2"></i>
                                <strong>{{ __('Declined:') }}</strong> {{ __('This offer was not selected. Keep looking for new opportunities!') }}
                            </div>
                        @endif

                        <div class="text-end">
                            <a href="{{ route('supplier.offers.index') }}" class="btn btn-light me-2">{{ __('Back to My Offers') }}</a>
                            <a href="{{ route('supplier.offers.show', $offer->id) }}" class="btn btn-info me-2">{{ __('View Details') }}</a>
                            @if($offer->status === 'pending')
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="mdi mdi-content-save me-1"></i> {{ __('Update Offer') }}
                                </button>
                            @endif
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
                        <p class="mb-2">#{{ $offer->request->id }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Clinic:') }}</strong>
                        <p class="mb-2">{{ $offer->request->clinic->name }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Category:') }}</strong>
                        <p class="mb-2">{{ $offer->request->categories->pluck('name')->join(', ') }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Quantity:') }}</strong>
                        <p class="mb-2">{{ number_format($offer->request->quantity) }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Request Status:') }}</strong>
                        <p class="mb-2">
                            @if($offer->request->status === 'open')
                                <span class="badge bg-success">{{ __('Open') }}</span>
                            @elseif($offer->request->status === 'closed')
                                <span class="badge bg-primary">{{ __('Closed') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Canceled') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Description:') }}</strong>
                        <div class="border rounded p-2 mt-1 bg-light">
                            <small>{{ Str::limit($offer->request->description, 150) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Offer Statistics -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Offer Statistics') }}</h5>

                    <div class="mb-3">
                        <strong>{{ __('Total Offers:') }}</strong>
                        <p class="mb-2">{{ $offer->request->offers->count() }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Pending Offers:') }}</strong>
                        <p class="mb-2">{{ $offer->request->offers->where('status', 'pending')->count() }}</p>
                    </div>

                    @if($offer->request->acceptedOffer)
                        <div class="mb-3">
                            <strong>{{ __('Accepted Offer:') }}</strong>
                            <p class="mb-2">
                                @if($offer->request->acceptedOffer->id === $offer->id)
                                    <span class="text-success">{{ __('Your offer!') }}</span>
                                @else
                                    <span class="text-muted">{{ __('Another supplier') }}</span>
                                @endif
                            </p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>{{ __('Last Updated:') }}</strong>
                        <p class="mb-2">{{ $offer->updated_at->format('M d, Y H:i') }}</p>
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
    $('#editOfferForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();

        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> {{ __("Updating...") }}');

        $.ajax({
            url: "{{ route('supplier.offers.update', $offer->id) }}",
            type: 'PUT',
            data: $(this).serialize() + "&_token={{ csrf_token() }}",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: '{{ __("Success!") }}',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: '{{ __("OK") }}'
                    }).then(() => {
                        window.location.href = "{{ route('supplier.offers.show', $offer->id) }}";
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

.alert {
    border-radius: 0.375rem;
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

.alert-success {
    border-left: 4px solid #28a745;
}

.alert-warning {
    border-left: 4px solid #ffc107;
}

.alert-secondary {
    border-left: 4px solid #6c757d;
}

#final-price {
    font-size: 1.25rem;
    color: #28a745;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

[readonly] {
    background-color: #f8f9fa !important;
}
</style>
@endpush
