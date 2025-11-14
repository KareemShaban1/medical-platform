@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Clinic Info'))

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
					<form id="clinic-info-form">
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
		$('#clinic-info-form').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this);
			$.ajax({
				url: "{{ route('clinic.settings.clinic-info.update') }}",
				type: 'POST',
				data: $form.serialize(),
				success: function(res) {
					if (res
						.success
					) {
						Swal.fire('{{ __("Success!") }}',
							res
							.message,
							'success'
						);
					} else {
						Swal.fire('{{ __("Error!") }}',
							res
							.message ||
							'{{ __("Something went wrong") }}',
							'error'
						);
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