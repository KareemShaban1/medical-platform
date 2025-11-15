<div class="application-details">
	<div class="row mb-3">
		<div class="col-md-6">
			<h6 class="text-muted mb-1">{{ __('Status') }}</h6>
			<span class="badge {{ $application->status === 'pending' ? 'bg-warning' : ($application->status === 'accepted' ? 'bg-success' : ($application->status === 'rejected' ? 'bg-danger' : 'bg-info')) }} text-white">
				{{ ucfirst($application->status) }}
			</span>
		</div>
		<div class="col-md-6">
			<h6 class="text-muted mb-1">{{ __('Applied Date') }}</h6>
			<p class="mb-0">{{ $application->created_at->format('M d, Y h:i A') }}</p>
		</div>
	</div>

	<hr>

	<div class="row mb-3">
		<div class="col-12">
			<h5 class="mb-3">{{ __('Applicant Information') }}</h5>
		</div>
		<div class="col-md-6 mb-3">
			<h6 class="text-muted mb-1">{{ __('Name') }}</h6>
			<p class="mb-0">{{ $application->getApplicantName() ?? 'N/A' }}</p>
		</div>
		<div class="col-md-6 mb-3">
			<h6 class="text-muted mb-1">{{ __('Email') }}</h6>
			<p class="mb-0">
				<i class="fa fa-envelope text-muted me-2"></i>
				{{ $application->getApplicantEmail() ?? 'N/A' }}
			</p>
		</div>
		@if($application->getApplicantPhone())
		<div class="col-md-6 mb-3">
			<h6 class="text-muted mb-1">{{ __('Phone') }}</h6>
			<p class="mb-0">
				<i class="fa fa-phone text-muted me-2"></i>
				{{ $application->getApplicantPhone() }}
			</p>
		</div>
		@endif
	</div>

	@if($application->getCvPath())
	<hr>
	<div class="row mb-3">
		<div class="col-12">
			<h6 class="text-muted mb-2">{{ __('CV/Resume') }}</h6>
			<a href="{{ asset('storage/' . $application->getCvPath()) }}"
				target="_blank"
				class="btn btn-sm btn-outline-primary">
				<i class="fa fa-download"></i>
				{{ __('Download CV') }}
			</a>
		</div>
	</div>
	@endif

	<hr>
	<div class="row mb-3">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center mb-2">
				<h6 class="text-muted mb-0">{{ __('Notes') }}</h6>
				<button type="button" class="btn btn-sm btn-outline-primary edit-notes-btn" data-application-id="{{ $application->id }}">
					<i class="fa fa-edit"></i> {{ __('Edit') }}
				</button>
			</div>
			<div id="notes-display-{{ $application->id }}" class="notes-display">
				<p class="text-muted mb-0">{{ $application->notes ?: __('No notes added yet.') }}</p>
			</div>
			<div id="notes-edit-{{ $application->id }}" class="notes-edit" style="display: none;">
				<textarea class="form-control" id="notes-textarea-{{ $application->id }}" rows="4">{{ $application->notes }}</textarea>
				<div class="mt-2">
					<button type="button" class="btn btn-sm btn-success save-notes-btn" data-application-id="{{ $application->id }}">
						<i class="fa fa-save"></i> {{ __('Save') }}
					</button>
					<button type="button" class="btn btn-sm btn-secondary cancel-notes-btn" data-application-id="{{ $application->id }}">
						<i class="fa fa-times"></i> {{ __('Cancel') }}
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Dynamic Fields -->
	@if($application->applicant_data)
	@php
		$excludedFields = ['name', 'email', 'phone', 'cv'];
		$hasDynamicFields = false;
		foreach($application->applicant_data as $key => $value) {
			if(!in_array($key, $excludedFields) && $value) {
				$hasDynamicFields = true;
				break;
			}
		}
	@endphp
	@if($hasDynamicFields)
	<hr>
	<div class="row mb-3">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h5 class="mb-0">{{ __('Additional Information') }}</h5>
				<button type="button" class="btn btn-sm btn-outline-primary edit-data-btn" data-application-id="{{ $application->id }}">
					<i class="fa fa-edit"></i> {{ __('Edit') }}
				</button>
			</div>
		</div>
		@foreach($application->applicant_data as $key => $value)
		@if(!in_array($key, $excludedFields) && $value)
		<div class="col-md-6 mb-3" data-field-key="{{ $key }}">
			<h6 class="text-muted mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</h6>
			<div class="field-display" id="field-display-{{ $application->id }}-{{ $key }}">
				<p class="mb-0">
					@if(is_array($value))
						{{ implode(', ', $value) }}
					@elseif(filter_var($value, FILTER_VALIDATE_URL) || (strpos($value, 'storage/') !== false))
						<a href="{{ asset('storage/' . $value) }}" target="_blank" class="btn btn-sm btn-outline-primary">
							<i class="fa fa-download"></i> {{ __('Download File') }}
						</a>
					@else
						{{ $value }}
					@endif
				</p>
			</div>
			<div class="field-edit" id="field-edit-{{ $application->id }}-{{ $key }}" style="display: none;">
				@if(is_array($value))
					<input type="text" class="form-control form-control-sm field-input" 
						data-field-key="{{ $key }}" 
						value="{{ implode(', ', $value) }}" 
						placeholder="{{ ucfirst(str_replace('_', ' ', $key)) }}">
					<small class="text-muted">{{ __('Separate multiple values with commas') }}</small>
				@elseif(filter_var($value, FILTER_VALIDATE_URL) || (strpos($value, 'storage/') !== false))
					<p class="text-muted small mb-0">{{ __('File fields cannot be edited') }}</p>
				@else
					<input type="text" class="form-control form-control-sm field-input" 
						data-field-key="{{ $key }}" 
						value="{{ $value }}" 
						placeholder="{{ ucfirst(str_replace('_', ' ', $key)) }}">
				@endif
			</div>
		</div>
		@endif
		@endforeach
		<div id="data-edit-actions-{{ $application->id }}" class="col-12 mt-2" style="display: none;">
			<button type="button" class="btn btn-sm btn-success save-data-btn" data-application-id="{{ $application->id }}">
				<i class="fa fa-save"></i> {{ __('Save Changes') }}
			</button>
			<button type="button" class="btn btn-sm btn-secondary cancel-data-btn" data-application-id="{{ $application->id }}">
				<i class="fa fa-times"></i> {{ __('Cancel') }}
			</button>
		</div>
	</div>
	@endif
	@endif

	<hr>

	<div class="row">
		<div class="col-12">
			<h5 class="mb-3">{{ __('Job Information') }}</h5>
		</div>
		<div class="col-md-6 mb-3">
			<h6 class="text-muted mb-1">{{ __('Job Title') }}</h6>
			<p class="mb-0">{{ $application->job->title ?? 'N/A' }}</p>
		</div>
		@if($application->job)
		<div class="col-md-6 mb-3">
			<h6 class="text-muted mb-1">{{ __('Job Status') }}</h6>
			<p class="mb-0">
				<span class="badge {{ $application->job->status ? 'bg-success' : 'bg-secondary' }} text-white">
					{{ $application->job->status ? __('Active') : __('Inactive') }}
				</span>
			</p>
		</div>
		@endif
	</div>
</div>

