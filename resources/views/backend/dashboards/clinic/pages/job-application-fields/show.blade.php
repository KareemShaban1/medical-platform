@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Job Application Field Details'))

@section('content')

<div class="card mt-3">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">{{ __('Job Application Field Details') }}</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('clinic.job-application-fields.edit', $jobApplicationField->id) }}"
                    class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                <a href="{{ route('clinic.job-application-fields.index') }}"
                    class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-6">
                <h5 class="text-primary mb-3">{{ __('Basic Information') }}</h5>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Job') }}</label>
                    <p class="form-control-plaintext">
                        {{ $jobApplicationField->job->title ?? 'N/A' }}
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Field Name') }}</label>
                    <p class="form-control-plaintext">
                        <code>{{ $jobApplicationField->field_name }}</code>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Field Label') }}</label>
                    <p class="form-control-plaintext">
                        {{ $jobApplicationField->field_label }}
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Field Type') }}</label>
                    <p class="form-control-plaintext">
                        <span
                            class="badge bg-info">{{ ucfirst($jobApplicationField->field_type) }}</span>
                    </p>
                </div>
            </div>

            <!-- Configuration -->
            <div class="col-md-6">
                <h5 class="text-primary mb-3">{{ __('Configuration') }}</h5>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Required Field') }}</label>
                    <p class="form-control-plaintext">
                        @if($jobApplicationField->required)
                        <span class="badge bg-danger">{{ __('Yes') }}</span>
                        @else
                        <span class="badge bg-success">{{ __('No') }}</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Display Order') }}</label>
                    <p class="form-control-plaintext">{{ $jobApplicationField->order }}
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Created At') }}</label>
                    <p class="form-control-plaintext">
                        {{ $jobApplicationField->created_at->format('M d, Y H:i') }}
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Updated At') }}</label>
                    <p class="form-control-plaintext">
                        {{ $jobApplicationField->updated_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Options Section (if applicable) -->
        @if($jobApplicationField->options && count($jobApplicationField->options) > 0)
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="text-primary mb-3">{{ __('Field Options') }}</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Option') }}</th>
                                <th>{{ __('Value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobApplicationField->options as $index
                            => $option)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $option }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Field Preview -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="text-primary mb-3">{{ __('Field Preview') }}</h5>
                <div class="border rounded p-3 bg-light">
                    @include('backend.dashboards.clinic.pages.job-application-fields.partials.field-preview',
                    [
                    'field' => $jobApplicationField
                    ])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection