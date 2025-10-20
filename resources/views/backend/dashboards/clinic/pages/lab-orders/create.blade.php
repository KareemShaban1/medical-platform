@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('Create Lab Order') }}</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('clinic.lab-orders.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Patient') }}</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">-- {{ __('Select Patient') }} --</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Test Name') }}</label>
                        <input type="text" class="form-control" name="test_name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Lab Name') }}</label>
                        <input type="text" class="form-control" name="lab_name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Cost Amount') }}</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="cost_amount">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Sent At') }}</label>
                        <input type="datetime-local" class="form-control" name="sent_at">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Notes') }}</label>
                        <textarea class="form-control" rows="3" name="notes"></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary">{{ __('Create') }}</button>
                    <a href="{{ route('clinic.lab-orders.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

