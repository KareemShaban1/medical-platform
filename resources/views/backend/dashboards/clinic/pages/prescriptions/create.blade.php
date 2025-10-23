@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
	<div class="card-header">
		<h4 class="card-title">{{ __('Add Prescription') }}</h4>
	</div>

	<div class="card-body p-4">
		<form action="{{ route('clinic.prescriptions.store') }}" method="POST"
			enctype="multipart/form-data">
			@csrf


			<!-- clinic_id -->
			<input type="hidden" name="clinic_id" value="{{ auth('clinic')->user()->clinic_id }}">
			<!-- patient_id -->
			<input type="hidden" name="patient_id" value="{{ $appointment->patient_id }}">
			<!-- doctor_profile_id -->
			<input type="hidden" name="doctor_profile_id"
				value="{{ $appointment->doctor_profile_id }}">
			<!-- appointment_id -->
			<input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

			<div class="row d-flex justify-content-between gap-4">
				<div class="col-md-7 mb-4 border p-3 rounded">

					<!-- Description -->
					<div class="mb-3">
						<label for="notes"
							class="form-label">{{ __('Notes') }}</label>
						<textarea name="notes" id="notes"
							class="form-control"
							required>{{ old('notes') }}</textarea>
						@error('notes') <span
							class="text-danger">{{ $message }}</span>
						@enderror
					</div>

					<!-- Prescription Items -->
					<div class="mt-4">
						<h5 class="mb-3">{{ __('Prescription Items') }}</h5>
						<table class="table table-bordered"
							id="prescription_items_table">
							<thead>
								<tr>
									<th>{{ __('Drug Name') }}</th>
									<th>{{ __('Dose') }}</th>
									<th>{{ __('Frequency') }}</th>
									<th>{{ __('Duration') }}</th>
									<th>{{ __('Notes') }}</th>
									<th>{{ __('Action') }}</th>
								</tr>
							</thead>
							<tbody id="prescription_items_body">
								<tr>
									<td><input type="text"
											name="items[0][drug_name]"
											class="form-control"
											required>
									</td>
									<td><input type="text"
											name="items[0][dose]"
											class="form-control">
									</td>
									<td><input type="text"
											name="items[0][frequency]"
											class="form-control">
									</td>
									<td><input type="text"
											name="items[0][duration]"
											class="form-control">
									</td>
									<td><input type="text"
											name="items[0][notes]"
											class="form-control">
									</td>
									<td><button type="button"
											class="btn btn-danger btn-sm remove-row">{{ __('Remove') }}</button>
									</td>
								</tr>
							</tbody>
						</table>
						<button type="button" id="add_row"
							class="btn btn-secondary btn-sm">{{ __('+ Add Item') }}</button>
					</div>
				</div>

				<div class="col-md-4 mb-4 border p-3 rounded">
					<!-- Images -->
					<div class="col-md-12 mb-3">
						<label for="images"
							class="form-label">{{ __('Additional Images') }}</label>
						<input type="file" name="images[]" id="images"
							class="form-control" accept="image/*" multiple>
						<div id="images_preview"
							class="d-flex flex-wrap gap-2 mt-2"></div>
						@error('images') <span
							class="text-danger">{{ $message }}</span>
						@enderror
					</div>
				</div>
			</div>

			<!-- Submit -->
			<button type="submit" class="btn btn-primary mt-3">{{ __('Submit') }}</button>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 1;

// Add new prescription item row
document.getElementById('add_row').addEventListener('click', function() {
	const tbody = document.getElementById('prescription_items_body');
	const newRow = document.createElement('tr');

	newRow.innerHTML = `
        <td><input type="text" name="items[${itemIndex}][drug_name]" class="form-control" required></td>
        <td><input type="text" name="items[${itemIndex}][dose]" class="form-control"></td>
        <td><input type="text" name="items[${itemIndex}][frequency]" class="form-control"></td>
        <td><input type="text" name="items[${itemIndex}][duration]" class="form-control"></td>
        <td><input type="text" name="items[${itemIndex}][notes]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-row">{{ __('Remove') }}</button></td>
    `;
	tbody.appendChild(newRow);
	itemIndex++;
});

// Remove row
document.addEventListener('click', function(e) {
	if (e.target.classList.contains('remove-row')) {
		e.target.closest('tr').remove();
	}
});

// Preview images
document.getElementById('images').addEventListener('change', function() {
	const previewContainer = document.getElementById('images_preview');
	previewContainer.innerHTML = '';
	Array.from(this.files).forEach(file => {
		const reader = new FileReader();
		reader.onload = (e) => {
			const img = document.createElement(
				'img');
			img.src = e.target.result;
			img.classList.add('img-thumbnail');
			img.style.maxHeight = '150px';
			previewContainer.appendChild(img);
		};
		reader.readAsDataURL(file);
	});
});
</script>
@endpush