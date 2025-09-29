@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Offer Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('supplier.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('supplier.offers.index') }}">{{ __('My Offers') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Offer Details') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Offer Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h5 class="card-title mb-0">{{ __('Offer Information') }}</h5>
                        @if($offer->status === 'pending')
                            <span class="badge bg-warning fs-6">{{ __('Pending') }}</span>
                        @elseif($offer->status === 'accepted')
                            <span class="badge bg-success fs-6">{{ __('Accepted') }}</span>
                        @else
                            <span class="badge bg-danger fs-6">{{ __('Declined') }}</span>
                        @endif
                    </div>

                    @if($offer->status === 'accepted')
                        <div class="alert alert-success">
                            <i class="mdi mdi-check-circle me-2"></i>
                            <strong>{{ __('Congratulations!') }}</strong> {{ __('Your offer has been accepted by') }} <strong>{{ $offer->request->clinic->name }}</strong>. {{ __('Please prepare to fulfill the order according to your terms.') }}
                        </div>
                    @elseif($offer->status === 'declined')
                        <div class="alert alert-secondary">
                            <i class="mdi mdi-information me-2"></i>
                            <strong>{{ __('Offer Declined:') }}</strong> {{ __('This offer was not selected. Don\'t worry, keep looking for new opportunities!') }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="mdi mdi-clock me-2"></i>
                            <strong>{{ __('Pending Review:') }}</strong> {{ __('Your offer is being reviewed by the clinic. You\'ll be notified once a decision is made.') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">{{ __('Pricing Details') }}</h6>
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Original Price:') }}</span>
                                    <strong>${{ number_format($offer->price, 2) }}</strong>
                                </div>
                                @if($offer->discount)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>{{ __('Discount:') }}</span>
                                        <strong>-${{ number_format($offer->discount, 2) }}</strong>
                                    </div>
                                    <hr class="my-2">
                                @endif
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">{{ __('Final Price:') }}</span>
                                    <strong class="text-success fs-5">${{ number_format($offer->final_price, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">{{ __('Delivery Information') }}</h6>
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Delivery Date:') }}</span>
                                    <strong>{{ $offer->delivery_time->format('M d, Y') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>{{ __('Days from now:') }}</span>
                                    <strong class="text-primary">{{ $offer->delivery_time->diffForHumans() }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-2">{{ __('Terms & Conditions') }}</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $offer->terms }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">{{ __('Timeline') }}</h6>
                            <div class="border rounded p-3">
                                <div class="mb-2">
                                    <small class="text-muted">{{ __('Submitted:') }}</small><br>
                                    <strong>{{ $offer->created_at->format('M d, Y H:i') }}</strong>
                                </div>
                                @if($offer->updated_at != $offer->created_at)
                                    <div class="mb-2">
                                        <small class="text-muted">{{ __('Last Updated:') }}</small><br>
                                        <strong>{{ $offer->updated_at->format('M d, Y H:i') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">{{ __('Actions') }}</h6>
                            <div class="d-grid gap-2">
                                @if($offer->status === 'pending')
                                    <a href="{{ route('supplier.offers.edit', $offer->id) }}" class="btn btn-primary">
                                        <i class="mdi mdi-pencil me-1"></i> {{ __('Edit Offer') }}
                                    </a>
                                @endif
                                <a href="{{ route('supplier.offers.index') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-arrow-left me-1"></i> {{ __('Back to My Offers') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Request Information -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Request Information') }}</h5>

                    <div class="mb-3">
                        <strong>{{ __('Request ID:') }}</strong>
                        <p class="mb-2">#{{ $offer->request->id }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Status:') }}</strong>
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
                        <strong>{{ __('Category:') }}</strong>
                        <p class="mb-2">{{ $offer->request->categories->pluck('name')->join(', ') }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Quantity:') }}</strong>
                        <p class="mb-2">{{ number_format($offer->request->quantity) }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Timeline:') }}</strong>
                        <p class="mb-2">
                            @if($offer->request->timeline)
                                {{ $offer->request->timeline->format('M d, Y') }}
                            @else
                                <span class="text-muted">{{ __('Not specified') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Description:') }}</strong>
                        <div class="border rounded p-2 mt-1 bg-light">
                            <small>{{ $offer->request->description }}</small>
                        </div>
                    </div>

                    @if($offer->request->preferred_specs)
                        <div class="mb-3">
                            <strong>{{ __('Preferred Specs:') }}</strong>
                            <div class="border rounded p-2 mt-1 bg-light">
                                <small>{{ $offer->request->preferred_specs }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Clinic Information -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Clinic Information') }}</h5>

                    <div class="mb-3">
                        <strong>{{ __('Name:') }}</strong>
                        <p class="mb-2">{{ $offer->request->clinic->name }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Phone:') }}</strong>
                        <p class="mb-2">{{ $offer->request->clinic->phone }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Address:') }}</strong>
                        <p class="mb-2">{{ $offer->request->clinic->address }}</p>
                    </div>
                </div>
            </div>

            <!-- Competition Overview -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Competition Overview') }}</h5>

                    <div class="mb-3">
                        <strong>{{ __('Total Offers:') }}</strong>
                        <p class="mb-2">{{ $offer->request->offers->count() }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Pending Offers:') }}</strong>
                        <p class="mb-2">{{ $offer->request->offers->where('status', 'pending')->count() }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Your Position:') }}</strong>
                        <p class="mb-2">
                            @php
                                $sortedOffers = $offer->request->offers->sortBy('final_price');
                                $position = $sortedOffers->search(function($item) use ($offer) {
                                    return $item->id === $offer->id;
                                }) + 1;
                            @endphp
                            #{{ $position }} {{ __('by price') }}
                            @if($position === 1)
                                <span class="badge bg-success ms-1">{{ __('Lowest') }}</span>
                            @elseif($position <= 3)
                                <span class="badge bg-warning ms-1">{{ __('Top 3') }}</span>
                            @endif
                        </p>
                    </div>

                    @if($offer->request->acceptedOffer)
                        <div class="mb-3">
                            <strong>{{ __('Winner:') }}</strong>
                            <p class="mb-2">
                                @if($offer->request->acceptedOffer->id === $offer->id)
                                    <span class="text-success fw-bold">{{ __('Your offer!') }}</span>
                                @else
                                    <span class="text-muted">{{ __('Another supplier') }}</span>
                                    <br><small class="text-muted">{{ __('Price:') }} ${{ number_format($offer->request->acceptedOffer->final_price, 2) }}</small>
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card-title {
    color: #6c757d;
    font-weight: 600;
}

.bg-light {
    background-color: #f8f9fa !important;
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

.alert-secondary {
    border-left: 4px solid #6c757d;
}

.border {
    border: 1px solid #dee2e6 !important;
}

.fs-6 {
    font-size: 0.875rem !important;
}

.text-success {
    color: #28a745 !important;
}

.text-primary {
    color: #007bff !important;
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

hr {
    margin: 0.5rem 0;
    color: inherit;
    background-color: currentColor;
    border: 0;
    opacity: 0.25;
}

hr:not([size]) {
    height: 1px;
}
</style>
@endpush
