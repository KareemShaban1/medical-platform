@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
	<div class="card-header">
		<h4 class="card-title">{{ __('Edit Clinic User Salary') }}</h4>
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
		<form action="{{ route('clinic.clinic-user-salaries.update', $clinicUserSalary->id) }}"
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
								:value="$clinicUserSalary->clinic_user_id"
								required />
						</div>

						<div class="row">

							<!-- Salary Frequency -->
							<div class="col-md-6 mb-3">
								<x-input type="select"
									name="salary_frequency"
									id="salary_frequency"
									label="{{ __('Salary Frequency') }}"
									placeholder="{{ __('e.g., Monthly, Weekly, Daily') }}"
									:options="[
										'monthly' => __('Monthly'),
										'weekly' => __('Weekly'),
										'daily' => __('Daily')
									]" required :value="$clinicUserSalary->salary_frequency" />
							</div>

							<!-- Amount per Salary Frequency -->
							<div class="col-md-6 mb-3">
								<x-input type="number"
									name="amount_per_salary_frequency"
									id="amount_per_salary_frequency"
									label="{{ __('Amount per Salary Frequency') }}"
									placeholder="{{ __('Enter amount per frequency') }}"
									min="0" step="0.01" required
									:value="$clinicUserSalary->amount_per_salary_frequency" />
							</div>
							<!-- Amount -->
							<div class="col-md-6 mb-3">
								<x-input type="number" name="amount"
									id="amount"
									label="{{ __('Amount') }}"
									placeholder="{{ __('Enter total amount') }}"
									min="0" step="0.01" required
									:value="$clinicUserSalary->amount" />
							</div>



							<!-- Paid Status -->
							<div class="col-md-6 mb-3">
								<x-input type="select" name="paid"
									id="paid"
									label="{{ __('Paid Status') }}"
									:options="[
										'1' => __('Paid'),
										'0' => __('Unpaid')
									]" required :value="$clinicUserSalary->paid" />
							</div>

							<!-- Start Date -->
							<div class="col-md-6 mb-3">
								<x-input type="date" name="start_date"
									id="start_date"
									label="{{ __('Start Date') }}"
									required
									:value="$clinicUserSalary->start_date" />
							</div>

							<!-- End Date -->
							<div class="col-md-6 mb-3">
								<x-input type="date" name="end_date"
									id="end_date"
									label="{{ __('End Date') }}"
									:value="$clinicUserSalary->end_date" />
							</div>




						</div>


						<!-- Notes -->
						<div class="col-md-12 mb-3">
							<x-input type="textarea" name="notes" id="notes"
								label="{{ __('Notes') }}"
								placeholder="{{ __('Enter any additional notes') }}"
								rows="4"
								:value="$clinicUserSalary->notes" />
						</div>

					</div>
				</div>
				<div class="col-md-4 mb-4 border p-3 rounded">


					<!-- Images -->
					<div class="col-md-12 mb-3">
						<x-input type="file" name="images" id="images"
							label="{{ __('Additional Images') }}"
							accept="image/*" multiple preview
							:value="$clinicUserSalary->images" />

						@foreach($clinicUserSalary->images as $image)
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
document.addEventListener("DOMContentLoaded", function() {
	const userSelect = document.getElementById('clinic_user_id');
	const salaryFrequencyInput = document.getElementById('salary_frequency');
	const amountPerSalaryFrequencyInput = document.getElementById(
		'amount_per_salary_frequency');

	// Handle user selection change
	userSelect.addEventListener('change', function() {
		const userId = this.value;

		if (userId) {
			// Show loading state
			salaryFrequencyInput.value = 'Loading...';
			amountPerSalaryFrequencyInput.value =
				'Loading...';

			// Fetch user salary data
			const url =
				'{{ route("clinic.clinic-user-salaries.user-salary-data", ["userId" => "PLACEHOLDER"]) }}'
				.replace('PLACEHOLDER', userId);
			fetch(url)
				.then(response => {
					if (!response.ok) {
						throw new Error(
							'Network response was not ok'
						);
					}
					return response
						.json();
				})
				.then(data => {
					// Populate the fields with user data
					salaryFrequencyInput
						.value =
						data
						.salary_frequency ||
						'';
					amountPerSalaryFrequencyInput
						.value =
						data
						.amount_per_salary_frequency ||
						'';
				})
				.catch(error => {
					console.error('Error fetching user salary data:',
						error
					);
					// Clear the fields on error
					salaryFrequencyInput
						.value =
						'';
					amountPerSalaryFrequencyInput
						.value =
						'';

					// Show error message
					alert(
						'Error loading user salary data. Please try again.'
					);
				});
		} else {
			// Clear fields when no user is selected
			salaryFrequencyInput.value = '';
			amountPerSalaryFrequencyInput.value = '';
		}
	});
});
</script>
@endpush