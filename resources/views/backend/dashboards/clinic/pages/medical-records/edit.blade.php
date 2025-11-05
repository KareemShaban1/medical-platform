@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Medical Record'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-0">{{ __('Medical Record for Appointment') }} #{{ $appointment->id }}</h4>
                    <ol class="breadcrumb m-0 mt-2">
                        <li class="breadcrumb-item"><a href="{{ route('clinic.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('clinic.medical-records.index') }}">{{ __('Medical Records') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Edit') }}</li>
                    </ol>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('clinic.medical-records.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                    <form method="POST" action="{{ route('clinic.medical-records.share', $record->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $record->is_shared_with_patient ? 'warning' : 'success' }}">
                            {{ $record->is_shared_with_patient ? __('Unshare with Patient') : __('Share with Patient') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('clinic.medical-records.update', $appointment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Visit Type') }}</label>
                                <select name="visit_type" class="form-select">
                                    @foreach([0 => __('Initial'), 1 => __('Follow-up'), 2 => __('Consultation')] as $k => $v)
                                        <option value="{{ $k }}" {{ old('visit_type', $record->visit_type) === $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                                @error('visit_type') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="shareWithPatient" name="is_shared_with_patient" value="1" {{ old('is_shared_with_patient', $record->is_shared_with_patient) ? 'checked' : '' }}>
                                    <label for="shareWithPatient" class="form-check-label">{{ __('Share with Patient') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Chief Complaint') }}</label>
                            <textarea name="chief_complaint" class="form-control" rows="2">{{ old('chief_complaint', $record->chief_complaint) }}</textarea>
                            @error('chief_complaint') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Diagnosis') }}</label>
                            <textarea name="diagnosis" class="form-control" rows="3">{{ old('diagnosis', $record->diagnosis) }}</textarea>
                            @error('diagnosis') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Treatment') }}</label>
                            <textarea name="treatment" class="form-control" rows="3">{{ old('treatment', $record->treatment) }}</textarea>
                            @error('treatment') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes', $record->notes) }}</textarea>
                            @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <button class="btn btn-primary">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Appointment Summary') }}</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ __('Patient') }}</dt>
                        <dd class="col-sm-7">{{ $record->patient?->name ?: 'N/A' }}</dd>
                        <dt class="col-sm-5">{{ __('Doctor') }}</dt>
                        <dd class="col-sm-7">{{ $record->doctor?->name ?: 'N/A' }}</dd>
                        <dt class="col-sm-5">{{ __('Date') }}</dt>
                        <dd class="col-sm-7">{{ optional($appointment->period?->date)->format('Y-m-d') ?: 'N/A' }}</dd>
                        <dt class="col-sm-5">{{ __('Visit Type') }}</dt>
                        <dd class="col-sm-7">
                            {{ $record->visit_type instanceof \App\Enums\VisitType ? $record->visit_type->label() : (
                                [0=>__('Initial'),1=>__('Follow-up'),2=>__('Consultation')][$record->visit_type ?? 0] ?? 'N/A'
                            ) }}
                        </dd>


                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



