@props([
'type' => 'text',
'name' => '',
'id' => '',
'label' => '',
'value' => '',
'placeholder' => '',
'required' => false,
'disabled' => false,
'readonly' => false,
'class' => '',
'labelClass' => '',
'inputClass' => '',
'errorClass' => '',
'helpText' => '',
'options' => [], // For select, radio, checkbox groups
'multiple' => false, // For select multiple
'accept' => '', // For file inputs
'min' => '',
'max' => '',
'step' => '',
'rows' => 3, // For textarea
'cols' => '', // For textarea
'autocomplete' => '',
'pattern' => '',
'size' => '', // For file inputs
'preview' => false, // For file inputs
'previewClass' => 'img-thumbnail',
'previewStyle' => 'max-height: 200px;',
'groupClass' => 'mb-3',
'wrapperClass' => '',
'icon' => '', // Icon class for input with icon
'iconPosition' => 'left', // left or right
'showLabel' => true,
'inline' => false, // For radio/checkbox inline
'switch' => false, // For checkbox as switch
'color' => 'primary', // For checkbox/radio color
'size' => 'md', // sm, md, lg
'floating' => false, // For floating labels
'validation' => true, // Enable/disable validation display
])

@php
// Generate unique ID if not provided
$inputId = $id ?: $name ?: 'input_' . uniqid();

// Base classes
$baseInputClass = 'form-control';
$baseLabelClass = 'form-label';

// Size classes
$sizeClasses = [
'sm' => 'form-control-sm',
'md' => '',
'lg' => 'form-control-lg'
];

// Color classes for checkboxes/radios
$colorClasses = [
'primary' => 'form-check-input',
'secondary' => 'form-check-input',
'success' => 'form-check-input',
'danger' => 'form-check-input',
'warning' => 'form-check-input',
'info' => 'form-check-input',
'light' => 'form-check-input',
'dark' => 'form-check-input'
];

// Combine classes
$finalInputClass = trim($baseInputClass . ' ' . ($sizeClasses[$size] ?? '') . ' ' . $inputClass . ' ' . $class);
$finalLabelClass = trim($baseLabelClass . ' ' . $labelClass);
$finalErrorClass = trim('text-danger ' . $errorClass);

// Determine if we need a wrapper
$needsWrapper = in_array($type, ['checkbox', 'radio', 'file', 'switch']);
$isCheckboxRadio = in_array($type, ['checkbox', 'radio']);
$isFile = $type === 'file';
$isSelect = $type === 'select';
$isTextarea = $type === 'textarea';
$isRange = $type === 'range';
$isSwitch = $switch && $type === 'checkbox';

// Handle old values for Laravel
$oldValue = old($name, $value);

// Handle array names for checkboxes/radios
$isArrayName = str_ends_with($name, '[]');
$baseName = $isArrayName ? rtrim($name, '[]') : $name;
@endphp

<div class="{{ $groupClass }} {{ $wrapperClass }}">
	@if($showLabel && $label && !$floating)
	<label for="{{ $inputId }}" class="{{ $finalLabelClass }}">
		{{ $label }}
		@if($required)
		<span class="text-danger">*</span>
		@endif
	</label>
	@endif

	@if($isCheckboxRadio && !$inline)
	<div class="form-check">
		@endif

		@if($isCheckboxRadio && $inline)
		<div class="form-check form-check-inline">
			@endif

			@if($isSwitch)
			<div class="form-check form-switch">
				@endif

				@if($icon && !$isCheckboxRadio && !$isFile)
				<div class="input-group">
					@if($iconPosition === 'left')
					<span class="input-group-text">
						<i class="{{ $icon }}"></i>
					</span>
					@endif
					@endif

					@switch($type)
					@case('text')
					@case('email')
					@case('password')
					@case('number')
					@case('tel')
					@case('url')
					@case('search')
					@case('date')
					@case('time')
					@case('datetime-local')
					@case('month')
					@case('week')
					@case('color')
					<input type="{{ $type }}" name="{{ $name }}" id="{{ $inputId }}"
						class="{{ $finalInputClass }}" value="{{ $oldValue }}"
						placeholder="{{ $placeholder }}" @if($required) required
						@endif @if($disabled) disabled @endif @if($readonly)
						readonly @endif @if($min !=='' ) min="{{ $min }}" @endif
						@if($max !=='' ) max="{{ $max }}" @endif @if($step !=='' )
						step="{{ $step }}" @endif @if($pattern)
						pattern="{{ $pattern }}" @endif @if($autocomplete)
						autocomplete="{{ $autocomplete }}" @endif @if($floating)
						placeholder="{{ $label }}" @endif {{ $attributes }}>
					@break

					@case('textarea')
					<textarea name="{{ $name }}" id="{{ $inputId }}"
						class="{{ $finalInputClass }}"
						placeholder="{{ $placeholder }}" @if($required) required
						@endif @if($disabled) disabled @endif @if($readonly)
						readonly @endif rows="{{ $rows }}" @if($cols)
						cols="{{ $cols }}" @endif @if($floating)
						placeholder="{{ $label }}" @endif
						{{ $attributes }}>{{ $oldValue }}</textarea>
					@break

					@case('select')
					<select name="{{ $name }}{{ $multiple ? '[]' : '' }}"
						id="{{ $inputId }}" class="{{ $finalInputClass }}"
						@if($required) required @endif @if($disabled) disabled
						@endif @if($multiple) multiple @endif {{ $attributes }}>
						@if($placeholder)
						<option value="">{{ $placeholder }}</option>
						@endif
						@foreach($options as $optionValue => $optionLabel)
						@if(is_array($optionLabel))
						<optgroup label="{{ $optionValue }}">
							@foreach($optionLabel as $subValue => $subLabel)
							<option value="{{ $subValue }}"
								@if(is_array($oldValue) ?
								in_array($subValue, $oldValue) :
								$oldValue==$subValue) selected @endif>
								{{ $subLabel }}
							</option>
							@endforeach
						</optgroup>
						@else
						<option value="{{ $optionValue }}" @if(is_array($oldValue) ?
							in_array($optionValue, $oldValue) :
							$oldValue==$optionValue) selected @endif>
							{{ $optionLabel }}
						</option>
						@endif
						@endforeach
					</select>
					@break

					@case('checkbox')
					<input type="checkbox" name="{{ $name }}" id="{{ $inputId }}"
						class="{{ $colorClasses[$color] ?? 'form-check-input' }}"
						value="{{ $value ?: '1' }}" @if($oldValue) checked @endif
						@if($required) required @endif @if($disabled) disabled
						@endif {{ $attributes }}>
					@if($label)
					<label for="{{ $inputId }}" class="form-check-label">
						{{ $label }}
					</label>
					@endif
					@break

					@case('radio')
					@foreach($options as $optionValue => $optionLabel)
					<div class="form-check {{ $inline ? 'form-check-inline' : '' }}">
						<input type="radio" name="{{ $name }}"
							id="{{ $inputId }}_{{ $optionValue }}"
							class="{{ $colorClasses[$color] ?? 'form-check-input' }}"
							value="{{ $optionValue }}"
							@if($oldValue==$optionValue) checked @endif
							@if($required) required @endif @if($disabled)
							disabled @endif {{ $attributes }}>
						<label for="{{ $inputId }}_{{ $optionValue }}"
							class="form-check-label">
							{{ $optionLabel }}
						</label>
					</div>
					@endforeach
					@break

					@case('file')
					<input type="file" name="{{ $name }}{{ $multiple ? '[]' : '' }}"
						id="{{ $inputId }}" class="{{ $finalInputClass }}"
						@if($required) required @endif @if($disabled) disabled
						@endif @if($multiple) multiple @endif @if($accept)
						accept="{{ $accept }}" @endif @if($size) size="{{ $size }}"
						@endif {{ $attributes }}>
					@if($preview && $multiple)
					<div id="{{ $inputId }}_preview" class="d-flex flex-wrap gap-2 mt-2">
					</div>
					@elseif($preview)
					<img id="{{ $inputId }}_preview" class="mt-2 {{ $previewClass }}"
						style="{{ $previewStyle }}; display:none;">
					@endif
					@break

					@case('range')
					<input type="range" name="{{ $name }}" id="{{ $inputId }}"
						class="{{ $finalInputClass }}" value="{{ $oldValue }}"
						@if($required) required @endif @if($disabled) disabled
						@endif @if($min !=='' ) min="{{ $min }}" @endif @if($max
						!=='' ) max="{{ $max }}" @endif @if($step !=='' )
						step="{{ $step }}" @endif {{ $attributes }}>
					@if($showLabel)
					<div class="form-text">{{ $oldValue }}</div>
					@endif
					@break

					@case('hidden')
					<input type="hidden" name="{{ $name }}" value="{{ $oldValue }}"
						{{ $attributes }}>
					@break

					@default
					<input type="text" name="{{ $name }}" id="{{ $inputId }}"
						class="{{ $finalInputClass }}" value="{{ $oldValue }}"
						placeholder="{{ $placeholder }}" @if($required) required
						@endif @if($disabled) disabled @endif @if($readonly)
						readonly @endif {{ $attributes }}>
					@endswitch

					@if($icon && !$isCheckboxRadio && !$isFile)
					@if($iconPosition === 'right')
					<span class="input-group-text">
						<i class="{{ $icon }}"></i>
					</span>
					@endif
				</div>
				@endif

				@if($isCheckboxRadio || $isSwitch)
			</div>
			@endif

			@if($floating && $label)
			<label for="{{ $inputId }}" class="form-label">
				{{ $label }}
			</label>
			@endif

			@if($helpText)
			<div class="form-text">{{ $helpText }}</div>
			@endif

			@if($validation && $errors->has($baseName))
			<div class="{{ $finalErrorClass }}">
				{{ $errors->first($baseName) }}
			</div>
			@endif
		</div>

		@if($isFile && $preview)
		@push('scripts')
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const fileInput = document.getElementById('{{ $inputId }}');
			const previewContainer = document.getElementById('{{ $inputId }}_preview');

			if (fileInput && previewContainer) {
				fileInput.addEventListener('change', function() {
					previewContainer.innerHTML = '';

					if (this.multiple) {
						Array.from(this.files).forEach(
						file => {
							if (file.type
								.startsWith(
									'image/'
									)
								) {
								const reader =
									new FileReader();
								reader.onload =
									(
										e) => {
										const img =
											document
											.createElement(
												'img'
												);
										img.src = e
											.target
											.result;
										img.classList
											.add(
												'{{ $previewClass }}');
										img.style.cssText =
											'{{ $previewStyle }}';
										previewContainer
											.appendChild(
												img
												);
									};
								reader.readAsDataURL(
									file
									);
							}
						});
					} else {
						const file = this.files[0];
						if (file && file.type.startsWith(
								'image/')) {
							const reader =
								new FileReader();
							reader.onload = (e) => {
								previewContainer
									.src =
									e
									.target
									.result;
								previewContainer
									.style
									.display =
									'block';
							};
							reader.readAsDataURL(file);
						}
					}
				});
			}
		});
		</script>
		@endpush
		@endif

		@if($isRange)
		@push('scripts')
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const rangeInput = document.getElementById('{{ $inputId }}');
			const valueDisplay = rangeInput.parentElement.querySelector('.form-text');

			if (rangeInput && valueDisplay) {
				rangeInput.addEventListener('input', function() {
					valueDisplay.textContent = this.value;
				});
			}
		});
		</script>
		@endpush
		@endif
