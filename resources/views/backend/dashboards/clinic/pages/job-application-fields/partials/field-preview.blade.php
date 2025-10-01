@php
$field = $field ?? $jobApplicationField ?? null;
@endphp

@if($field)
<div class="form-group mb-3">
	<label for="preview_{{ $field->field_name }}" class="form-label">
		{{ $field->field_label }}
		@if($field->required)
		<span class="text-danger">*</span>
		@endif
	</label>

	@switch($field->field_type)
	@case('text')
	<input type="text"
		id="preview_{{ $field->field_name }}"
		name="{{ $field->field_name }}"
		class="form-control"
		placeholder="Enter {{ strtolower($field->field_label) }}"
		{{ $field->required ? 'required' : '' }}
		disabled>
	@break

	@case('email')
	<input type="email"
		id="preview_{{ $field->field_name }}"
		name="{{ $field->field_name }}"
		class="form-control"
		placeholder="Enter {{ strtolower($field->field_label) }}"
		{{ $field->required ? 'required' : '' }}
		disabled>
	@break

	@case('phone')
	<input type="tel"
		id="preview_{{ $field->field_name }}"
		name="{{ $field->field_name }}"
		class="form-control"
		placeholder="Enter {{ strtolower($field->field_label) }}"
		{{ $field->required ? 'required' : '' }}
		disabled>
	@break

	@case('textarea')
	<textarea id="preview_{{ $field->field_name }}"
		name="{{ $field->field_name }}"
		class="form-control"
		rows="3"
		placeholder="Enter {{ strtolower($field->field_label) }}"
		{{ $field->required ? 'required' : '' }}
		disabled></textarea>
	@break

	@case('file')
	<input type="file"
		id="preview_{{ $field->field_name }}"
		name="{{ $field->field_name }}"
		class="form-control"
		{{ $field->required ? 'required' : '' }}
		disabled>
	<small class="form-text text-muted">{{ __('Upload a file') }}</small>
	@break

	@case('select')
	<select id="preview_{{ $field->field_name }}"
		name="{{ $field->field_name }}"
		class="form-select"
		{{ $field->required ? 'required' : '' }}
		disabled>
		<option value="">{{ __('Choose an option') }}</option>
		@if($field->options)
		@foreach($field->options as $option)
		<option value="{{ $option }}">{{ $option }}</option>
		@endforeach
		@endif
	</select>
	@break

	@case('checkbox')
	@if($field->options)
	@foreach($field->options as $index => $option)
	<div class="form-check">
		<input type="checkbox"
			id="preview_{{ $field->field_name }}_{{ $index }}"
			name="{{ $field->field_name }}[]"
			value="{{ $option }}"
			class="form-check-input"
			{{ $field->required ? 'required' : '' }}
			disabled>
		<label for="preview_{{ $field->field_name }}_{{ $index }}" class="form-check-label">
			{{ $option }}
		</label>
	</div>
	@endforeach
	@endif
	@break

	@case('radio')
	@if($field->options)
	@foreach($field->options as $index => $option)
	<div class="form-check">
		<input type="radio"
			id="preview_{{ $field->field_name }}_{{ $index }}"
			name="{{ $field->field_name }}"
			value="{{ $option }}"
			class="form-check-input"
			{{ $field->required ? 'required' : '' }}
			disabled>
		<label for="preview_{{ $field->field_name }}_{{ $index }}" class="form-check-label">
			{{ $option }}
		</label>
	</div>
	@endforeach
	@endif
	@break

	@default
	<input type="text"
		id="preview_{{ $field->field_name }}"
		name="{{ $field->field_name }}"
		class="form-control"
		placeholder="Enter {{ strtolower($field->field_label) }}"
		{{ $field->required ? 'required' : '' }}
		disabled>
	@endswitch

	@if($field->required)
	<small class="form-text text-danger">{{ __('This field is required') }}</small>
	@endif
</div>
@endif