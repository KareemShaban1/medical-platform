@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Request Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('clinic.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('clinic.requests.index') }}">{{ __('Purchase Requests') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Request Details') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Request Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h5 class="card-title mb-0">{{ __('Request Information') }}</h5>
                        @if($request->status === 'open')
                            <span class="badge bg-success fs-6">{{ __('Open') }}</span>
                        @elseif($request->status === 'closed')
                            <span class="badge bg-primary fs-6">{{ __('Closed') }}</span>
                        @else
                            <span class="badge bg-danger fs-6">{{ __('Canceled') }}</span>
                        @endif
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('Request ID:') }}</strong>
                            <p class="mb-2">#{{ $request->id }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Categories:') }}</strong>
                            <p class="mb-2">{{ $request->categories->pluck('name')->join(', ') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('Quantity:') }}</strong>
                            <p class="mb-2">{{ number_format($request->quantity) }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Timeline:') }}</strong>
                            <p class="mb-2">
                                @if($request->timeline)
                                    {{ $request->timeline->format('M d, Y') }}
                                    <small class="text-muted">({{ $request->timeline->diffForHumans() }})</small>
                                @else
                                    <span class="text-muted">{{ __('Not specified') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Description:') }}</strong>
                        <div class="border rounded p-3 mt-2 bg-light">
                            {{ $request->description }}
                        </div>
                    </div>

                    @if($request->preferred_specs)
                        <div class="mb-3">
                            <strong>{{ __('Preferred Specifications:') }}</strong>
                            <div class="border rounded p-3 mt-2 bg-light">
                                {{ $request->preferred_specs }}
                            </div>
                        </div>
                    @endif

                    @if(count($request->attachments) > 0)
                        <div class="mb-3">
                            <strong>{{ __('Attachments:') }}</strong>
                            <div class="mt-2">
                                @foreach($request->attachments as $attachment)
                                    <div class="d-flex align-items-center border rounded p-2 mb-2">
                                        <i class="mdi mdi-file-document me-2 text-primary"></i>
                                        <a href="{{ $attachment['url'] }}" target="_blank" class="flex-grow-1 text-decoration-none">
                                            {{ $attachment['name'] }}
                                        </a>
                                        <small class="text-muted">{{ number_format($attachment['size'] / 1024, 1) }} KB</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <strong>{{ __('Created:') }}</strong>
                            <p class="mb-2">{{ $request->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Last Updated:') }}</strong>
                            <p class="mb-2">{{ $request->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Offers Section -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">{{ __('Received Offers') }} ({{ $request->offers->count() }})</h5>
                        @if($request->status === 'open' && $request->offers->where('status', 'pending')->count() > 0)
                            <small class="text-muted">{{ __('Click "Accept" to choose the best offer') }}</small>
                        @endif
                    </div>

                    @if($request->offers->count() === 0)
                        <div class="text-center py-5">
                            <i class="mdi mdi-email-outline display-4 text-muted"></i>
                            <h5 class="mt-3 text-muted">{{ __('No offers received yet') }}</h5>
                            <p class="text-muted">{{ __('Suppliers will submit their offers soon. You\'ll be notified when new offers arrive.') }}</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($request->offers->sortBy('final_price') as $index => $offer)
                                <div class="col-md-6 mb-4">
                                    <div class="card border {{ $offer->status === 'accepted' ? 'border-success' : ($offer->status === 'declined' ? 'border-secondary' : 'border-warning') }}">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $offer->supplier->name }}</h6>
                                            @if($offer->status === 'pending')
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @elseif($offer->status === 'accepted')
                                                <span class="badge bg-success">{{ __('Accepted') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Declined') }}</span>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <small class="text-muted">{{ __('Original Price') }}</small>
                                                    <div class="fw-bold">${{ number_format($offer->price, 2) }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">{{ __('Discount') }}</small>
                                                    <div class="fw-bold text-success">
                                                        @if($offer->discount)
                                                            -${{ number_format($offer->discount, 2) }}
                                                        @else
                                                            $0.00
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted">{{ __('Final Price') }}</small>
                                                <div class="h5 text-primary mb-0">${{ number_format($offer->final_price, 2) }}</div>
                                                @if($index === 0)
                                                    <small class="badge bg-success">{{ __('Best Price') }}</small>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted">{{ __('Delivery Date') }}</small>
                                                <div class="fw-bold">{{ $offer->delivery_time->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $offer->delivery_time->diffForHumans() }}</small>
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted">{{ __('Terms & Conditions') }}</small>
                                                <div class="small">{{ Str::limit($offer->terms, 100) }}</div>
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted">{{ __('Submitted') }}</small>
                                                <div class="small">{{ $offer->created_at->format('M d, Y H:i') }}</div>
                                            </div>

                                            @if($request->status === 'open' && $offer->status === 'pending')
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-success btn-sm" onclick="acceptOffer({{ $offer->id }})">
                                                        <i class="mdi mdi-check me-1"></i> {{ __('Accept Offer') }}
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm" onclick="viewOfferDetails({{ $offer->id }})">
                                                        <i class="mdi mdi-eye me-1"></i> {{ __('View Details') }}
                                                    </button>
                                                </div>
                                            @elseif($offer->status === 'accepted')
                                                <div class="alert alert-success mb-0">
                                                    <i class="mdi mdi-check-circle me-2"></i>
                                                    <strong>{{ __('Accepted!') }}</strong> {{ __('This offer has been accepted.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Request Summary -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Request Summary') }}</h5>

                    <div class="mb-3">
                        <strong>{{ __('Total Offers:') }}</strong>
                        <p class="mb-2">{{ $request->offers->count() }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Pending Offers:') }}</strong>
                        <p class="mb-2">{{ $request->offers->where('status', 'pending')->count() }}</p>
                    </div>

                    @if($request->offers->count() > 0)
                        <div class="mb-3">
                            <strong>{{ __('Price Range:') }}</strong>
                            <p class="mb-2">
                                ${{ number_format($request->offers->min('final_price'), 2) }} -
                                ${{ number_format($request->offers->max('final_price'), 2) }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <strong>{{ __('Average Price:') }}</strong>
                            <p class="mb-2">${{ number_format($request->offers->avg('final_price'), 2) }}</p>
                        </div>
                    @endif

                    @if($request->acceptedOffer)
                        <div class="mb-3">
                            <strong>{{ __('Accepted Offer:') }}</strong>
                            <div class="alert alert-success">
                                <strong>{{ $request->acceptedOffer->supplier->name }}</strong><br>
                                <small>{{ __('Price:') }} ${{ number_format($request->acceptedOffer->final_price, 2) }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Actions') }}</h5>

                    <div class="d-grid gap-2">
                        @if($request->status === 'open')
                            <a href="{{ route('clinic.requests.edit', $request->id) }}" class="btn btn-primary">
                                <i class="mdi mdi-pencil me-1"></i> {{ __('Edit Request') }}
                            </a>
                            <button class="btn btn-warning" onclick="cancelRequest({{ $request->id }})">
                                <i class="mdi mdi-cancel me-1"></i> {{ __('Cancel Request') }}
                            </button>
                        @endif

                        <a href="{{ route('clinic.requests.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> {{ __('Back to Requests') }}
                        </a>

                        @if($request->status !== 'closed' || !$request->acceptedOffer)
                            <button class="btn btn-outline-danger" onclick="deleteRequest({{ $request->id }})">
                                <i class="mdi mdi-delete me-1"></i> {{ __('Delete Request') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tips -->
            @if($request->status === 'open' && $request->offers->where('status', 'pending')->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Selection Tips') }}</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="mdi mdi-lightbulb text-warning me-2"></i>
                                {{ __('Compare prices and delivery times') }}
                            </li>
                            <li class="mb-2">
                                <i class="mdi mdi-lightbulb text-warning me-2"></i>
                                {{ __('Read terms & conditions carefully') }}
                            </li>
                            <li class="mb-2">
                                <i class="mdi mdi-lightbulb text-warning me-2"></i>
                                {{ __('Consider supplier reputation') }}
                            </li>
                            <li class="mb-2">
                                <i class="mdi mdi-lightbulb text-warning me-2"></i>
                                {{ __('Check warranty and return policies') }}
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Offer Details Modal -->
<div class="modal fade" id="offerDetailsModal" tabindex="-1" aria-labelledby="offerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="offerDetailsModalLabel">{{ __('Offer Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="offerDetailsContent">
                <!-- Content will be loaded here -->
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
function acceptOffer(offerId) {
    Swal.fire({
        title: '{{ __("Accept This Offer?") }}',
        text: '{{ __("This will accept the selected offer and decline all others. This action cannot be undone.") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("Yes, accept it!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('clinic.requests.accept-offer', $request->id) }}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "offer_id": offerId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: '{{ __("Success!") }}',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: '{{ __("OK") }}'
                        }).then(() => {
                            location.reload();
                        });
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

function viewOfferDetails(offerId) {
    $('#offerDetailsModal').modal('show');
    $('#offerDetailsContent').html('<div class="text-center p-4"><i class="mdi mdi-loading mdi-spin"></i> {{ __("Loading...") }}</div>');

    // Find the offer data from the current page
    const offers = @json($request->offers);
    const offer = offers.find(o => o.id === offerId);

    if (!offer) {
        $('#offerDetailsContent').html('<div class="alert alert-danger">{{ __("Offer not found") }}</div>');
        return;
    }

    const finalPrice = offer.price - (offer.discount || 0);
    const statusBadge = getStatusBadge(offer.status);

    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">{{ __('Supplier Information') }}</h6>
                <div class="mb-3">
                    <strong>{{ __('Supplier Name:') }}</strong>
                    <p class="mb-1">${offer.supplier.name}</p>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Phone:') }}</strong>
                    <p class="mb-1">${offer.supplier.phone || 'N/A'}</p>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Address:') }}</strong>
                    <p class="mb-1">${offer.supplier.address || 'N/A'}</p>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">{{ __('Offer Status') }}</h6>
                <div class="mb-3">
                    <strong>{{ __('Status:') }}</strong>
                    <div class="mt-1">${statusBadge}</div>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Submitted:') }}</strong>
                    <p class="mb-1">${new Date(offer.created_at).toLocaleDateString('{{ app()->getLocale() }}', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}</p>
                </div>
                ${offer.updated_at !== offer.created_at ? `
                <div class="mb-3">
                    <strong>{{ __('Last Updated:') }}</strong>
                    <p class="mb-1">${new Date(offer.updated_at).toLocaleDateString('{{ app()->getLocale() }}', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}</p>
                </div>
                ` : ''}
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">{{ __('Pricing Details') }}</h6>
                <div class="border rounded p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Original Price:') }}</span>
                        <strong>$${parseFloat(offer.price).toFixed(2)}</strong>
                    </div>
                    ${offer.discount ? `
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>{{ __('Discount:') }}</span>
                        <strong>-$${parseFloat(offer.discount).toFixed(2)}</strong>
                    </div>
                    <hr class="my-2">
                    ` : ''}
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">{{ __('Final Price:') }}</span>
                        <strong class="text-success fs-5">$${finalPrice.toFixed(2)}</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">{{ __('Delivery Information') }}</h6>
                <div class="border rounded p-3">
                    <div class="mb-2">
                        <strong>{{ __('Delivery Date:') }}</strong>
                        <p class="mb-1">${new Date(offer.delivery_time).toLocaleDateString('{{ app()->getLocale() }}', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}</p>
                    </div>
                    <div>
                        <strong>{{ __('Days from now:') }}</strong>
                        <p class="mb-0 text-primary">${Math.ceil((new Date(offer.delivery_time) - new Date()) / (1000 * 60 * 60 * 24))} {{ __('days') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <h6 class="text-muted mb-3">{{ __('Terms & Conditions') }}</h6>
            <div class="border rounded p-3 bg-light">
                <p class="mb-0">${offer.terms}</p>
            </div>
        </div>

        ${offer.status === 'pending' && '{{ $request->status }}' === 'open' ? `
        <hr>
        <div class="text-center">
            <button class="btn btn-success me-2" onclick="acceptOfferFromModal(${offer.id})">
                <i class="mdi mdi-check me-1"></i> {{ __('Accept This Offer') }}
            </button>
            <button class="btn btn-secondary" data-bs-dismiss="modal">
                {{ __('Close') }}
            </button>
        </div>
        ` : ''}
    `;

    $('#offerDetailsContent').html(content);
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">{{ __("Pending") }}</span>',
        'accepted': '<span class="badge bg-success">{{ __("Accepted") }}</span>',
        'declined': '<span class="badge bg-danger">{{ __("Declined") }}</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">{{ __("Unknown") }}</span>';
}

function acceptOfferFromModal(offerId) {
    $('#offerDetailsModal').modal('hide');
    acceptOffer(offerId);
}

function cancelRequest(id) {
    Swal.fire({
        title: '{{ __("Cancel Request?") }}',
        text: '{{ __("This will decline all pending offers and close the request.") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f1556c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("Yes, cancel it!") }}',
        cancelButtonText: '{{ __("No, keep it") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('clinic.requests.cancel', $request->id) }}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: '{{ __("Canceled!") }}',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: '{{ __("OK") }}'
                        }).then(() => {
                            location.reload();
                        });
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

function deleteRequest(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("You won\'t be able to revert this!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("Yes, delete it!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('clinic.requests.destroy', $request->id) }}",
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: '{{ __("Deleted!") }}',
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
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
                }
            });
        }
    });
}
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

.border {
    border: 1px solid #dee2e6 !important;
}

.fs-6 {
    font-size: 0.875rem !important;
}

.alert {
    border-radius: 0.375rem;
}

.list-unstyled li {
    padding: 0.25rem 0;
}

.text-decoration-none:hover {
    text-decoration: underline !important;
}

.card.border-success {
    border-color: #28a745 !important;
}

.card.border-warning {
    border-color: #ffc107 !important;
}

.card.border-secondary {
    border-color: #6c757d !important;
}

.h5 {
    font-size: 1.25rem;
    font-weight: 500;
}

.fw-bold {
    font-weight: 700 !important;
}

.d-grid {
    display: grid !important;
}

.gap-2 {
    gap: 0.5rem !important;
}
</style>
@endpush
