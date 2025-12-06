@extends('frontend.layouts.app')

@section('title', __('Edit Doctor Profile'))

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('Edit Doctor Profile') }}</h1>
                    <p class="text-gray-600 mt-2">{{ __('Update your professional information') }}</p>
                </div>
                <a href="{{ route('doctor.profile.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        @if($profile->rejection_reason)
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">{{ __('Profile Rejection Notice') }}</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p><strong>{{ __('Rejection Reason') }}:</strong> {{ $profile->rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <form id="profileForm" action="{{ route('doctor.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">{{ __('Basic Information') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Full Name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', $profile->name) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Email') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email', $profile->email) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone') }}</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label for="years_experience" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Years of Experience') }}</label>
                                <input type="number" id="years_experience" name="years_experience" value="{{ old('years_experience', $profile->years_experience) }}" min="0" max="50"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label for="speciality_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Main Speciality') }}</label>
                                <select id="speciality_id" name="speciality_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">{{ __('Select Speciality') }}</option>
                                    @foreach($specialities as $spec)
                                        <option value="{{ $spec->id }}" {{ old('speciality_id', $profile->speciality_id) == $spec->id ? 'selected' : '' }}>
                                            {{ $spec->name_en }} - {{ $spec->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            </div>

                            <div class="md:col-span-2">
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Bio/Summary') }}</label>
                                <textarea id="bio" name="bio" rows="4" maxlength="2000"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('bio', $profile->bio) }}</textarea>
                                <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                                <small class="text-gray-500">{{ __('Maximum 2000 characters') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Specialties and Services -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">{{ __('Specialties & Services') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Specialties') }}</label>
                                <div id="specialties-container">
                                    @if($profile->specialties && count($profile->specialties) > 0)
                                        @foreach($profile->specialties as $specialty)
                                            <div class="flex gap-2 mb-2">
                                                <input type="text" name="specialties[]" value="{{ $specialty }}" placeholder="{{ __('Enter specialty') }}"
                                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <button type="button" onclick="removeField(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="flex gap-2 mb-2">
                                        <input type="text" name="specialties[]" placeholder="{{ __('Enter specialty') }}"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <button type="button" onclick="addSpecialty()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Services Offered') }}</label>
                                <div id="services-container">
                                    @if($profile->services_offered && count($profile->services_offered) > 0)
                                        @foreach($profile->services_offered as $service)
                                            <div class="flex gap-2 mb-2">
                                                <input type="text" name="services_offered[]" value="{{ $service }}" placeholder="{{ __('Enter service') }}"
                                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <button type="button" onclick="removeField(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="flex gap-2 mb-2">
                                        <input type="text" name="services_offered[]" placeholder="{{ __('Enter service') }}"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <button type="button" onclick="addService()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Education -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">{{ __('Education') }}</h3>
                        <div id="education-container">
                            @if($profile->education && count($profile->education) > 0)
                                @foreach($profile->education as $index => $edu)
                                    <div class="grid grid-cols-12 gap-2 mb-3 education-entry">
                                        <div class="col-span-4">
                                            <input type="text" name="education[{{ $index }}][degree]" value="{{ $edu['degree'] ?? '' }}" placeholder="{{ __('Degree') }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div class="col-span-4">
                                            <input type="text" name="education[{{ $index }}][institution]" value="{{ $edu['institution'] ?? '' }}" placeholder="{{ __('Institution') }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div class="col-span-3">
                                            <input type="number" name="education[{{ $index }}][year]" value="{{ $edu['year'] ?? '' }}" placeholder="{{ __('Year') }}" min="1950" max="{{ date('Y') }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div class="col-span-1">
                                            <button type="button" onclick="removeField(this, true)" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            <div class="grid grid-cols-12 gap-2 mb-3 education-entry">
                                <div class="col-span-4">
                                    <input type="text" name="education[{{ $profile->education ? count($profile->education) : 0 }}][degree]" placeholder="{{ __('Degree') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="col-span-4">
                                    <input type="text" name="education[{{ $profile->education ? count($profile->education) : 0 }}][institution]" placeholder="{{ __('Institution') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="col-span-3">
                                    <input type="number" name="education[{{ $profile->education ? count($profile->education) : 0 }}][year]" placeholder="{{ __('Year') }}" min="1950" max="{{ date('Y') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="col-span-1">
                                    <button type="button" onclick="addEducation()" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Experience -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">{{ __('Experience') }}</h3>
                        <div id="experience-container">
                            @if($profile->experience && count($profile->experience) > 0)
                                @foreach($profile->experience as $index => $exp)
                                    <div class="experience-entry mb-3 p-4 border border-gray-200 rounded-lg">
                                        <div class="grid grid-cols-12 gap-2 mb-2">
                                            <div class="col-span-6">
                                                <input type="text" name="experience[{{ $index }}][position]" value="{{ $exp['position'] ?? '' }}" placeholder="{{ __('Position') }}"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            </div>
                                            <div class="col-span-6">
                                                <input type="text" name="experience[{{ $index }}][company]" value="{{ $exp['company'] ?? '' }}" placeholder="{{ __('Company/Hospital') }}"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            </div>
                                            <div class="col-span-5">
                                                <input type="number" name="experience[{{ $index }}][start_year]" value="{{ $exp['start_year'] ?? '' }}" placeholder="{{ __('Start Year') }}" min="1950" max="{{ date('Y') }}"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            </div>
                                            <div class="col-span-5">
                                                <input type="number" name="experience[{{ $index }}][end_year]" value="{{ $exp['end_year'] ?? '' }}" placeholder="{{ __('End Year') }}" min="1950" max="{{ date('Y') }}"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            </div>
                                            <div class="col-span-2">
                                                <button type="button" onclick="removeField(this, true)" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="col-span-12">
                                                <textarea name="experience[{{ $index }}][description]" placeholder="{{ __('Description') }}" rows="2" maxlength="500"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $exp['description'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            <div class="experience-entry mb-3 p-4 border border-gray-200 rounded-lg">
                                <div class="grid grid-cols-12 gap-2 mb-2">
                                    <div class="col-span-6">
                                        <input type="text" name="experience[{{ $profile->experience ? count($profile->experience) : 0 }}][position]" placeholder="{{ __('Position') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div class="col-span-6">
                                        <input type="text" name="experience[{{ $profile->experience ? count($profile->experience) : 0 }}][company]" placeholder="{{ __('Company/Hospital') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div class="col-span-5">
                                        <input type="number" name="experience[{{ $profile->experience ? count($profile->experience) : 0 }}][start_year]" placeholder="{{ __('Start Year') }}" min="1950" max="{{ date('Y') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div class="col-span-5">
                                        <input type="number" name="experience[{{ $profile->experience ? count($profile->experience) : 0 }}][end_year]" placeholder="{{ __('End Year') }}" min="1950" max="{{ date('Y') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div class="col-span-2">
                                        <button type="button" onclick="addExperience()" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="col-span-12">
                                        <textarea name="experience[{{ $profile->experience ? count($profile->experience) : 0 }}][description]" placeholder="{{ __('Description') }}" rows="2" maxlength="500"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Profile Photo -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">{{ __('Profile Photo') }}</h3>
                        <div class="text-center">
                            <div class="mb-4">
                                @if($profile->profile_photo_url)
                                    <img id="photoPreview" src="{{ $profile->profile_photo_url }}" alt="Profile Preview"
                                        class="w-48 h-48 object-cover rounded-full mx-auto mb-3">
                                    <div id="photoPlaceholder" class="hidden"></div>
                                @else
                                    <img id="photoPreview" src="#" alt="Profile Preview"
                                        class="w-48 h-48 object-cover rounded-full mx-auto mb-3 hidden">
                                    <div id="photoPlaceholder" class="w-48 h-48 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-user text-6xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                            <small class="text-gray-500 block mt-2">{{ __('Upload profile photo (max 2MB)') }}</small>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">{{ __('Social Media Links') }}</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="linkedin_link" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-linkedin text-blue-600"></i> {{ __('LinkedIn') }}
                                </label>
                                <input type="url" id="linkedin_link" name="linkedin_link" value="{{ old('linkedin_link', $profile->linkedin_link) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="twitter_link" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" class="inline-block align-text-bottom"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/></svg> {{ __('X') }}
                                </label>
                                <input type="url" id="twitter_link" name="twitter_link" value="{{ old('twitter_link', $profile->twitter_link) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="facebook_link" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-facebook text-blue-700"></i> {{ __('Facebook') }}
                                </label>
                                <input type="url" id="facebook_link" name="facebook_link" value="{{ old('facebook_link', $profile->facebook_link) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="instagram_link" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-instagram text-pink-600"></i> {{ __('Instagram') }}
                                </label>
                                <input type="url" id="instagram_link" name="instagram_link" value="{{ old('instagram_link', $profile->instagram_link) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Research Links -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">{{ __('Research Links') }}</h3>
                        <div id="research-container">
                            @if($profile->research_links && count($profile->research_links) > 0)
                                @foreach($profile->research_links as $link)
                                    <div class="flex gap-2 mb-2">
                                        <input type="url" name="research_links[]" value="{{ $link }}" placeholder="{{ __('Research paper URL') }}"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <button type="button" onclick="removeField(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                            <div class="flex gap-2 mb-2">
                                <input type="url" name="research_links[]" placeholder="{{ __('Research paper URL') }}"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button type="button" onclick="addResearchLink()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                            <i class="fas fa-save"></i> {{ __('Update Profile') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Profile photo preview
$('#profile_photo').on('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#photoPreview').attr('src', e.target.result).removeClass('hidden');
            $('#photoPlaceholder').addClass('hidden');
        };
        reader.readAsDataURL(file);
    }
});

// Dynamic field functions (same as create view)
function addSpecialty() {
    const container = $('#specialties-container');
    const newField = `
        <div class="flex gap-2 mb-2">
            <input type="text" name="specialties[]" placeholder="{{ __('Enter specialty') }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="button" onclick="removeField(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.append(newField);
}

function addService() {
    const container = $('#services-container');
    const newField = `
        <div class="flex gap-2 mb-2">
            <input type="text" name="services_offered[]" placeholder="{{ __('Enter service') }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="button" onclick="removeField(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.append(newField);
}

function addResearchLink() {
    const container = $('#research-container');
    const newField = `
        <div class="flex gap-2 mb-2">
            <input type="url" name="research_links[]" placeholder="{{ __('Research paper URL') }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="button" onclick="removeField(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.append(newField);
}

function addEducation() {
    const container = $('#education-container');
    const index = container.children('.education-entry').length;
    const newField = `
        <div class="grid grid-cols-12 gap-2 mb-3 education-entry">
            <div class="col-span-4">
                <input type="text" name="education[${index}][degree]" placeholder="{{ __('Degree') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="col-span-4">
                <input type="text" name="education[${index}][institution]" placeholder="{{ __('Institution') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="col-span-3">
                <input type="number" name="education[${index}][year]" placeholder="{{ __('Year') }}" min="1950" max="{{ date('Y') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="col-span-1">
                <button type="button" onclick="removeField(this, true)" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    container.append(newField);
}

function addExperience() {
    const container = $('#experience-container');
    const index = container.children('.experience-entry').length;
    const newField = `
        <div class="experience-entry mb-3 p-4 border border-gray-200 rounded-lg">
            <div class="grid grid-cols-12 gap-2 mb-2">
                <div class="col-span-6">
                    <input type="text" name="experience[${index}][position]" placeholder="{{ __('Position') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="col-span-6">
                    <input type="text" name="experience[${index}][company]" placeholder="{{ __('Company/Hospital') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="col-span-5">
                    <input type="number" name="experience[${index}][start_year]" placeholder="{{ __('Start Year') }}" min="1950" max="{{ date('Y') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="col-span-5">
                    <input type="number" name="experience[${index}][end_year]" placeholder="{{ __('End Year') }}" min="1950" max="{{ date('Y') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="col-span-2">
                    <button type="button" onclick="removeField(this, true)" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="col-span-12">
                    <textarea name="experience[${index}][description]" placeholder="{{ __('Description') }}" rows="2" maxlength="500"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
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
        $(button).closest('.flex').remove();
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
                .then(() => window.location.href = '{{ route("doctor.profile.index") }}');
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors || {};
                $('.is-invalid').removeClass('is-invalid border-red-500');
                $('.invalid-feedback').text('');

                Object.keys(errors).forEach(function(key) {
                    let $input = $('[name="' + key + '"]');
                    if ($input.length) {
                        $input.addClass('is-invalid border-red-500');
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
@endsection

