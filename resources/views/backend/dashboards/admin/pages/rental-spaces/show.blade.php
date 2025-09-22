@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.rental-spaces.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Rental Spaces') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Rental Space Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('Rental Space Information') }}</h5>

                            <div class="table-responsive">
                                <table class="table table-borderless table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 50%;">{{ __('Name') }}:</th>
                                            <td>{{ $rentalSpace->name }}</td>
                                        </tr>


                                        <tr>
                                            <th scope="row">{{ __('Status') }}:</th>
                                            <td>
                                                @if($rentalSpace->status)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Approval Status') }}:</th>
                                            <td>
                                               @if($rentalSpace->approvement->action == 'under_review')
                                                <span class="badge bg-warning">{{ __('Under Review') }}</span>
                                               @elseif($rentalSpace->approvement->action == 'approved')
                                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                               @else
                                                <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                               @endif
                                            </td>
                                        </tr>
                                        @if($rentalSpace->approvement->action && $rentalSpace->approvement->notes)
                                        <tr>
                                            <th scope="row">{{ __('Approval Notes') }}:</th>
                                            <td class="text-danger">{{ $rentalSpace->approvement->notes }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('Description') }}</h5>

                            <div class="mb-3">
                                <h6>{{ __('Description') }}:</h6>
                                <p class="text-muted">{{ $rentalSpace->description ?: __('No description available') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Availability') }}</h5>

                    <table class="table table-borderless table-nowrap mb-0">
                        <tbody>
                            <!-- type -->
                            <tr>
                                <th scope="row">{{ __('Type') }}:</th>
                                <td>{{ $rentalSpace->availability->type ?: __('No type available') }}</td>
                            </tr>
                            @if($rentalSpace->availability->type == 'daily')

                            @elseif($rentalSpace->availability->type == 'weekly')
                            <tr>
                                <th scope="row">{{ __('From Day') }}:</th>
                                <td>{{ $rentalSpace->availability->from_day ?: __('No from day available') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">{{ __('To Day') }}:</th>
                                <td>{{ $rentalSpace->availability->to_day ?: __('No to day available') }}</td>
                            </tr>
                            @elseif($rentalSpace->availability->type == 'monthly')
                            <tr>
                                <th scope="row">{{ __('From Date') }}:</th>
                                <td>{{ $rentalSpace->availability->from_date ?: __('No from date available') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">{{ __('To Date') }}:</th>
                                <td>{{ $rentalSpace->availability->to_date ?: __('No to date available') }}</td>
                            </tr>

                            @endif
                            <tr>
                                <th scope="row">{{ __('From Time') }}:</th>
                                <td>{{ $rentalSpace->availability->from_time ?: __('No from time available') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">{{ __('To Time') }}:</th>
                                <td>{{ $rentalSpace->availability->to_time ?: __('No to time available') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Pricing') }}</h5>

                    <table class="table table-borderless table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">{{ __('Price') }}:</th>
                                <td>{{ $rentalSpace->pricing->price ?: __('No price available') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">{{ __('Notes') }}:</th>
                                <td>{{ $rentalSpace->pricing->notes ?: __('No notes available') }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="col-lg-4">

        <!-- main image -->
        <div class="card">
            <div class="card-body">
               <div class="col-md-6">
               <img src="{{ $rentalSpace->main_image }}" alt="Rental Space Image" 
                class="img-fluid rounded"
                style="width: 100%; height: 150px; object-fit: contain;">
               </div>
            </div>
        </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Rental Space Images') }}</h5>

                    @if($rentalSpace->images)
                    <div class="row">
                        @foreach($rentalSpace->images as $image)
                        <div class="col-6 mb-3">
                            <div class="position-relative">
                                <img src="{{ $image }}"
                                    alt="Rental Space Image"
                                    class="img-fluid rounded"
                                    style="width: 100%; height: 150px; object-fit: cover;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#imageModal{{ $loop->index }}"
                                    style="cursor: pointer;">
                            </div>
                        </div>

                        <!-- Image Modal -->
                        <div class="modal fade" id="imageModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Rental Space Image') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="{{ $image }}"
                                            alt="Rental Space Image"
                                            class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center">
                        <i class="mdi mdi-image-off display-4 text-muted"></i>
                        <p class="text-muted mt-2">{{ __('No images available') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Timestamps') }}</h5>

                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">{{ __('Created At') }}:</th>
                                    <td>{{ $rentalSpace->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Updated At') }}:</th>
                                    <td>{{ $rentalSpace->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .img-fluid:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease-in-out;
    }
</style>
@endpush