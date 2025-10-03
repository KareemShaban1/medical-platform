@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a
                                href="{{ route('clinic.dashboard') }}">{{ __('Dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('clinic.jobs.index') }}">{{ __('Jobs') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Applicants') }}
                        </li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Job Applicants') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">{{ __('Job') }}:
                                {{ $job->title ?? '' }}
                            </h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('clinic.jobs.index') }}"
                                class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i>
                                {{ __('Back to Jobs') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($applicants) && $applicants->count() > 0)
                    <div class="row">
                        @foreach($applicants as $application)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <div
                                        class="d-flex justify-content-between align-items-center">
                                        <h6
                                            class="mb-0 text-primary">
                                            {{ $application->getApplicantName() ?? 'N/A' }}
                                        </h6>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($application->status === 'accepted' ? 'bg-green-100 text-green-800' : ($application->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </div>
                                    <small
                                        class="text-muted">{{ $application->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div
                                            class="d-flex align-items-center mb-2">
                                            <i
                                                class="fa fa-envelope text-muted me-2"></i>
                                            <span
                                                class="text-muted">{{ $application->getApplicantEmail() ?? 'N/A' }}</span>
                                        </div>
                                        @if($application->getApplicantPhone())
                                        <div
                                            class="d-flex align-items-center mb-2">
                                            <i
                                                class="fa fa-phone text-muted me-2"></i>
                                            <span
                                                class="text-muted">{{ $application->getApplicantPhone() ?? 'N/A' }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    @if($application->getCvPath())
                                    <div class="mb-3">
                                        <a href="{{ asset('storage/' . $application->getCvPath()) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i
                                                class="fa fa-download"></i>
                                            {{ __('Download CV') }}
                                        </a>
                                    </div>
                                    @endif

                                    @if($application->notes)
                                    <div class="mb-3">
                                        <h6
                                            class="text-muted">
                                            {{ __('Notes') }}:
                                        </h6>
                                        <p
                                            class="text-muted small">
                                            {{ $application->notes }}
                                        </p>
                                    </div>
                                    @endif

                                    <!-- Dynamic Fields -->
                                    @if($application->applicant_data)
                                    @foreach($application->applicant_data as $key => $value)
                                    @if(!in_array($key, ['name',
                                    'email', 'phone', 'cv']) &&
                                    $value)
                                    <div class="mb-2">
                                        <strong
                                            class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                        <span
                                            class="text-dark">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                                    </div>
                                    @endif
                                    @endforeach
                                    @endif
                                </div>
                                <div class="card-footer bg-light">
                                    <div
                                        class="d-flex justify-content-between">
                                        <div class="btn-group"
                                            role="group">
                                            <button type="button"
                                                class="btn btn-sm btn-success update-status-btn"
                                                data-application-id="{{ $application->id }}"
                                                data-status="accepted">
                                                <i
                                                    class="fa fa-check"></i>
                                                {{ __('Accept') }}
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-danger update-status-btn"
                                                data-application-id="{{ $application->id }}"
                                                data-status="rejected">
                                                <i
                                                    class="fa fa-times"></i>
                                                {{ __('Reject') }}
                                            </button>
                                        </div>
                                        <button type="button"
                                            class="btn btn-sm btn-info view-details-btn"
                                            data-application-id="{{ $application->id }}">
                                            <i
                                                class="fa fa-eye"></i>
                                            {{ __('View Details') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fa fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('No Applicants Found') }}</h5>
                        <p class="text-muted">
                            {{ __('There are no applications for this job yet.') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Application Details Modal -->
<div class="modal fade" id="applicationDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Application Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="applicationDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle status update buttons
        $('.update-status-btn').on('click', function() {
            var applicationId = $(this).data('application-id');
            var status = $(this).data('status');

            if (confirm('Are you sure you want to ' + status +
                    ' this application?')) {
                $.ajax({
                    url: '{{ route("clinic.job-applications.update-status") }}',
                    method: 'POST',
                    data: {
                        application_id: applicationId,
                        status: status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'An error occurred while updating the application status.';
                        toastr.error(message);
                    }
                });
            }
        });

        // Handle view details buttons
        $('.view-details-btn').on('click', function() {
            var applicationId = $(this).data('application-id');
            // This would typically load application details via AJAX
            $('#applicationDetailsModal').modal('show');
        });
    });
</script>
@endpush
