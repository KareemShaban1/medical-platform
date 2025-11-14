@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Clinic Info'))

@push('styles')
<style>
.service-item, .working-hour-item {
	transition: all 0.3s ease;
}
.service-item:hover {
	box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.remove-service {
	transition: all 0.3s ease;
}
.remove-service:hover {
	transform: scale(1.1);
}
#clinicImagesCarousel .carousel-control-prev,
#clinicImagesCarousel .carousel-control-next {
	width: 40px;
	height: 40px;
	background: rgba(0, 0, 0, 0.5);
	border-radius: 50%;
	top: 50%;
	transform: translateY(-50%);
}
#clinicImagesCarousel .carousel-control-prev {
	left: 10px;
}
#clinicImagesCarousel .carousel-control-next {
	right: 10px;
}
#clinicImagesCarousel .carousel-indicators {
	bottom: 10px;
}
#clinicImagesCarousel .carousel-indicators button {
	width: 10px;
	height: 10px;
	border-radius: 50%;
	background-color: rgba(255, 255, 255, 0.7);
}
#clinicImagesCarousel .carousel-indicators button.active {
	background-color: white;
}
</style>
@endpush

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
						<li class="breadcrumb-item active">{{ __('Clinic Info') }}
						</li>
					</ol>
				</div>
				<h4 class="page-title">{{ __('Clinic Info') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
                    <form id="clinic-info-form" enctype="multipart/form-data">
						@csrf
						@method('PUT')
						<div class="mb-3">
							<label for="name"
								class="form-label">{{ __('Name') }}
								<span
									class="text-danger">*</span></label>
							<input type="text" id="name" name="name"
								class="form-control"
								value="{{ old('name', $clinic->name) }}"
								required>
						</div>
						<div class="mb-3">
							<label for="phone"
								class="form-label">{{ __('Phone') }}
								<span
									class="text-danger">*</span></label>
							<input type="text" id="phone" name="phone"
								class="form-control"
								value="{{ old('phone', $clinic->phone) }}"
								required>
						</div>
						<div class="mb-3">
							<label for="clinic_email"
								class="form-label">{{ __('Clinic Email') }}</label>
							<input type="email" id="clinic_email" name="clinic_email"
								class="form-control"
								value="{{ old('clinic_email', $clinic->clinic_email) }}">
						</div>
						<div class="mb-3">
							<label for="clinic_website"
								class="form-label">{{ __('Clinic Website') }}</label>
							<input type="url" id="clinic_website" name="clinic_website"
								class="form-control"
								placeholder="https://example.com"
								value="{{ old('clinic_website', $clinic->clinic_website) }}">
						</div>
						<!-- governorate , city , area -->
						<div class="row mb-4">
							<div
								class="col-md-4">
								<div
									class="form-group">
									<label
										class="form-label required">{{ __('Governorate') }}</label>
									<select name="governorate_id"
										id="governorate_id"
										class="form-control p-0 @error('governorate_id') is-invalid @enderror"
										required>
										<option
											value="">
											{{ __('Select Governorate') }}
										</option>
									</select>
									<div class="validation-feedback"
										id="governorate_id_feedback">
									</div>
									@error('governorate_id')
									<div
										class="validation-feedback invalid">
										<i
											class="fa fa-exclamation-circle"></i>
										{{ $message }}
									</div>
									@enderror
								</div>
							</div>
							<div
								class="col-md-4">
								<div
									class="form-group">
									<label
										class="form-label required">{{ __('City') }}</label>
									<select name="city_id"
										id="city_id"
										class="form-control p-0 @error('city_id') is-invalid @enderror"
										required
										disabled>
										<option
											value="">
											{{ __('Select City') }}
										</option>
									</select>
									<div class="validation-feedback"
										id="city_id_feedback">
									</div>
									@error('city_id')
									<div
										class="validation-feedback invalid">
										<i
											class="fa fa-exclamation-circle"></i>
										{{ $message }}
									</div>
									@enderror
								</div>
							</div>

							<div
								class="col-md-4">
								<div
									class="form-group">
									<label
										class="form-label required">{{ __('Area') }}</label>
									<select name="area_id"
										id="area_id"
										class="form-control p-0 @error('area_id') is-invalid @enderror"
										required
										disabled>
										<option
											value="">
											{{ __('Select Area') }}
										</option>
									</select>
									<div class="validation-feedback"
										id="area_id_feedback">
									</div>
									@error('area_id')
									<div
										class="validation-feedback invalid">
										<i
											class="fa fa-exclamation-circle"></i>
										{{ $message }}
									</div>
									@enderror
								</div>
							</div>
						</div>
                    <div class="mb-3">
                        <label for="address"
                            class="form-label">{{ __('Address') }}
                            <span
                                class="text-danger">*</span></label>
                        <textarea id="address" name="address" rows="3"
                            class="form-control"
                            required>{{ old('address', $clinic->address) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="about"
                            class="form-label">{{ __('About Clinic') }}</label>
                        <textarea id="about" name="about" rows="5"
                            class="form-control"
                            placeholder="{{ __('Tell us about your clinic, mission, and values...') }}">{{ old('about', $clinic->about) }}</textarea>
                    </div>

                    <!-- Services Offered Repeater -->
                    <div class="mb-4">
                        <label class="form-label d-block">{{ __('Services Offered') }}</label>
                        <div id="services-container">
                            @php
                                $services = old('services_offered', $clinic->services_offered ?? []);
                            @endphp
                            @if(count($services) > 0)
                                @foreach($services as $index => $service)
                                <div class="service-item card mb-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="text" name="services_offered[{{ $index }}][name]"
                                                    class="form-control mb-2"
                                                    placeholder="{{ __('Service Name') }}"
                                                    value="{{ $service['name'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-7">
                                                <input type="text" name="services_offered[{{ $index }}][description]"
                                                    class="form-control mb-2"
                                                    placeholder="{{ __('Service Description (optional)') }}"
                                                    value="{{ $service['description'] ?? '' }}">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-service">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" id="add-service" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-plus"></i> {{ __('Add Service') }}
                        </button>
                    </div>

                    <!-- Working Hours Repeater -->
                    <div class="mb-4">
                        <label class="form-label d-block">{{ __('Working Hours') }}</label>
                        <div id="working-hours-container">
                            @php
                                $workingHours = old('working_hours', $clinic->working_hours ?? []);
                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

                                // Create associative array for easier access
                                $hoursMap = [];
                                foreach($workingHours as $wh) {
                                    $hoursMap[$wh['day']] = $wh;
                                }
                            @endphp
                            @foreach($days as $index => $day)
                                @php
                                    $dayData = $hoursMap[$day] ?? ['day' => $day, 'is_open' => false, 'open_time' => '09:00', 'close_time' => '17:00'];
                                @endphp
                                <div class="working-hour-item card mb-2">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <strong>{{ __(ucfirst($day)) }}</strong>
                                                <input type="hidden" name="working_hours[{{ $index }}][day]" value="{{ $day }}">
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input working-hour-toggle" type="checkbox"
                                                        name="working_hours[{{ $index }}][is_open]"
                                                        value="1"
                                                        id="is_open_{{ $day }}"
                                                        {{ ($dayData['is_open'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_open_{{ $day }}">
                                                        {{ __('Open') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">{{ __('Open Time') }}</label>
                                                <input type="time" name="working_hours[{{ $index }}][open_time]"
                                                    class="form-control form-control-sm open-time-input"
                                                    value="{{ $dayData['open_time'] ?? '09:00' }}"
                                                    {{ !($dayData['is_open'] ?? false) ? 'disabled' : '' }}>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">{{ __('Close Time') }}</label>
                                                <input type="time" name="working_hours[{{ $index }}][close_time]"
                                                    class="form-control form-control-sm close-time-input"
                                                    value="{{ $dayData['close_time'] ?? '17:00' }}"
                                                    {{ !($dayData['is_open'] ?? false) ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Emergency Services -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="has_emergency"
                                id="has_emergency" value="1"
                                {{ old('has_emergency', $clinic->has_emergency) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_emergency">
                                <i class="fas fa-ambulance text-danger me-1"></i>
                                {{ __('Provide Emergency Services') }}
                            </label>
                        </div>
                    </div>

                    <!-- Images management -->
                    <div class="mb-4">
                        <label class="form-label d-block">{{ __('Images') }}</label>

                        @php
                            $clinicImages = $clinic->getMedia('clinic_images');
                            $hasImages = $clinicImages->count() > 0;
                            $defaultImage = 'https://ui-avatars.com/api/?name=' . urlencode($clinic->name) . '&size=256&background=0D8ABC&color=fff';
                        @endphp

                        @if(!$hasImages)
                            <!-- Default Avatar Preview -->
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('No images uploaded yet. A default avatar will be used based on your clinic name.') }}
                            </div>
                            <div class="mb-3 text-center">
                                <img src="{{ $defaultImage }}" class="img-fluid rounded border" style="max-width: 256px;" alt="{{ $clinic->name }}">
                                <p class="text-muted small mt-2">{{ __('Default clinic avatar') }}</p>
                            </div>
                        @else
                            <!-- Image Slider for Multiple Images -->
                            @if($clinicImages->count() > 1)
                                <div id="clinicImagesCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        @foreach($clinicImages as $index => $media)
                                            <button type="button" data-bs-target="#clinicImagesCarousel" data-bs-slide-to="{{ $index }}"
                                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-label="Slide {{ $index + 1 }}"></button>
                                        @endforeach
                                    </div>
                                    <div class="carousel-inner rounded">
                                        @foreach($clinicImages as $index => $media)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ $media->getUrl() }}" class="d-block w-100" style="height:400px;object-fit:cover;" alt="Clinic Image {{ $index + 1 }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#clinicImagesCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#clinicImagesCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            @else
                                <!-- Single Image Display -->
                                <div class="mb-3 text-center">
                                    <img src="{{ $clinicImages->first()->getUrl() }}" class="img-fluid rounded border" style="max-height:400px;object-fit:cover;" alt="{{ $clinic->name }}">
                                </div>
                            @endif

                            <!-- Image Grid for Management -->
                            <div class="row g-2 mb-3" id="current-images">
                                @foreach($clinicImages as $media)
                                    <div class="col-md-3 col-6 position-relative">
                                        <img src="{{ $media->getUrl() }}" class="img-fluid rounded border" style="height:130px;object-fit:cover;width:100%">
                                        <div class="form-check mt-1">
                                            <input class="form-check-input remove-media-checkbox" type="checkbox" value="{{ $media->id }}" id="remove-media-{{ $media->id }}">
                                            <label class="form-check-label small" for="remove-media-{{ $media->id }}">
                                                {{ __('Remove') }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
                        <div class="form-text">
                            @if($hasImages)
                                {{ __('Upload additional images or manage existing ones above.') }}
                            @else
                                {{ __('Upload clinic images to replace the default avatar.') }}
                            @endif
                        </div>
                    </div>

						<div class="text-end">
							<button type="submit"
								class="btn btn-primary">{{ __('Save Changes') }}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	$(function() {
		let serviceIndex = {{ count(old('services_offered', $clinic->services_offered ?? [])) }};

		// Add Service
		$('#add-service').on('click', function() {
			const serviceHtml = `
				<div class="service-item card mb-2">
					<div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<input type="text" name="services_offered[${serviceIndex}][name]"
									class="form-control mb-2"
									placeholder="{{ __('Service Name') }}" required>
							</div>
							<div class="col-md-7">
								<input type="text" name="services_offered[${serviceIndex}][description]"
									class="form-control mb-2"
									placeholder="{{ __('Service Description (optional)') }}">
							</div>
							<div class="col-md-1">
								<button type="button" class="btn btn-danger btn-sm remove-service">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</div>
					</div>
				</div>
			`;
			$('#services-container').append(serviceHtml);
			serviceIndex++;
		});

		// Remove Service
		$(document).on('click', '.remove-service', function() {
			$(this).closest('.service-item').remove();
		});

		// Toggle working hours inputs
		$(document).on('change', '.working-hour-toggle', function() {
			const card = $(this).closest('.working-hour-item');
			const isOpen = $(this).is(':checked');
			card.find('.open-time-input, .close-time-input').prop('disabled', !isOpen);
		});

		// Form submission
		$('#clinic-info-form').on('submit', function(e) {
			e.preventDefault();
            var form = document.getElementById('clinic-info-form');
            var formData = new FormData(form);
            $('.remove-media-checkbox:checked').each(function(){
                formData.append('remove_media_ids[]', $(this).val());
            });
            $.ajax({
                url: "{{ route('clinic.settings.clinic-info.update') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res && res.success) {
                        Swal.fire('{{ __("Success!") }}', res.message, 'success').then(function(){
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('{{ __("Error!") }}', (res && res.message) || '{{ __("Something went wrong") }}', 'error');
                    }
                },
				error: function(xhr) {
					let errors =
						xhr
						.responseJSON
						?.errors;
					if (
						errors
					) {
						let errorMessage =
							Object
							.values(
								errors
							)
							.flat()
							.join(
								'\n'
							);
						Swal.fire('{{ __("Validation Error!") }}',
							errorMessage,
							'error'
						);
					} else {
						Swal.fire('{{ __("Error!") }}',
							xhr
							.responseJSON
							?.message ||
							'{{ __("Something went wrong!") }}',
							'error'
						);
					}
				}
			});
		});
	});
</script>
<!-- governorate , city , area scripts -->
<script>
$(document).ready(function () {

    const clinicGovernorateId = "{{ $clinic->governorate_id }}";
    const clinicCityId = "{{ $clinic->city_id }}";
    const clinicAreaId = "{{ $clinic->area_id }}";

    // Load all governorates first
    loadGovernorates();

    function loadGovernorates() {
        $.ajax({
            url: '{{ route("clinic.governorates") }}',
            type: 'GET',
            success: function (response) {
                const select = $('#governorate_id');
                select.empty();
                select.append("<option value=''>{{ __('Select Governorate') }}</option>");

                response.forEach(function (governorate) {
                    select.append(`<option value="${governorate.id}">${governorate.name}</option>`);
                });

                // If clinic already has a governorate, set it
                if (clinicGovernorateId) {
                    select.val(clinicGovernorateId).trigger('change');
                }
            },
            error: function () {
                toastr.error('Failed to load governorates. Please refresh the page.');
            }
        });
    }

    function loadCities(governorateId) {
        if (!governorateId) {
            $('#city_id').empty().append("<option value=''>{{ __('Select City') }}</option>").prop('disabled', true);
            $('#area_id').empty().append("<option value=''>{{ __('Select Area') }}</option>").prop('disabled', true);
            return;
        }

        $.ajax({
            url: '{{ route("clinic.cities") }}',
            type: 'GET',
            data: { governorate_id: governorateId },
            success: function (response) {
                const select = $('#city_id');
                select.empty();
                select.append("<option value=''>{{ __('Select City') }}</option>");

                response.forEach(function (city) {
                    select.append(`<option value="${city.id}">${city.name}</option>`);
                });

                select.prop('disabled', false);

                // If clinic already has a city, set it
                if (clinicCityId && governorateId == clinicGovernorateId) {
                    select.val(clinicCityId).trigger('change');
                }

                // Reset area dropdown
                $('#area_id').empty().append("<option value=''>{{ __('Select Area') }}</option>").prop('disabled', true);
            },
            error: function () {
                toastr.error('Failed to load cities. Please try again.');
            }
        });
    }

    function loadAreas(cityId) {
        if (!cityId) {
            $('#area_id').empty().append("<option value=''>{{ __('Select Area') }}</option>").prop('disabled', true);
            return;
        }

        $.ajax({
            url: '{{ route("clinic.areas") }}',
            type: 'GET',
            data: { city_id: cityId },
            success: function (response) {
                const select = $('#area_id');
                select.empty();
                select.append("<option value=''>{{ __('Select Area') }}</option>");

                response.forEach(function (area) {
                    select.append(`<option value="${area.id}">${area.name}</option>`);
                });

                select.prop('disabled', false);

                // If clinic already has an area, set it
                if (clinicAreaId && cityId == clinicCityId) {
                    select.val(clinicAreaId);
                }
            },
            error: function () {
                toastr.error('Failed to load areas. Please try again.');
            }
        });
    }

    // Event handlers
    $('#governorate_id').on('change', function () {
        const governorateId = $(this).val();
        loadCities(governorateId);
    });

    $('#city_id').on('change', function () {
        const cityId = $(this).val();
        loadAreas(cityId);
    });
});
</script>

@endpush
