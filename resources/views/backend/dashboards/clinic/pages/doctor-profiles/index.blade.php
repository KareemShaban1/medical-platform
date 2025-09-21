@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    @if(!$userProfile)
                        <a href="{{ route('clinic.doctor-profiles.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> {{ __('Create Profile') }}
                        </a>
                    @endif
                </div>
                <h4 class="page-title">{{ __('My Doctor Profile') }}</h4>
            </div>
        </div>
    </div>

    @if($userProfile)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 text-center">
                                @if($userProfile->profile_photo_url)
                                    <img src="{{ $userProfile->profile_photo_url }}"
                                         alt="Profile Photo"
                                         class="img-fluid rounded-circle mb-3"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                         style="width: 150px; height: 150px; color: white; font-size: 48px;">
                                        <i class="mdi mdi-account"></i>
                                    </div>
                                @endif

                                <h4>{{ $userProfile->name }}</h4>
                                <p class="text-muted">{{ $userProfile->email }}</p>

                                <div class="mb-3">
                                    {!! $userProfile->status_badge !!}
                                </div>

                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('clinic.doctor-profiles.show', $userProfile->id) }}"
                                       class="btn btn-info btn-sm">
                                        <i class="fa fa-eye"></i> {{ __('View') }}
                                    </a>

                                    @if($userProfile->canBeEdited())
                                        <a href="{{ route('clinic.doctor-profiles.edit', $userProfile->id) }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i> {{ __('Edit') }}
                                        </a>
                                    @endif

                                    @if(in_array($userProfile->status, ['draft', 'rejected']))
                                        <button onclick="submitProfile({{ $userProfile->id }})"
                                                class="btn btn-success btn-sm">
                                            <i class="fa fa-paper-plane"></i> {{ __('Submit') }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-uppercase text-muted">{{ __('Contact Information') }}</h6>
                                        <p><strong>{{ __('Phone') }}:</strong> {{ $userProfile->phone ?: 'N/A' }}</p>
                                        <p><strong>{{ __('Years of Experience') }}:</strong> {{ $userProfile->years_experience ?: 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-uppercase text-muted">{{ __('Status Information') }}</h6>
                                        @if($userProfile->reviewed_at)
                                            <p><strong>{{ __('Reviewed At') }}:</strong> {{ $userProfile->reviewed_at->format('Y-m-d H:i') }}</p>
                                        @endif
                                        @if($userProfile->reviewer)
                                            <p><strong>{{ __('Reviewed By') }}:</strong> {{ $userProfile->reviewer->name }}</p>
                                        @endif
                                        @if($userProfile->rejection_reason)
                                            <div class="alert alert-danger">
                                                <strong>{{ __('Rejection Reason') }}:</strong><br>
                                                {{ $userProfile->rejection_reason }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($userProfile->bio)
                                    <div class="mt-3">
                                        <h6 class="text-uppercase text-muted">{{ __('Bio') }}</h6>
                                        <p>{{ $userProfile->bio }}</p>
                                    </div>
                                @endif

                                @if($userProfile->specialties && count($userProfile->specialties) > 0)
                                    <div class="mt-3">
                                        <h6 class="text-uppercase text-muted">{{ __('Specialties') }}</h6>
                                        @foreach($userProfile->specialties as $specialty)
                                            <span class="badge bg-primary me-1 mb-1">{{ $specialty }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                @if($userProfile->services_offered && count($userProfile->services_offered) > 0)
                                    <div class="mt-3">
                                        <h6 class="text-uppercase text-muted">{{ __('Services Offered') }}</h6>
                                        @foreach($userProfile->services_offered as $service)
                                            <span class="badge bg-info me-1 mb-1">{{ $service }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="mdi mdi-account-plus display-3 text-muted"></i>
                        </div>
                        <h4>{{ __('No Profile Created Yet') }}</h4>
                        <p class="text-muted">{{ __('Create your doctor profile to be visible to patients and showcase your expertise.') }}</p>
                        <a href="{{ route('clinic.doctor-profiles.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> {{ __('Create Your Profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
