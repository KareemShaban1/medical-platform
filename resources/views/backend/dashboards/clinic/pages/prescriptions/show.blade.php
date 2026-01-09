@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.patients.show', $prescription->patient_id) }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Prescription Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Prescription Information') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 40%;">{{ __('Patient') }}:</th>
                                    <td>{{ $prescription->patient?->name ?? __('N/A') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Doctor') }}:</th>
                                    <td>{{ $prescription->doctorProfile?->name ?? __('N/A') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Appointment') }}:</th>
                                    <td>
                                        @if($prescription->appointment)
                                            {{ $prescription->appointment->period ? $prescription->appointment->period->date->format('Y-m-d') : 'N/A' ?? __('N/A') }}
                                            {{ $prescription->appointment->period ? $prescription->appointment->period->start_time . ' - ' . $prescription->appointment->period->end_time: 'N/A' }}
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Notes') }}:</th>
                                    <td>{{ $prescription->notes ?: __('No notes available') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Created At') }}:</th>
                                    <td>{{ $prescription->created_at?->format('Y-m-d H:i') ?? __('N/A') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Updated At') }}:</th>
                                    <td>{{ $prescription->updated_at?->format('Y-m-d H:i') ?? __('N/A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Prescription Items') }}</h5>
                    @if($prescription->items && $prescription->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Drug Name') }}</th>
                                        <th>{{ __('Dose') }}</th>
                                        <th>{{ __('Frequency') }}</th>
                                        <th>{{ __('Duration') }}</th>
                                        <th>{{ __('Notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prescription->items as $item)
                                    <tr>
                                        <td>{{ $item->drug_name ?: __('N/A') }}</td>
                                        <td>{{ $item->dose ?: __('N/A') }}</td>
                                        <td>{{ $item->frequency ?: __('N/A') }}</td>
                                        <td>{{ $item->duration ?: __('N/A') }}</td>
                                        <td>{{ $item->notes ?: __('N/A') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('No items available') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Prescription Images') }}</h5>
                    @if($prescription->images && count($prescription->images) > 0)
                        <div class="row">
                            @foreach($prescription->images as $image)
                            <div class="col-6 mb-3">
                                <img src="{{ $image }}"
                                    alt="{{ __('Prescription Image') }}"
                                    class="img-fluid rounded"
                                    style="width: 100%; height: 140px; object-fit: cover;">
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
        </div>
    </div>
</div>
@endsection
