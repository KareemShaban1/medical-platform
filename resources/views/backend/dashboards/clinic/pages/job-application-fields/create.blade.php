@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Create Job Application Field'))

@section('content')

<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">{{ __('Create Job Application Field') }}</h4>
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
        <form action="{{ route('clinic.job-application-fields.store') }}" method="POST"
            id="jobApplicationFieldForm">
            @csrf
            <input type="hidden" name="job_id" value="{{ $job->id }}">

            <!-- Job Info Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5 class="mb-1">{{ __('Creating fields for job') }}:
                            <strong>{{ $job->title }}</strong>
                        </h5>
                        <small
                            class="text-muted">{{ __('Add custom fields that applicants will fill when applying for this job') }}</small>
                    </div>
                </div>
            </div>

            <!-- Fields Container -->
            <div id="fields-container">
                <!-- Field Row Template -->
                <div class="field-row border rounded p-3 mb-3" data-row="0">
                    <div class="row">
                        <div
                            class="col-12 d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-primary">{{ __('Field') }}
                                <span class="field-number">1</span>
                            </h6>
                            <button type="button"
                                class="btn btn-outline-danger btn-sm remove-field"
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
                            <input type="text" name="fields[0][field_name]"
                                class="form-control field-name"
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
                            <input type="text" name="fields[0][field_label]"
                                class="form-control field-label"
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
                            <select name="fields[0][field_type]"
                                class="form-select field-type" required>
                                <option value="">{{ __('Select type') }}
                                </option>
                                <option value="text">
                                    {{ __('Text Input') }}
                                </option>
                                <option value="email">{{ __('Email') }}
                                </option>
                                <option value="phone">
                                    {{ __('Phone Number') }}
                                </option>
                                <option value="textarea">
                                    {{ __('Text Area') }}
                                </option>
                                <option value="file">
                                    {{ __('File Upload') }}
                                </option>
                                <option value="select">
                                    {{ __('Select Dropdown') }}
                                </option>
                                <option value="checkbox">
                                    {{ __('Checkbox') }}
                                </option>
                                <option value="radio">
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
                                        name="fields[0][required]"
                                        class="form-check-input"
                                        value="1">
                                    <label
                                        class="form-check-label">{{ __('Required') }}</label>
                                </div>
                            </div>
                            <input type="number" name="fields[0][order]"
                                class="form-control" value="0" min="0"
                                placeholder="Order">
                        </div>
                    </div>

                    <!-- Options Section (hidden by default) -->
                    <div class="row options-section" style="display: none;">
                        <div class="col-12">
                            <label
                                class="form-label">{{ __('Field Options') }}</label>
                            <div class="options-inputs">
                                <div class="input-group mb-2">
                                    <input type="text"
                                        name="fields[0][options][]"
                                        class="form-control"
                                        placeholder="{{ __('Option 1') }}">
                                    <button type="button"
                                        class="btn btn-outline-danger"
                                        onclick="removeOption(this)">{{ __('Remove') }}</button>
                                </div>
                            </div>
                            <button type="button"
                                class="btn btn-outline-primary btn-sm"
                                onclick="addOption(this)">{{ __('Add Option') }}</button>
                            <small
                                class="form-text text-muted">{{ __('Add options for select, checkbox, or radio fields') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Field Button -->
            <div class="row mb-4">
                <div class="col-12">
                    <button type="button" class="btn btn-success" onclick="addField()">
                        <i class="fas fa-plus"></i> {{ __('Add Another Field') }}
                    </button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('clinic.jobs.index') }}"
                            class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            {{ __('Create Field') }}
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
    let fieldRowCount = 0;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize first field
        initializeFieldRow(document.querySelector('.field-row'));
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
        fieldRowCount++;
        const container = document.getElementById('fields-container');
        const fieldRow = document.createElement('div');
        fieldRow.className = 'field-row border rounded p-3 mb-3';
        fieldRow.setAttribute('data-row', fieldRowCount);

        fieldRow.innerHTML = `
		<div class="row">
			<div class="col-12 d-flex justify-content-between align-items-center mb-3">
				<h6 class="mb-0 text-primary">{{ __('Field') }} <span class="field-number">${fieldRowCount + 1}</span></h6>
				<button type="button" class="btn btn-outline-danger btn-sm remove-field" onclick="removeField(this)">
					<i class="fas fa-trash"></i> {{ __('Remove') }}
				</button>
			</div>
		</div>

		<div class="row">
			<!-- Field Name -->
			<div class="col-md-3 mb-3">
				<label class="form-label">{{ __('Field Name') }} <span class="text-danger">*</span></label>
				<input type="text" name="fields[${fieldRowCount}][field_name]" class="form-control field-name" 
					   placeholder="e.g., name, email, phone" required>
				<small class="form-text text-muted">{{ __('Internal name (lowercase)') }}</small>
			</div>

			<!-- Field Label -->
			<div class="col-md-3 mb-3">
				<label class="form-label">{{ __('Field Label') }} <span class="text-danger">*</span></label>
				<input type="text" name="fields[${fieldRowCount}][field_label]" class="form-control field-label" 
					   placeholder="e.g., Full Name, Email Address" required>
				<small class="form-text text-muted">{{ __('Display label') }}</small>
			</div>

			<!-- Field Type -->
			<div class="col-md-3 mb-3">
				<label class="form-label">{{ __('Field Type') }} <span class="text-danger">*</span></label>
				<select name="fields[${fieldRowCount}][field_type]" class="form-select field-type" required>
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
						<input type="checkbox" name="fields[${fieldRowCount}][required]" class="form-check-input" value="1">
						<label class="form-check-label">{{ __('Required') }}</label>
					</div>
				</div>
				<input type="number" name="fields[${fieldRowCount}][order]" class="form-control" value="${fieldRowCount}" min="0" placeholder="Order">
			</div>
		</div>

		<!-- Options Section (hidden by default) -->
		<div class="row options-section" style="display: none;">
			<div class="col-12">
				<label class="form-label">{{ __('Field Options') }}</label>
				<div class="options-inputs">
					<div class="input-group mb-2">
						<input type="text" name="fields[${fieldRowCount}][options][]" class="form-control" placeholder="{{ __('Option 1') }}">
						<button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">{{ __('Remove') }}</button>
					</div>
				</div>
				<button type="button" class="btn btn-outline-primary btn-sm" onclick="addOption(this)">{{ __('Add Option') }}</button>
				<small class="form-text text-muted">{{ __('Add options for select, checkbox, or radio fields') }}</small>
			</div>
		</div>
	`;

        container.appendChild(fieldRow);
        initializeFieldRow(fieldRow);
        updateFieldNumbers();
    }

    function removeField(button) {
        const fieldRow = button.closest('.field-row');
        const container = document.getElementById('fields-container');

        if (container.children.length > 1) {
            fieldRow.remove();
            updateFieldNumbers();
        } else {
            alert('{{ __("At least one field is required") }}');
        }
    }

    function updateFieldNumbers() {
        const fieldRows = document.querySelectorAll('.field-row');
        fieldRows.forEach((row, index) => {
            const fieldNumber = row.querySelector('.field-number');
            fieldNumber.textContent = index + 1;
        });
    }

    function addOption(button) {
        const fieldRow = button.closest('.field-row');
        const optionsInputs = fieldRow.querySelector('.options-inputs');
        const optionCount = optionsInputs.children.length + 1;
        const fieldIndex = fieldRow.getAttribute('data-row');

        const optionDiv = document.createElement('div');
        optionDiv.className = 'input-group mb-2';
        optionDiv.innerHTML = `
		<input type="text" name="fields[${fieldIndex}][options][]" class="form-control" placeholder="Option ${optionCount}">
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