@extends('backend.dashboards.clinic.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="page-title mb-0">{{ __('Rental Space Details') }}</h4>
                    <div class="page-title-right">
                        <a href="{{ route('clinic.rental-spaces.edit', $rentalSpace->id) }}" class="btn btn-primary me-2">
                            <i class="mdi mdi-pencil"></i> {{ __('Edit') }}
                        </a>
                        <a href="{{ route('clinic.rental-spaces.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i
                                class="mdi mdi-information-outline me-2"></i>{{ __('Basic Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="width: 40%;">{{ __('Name') }}:</th>
                                            <td><strong>{{ $rentalSpace->name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Listing Type') }}:</th>
                                            <td>
                                                @if ($rentalSpace->listing_type === 'sale')
                                                    <span class="badge bg-warning text-dark"><i
                                                            class="mdi mdi-tag me-1"></i>{{ __('For Sale') }}</span>
                                                @else
                                                    <span class="badge bg-success"><i
                                                            class="mdi mdi-key me-1"></i>{{ __('For Rent') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Status') }}:</th>
                                            <td>
                                                @if ($rentalSpace->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Approval') }}:</th>
                                            <td>
                                                @if (optional($rentalSpace->approvement)->action == 'under_review')
                                                    <span
                                                        class="badge bg-warning text-dark">{{ __('Under Review') }}</span>
                                                @elseif(optional($rentalSpace->approvement)->action == 'approved')
                                                    <span class="badge bg-success">{{ __('Approved') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if (optional($rentalSpace->approvement)->notes)
                                            <tr>
                                                <th>{{ __('Notes') }}:</th>
                                                <td class="text-danger">{{ $rentalSpace->approvement->notes }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        @if ($rentalSpace->capacity)
                                            <tr>
                                                <th style="width: 40%;">{{ __('Capacity') }}:</th>
                                                <td><i
                                                        class="mdi mdi-account-group me-1 text-primary"></i>{{ $rentalSpace->capacity }}
                                                    {{ __('persons') }}</td>
                                            </tr>
                                        @endif
                                        @if ($rentalSpace->area_sqm)
                                            <tr>
                                                <th>{{ __('Area') }}:</th>
                                                <td><i
                                                        class="mdi mdi-ruler-square me-1 text-primary"></i>{{ number_format($rentalSpace->area_sqm, 2) }}
                                                    {{ __('sqm') }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>{{ __('Created') }}:</th>
                                            <td>{{ $rentalSpace->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Updated') }}:</th>
                                            <td>{{ $rentalSpace->updated_at->format('Y-m-d') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted">{{ __('Location') }}</h6>
                                <p><i class="mdi mdi-map-marker text-danger me-1"></i>{{ $rentalSpace->location }}</p>
                            </div>
                            <div class="col-12">
                                <h6 class="text-muted">{{ __('Description') }}</h6>
                                <p>{{ $rentalSpace->description ?: __('No description available') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="mdi mdi-currency-usd me-2"></i>{{ __('Pricing') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($rentalSpace->listing_type === 'sale')
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">{{ __('Sale Price') }}:</th>
                                        <td>
                                            <strong class="text-success fs-4">
                                                {{ $rentalSpace->sale_price ? number_format($rentalSpace->sale_price, 2) . ' ' . __('EGP') : __('Price on Request') }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            @if ($rentalSpace->pricing)
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%;">{{ __('Price') }}:</th>
                                            <td>
                                                <strong
                                                    class="text-primary fs-4">{{ number_format($rentalSpace->pricing->price, 2) }}
                                                    {{ __('EGP') }}</strong>
                                                <span class="text-muted">/
                                                    {{ __(ucfirst($rentalSpace->pricing->pricing_type ?? 'day')) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Pricing Type') }}:</th>
                                            <td>
                                                <span
                                                    class="badge bg-secondary">{{ __(ucfirst($rentalSpace->pricing->pricing_type ?? 'daily')) }}</span>
                                            </td>
                                        </tr>
                                        @if ($rentalSpace->pricing->notes)
                                            <tr>
                                                <th>{{ __('Notes') }}:</th>
                                                <td>{{ $rentalSpace->pricing->notes }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted mb-0">{{ __('No pricing information available') }}</p>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Amenities -->
                @if ($rentalSpace->amenities && count($rentalSpace->amenities) > 0)
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0"><i class="mdi mdi-star me-2"></i>{{ __('Amenities') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($rentalSpace->amenities_labels as $amenity)
                                    <span class="badge bg-soft-primary text-primary px-3 py-2">
                                        <i class="mdi mdi-check-circle me-1"></i>{{ $amenity }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Weekly Schedule -->
                @if ($rentalSpace->schedules && $rentalSpace->schedules->count() > 0)
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0"><i
                                    class="mdi mdi-calendar-clock me-2"></i>{{ __('Weekly Schedule') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            @foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                                <th>{{ __(ucfirst(substr($day, 0, 3))) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                                @php
                                                    $schedule = $rentalSpace->schedules->firstWhere(
                                                        'day_of_week',
                                                        $day,
                                                    );
                                                    $isAvailable = $schedule && $schedule->is_available;
                                                @endphp
                                                <td
                                                    class="{{ $isAvailable ? 'table-success' : 'table-light text-muted' }}">
                                                    @if ($isAvailable)
                                                        <i class="mdi mdi-check-circle text-success"></i><br>
                                                        <small>{{ date('g:i A', strtotime($schedule->start_time)) }}</small><br>
                                                        <small>{{ date('g:i A', strtotime($schedule->end_time)) }}</small>
                                                    @else
                                                        <i class="mdi mdi-close-circle text-muted"></i><br>
                                                        <small>{{ __('Closed') }}</small>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Legacy Availability -->
                @if ($rentalSpace->availability)
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0"><i class="mdi mdi-clock-outline me-2"></i>{{ __('Availability') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">{{ __('Type') }}:</th>
                                        <td>
                                            <span
                                                class="badge bg-info">{{ __(ucfirst($rentalSpace->availability->type ?? 'daily')) }}</span>
                                        </td>
                                    </tr>
                                    @if ($rentalSpace->availability->type == 'weekly')
                                        <tr>
                                            <th>{{ __('Days') }}:</th>
                                            <td>{{ $rentalSpace->availability->from_day }} -
                                                {{ $rentalSpace->availability->to_day }}</td>
                                        </tr>
                                    @elseif($rentalSpace->availability->type == 'monthly')
                                        <tr>
                                            <th>{{ __('Dates') }}:</th>
                                            <td>{{ $rentalSpace->availability->from_date }} -
                                                {{ $rentalSpace->availability->to_date }}</td>
                                        </tr>
                                    @endif
                                    @if ($rentalSpace->availability->from_time || $rentalSpace->availability->to_time)
                                        <tr>
                                            <th>{{ __('Time') }}:</th>
                                            <td>{{ $rentalSpace->availability->from_time }} -
                                                {{ $rentalSpace->availability->to_time }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Main Image -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="mdi mdi-image me-2"></i>{{ __('Main Image') }}</h5>
                    </div>
                    <div class="card-body text-center">
                        @if ($rentalSpace->main_image)
                            <img src="{{ $rentalSpace->main_image }}" alt="{{ $rentalSpace->name }}"
                                class="img-fluid rounded shadow-sm" style="max-height: 250px; object-fit: cover;">
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="mdi mdi-image-off display-4"></i>
                                <p class="mt-2">{{ __('No main image') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gallery -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="mdi mdi-image-multiple me-2"></i>{{ __('Gallery') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($rentalSpace->images && count($rentalSpace->images) > 0)
                            <div class="row g-2">
                                @foreach ($rentalSpace->images as $index => $image)
                                    <div class="col-6">
                                        <img src="{{ $image }}" alt="Image {{ $index + 1 }}"
                                            class="img-fluid rounded cursor-pointer gallery-img"
                                            style="height: 100px; width: 100%; object-fit: cover;" data-bs-toggle="modal"
                                            data-bs-target="#imageModal{{ $index }}">
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Image') }} {{ $index + 1 }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ $image }}" class="img-fluid rounded">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="mdi mdi-image-off display-4"></i>
                                <p class="mt-2 mb-0">{{ __('No gallery images') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="mdi mdi-chart-box me-2"></i>{{ __('Quick Stats') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h4 class="text-primary mb-1">{{ $rentalSpace->capacity ?? '-' }}</h4>
                                <small class="text-muted">{{ __('Capacity') }}</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-primary mb-1">
                                    {{ $rentalSpace->area_sqm ? number_format($rentalSpace->area_sqm, 0) : '-' }}</h4>
                                <small class="text-muted">{{ __('sqm') }}</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-primary mb-1">
                                    {{ $rentalSpace->schedules ? $rentalSpace->schedules->where('is_available', true)->count() : 0 }}
                                </h4>
                                <small class="text-muted">{{ __('Days') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .gallery-img {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .gallery-img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .bg-soft-primary {
            background-color: rgba(7, 145, 132, 0.15) !important;
        }
    </style>
@endpush
