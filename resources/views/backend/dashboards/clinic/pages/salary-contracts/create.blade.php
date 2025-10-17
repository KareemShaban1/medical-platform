@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
	<div class="card-header">
		<h4 class="card-title">{{ __('Add Salary Contract') }}</h4>
	</div>
	<div class="card-body p-4">
		<form action="{{ route('clinic.salary-contracts.store') }}" method="POST"
			enctype="multipart/form-data">
			@csrf


			<div class="row d-flex justify-content-between gap-4">
				<div class="col-md-7 mb-4 border p-3 rounded">
					<div class="row" style="display: flex; align-items: center;">
						<!-- User Selection -->
						<div class="col-md-6 mb-3">
							<x-input type="select" name="clinic_user_id"
								id="clinic_user_id"
								label="{{ __('User') }}"
								:options="$clinicUsers->pluck('name', 'id')->toArray()"
								placeholder="{{ __('Select a user') }}"
								required />
						</div>

						<div class="row">

							<!-- Salary Type -->
							<div class="col-md-6 mb-3">
								<x-input type="select"
									name="salary_type"
									id="salary_type"
									label="{{ __('Salary Type') }}"
									placeholder="{{ __('e.g., Fixed, Hourly, Percentage') }}"
									:options="[
										'fixed' => __('Fixed'),
										'hourly' => __('Hourly'),
										'percentage' => __('Percentage')
									]"
									required />
							</div>

							<!-- Base Amount -->
							<div class="col-md-6 mb-3" id="base_amount_wrapper">
								<x-input type="number"
									name="base_amount"
									id="base_amount"
									label="{{ __('Base Amount') }}"
									placeholder="{{ __('Enter base amount') }}"
									min="0" step="0.01"
									required />
							</div>

							<!-- Percentage Rate -->
							<div class="col-md-6 mb-3" id="percentage_rate_wrapper">
								<x-input type="number"
									name="percentage_rate"
									id="percentage_rate"
									label="{{ __('Percentage Rate') }}"
									placeholder="{{ __('Enter percentage rate') }}"
									min="0" step="0.01"
									required />
							</div>



							<!-- Effective From -->
							<div class="col-md-6 mb-3">
								<x-input type="date" name="effective_from"
									id="effective_from"
									label="{{ __('Effective From') }}"
									required />
							</div>

							<!-- Effective To -->
							<div class="col-md-6 mb-3">
								<x-input type="date" name="effective_to"
									id="effective_to"
									label="{{ __('Effective To') }}" />
							</div>




						</div>


						<!-- Notes -->
						<div class="col-md-12 mb-3">
							<x-input type="textarea" name="notes" id="notes"
								label="{{ __('Notes') }}"
								placeholder="{{ __('Enter any additional notes') }}"
								rows="4" />
						</div>

					</div>
				</div>
				<div class="col-md-4 mb-4 border p-3 rounded">


					<!-- Images -->
					<div class="col-md-12 mb-3">
						<x-input type="file" name="images" id="images"
							label="{{ __('Additional Images') }}"
							accept="image/*" multiple preview />
					</div>
				</div>



			</div>
			<!-- Submit -->
			<button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
		</form>
	</div>
</div>
@endsection
@push('scripts')
<script>
	$(document).ready(function() {
    function toggleFields() {
        const type = $('#salary_type').val();

        if (type === 'percentage') {
            $('#percentage_rate_wrapper').show();
            $('#percentage_rate').attr('required', true);

            $('#base_amount_wrapper').hide();
            $('#base_amount').removeAttr('required').val('');
        } else if (type === 'fixed' || type === 'hourly' || type === 'monthly') {
            $('#base_amount_wrapper').show();
            $('#base_amount').attr('required', true);

            $('#percentage_rate_wrapper').hide();
            $('#percentage_rate').removeAttr('required').val('');
        } else {
            $('#base_amount_wrapper, #percentage_rate_wrapper').hide();
            $('#base_amount, #percentage_rate').removeAttr('required').val('');
        }
    }

    toggleFields();
    $('#salary_type').on('change', toggleFields);
});

</script>
@endpush