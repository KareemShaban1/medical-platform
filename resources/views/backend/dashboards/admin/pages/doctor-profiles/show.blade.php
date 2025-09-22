@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.doctor-profiles.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Profiles') }}
                    </a>
                    @if($profile->status === 'pending')
                        <button onclick="approveProfile({{ $profile->id }})" class="btn btn-success">
                            <i class="fa fa-check"></i> {{ __('Approve') }}
                        </button>
                        <button onclick="rejectProfile({{ $profile->id }})" class="btn btn-danger">
                            <i class="fa fa-times"></i> {{ __('Reject') }}
                        </button>
                    @endif
                    @if($profile->status === 'approved')
                        <button onclick="toggleFeatured({{ $profile->id }})" class="btn {{ $profile->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                            <i class="fa fa-star"></i> {{ $profile->is_featured ? __('Remove Featured') : __('Make Featured') }}
                        </button>
                    @endif
                </div>
                <h4 class="page-title">{{ __('Doctor Profile Review') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Header -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 text-center">
                            @if($profile->profile_photo_url)
                                <img src="{{ $profile->profile_photo_url }}"
                                     alt="Profile Photo"
                                     class="img-fluid rounded-circle mb-3"
                                     style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                     style="width: 200px; height: 200px; color: white; font-size: 64px;">
                                    <i class="mdi mdi-account"></i>
                                </div>
                            @endif

                            <h3>{{ $profile->name }}</h3>
                            <p class="text-muted lead">{{ $profile->email }}</p>

                            <div class="mb-3">
                                {!! $profile->status_badge !!}
                            </div>

                            @if($profile->status === 'pending')
                                <div class="d-flex gap-2 justify-content-center">
                                    <button onclick="approveProfile({{ $profile->id }})" class="btn btn-success">
                                        <i class="fa fa-check"></i> {{ __('Approve') }}
                                    </button>
                                    <button onclick="rejectProfile({{ $profile->id }})" class="btn btn-danger">
                                        <i class="fa fa-times"></i> {{ __('Reject') }}
                                    </button>
                                </div>
                            @endif
                            @if($profile->status === 'approved')
                                <div class="text-center mt-3">
                                    <button onclick="toggleFeatured({{ $profile->id }})" class="btn {{ $profile->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                                        <i class="fa fa-star"></i> {{ $profile->is_featured ? __('Remove Featured') : __('Make Featured') }}
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-9">
                            <div class="row">
                                <!-- Basic Info -->
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th>{{ __('Clinic') }}:</th>
                                                    <td>
                                                        @if($profile->clinicUser && $profile->clinicUser->clinic)
                                                            <a href="{{ route('admin.clinics.show', $profile->clinicUser->clinic->id) }}" class="text-primary">
                                                                {{ $profile->clinicUser->clinic->name }}
                                                            </a>
                                                            <br>
                                                            <small class="text-muted">{{ __('User') }}: {{ $profile->clinicUser->name }}</small>
                                                        @else
                                                            {{ $profile->clinicUser->name ?? 'N/A' }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('Phone') }}:</th>
                                                    <td>{{ $profile->phone ?: 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('Years of Experience') }}:</th>
                                                    <td>{{ $profile->years_experience ?: 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('Status') }}:</th>
                                                    <td>{!! $profile->status_badge !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Review Info -->
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th>{{ __('Created At') }}:</th>
                                                    <td>{{ $profile->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('Updated At') }}:</th>
                                                    <td>{{ $profile->updated_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                                @if($profile->reviewed_at)
                                                <tr>
                                                    <th>{{ __('Reviewed At') }}:</th>
                                                    <td>{{ $profile->reviewed_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                                @endif
                                                @if($profile->reviewer)
                                                <tr>
                                                    <th>{{ __('Reviewed By') }}:</th>
                                                    <td>{{ $profile->reviewer->name }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <th>{{ __('Featured Status') }}:</th>
                                                    <td>
                                                        @if($profile->is_featured)
                                                            <span class="badge bg-warning"><i class="fa fa-star"></i> {{ __('Featured') }}</span>
                                                            @if($profile->featuredBy)
                                                                <br><small class="text-muted">{{ __('Featured by') }}: {{ $profile->featuredBy->name }}</small>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-light text-dark">{{ __('Not Featured') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            @if($profile->rejection_reason)
                                <div class="alert alert-danger">
                                    <h6 class="alert-heading">{{ __('Previous Rejection Reason') }}:</h6>
                                    {{ $profile->rejection_reason }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bio Section -->
        @if($profile->bio)
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Bio/Summary') }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ $profile->bio }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Specialties and Services -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Specialties') }}</h5>
                </div>
                <div class="card-body">
                    @if($profile->specialties && count($profile->specialties) > 0)
                        @foreach($profile->specialties as $specialty)
                            <span class="badge bg-primary me-1 mb-1">{{ $specialty }}</span>
                        @endforeach
                    @else
                        <p class="text-muted">{{ __('No specialties listed') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Services Offered') }}</h5>
                </div>
                <div class="card-body">
                    @if($profile->services_offered && count($profile->services_offered) > 0)
                        @foreach($profile->services_offered as $service)
                            <span class="badge bg-info me-1 mb-1">{{ $service }}</span>
                        @endforeach
                    @else
                        <p class="text-muted">{{ __('No services listed') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Education -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Education') }}</h5>
                </div>
                <div class="card-body">
                    @if($profile->education && count($profile->education) > 0)
                        @foreach($profile->education as $edu)
                            @if($edu['degree'] || $edu['institution'] || $edu['year'])
                            <div class="mb-3 pb-3 border-bottom">
                                @if($edu['degree'])
                                    <h6 class="mb-1">{{ $edu['degree'] }}</h6>
                                @endif
                                @if($edu['institution'])
                                    <p class="text-muted mb-1">{{ $edu['institution'] }}</p>
                                @endif
                                @if($edu['year'])
                                    <small class="text-muted">{{ $edu['year'] }}</small>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-muted">{{ __('No education information provided') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Experience -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Experience') }}</h5>
                </div>
                <div class="card-body">
                    @if($profile->experience && count($profile->experience) > 0)
                        @foreach($profile->experience as $exp)
                            @if($exp['position'] || $exp['company'] || $exp['start_year'] || $exp['end_year'])
                            <div class="mb-3 pb-3 border-bottom">
                                @if($exp['position'])
                                    <h6 class="mb-1">{{ $exp['position'] }}</h6>
                                @endif
                                @if($exp['company'])
                                    <p class="text-muted mb-1">{{ $exp['company'] }}</p>
                                @endif
                                @if($exp['start_year'] || $exp['end_year'])
                                    <small class="text-muted">
                                        {{ $exp['start_year'] ?: 'Unknown' }} - {{ $exp['end_year'] ?: 'Present' }}
                                    </small>
                                @endif
                                @if($exp['description'])
                                    <p class="mt-2 mb-0">{{ $exp['description'] }}</p>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-muted">{{ __('No experience information provided') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Social Media') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($profile->linkedin_link)
                        <div class="col-6 mb-2">
                            <a href="{{ $profile->linkedin_link }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </a>
                        </div>
                        @endif
                        @if($profile->twitter_link)
                        <div class="col-6 mb-2">
                            <a href="{{ $profile->twitter_link }}" target="_blank" class="btn btn-outline-info btn-sm w-100">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                        </div>
                        @endif
                        @if($profile->facebook_link)
                        <div class="col-6 mb-2">
                            <a href="{{ $profile->facebook_link }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fab fa-facebook"></i> Facebook
                            </a>
                        </div>
                        @endif
                        @if($profile->instagram_link)
                        <div class="col-6 mb-2">
                            <a href="{{ $profile->instagram_link }}" target="_blank" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fab fa-instagram"></i> Instagram
                            </a>
                        </div>
                        @endif
                    </div>
                    @if(!$profile->linkedin_link && !$profile->twitter_link && !$profile->facebook_link && !$profile->instagram_link)
                        <p class="text-muted">{{ __('No social media links provided') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Research Links -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Research Links') }}</h5>
                </div>
                <div class="card-body">
                    @if($profile->research_links && count($profile->research_links) > 0)
                        @foreach($profile->research_links as $link)
                            <div class="mb-2">
                                <a href="{{ $link }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa fa-external-link-alt"></i> {{ __('Research Paper') }}
                                </a>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">{{ __('No research links provided') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">{{ __('Reject Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" id="rejectProfileId" value="{{ $profile->id }}">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required maxlength="1000"></textarea>
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">{{ __('Please provide a clear reason for rejection so the doctor can make necessary improvements.') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Reject Profile') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Approve profile
function approveProfile(id) {
    Swal.fire({
        title: 'Approve Profile?',
        text: "This will approve the doctor's profile and make it publicly visible.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.doctor-profiles.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire('Approved!', response.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
                }
            });
        }
    });
}

// Reject profile - show modal
function rejectProfile(id) {
    $('#rejectProfileId').val(id);
    $('#rejection_reason').val('');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#rejectModal').modal('show');
}

// Handle reject form submission
$('#rejectForm').on('submit', function(e) {
    e.preventDefault();

    let profileId = $('#rejectProfileId').val();
    let reason = $('#rejection_reason').val();

    $.ajax({
        url: '{{ route("admin.doctor-profiles.reject", ":id") }}'.replace(':id', profileId),
        method: 'POST',
        data: {
            rejection_reason: reason,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#rejectModal').modal('hide');
            Swal.fire('Rejected!', response.message, 'success')
                .then(() => location.reload());
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors || {};
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                Object.keys(errors).forEach(function(key) {
                    let $input = $('#' + key);
                    if ($input.length) {
                        $input.addClass('is-invalid');
                        $input.next('.invalid-feedback').text(errors[key][0]);
                    }
                });
            } else {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
            }
        }
    });
});

// Toggle featured status
function toggleFeatured(id) {
    let isCurrentlyFeatured = {{ $profile->is_featured ? 'true' : 'false' }};
    let title = isCurrentlyFeatured ? 'Remove from Featured?' : 'Make Featured?';
    let text = isCurrentlyFeatured
        ? "This will remove the doctor's profile from featured listings."
        : "This will make the doctor's profile featured and more visible.";

    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#d33',
        confirmButtonText: isCurrentlyFeatured ? 'Yes, remove it!' : 'Yes, make it featured!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.doctor-profiles.toggle-featured", ":id") }}'.replace(':id', id),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire('Success!', response.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
                }
            });
        }
    });
}
</script>
@endpush
