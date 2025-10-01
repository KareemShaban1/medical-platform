@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Edit Job Application Fields'))

@section('content')

<div class="card mt-3">
	<div class="card-header">
		<h4 class="card-title">{{ __('Edit Job Application Fields') }}</h4>
	</div>

	<!-- Success Message -->
	@if(session('success'))
	<div class="alert alert-success">
		{{ session('success') }}
	</div>
	@endif

	<!-- Error Message -->
	@if(session('error'))
	<div class="alert alert-danger">
		{{ session('error') }}
	</div>
	@endif

	<!-- Validation Errors -->
	@if($errors->any())
	<div class="alert alert-danger">
		<ul class="mb-0">
			@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif

	<div class="card-body p-4">
		<form action="{{ route('clinic.job-application-fields.update', $job->id) }}" method="POST"
			id="jobApplicationFieldForm">
			@csrf
			@method('PUT')
			<input type="hidden" name="job_id" value="{{ $job->id }}">

			<!-- Job Info Header -->
			<div class="row mb-4">
				<div class="col-12">
					<div class="alert alert-info">
						<h5 class="mb-1">{{ __('Editing fields for job') }}:
							<strong>{{ $job->title }}</strong>
						</h5>
						<small
							class="text-muted">{{ __('Update the field configurations below') }}</small>
					</div>
				</div>
			</div>

			<!-- Fields Container -->
			<div class="fields-container">
				@if($job->jobApplicationFields && count($job->jobApplicationFields) > 0)
				@foreach($job->jobApplicationFields as $index => $field)
				<div class="field-row border rounded p-3 mb-3" data-row="{{ $index }}">
					<div class="row">
						<div
							class="col-12 d-flex justify-content-between align-items-center mb-3">
							<h6 class="mb-0 text-primary">{{ __('Field') }}
								{{ $index + 1 }}</h6>
							<button type="button"
								class="btn btn-outline-danger btn-sm"
								onclick="removeField(this)">
								<i class="fas fa-trash"></i>
								{{ __('Remove') }}
							</button>
						</div>
					</div>

					<div class="row">
						<!-- Field Name -->
						<div class="col-md-3 mb-3">
							<label class="form-label">{{ __('Field Name') }}
								<span
									class="text-danger">*</span></label>
							<input type="text"
								name="fields[{{ $index }}][field_name]"
								class="form-control field-name"
								value="{{ old('fields.' . $index . '.field_name', $field->field_name) }}"
								placeholder="e.g., name, email, phone"
								required>
							<small
								class="form-text text-muted">{{ __('Internal name (lowercase)') }}</small>
						</div>

						<!-- Field Label -->
						<div class="col-md-3 mb-3">
							<label class="form-label">{{ __('Field Label') }}
								<span
									class="text-danger">*</span></label>
							<input type="text"
								name="fields[{{ $index }}][field_label]"
								class="form-control field-label"
								value="{{ old('fields.' . $index . '.field_label', $field->field_label) }}"
								placeholder="e.g., Full Name, Email Address"
								required>
							<small
								class="form-text text-muted">{{ __('Display label') }}</small>
						</div>

						<!-- Field Type -->
						<div class="col-md-3 mb-3">
							<label class="form-label">{{ __('Field Type') }}
								<span
									class="text-danger">*</span></label>
							<select name="fields[{{ $index }}][field_type]"
								class="form-select field-type" required>
								<option value="">{{ __('Select type') }}
								</option>
								<option value="text"
									{{ old('fields.' . $index . '.field_type', $field->field_type) == 'text' ? 'selected' : '' }}>
									{{ __('Text Input') }}
								</option>
								<option value="email"
									{{ old('fields.' . $index . '.field_type', $field->field_type) == 'email' ? 'selected' : '' }}>
									{{ __('Email') }}</option>
								<option value="phone"
									{{ old('fields.' . $index . '.field_type', $field->field_type) == 'phone' ? 'selected' : '' }}>
									{{ __('Phone Number') }}
								</option>
								<option value="textarea"
									{{ old('fields.' . $index . '.field_type', $field->field_type) == 'textarea' ? 'selected' : '' }}>
									{{ __('Text Area') }}</option>
								<option value="file"
									{{ old('fields.' . $index . '.field_type', $field->field_type) == 'file' ? 'selected' : '' }}>
									{{ __('File Upload') }}
								</option>
								<option value="select"
									{{ old('fields.' . $index . '.field_type', $field->field_type) == 'select' ? 'selected' : '' }}>
									{{ __('Select Dropdown') }}
								</option>
								<option value="checkbox"
									{{ old('fields.' . $index . '.field_type', $field->field_type) == 'checkbox' ? 'selected' : '' }}>
									{{ __('Checkbox') }}</option>
								<option value="radio"
									{{ old('fields.' . $index . '.field_type', $field->field_type) == 'radio' ? 'selected' : '' }}>
									{{ __('Radio Buttons') }}
								</option>
							</select>
						</div>

						<!-- Required & Order -->
						<div class="col-md-3 mb-3">
							<label
								class="form-label">{{ __('Settings') }}</label>
							<div class="d-flex gap-2 mb-2">
								<div class="form-check">
									<input type="checkbox"
										name="fields[{{ $index }}][required]"
										class="form-check-input"
										value="1"
										{{ old('fields.' . $index . '.required', $field->required) ? 'checked' : '' }}>
									<label
										class="form-check-label">{{ __('Required') }}</label>
								</div>
							</div>
							<input type="number"
								name="fields[{{ $index }}][order]"
								class="form-control"
								value="{{ old('fields.' . $index . '.order', $field->order) }}"
								min="0" placeholder="Order">
						</div>
					</div>

					<!-- Options Section -->
					<div class="row options-section"
						style="display: {{ in_array($field->field_type, ['select', 'checkbox', 'radio']) ? 'block' : 'none' }};">
						<div class="col-12">
							<label
								class="form-label">{{ __('Field Options') }}</label>
							<div class="options-inputs">
								@if($field->options &&
								count($field->options) > 0)
								@foreach($field->options as $optionIndex
								=> $option)
								<div class="input-group mb-2">
									<input type="text"
										name="fields[{{ $index }}][options][]"
										class="form-control"
										value="{{ $option }}"
										placeholder="{{ __('Option') }} {{ $optionIndex + 1 }}">
									<button type="button"
										class="btn btn-outline-danger"
										onclick="removeOption(this)">{{ __('Remove') }}</button>
								</div>
								@endforeach
								@else
								<div class="input-group mb-2">
									<input type="text"
										name="fields[{{ $index }}][options][]"
										class="form-control"
										placeholder="{{ __('Option 1') }}">
									<button type="button"
										class="btn btn-outline-danger"
										onclick="removeOption(this)">{{ __('Remove') }}</button>
								</div>
								@endif
							</div>
							<button type="button"
								class="btn btn-outline-primary btn-sm"
								onclick="addOption(this)">{{ __('Add Option') }}</button>
							<small
								class="form-text text-muted">{{ __('Add options for select, checkbox, or radio fields') }}</small>
						</div>
					</div>
				</div>
				@endforeach
				@else
				<div class="alert alert-warning">
					{{ __('No application fields found for this job.') }}
				</div>
				@endif
			</div>

			<!-- Add Field Button -->
			<div class="row mb-4">
				<div class="col-12">
					<button type="button" class="btn btn-outline-primary"
						onclick="addField()">
						<i class="fas fa-plus"></i> {{ __('Add Another Field') }}
					</button>
				</div>
			</div>

			<!-- Form Actions -->
			<div class="row">
				<div class="col-12">
					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('clinic.jobs.index') }}"
							class="btn btn-outline-secondary">
							<i class="fas fa-arrow-left"></i>
							{{ __('Back to Jobs') }}
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save"></i>
							{{ __('Update Fields') }}
						</button>
					</div>
				</div>
			</div>

		</form>
	</div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
	// Initialize all existing field rows
	document.querySelectorAll('.field-row').forEach(function(fieldRow) {
		initializeFieldRow(fieldRow);
	});
});

function initializeFieldRow(fieldRow) {
	// Auto-generate field name from label
	const fieldLabelInput = fieldRow.querySelector('.field-label');
	const fieldNameInput = fieldRow.querySelector('.field-name');

	fieldLabelInput.addEventListener('input', function() {
		if (!fieldNameInput.value) {
			const fieldName = this.value
				.toLowerCase()
				.replace(/[^a-z0-9\s]/g, '')
				.replace(/\s+/g, '_');
			fieldNameInput.value = fieldName;
		}
	});

	// Show/hide options based on field type
	const fieldTypeSelect = fieldRow.querySelector('.field-type');
	const optionsSection = fieldRow.querySelector('.options-section');

	fieldTypeSelect.addEventListener('change', function() {
		const selectedType = this.value;
		const needsOptions = ['select', 'checkbox', 'radio'].includes(selectedType);

		if (needsOptions) {
			optionsSection.style.display = 'block';
		} else {
			optionsSection.style.display = 'none';
		}
	});
}

function addField() {
	const fieldsContainer = document.querySelector('.fields-container');
	const fieldCount = fieldsContainer.children.length;
	const fieldIndex = fieldCount;

	const fieldRow = document.createElement('div');
	fieldRow.className = 'field-row border rounded p-3 mb-3';
	fieldRow.setAttribute('data-row', fieldIndex);
	fieldRow.innerHTML = `
		<div class="row">
			<div class="col-12 d-flex justify-content-between align-items-center mb-3">
				<h6 class="mb-0 text-primary">{{ __('Field') }} ${fieldCount + 1}</h6>
				<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeField(this)">
					<i class="fas fa-trash"></i> {{ __('Remove') }}
				</button>
			</div>
		</div>

		<div class="row">
			<!-- Field Name -->
			<div class="col-md-3 mb-3">
				<label class="form-label">{{ __('Field Name') }} <span class="text-danger">*</span></label>
				<input type="text" name="fields[${fieldIndex}][field_name]" class="form-control field-name" 
					   placeholder="e.g., name, email, phone" required>
				<small class="form-text text-muted">{{ __('Internal name (lowercase)') }}</small>
			</div>

			<!-- Field Label -->
			<div class="col-md-3 mb-3">
				<label class="form-label">{{ __('Field Label') }} <span class="text-danger">*</span></label>
				<input type="text" name="fields[${fieldIndex}][field_label]" class="form-control field-label" 
					   placeholder="e.g., Full Name, Email Address" required>
				<small class="form-text text-muted">{{ __('Display label') }}</small>
			</div>

			<!-- Field Type -->
			<div class="col-md-3 mb-3">
				<label class="form-label">{{ __('Field Type') }} <span class="text-danger">*</span></label>
				<select name="fields[${fieldIndex}][field_type]" class="form-select field-type" required>
					<option value="">{{ __('Select type') }}</option>
					<option value="text">{{ __('Text Input') }}</option>
					<option value="email">{{ __('Email') }}</option>
					<option value="phone">{{ __('Phone Number') }}</option>
					<option value="textarea">{{ __('Text Area') }}</option>
					<option value="file">{{ __('File Upload') }}</option>
					<option value="select">{{ __('Select Dropdown') }}</option>
					<option value="checkbox">{{ __('Checkbox') }}</option>
					<option value="radio">{{ __('Radio Buttons') }}</option>
				</select>
			</div>

			<!-- Required & Order -->
			<div class="col-md-3 mb-3">
				<label class="form-label">{{ __('Settings') }}</label>
				<div class="d-flex gap-2 mb-2">
					<div class="form-check">
						<input type="checkbox" name="fields[${fieldIndex}][required]" class="form-check-input" value="1">
						<label class="form-check-label">{{ __('Required') }}</label>
					</div>
				</div>
				<input type="number" name="fields[${fieldIndex}][order]" class="form-control" min="0" placeholder="Order">
			</div>
		</div>

		<!-- Options Section -->
		<div class="row options-section" style="display: none;">
			<div class="col-12">
				<label class="form-label">{{ __('Field Options') }}</label>
				<div class="options-inputs">
					<div class="input-group mb-2">
						<input type="text" name="fields[${fieldIndex}][options][]" class="form-control" placeholder="{{ __('Option 1') }}">
						<button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">{{ __('Remove') }}</button>
					</div>
				</div>
				<button type="button" class="btn btn-outline-primary btn-sm" onclick="addOption(this)">{{ __('Add Option') }}</button>
				<small class="form-text text-muted">{{ __('Add options for select, checkbox, or radio fields') }}</small>
			</div>
		</div>
	`;

	fieldsContainer.appendChild(fieldRow);
	initializeFieldRow(fieldRow);
}

function removeField(button) {
	const fieldRow = button.closest('.field-row');
	fieldRow.remove();

	// Renumber remaining fields
	document.querySelectorAll('.field-row').forEach(function(row, index) {
		row.setAttribute('data-row', index);
		const fieldNumber = row.querySelector('h6');
		fieldNumber.textContent = `{{ __('Field') }} ${index + 1}`;
	});
}

function addOption(button) {
	const fieldRow = button.closest('.field-row');
	const optionsInputs = fieldRow.querySelector('.options-inputs');
	const optionCount = optionsInputs.children.length + 1;

	const optionDiv = document.createElement('div');
	optionDiv.className = 'input-group mb-2';
	optionDiv.innerHTML = `
		<input type="text" name="fields[${fieldRow.getAttribute('data-row')}][options][]" class="form-control" placeholder="Option ${optionCount}">
		<button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">{{ __('Remove') }}</button>
	`;

	optionsInputs.appendChild(optionDiv);
}

function removeOption(button) {
	const optionsInputs = button.closest('.options-inputs');
	if (optionsInputs.children.length > 1) {
		button.parentElement.remove();
	} else {
		alert('{{ __("At least one option is required") }}');
	}
}
</script>
@endpush