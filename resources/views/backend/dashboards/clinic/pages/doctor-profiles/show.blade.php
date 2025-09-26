@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.doctor-profiles.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back') }}
                    </a>
                    @if($profile->canBeEdited())
                        <a href="{{ route('clinic.doctor-profiles.edit', $profile->id) }}" class="btn btn-primary">
                            <i class="fa fa-edit"></i> {{ __('Edit Profile') }}
                        </a>
                    @endif
                </div>
                <h4 class="page-title">{{ __('Doctor Profile Details') }}</h4>
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
                                @if($profile->locked_for_edit && $profile->status === 'approved')
                                    <br><span class="badge bg-danger mt-1"><i class="fa fa-lock"></i> {{ __('Locked for Edit') }}</span>
                                @endif
                            </div>

                            @if(in_array($profile->status, ['draft', 'rejected']))
                                <button onclick="submitProfile({{ $profile->id }})" class="btn btn-success">
                                    <i class="fa fa-paper-plane"></i> {{ __('Submit for Review') }}
                                </button>
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
                                                    <th>{{ __('Created At') }}:</th>
                                                    <td>{{ $profile->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            @if($profile->rejection_reason)
                                <div class="alert alert-danger">
                                    <h6 class="alert-heading">{{ __('Rejection Reason') }}:</h6>
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
@endsection

@push('scripts')
<script>
function submitProfile(id) {
    Swal.fire({
        title: 'Submit Profile for Review?',
        text: "Your profile will be submitted to admins for review and approval.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, submit it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.doctor-profiles.submit", ":id") }}'.replace(':id', id),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire('Submitted!', response.message, 'success')
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
