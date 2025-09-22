@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.doctor-profiles.show', $profile->id) }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Edit Doctor Profile') }}</h4>
            </div>
        </div>
    </div>

    @if($profile->rejection_reason)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h6 class="alert-heading">{{ __('Profile was rejected for the following reason:') }}</h6>
                    <p class="mb-0">{{ $profile->rejection_reason }}</p>
                </div>
            </div>
        </div>
    @endif

    <form id="profileForm" action="{{ route('clinic.doctor-profiles.update', $profile->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Basic Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $profile->name }}" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $profile->email }}" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $profile->phone }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="years_experience" class="form-label">{{ __('Years of Experience') }}</label>
                                <input type="number" class="form-control" id="years_experience" name="years_experience" value="{{ $profile->years_experience }}" min="0" max="50">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="bio" class="form-label">{{ __('Bio/Summary') }}</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4" maxlength="2000">{{ $profile->bio }}</textarea>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">{{ __('Maximum 2000 characters') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specialties and Services -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Specialties & Services') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="specialties" class="form-label">{{ __('Specialties') }}</label>
                                <div id="specialties-container">
                                    @if($profile->specialties && count($profile->specialties) > 0)
                                        @foreach($profile->specialties as $index => $specialty)
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="specialties[]" value="{{ $specialty }}" placeholder="{{ __('Enter specialty') }}">
                                                @if($index === 0)
                                                    <button type="button" class="btn btn-outline-success" onclick="addSpecialty()">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="specialties[]" placeholder="{{ __('Enter specialty') }}">
                                            <button type="button" class="btn btn-outline-success" onclick="addSpecialty()">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="services_offered" class="form-label">{{ __('Services Offered') }}</label>
                                <div id="services-container">
                                    @if($profile->services_offered && count($profile->services_offered) > 0)
                                        @foreach($profile->services_offered as $index => $service)
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="services_offered[]" value="{{ $service }}" placeholder="{{ __('Enter service') }}">
                                                @if($index === 0)
                                                    <button type="button" class="btn btn-outline-success" onclick="addService()">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="services_offered[]" placeholder="{{ __('Enter service') }}">
                                            <button type="button" class="btn btn-outline-success" onclick="addService()">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Education -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Education') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="education-container">
                            @if($profile->education && count($profile->education) > 0)
                                @foreach($profile->education as $index => $edu)
                                    <div class="row education-entry mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="education[{{ $index }}][degree]" value="{{ $edu['degree'] ?? '' }}" placeholder="{{ __('Degree') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="education[{{ $index }}][institution]" value="{{ $edu['institution'] ?? '' }}" placeholder="{{ __('Institution') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" name="education[{{ $index }}][year]" value="{{ $edu['year'] ?? '' }}" placeholder="{{ __('Year') }}" min="1950" max="{{ date('Y') }}">
                                        </div>
                                        <div class="col-md-1">
                                            @if($index === 0)
                                                <button type="button" class="btn btn-outline-success" onclick="addEducation()">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-danger" onclick="removeField(this, true)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row education-entry mb-3">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="education[0][degree]" placeholder="{{ __('Degree') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="education[0][institution]" placeholder="{{ __('Institution') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" name="education[0][year]" placeholder="{{ __('Year') }}" min="1950" max="{{ date('Y') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-success" onclick="addEducation()">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Experience -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Experience') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="experience-container">
                            @if($profile->experience && count($profile->experience) > 0)
                                @foreach($profile->experience as $index => $exp)
                                    <div class="experience-entry mb-3 p-3 border rounded">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <input type="text" class="form-control" name="experience[{{ $index }}][position]" value="{{ $exp['position'] ?? '' }}" placeholder="{{ __('Position') }}">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <input type="text" class="form-control" name="experience[{{ $index }}][company]" value="{{ $exp['company'] ?? '' }}" placeholder="{{ __('Company/Hospital') }}">
                                            </div>
                                            <div class="col-md-5 mb-2">
                                                <input type="number" class="form-control" name="experience[{{ $index }}][start_year]" value="{{ $exp['start_year'] ?? '' }}" placeholder="{{ __('Start Year') }}" min="1950" max="{{ date('Y') }}">
                                            </div>
                                            <div class="col-md-5 mb-2">
                                                <input type="number" class="form-control" name="experience[{{ $index }}][end_year]" value="{{ $exp['end_year'] ?? '' }}" placeholder="{{ __('End Year') }}" min="1950" max="{{ date('Y') }}">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                @if($index === 0)
                                                    <button type="button" class="btn btn-outline-success" onclick="addExperience()">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this, true)">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                <textarea class="form-control" name="experience[{{ $index }}][description]" placeholder="{{ __('Description') }}" rows="2" maxlength="500">{{ $exp['description'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="experience-entry mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <input type="text" class="form-control" name="experience[0][position]" placeholder="{{ __('Position') }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <input type="text" class="form-control" name="experience[0][company]" placeholder="{{ __('Company/Hospital') }}">
                                        </div>
                                        <div class="col-md-5 mb-2">
                                            <input type="number" class="form-control" name="experience[0][start_year]" placeholder="{{ __('Start Year') }}" min="1950" max="{{ date('Y') }}">
                                        </div>
                                        <div class="col-md-5 mb-2">
                                            <input type="number" class="form-control" name="experience[0][end_year]" placeholder="{{ __('End Year') }}" min="1950" max="{{ date('Y') }}">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <button type="button" class="btn btn-outline-success" onclick="addExperience()">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <textarea class="form-control" name="experience[0][description]" placeholder="{{ __('Description') }}" rows="2" maxlength="500"></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Photo and Links -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Profile Photo') }}</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($profile->profile_photo_url)
                                <img id="photoPreview" src="{{ $profile->profile_photo_url }}" alt="Profile Preview"
                                     style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%;"
                                     class="mb-3">
                                <div id="photoPlaceholder" class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                     style="width: 200px; height: 200px; color: white; font-size: 48px; display: none;">
                                    <i class="mdi mdi-account"></i>
                                </div>
                            @else
                                <img id="photoPreview" src="#" alt="Profile Preview"
                                     style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; display: none;"
                                     class="mb-3">
                                <div id="photoPlaceholder" class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                     style="width: 200px; height: 200px; color: white; font-size: 48px;">
                                    <i class="mdi mdi-account"></i>
                                </div>
                            @endif
                        </div>
                        <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">{{ __('Upload new photo to replace current one (max 2MB)') }}</small>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Social Media Links') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="linkedin_link" class="form-label">
                                <i class="fab fa-linkedin text-primary"></i> {{ __('LinkedIn') }}
                            </label>
                            <input type="url" class="form-control" id="linkedin_link" name="linkedin_link" value="{{ $profile->linkedin_link }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="twitter_link" class="form-label">
                                <i class="fab fa-twitter text-info"></i> {{ __('Twitter') }}
                            </label>
                            <input type="url" class="form-control" id="twitter_link" name="twitter_link" value="{{ $profile->twitter_link }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="facebook_link" class="form-label">
                                <i class="fab fa-facebook text-primary"></i> {{ __('Facebook') }}
                            </label>
                            <input type="url" class="form-control" id="facebook_link" name="facebook_link" value="{{ $profile->facebook_link }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="instagram_link" class="form-label">
                                <i class="fab fa-instagram text-danger"></i> {{ __('Instagram') }}
                            </label>
                            <input type="url" class="form-control" id="instagram_link" name="instagram_link" value="{{ $profile->instagram_link }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Research Links') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="research-container">
                            @if($profile->research_links && count($profile->research_links) > 0)
                                @foreach($profile->research_links as $index => $link)
                                    <div class="input-group mb-2">
                                        <input type="url" class="form-control" name="research_links[]" value="{{ $link }}" placeholder="{{ __('Research paper URL') }}">
                                        @if($index === 0)
                                            <button type="button" class="btn btn-outline-success" onclick="addResearchLink()">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2">
                                    <input type="url" class="form-control" name="research_links[]" placeholder="{{ __('Research paper URL') }}">
                                    <button type="button" class="btn btn-outline-success" onclick="addResearchLink()">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-save"></i> {{ __('Update Profile') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Profile photo preview
$('#profile_photo').on('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#photoPreview').attr('src', e.target.result).show();
            $('#photoPlaceholder').hide();
        };
        reader.readAsDataURL(file);
    }
});

// Dynamic field functions (same as create form)
function addSpecialty() {
    const container = $('#specialties-container');
    const newField = `
        <div class="input-group mb-2">
            <input type="text" class="form-control" name="specialties[]" placeholder="{{ __('Enter specialty') }}">
            <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">
                <i class="fa fa-times"></i>
            </button>
        </div>
    `;
    container.append(newField);
}

function addService() {
    const container = $('#services-container');
    const newField = `
        <div class="input-group mb-2">
            <input type="text" class="form-control" name="services_offered[]" placeholder="{{ __('Enter service') }}">
            <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">
                <i class="fa fa-times"></i>
            </button>
        </div>
    `;
    container.append(newField);
}

function addResearchLink() {
    const container = $('#research-container');
    const newField = `
        <div class="input-group mb-2">
            <input type="url" class="form-control" name="research_links[]" placeholder="{{ __('Research paper URL') }}">
            <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">
                <i class="fa fa-times"></i>
            </button>
        </div>
    `;
    container.append(newField);
}

function addEducation() {
    const container = $('#education-container');
    const index = container.children().length;
    const newField = `
        <div class="row education-entry mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="education[${index}][degree]" placeholder="{{ __('Degree') }}">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="education[${index}][institution]" placeholder="{{ __('Institution') }}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="education[${index}][year]" placeholder="{{ __('Year') }}" min="1950" max="{{ date('Y') }}">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger" onclick="removeField(this, true)">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    `;
    container.append(newField);
}

function addExperience() {
    const container = $('#experience-container');
    const index = container.children().length;
    const newField = `
        <div class="experience-entry mb-3 p-3 border rounded">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" class="form-control" name="experience[${index}][position]" placeholder="{{ __('Position') }}">
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" class="form-control" name="experience[${index}][company]" placeholder="{{ __('Company/Hospital') }}">
                </div>
                <div class="col-md-5 mb-2">
                    <input type="number" class="form-control" name="experience[${index}][start_year]" placeholder="{{ __('Start Year') }}" min="1950" max="{{ date('Y') }}">
                </div>
                <div class="col-md-5 mb-2">
                    <input type="number" class="form-control" name="experience[${index}][end_year]" placeholder="{{ __('End Year') }}" min="1950" max="{{ date('Y') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this, true)">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="col-12">
                    <textarea class="form-control" name="experience[${index}][description]" placeholder="{{ __('Description') }}" rows="2" maxlength="500"></textarea>
                </div>
            </div>
        </div>
    `;
    container.append(newField);
}

function removeField(button, isParent = false) {
    if (isParent) {
        $(button).closest('.education-entry, .experience-entry').remove();
    } else {
        $(button).closest('.input-group').remove();
    }
}

// Form submission
$('#profileForm').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.fire('Success', response.message, 'success')
                .then(() => window.location.href = '{{ route("clinic.doctor-profiles.show", $profile->id) }}');
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors || {};
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                Object.keys(errors).forEach(function(key) {
                    let $input = $('[name="' + key + '"]');
                    if ($input.length) {
                        $input.addClass('is-invalid');
                        $input.next('.invalid-feedback').text(errors[key][0]);
                    }
                });

                Swal.fire('Validation Error', 'Please check the form for errors.', 'error');
            } else {
                Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
            }
        }
    });
});
</script>
@endpush
