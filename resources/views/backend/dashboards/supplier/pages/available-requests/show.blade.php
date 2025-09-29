@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Request Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('supplier.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('supplier.available-requests.index') }}">{{ __('Available Requests') }}</a></li>
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
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">{{ __('Request Information') }}</h5>
                        <span class="badge bg-success">{{ __('Open') }}</span>
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
        </div>

        <div class="col-lg-4">
            <!-- Clinic Information -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Clinic Information') }}</h5>

                    <div class="mb-3">
                        <strong>{{ __('Clinic Name:') }}</strong>
                        <p class="mb-2">{{ $request->clinic->name }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Phone:') }}</strong>
                        <p class="mb-2">{{ $request->clinic->phone }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Address:') }}</strong>
                        <p class="mb-2">{{ $request->clinic->address }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Card -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Actions') }}</h5>

                    @if($hasOffer)
                        <div class="alert alert-info">
                            <i class="mdi mdi-information me-2"></i>
                            {{ __('You have already submitted an offer for this request.') }}
                        </div>
                        <a href="{{ route('supplier.offers.index') }}" class="btn btn-primary w-100">
                            <i class="mdi mdi-eye me-1"></i> {{ __('View My Offers') }}
                        </a>
                    @else
                        <div class="alert alert-success">
                            <i class="mdi mdi-lightbulb-on me-2"></i>
                            {{ __('This is a great opportunity! Submit your competitive offer now.') }}
                        </div>
                        <a href="{{ route('supplier.available-requests.create-offer', $request->id) }}" class="btn btn-success w-100 mb-2">
                            <i class="mdi mdi-paper-plane me-1"></i> {{ __('Submit Offer') }}
                        </a>
                    @endif

                    <a href="{{ route('supplier.available-requests.index') }}" class="btn btn-light w-100">
                        <i class="mdi mdi-arrow-left me-1"></i> {{ __('Back to Available Requests') }}
                    </a>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Tips for Success') }}</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="mdi mdi-check-circle text-success me-2"></i>
                            {{ __('Offer competitive pricing') }}
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-check-circle text-success me-2"></i>
                            {{ __('Provide realistic delivery times') }}
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-check-circle text-success me-2"></i>
                            {{ __('Include clear terms & conditions') }}
                        </li>
                        <li class="mb-2">
                            <i class="mdi mdi-check-circle text-success me-2"></i>
                            {{ __('Respond quickly to opportunities') }}
                        </li>
                    </ul>
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

.border {
    border: 1px solid #dee2e6 !important;
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

.list-unstyled li {
    padding: 0.25rem 0;
}

.btn {
    border-radius: 0.375rem;
}

.text-decoration-none:hover {
    text-decoration: underline !important;
}
</style>
@endpush
