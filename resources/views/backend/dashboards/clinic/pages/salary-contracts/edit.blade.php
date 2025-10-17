@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
	<div class="card-header">
		<h4 class="card-title">{{ __('Edit Salary Contract') }}</h4>
	</div>
	<!-- error messages -->
	@if ($errors->any())
	<div class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif
	<div class="card-body p-4">
		<form action="{{ route('clinic.salary-contracts.update', $salaryContract->id) }}"
			method="POST" enctype="multipart/form-data">
			@csrf
			@method('PUT')

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
								:value="$salaryContract->clinic_user_id"
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
									required :value="$salaryContract->salary_type" />
							</div>

							<!-- Base Amount -->
							<div class="col-md-6 mb-3" id="base_amount_wrapper">
								<x-input type="number"
									name="base_amount"
									id="base_amount"
									label="{{ __('Base Amount') }}"
									placeholder="{{ __('Enter base amount') }}"
									min="0" step="0.01"
									:value="$salaryContract->base_amount" />
							</div>
							<!-- Percentage Rate -->
							<div class="col-md-6 mb-3" id="percentage_rate_wrapper">
								<x-input type="number" name="percentage_rate"
									id="percentage_rate"
									label="{{ __('Percentage Rate') }}"
									placeholder="{{ __('Enter percentage rate') }}"
									min="0" step="0.01"
									:value="$salaryContract->percentage_rate" />
							</div>



							<!-- Effective From -->
							<div class="col-md-6 mb-3">
								<x-input type="date" name="effective_from"
									id="effective_from"
									label="{{ __('Effective From') }}"
									required
									:value="$salaryContract->effective_from" />
							</div>

							<!-- Effective To -->
							<div class="col-md-6 mb-3">
								<x-input type="date" name="effective_to"
									id="effective_to"
									label="{{ __('Effective To') }}"
									:value="$salaryContract->effective_to" />
							</div>




						</div>


						<!-- Notes -->
						<div class="col-md-12 mb-3">
							<x-input type="textarea" name="notes" id="notes"
								label="{{ __('Notes') }}"
								placeholder="{{ __('Enter any additional notes') }}"
								rows="4"
								:value="$salaryContract->notes" />
						</div>

					</div>
				</div>
				<div class="col-md-4 mb-4 border p-3 rounded">


					<!-- Images -->
					<div class="col-md-12 mb-3">
						<x-input type="file" name="images" id="images"
							label="{{ __('Additional Images') }}"
							accept="image/*" multiple preview
							:value="$salaryContract->images" />

						@foreach($salaryContract->images as $image)
						<div class="col-6 mb-3">
							<div class="position-relative">
								<img src="{{ $image }}"
									alt="Clinic User Salary Image"
									class="img-fluid rounded"
									style="width: 100%; height: 150px; object-fit: cover;"
									data-bs-toggle="modal"
									data-bs-target="#imageModal{{ $loop->index }}"
									style="cursor: pointer;">
							</div>
						</div>
						@endforeach
					</div>
				</div>



			</div>

			<!-- Submit -->
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const salaryType = document.getElementById('salary_type');
    const baseAmountWrapper = document.getElementById('base_amount_wrapper');
    const percentageRateWrapper = document.getElementById('percentage_rate_wrapper');
    const baseAmount = document.getElementById('base_amount');
    const percentageRate = document.getElementById('percentage_rate');

    function toggleFields() {
        const type = salaryType.value;

        if (type === 'percentage') {
            // Show percentage rate
            percentageRateWrapper.style.display = 'block';
            percentageRate.setAttribute('required', 'required');

            // Hide base amount
            baseAmountWrapper.style.display = 'none';
            baseAmount.removeAttribute('required');
            baseAmount.value = '';
        } else if (type === 'fixed' || type === 'hourly' || type === 'monthly') {
            // Show base amount
            baseAmountWrapper.style.display = 'block';
            baseAmount.setAttribute('required', 'required');

            // Hide percentage rate
            percentageRateWrapper.style.display = 'none';
            percentageRate.removeAttribute('required');
            percentageRate.value = '';
        } else {
            // Hide both
            baseAmountWrapper.style.display = 'none';
            percentageRateWrapper.style.display = 'none';
            baseAmount.removeAttribute('required');
            percentageRate.removeAttribute('required');
        }
    }

    // Initialize on page load (edit mode)
    toggleFields();

    // Update on change
    salaryType.addEventListener('change', toggleFields);
});
</script>

@endpush