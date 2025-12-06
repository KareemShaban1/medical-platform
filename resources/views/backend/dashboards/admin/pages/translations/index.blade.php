@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<button type="button" class="btn btn-primary" data-bs-toggle="modal"
						data-bs-target="#translationModal" onclick="resetForm()">
						<i class="mdi mdi-plus"></i> {{ __('Add Translation') }}
					</button>
					<!-- <button type="button" class="btn btn-success"
						onclick="scanTranslations()">
						<i class="mdi mdi-refresh"></i>
						{{ __('Scan Translations') }}
					</button> -->
				</div>
				<h4 class="page-title">{{ __('Translations Management') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<!-- Language Tabs -->
					<ul class="nav nav-tabs nav-bordered mb-3" id="localeTabs"
						role="tablist">
						@foreach($locales as $index => $loc)
						<li class="nav-item" role="presentation">
							<button class="nav-link {{ $locale == $loc ? 'active' : '' }}"
								id="tab-{{ $loc }}" data-bs-toggle="tab"
								data-bs-target="#content-{{ $loc }}"
								type="button" role="tab"
								onclick="switchLocale('{{ $loc }}')">
								{{ strtoupper($loc) }}
							</button>
						</li>
						@endforeach
					</ul>

					<!-- Tab Content -->
					<div class="tab-content" id="localeTabContent">
						@foreach($locales as $loc)
						<div class="tab-pane fade {{ $locale == $loc ? 'show active' : '' }}"
							id="content-{{ $loc }}" role="tabpanel">

							<!-- Search Bar -->
							<div class="row mb-3">
								<div class="col-md-12">
									<label for="searchInput-{{ $loc }}"
										class="form-label">{{ __('Search') }}</label>
									<div class="input-group">
										<span
											class="input-group-text">
											<i
												class="mdi mdi-magnify"></i>
										</span>
										<input type="text"
											id="searchInput-{{ $loc }}"
											class="form-control search-input"
											data-locale="{{ $loc }}"
											placeholder="{{ __('Search by key, value, or file path...') }}"
											autocomplete="off">
										<button type="button"
											class="btn btn-outline-secondary"
											onclick="clearSearch('{{ $loc }}')"
											title="{{ __('Clear') }}">
											<i
												class="mdi mdi-close"></i>
										</button>
									</div>
									<small
										class="form-text text-muted">{{ __('Type to search instantly...') }}</small>
								</div>
							</div>

							<!-- Translations Table -->
							<table id="translations-table-{{ $loc }}"
								class="table dt-responsive nowrap w-100">
								<thead>
									<tr>
										<th>{{ __('Key') }}
										</th>
										<th>{{ __('Value') }}
										</th>
										<th>{{ __('File') }}
										</th>
										<th>{{ __('File Path') }}
										</th>
										<th>{{ __('Type') }}
										</th>
										<th>{{ __('Actions') }}
										</th>
									</tr>
								</thead>
							</table>
						</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Add/Edit Translation Modal -->
<div class="modal fade" id="translationModal" tabindex="-1" role="dialog" aria-labelledby="translationModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="translationModalLabel">{{ __('Add Translation') }}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="translationForm" method="POST">
					@csrf
					<input type="hidden" id="translationId">

					<div class="row">
						<div class="col-12 mb-3">
							<label for="modalLocale"
								class="form-label">{{ __('Locale') }}
								<span
									class="text-danger">*</span></label>
							<select class="form-select" id="modalLocale"
								name="locale" required>
								@foreach($locales as $loc)
								<option value="{{ $loc }}"
									{{ $locale == $loc ? 'selected' : '' }}>
									{{ strtoupper($loc) }}
								</option>
								@endforeach
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 mb-3">
							<label for="translationFile"
								class="form-label">{{ __('File') }}
								({{ __('Optional') }})</label>
							<input type="text" class="form-control"
								id="translationFile" name="file"
								placeholder="{{ __('e.g., legal, validation') }}">
							<small
								class="form-text text-muted">{{ __('Leave empty for JSON translations') }}</small>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 mb-3">
							<label for="translationKey"
								class="form-label">{{ __('Key') }} <span
									class="text-danger">*</span></label>
							<input type="text" class="form-control"
								id="translationKey" name="key" required
								placeholder="{{ __('e.g., welcome.message or legal.title') }}">
							<small
								class="form-text text-muted">{{ __('Use dot notation for nested keys') }}</small>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 mb-3">
							<label for="translationValue"
								class="form-label">{{ __('Value') }}
								<span
									class="text-danger">*</span></label>
							<textarea class="form-control"
								id="translationValue" name="value"
								rows="4" required
								placeholder="{{ __('Enter translation value...') }}"></textarea>
							<div class="invalid-feedback"></div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary"
					data-bs-dismiss="modal">{{ __('Close') }}</button>
				<button type="button" class="btn btn-primary"
					onclick="saveTranslation()">{{ __('Save') }}</button>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
let tables = {};
let currentLocale = '{{ $locale }}';
let searchTimeouts = {};

// Initialize DataTables for each locale
@foreach($locales as $loc)
tables['{{ $loc }}'] = $('#translations-table-{{ $loc }}').DataTable({
	ajax: {
		url: '{{ route("admin.translations.data") }}',
		data: function(d) {
			d.locale = '{{ $loc }}';
			d.search = $('#searchInput-{{ $loc }}').val();
		}
	},
	columns: [{
			data: 'key',
			name: 'key'
		},
		{
			data: 'value',
			name: 'value',
			render: function(data, type, row) {
				if (type === 'display' && data && data
					.length > 100) {
					return '<span title="' + (
							data || ''
							).replace(
							/"/g,
							'&quot;'
							) + '">' +
						(data || '')
						.substring(0, 100) +
						'...</span>';
				}
				return data || '';
			}
		},
		{
			data: 'file',
			name: 'file',
			render: function(data) {
				return data ||
					'<span class="text-muted">JSON</span>';
			}
		},
		{
			data: 'file_path',
			name: 'file_path',
			render: function(data) {
				if (!data)
			return '<span class="text-muted">-</span>';
				return '<code class="text-primary" style="direction:ltr; font-size: 0.85em;" title="' +
					data + '">' + data +
					'</code>';
			}
		},
		{
			data: 'type',
			name: 'type',
			render: function(data) {
				return '<span class="badge bg-' + (
						data === 'php' ?
						'primary' : 'info'
						) + '">' + (data ||
						'').toUpperCase() +
					'</span>';
			}
		},
		{
			data: 'actions',
			name: 'actions',
			orderable: false,
			searchable: false
		},
	],
	order: [
		[0, 'asc']
	],
	dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
	pageLength: 25,
	responsive: true,
	language: languages[language],
	buttons: [{
			extend: 'print',
			exportOptions: {
				columns: [0, 1, 2, 3, 4]
			}
		},
		{
			extend: 'excel',
			text: 'Excel',
			title: 'Translations Data',
			exportOptions: {
				columns: [0, 1, 2, 3, 4]
			}
		},
		{
			extend: 'copy',
			exportOptions: {
				columns: [0, 1, 2, 3, 4]
			}
		},
	],
	drawCallback: function() {
		$('.dataTables_paginate > .pagination').addClass(
		'pagination-rounded');
	}
});
@endforeach

// Switch locale
function switchLocale(locale) {
	currentLocale = locale;
	// Reload the table for the new locale
	if (tables[locale]) {
		tables[locale].ajax.reload();
	}
}

// Live search with debounce
$('.search-input').on('input', function() {
	const locale = $(this).data('locale');
	const searchInput = $(this);

	// Clear existing timeout
	if (searchTimeouts[locale]) {
		clearTimeout(searchTimeouts[locale]);
	}

	// Set new timeout for debounce (500ms)
	searchTimeouts[locale] = setTimeout(function() {
		if (tables[locale]) {
			tables[locale].ajax.reload();
		}
	}, 500);
});

// Clear search
function clearSearch(locale) {
	$('#searchInput-' + locale).val('');
	if (tables[locale]) {
		tables[locale].ajax.reload();
	}
}

// Get current table
function getCurrentTable() {
	return tables[currentLocale];
}

// Reset form
function resetForm() {
	$('#translationForm')[0].reset();
	$('#translationId').val('');
	$('#translationModalLabel').text('{{ __("Add Translation") }}');
	$('#modalLocale').val(currentLocale);
}

// Save translation
function saveTranslation() {
	const form = $('#translationForm');
	const formData = {
		locale: $('#modalLocale').val(),
		key: $('#translationKey').val(),
		value: $('#translationValue').val(),
		file: $('#translationFile').val() || null,
		_token: $('meta[name="csrf-token"]').attr('content')
	};

	if (!formData.key || !formData.value) {
		Swal.fire({
			icon: 'error',
			title: '{{ __("Validation Error") }}',
			text: '{{ __("Please fill in all required fields") }}'
		});
		return;
	}

	const url = $('#translationId').val() ?
		'{{ route("admin.translations.update") }}' :
		'{{ route("admin.translations.store") }}';

	$.ajax({
		url: url,
		method: 'POST',
		data: formData,
		success: function(response) {
			if (response.success) {
				$('#translationModal').modal('hide');
				// Reload all tables
				Object.keys(tables).forEach(function(locale) {
					if (tables[
						locale]) {
						tables[locale]
							.ajax
							.reload();
					}
				});
				Swal.fire({
					icon: 'success',
					title: '{{ __("Success") }}',
					text: response
						.message
				});
			} else {
				Swal.fire({
					icon: 'error',
					title: '{{ __("Error") }}',
					text: response
						.message
				});
			}
		},
		error: function(xhr) {
			const message = xhr.responseJSON?.message ||
				'{{ __("An error occurred") }}';
			Swal.fire({
				icon: 'error',
				title: '{{ __("Error") }}',
				text: message
			});
		}
	});
}

// Edit translation
function editTranslation(key, value, file, type) {
	$('#translationId').val(key);
	$('#translationKey').val(key);
	$('#translationValue').val(value);
	$('#translationFile').val(file || '');
	$('#modalLocale').val(currentLocale);
	$('#translationModalLabel').text('{{ __("Edit Translation") }}');

	// Disable key and file editing for existing translations
	$('#translationKey').prop('readonly', true);
	$('#translationFile').prop('readonly', true);

	$('#translationModal').modal('show');
}

// Delete translation
function deleteTranslation(key, file) {
	Swal.fire({
		title: '{{ __("Are you sure?") }}',
		text: "{{ __('You will not be able to revert this!') }}",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: '{{ __("Yes, delete it!") }}'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: '{{ route("admin.translations.destroy") }}',
				method: 'DELETE',
				data: {
					locale: currentLocale,
					key: key,
					file: file || null,
					_token: $('meta[name="csrf-token"]')
						.attr('content')
				},
				success: function(response) {
					if (response
						.success) {
						if (tables[
								currentLocale]) {
							tables[currentLocale]
								.ajax
								.reload();
						}
						Swal.fire({
							icon: 'success',
							title: '{{ __("Deleted!") }}',
							text: response
								.message
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: '{{ __("Error") }}',
							text: response
								.message
						});
					}
				},
				error: function(xhr) {
					const message = xhr
						.responseJSON
						?.message ||
						'{{ __("An error occurred") }}';
					Swal.fire({
						icon: 'error',
						title: '{{ __("Error") }}',
						text: message
					});
				}
			});
		}
	});
}

// Scan translations
function scanTranslations() {
	Swal.fire({
		title: '{{ __("Scanning Translations") }}',
		text: '{{ __("This may take a few moments...") }}',
		icon: 'info',
		showConfirmButton: false,
		allowOutsideClick: false,
		didOpen: () => {
			Swal.showLoading();
		}
	});

	$.ajax({
		url: '{{ route("admin.translations.scan") }}',
		method: 'POST',
		data: {
			_token: $('meta[name="csrf-token"]').attr('content')
		},
		success: function(response) {
			Swal.fire({
				icon: 'success',
				title: '{{ __("Success") }}',
				text: response.message ||
					'{{ __("Translations scanned successfully") }}',
				confirmButtonText: '{{ __("OK") }}'
			}).then(() => {
				// Reload all tables
				Object.keys(tables).forEach(
					function(
						locale) {
						if (tables[
								locale]) {
							tables[locale]
								.ajax
								.reload();
						}
					});
			});
		},
		error: function(xhr) {
			const message = xhr.responseJSON?.message ||
				'{{ __("An error occurred while scanning") }}';
			Swal.fire({
				icon: 'error',
				title: '{{ __("Error") }}',
				text: message
			});
		}
	});
}

// Reset form when modal is closed
$('#translationModal').on('hidden.bs.modal', function() {
	resetForm();
	$('#translationKey').prop('readonly', false);
	$('#translationFile').prop('readonly', false);
});
</script>
@endpush