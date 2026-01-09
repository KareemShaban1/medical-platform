@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Appointments'))

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					@hasPermission('view trash appointments')
					<a href="{{ route('clinic.appointments.trash') }}"
						class="btn btn-secondary me-2">
						<i class="fas fa-trash"></i> {{ __('Trash') }}
					</a>
					@endhasPermission
					@hasPermission('create appointment')
					<a href="{{ route('clinic.appointments.analytics', $doctors->first()?->id ?? 0) }}"
						class="btn btn-info me-2">
						<i class="fas fa-chart-bar"></i> {{ __('Analytics') }}
					</a>
					@endhasPermission

					@hasPermission('delete appointment')
					<button type="button" class="btn btn-primary" data-bs-toggle="modal"
						data-bs-target="#createAppointmentModal">
						<i class="fas fa-plus"></i> {{ __('Book Appointment') }}
					</button>
					@endhasPermission
				</div>
				<h4 class="page-title">{{ __('Appointments') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="row g-2 align-items-end mb-3">
						<div class="col-md-3">
							<label
								class="form-label mb-1">{{ __('Start Date') }}</label>
							<input type="date" id="filter_start_date"
								class="form-control"
								value="{{ now()->startOfMonth()->toDateString() }}">
						</div>
						<div class="col-md-3">
							<label
								class="form-label mb-1">{{ __('End Date') }}</label>
							<input type="date" id="filter_end_date"
								class="form-control"
								value="{{ now()->endOfMonth()->toDateString() }}">
						</div>
						<div class="col-md-3 d-flex gap-2">
							<button id="applyFilters"
								class="btn btn-primary w-100"
								type="button">
								<i class="mdi mdi-filter"></i>
								{{ __('Filter') }}
							</button>
							<button id="resetToday"
								class="btn btn-outline-secondary w-100"
								type="button">
								<i class="mdi mdi-calendar-today"></i>
								{{ __('Today') }}
							</button>
						</div>
					</div>
					<table id="appointments-table"
						class="table dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('Doctor') }}</th>
								<th>{{ __('Patient') }}</th>
								<th>{{ __('Date') }}</th>
								<th>{{ __('Time') }}</th>
								<th>{{ __('Visit Type') }}</th>
								<th>{{ __('Slot Number') }}</th>
								<th>{{ __('Status') }}</th>
								<th>{{ __('Prescription Actions') }}
								</th>
								<th>{{ __('Actions') }}</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createAppointmentModalLabel">
					{{ __('Book Appointment') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form id="createAppointmentForm">
				@csrf
				<div class="modal-body">
					<div class="row">
						<div class="col-12 col-md-6 mb-3">
							<label for="create_patient_id"
								class="form-label">{{ __('Patient') }}
								<span
									class="text-danger">*</span></label>
							<select class="form-control select2"
								id="create_patient_id" name="patient_id"
								required>
								<option value="">
									{{ __('Select a Patient') }}
								</option>
								@foreach($patients as $patient)
								<option value="{{ $patient->id }}">
									{{ $patient->user->name ?? 'N/A' }}
								</option>
								@endforeach
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="create_doctor_profile_id"
								class="form-label">{{ __('Doctor') }}
								<span
									class="text-danger">*</span></label>
							<select class="form-control select2"
								id="create_doctor_profile_id"
								name="doctor_profile_id" required>
								<option value="">
									{{ __('Select a Doctor') }}
								</option>
								@foreach($doctors as $doctor)
								<option value="{{ $doctor->id }}">
									{{ $doctor->name }}</option>
								@endforeach
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="create_appointment_date"
								class="form-label">{{ __('Date') }}
								<span
									class="text-danger">*</span></label>
							<input type="date" class="form-control"
								id="create_appointment_date"
								name="appointment_date" required>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="create_period_id"
								class="form-label">{{ __('Time Slot') }}
								<span
									class="text-danger">*</span></label>
							<select class="form-select" id="create_period_id"
								name="period_id" required>
								<option value="">
									{{ __('Select Date & Doctor First') }}
								</option>
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="create_status"
								class="form-label">{{ __('Status') }}</label>
							<select class="form-select" id="create_status"
								name="status">
								<option value="confirmed" selected>
									{{ __('Confirmed') }}</option>
								<option value="pending">
									{{ __('Pending') }}</option>
								<option value="waiting">
									{{ __('Waiting') }}</option>
								<option value="completed">
									{{ __('Completed') }}</option>
								<option value="cancelled">
									{{ __('Cancelled') }}</option>
								<option value="expired">
									{{ __('Expired') }}</option>
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="create_visit_type"
								class="form-label">{{ __('Visit Type') }}</label>
							<select class="form-select" id="create_visit_type"
								name="visit_type">
								@foreach($visitTypes as $value =>
								$label)
								<option value="{{ $value }}"
									{{ $value == 0 ? 'selected' : '' }}>
									{{ __($label) }}</option>
								@endforeach
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="create_cost_amount"
								class="form-label">{{ __('Cost Amount') }}</label>
							<input type="number" step="0.01"
								class="form-control"
								id="create_cost_amount"
								name="cost_amount" placeholder="0.00">
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="create_payment_status"
								class="form-label">{{ __('Payment Status') }}</label>
							<select class="form-select"
								id="create_payment_status"
								name="payment_status">
								<option value="pending" selected>
									{{ __('Pending') }}</option>
								<option value="paid">{{ __('Paid') }}
								</option>
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 mb-3">
							<label for="create_patient_notes"
								class="form-label">{{ __('Patient Notes') }}</label>
							<textarea class="form-control"
								id="create_patient_notes"
								name="patient_notes"
								rows="2"></textarea>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 mb-3">
							<label for="create_doctor_notes"
								class="form-label">{{ __('Doctor Notes') }}</label>
							<textarea class="form-control"
								id="create_doctor_notes"
								name="doctor_notes" rows="2"></textarea>
							<div class="invalid-feedback"></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light"
						data-bs-dismiss="modal">{{ __('Close') }}</button>
					<button type="submit"
						class="btn btn-primary">{{ __('Save Appointment') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Edit Appointment Modal -->
<div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-labelledby="editAppointmentModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editAppointmentModalLabel">
					{{ __('Edit Appointment') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form id="editAppointmentForm">
				@csrf
				@method('PUT')
				<input type="hidden" id="edit_appointment_id">
				<div class="modal-body">
					<!-- Read-only Information -->
					<div class="alert alert-info">
						<i class="mdi mdi-information"></i>
						{{ __('Patient, Doctor, Date, and Time Slot cannot be changed when editing.') }}
					</div>

					<div class="row mb-3">
						<div class="col-12 col-md-6 mb-3">
							<label
								class="form-label fw-bold">{{ __('Patient') }}</label>
							<p class="form-control-plaintext"
								id="edit_display_patient_name"></p>
						</div>
						<div class="col-12 col-md-6 mb-3">
							<label
								class="form-label fw-bold">{{ __('Doctor') }}</label>
							<p class="form-control-plaintext"
								id="edit_display_doctor_name"></p>
						</div>
						<div class="col-12 col-md-6 mb-3">
							<label
								class="form-label fw-bold">{{ __('Date') }}</label>
							<p class="form-control-plaintext"
								id="edit_display_date"></p>
						</div>
						<div class="col-12 col-md-6 mb-3">
							<label
								class="form-label fw-bold">{{ __('Time Slot') }}</label>
							<p class="form-control-plaintext"
								id="edit_display_time_slot"></p>
						</div>
					</div>

					<hr>

					<!-- Editable Fields -->
					<div class="row">
						<div class="col-12 col-md-6 mb-3">
							<label for="edit_status"
								class="form-label">{{ __('Status') }}</label>
							<select class="form-select" id="edit_status"
								name="status">
								<option value="confirmed">
									{{ __('Confirmed') }}</option>
								<option value="pending">
									{{ __('Pending') }}</option>
								<option value="waiting">
									{{ __('Waiting') }}</option>
								<option value="completed">
									{{ __('Completed') }}</option>
								<option value="cancelled">
									{{ __('Cancelled') }}</option>
								<option value="expired">
									{{ __('Expired') }}</option>
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="edit_visit_type"
								class="form-label">{{ __('Visit Type') }}</label>
							<select class="form-select" id="edit_visit_type"
								name="visit_type">
								@foreach($visitTypes as $value =>
								$label)
								<option value="{{ $value }}">
									{{ __($label) }}</option>
								@endforeach
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="edit_cost_amount"
								class="form-label">{{ __('Cost Amount') }}</label>
							<input type="number" step="0.01"
								class="form-control"
								id="edit_cost_amount" name="cost_amount"
								placeholder="0.00">
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="edit_payment_status"
								class="form-label">{{ __('Payment Status') }}</label>
							<select class="form-select"
								id="edit_payment_status"
								name="payment_status">
								<option value="pending">
									{{ __('Pending') }}</option>
								<option value="paid">{{ __('Paid') }}
								</option>
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 mb-3">
							<label for="edit_patient_notes"
								class="form-label">{{ __('Patient Notes') }}</label>
							<textarea class="form-control"
								id="edit_patient_notes"
								name="patient_notes"
								rows="2"></textarea>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 mb-3">
							<label for="edit_doctor_notes"
								class="form-label">{{ __('Doctor Notes') }}</label>
							<textarea class="form-control"
								id="edit_doctor_notes"
								name="doctor_notes" rows="2"></textarea>
							<div class="invalid-feedback"></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light"
						data-bs-dismiss="modal">{{ __('Close') }}</button>
					<button type="submit"
						class="btn btn-primary">{{ __('Update Appointment') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
// ============================================
// DataTable Initialization
// ============================================
let table = $('#appointments-table').DataTable({
	processing: true,
	serverSide: true,
	ajax: {
		url: '{{ route("clinic.appointments.data") }}',
		data: function(d) {
			d.start_date = $('#filter_start_date').val();
			d.end_date = $('#filter_end_date').val();
		}
	},
	columns: [{
			data: 'id',
			name: 'id'
		},
		{
			data: 'doctor_name',
			name: 'doctor_name'
		},
		{
			data: 'patient_name',
			name: 'patient_name'
		},
		{
			data: 'appointment_date',
			name: 'appointment_date'
		},
		{
			data: 'appointment_time',
			name: 'appointment_time'
		},
		{
			data: 'visit_type',
			name: 'visit_type'
		},
		{
			data: 'slot_number',
			name: 'slot_number'
		},
		{
			data: 'status',
			name: 'status'
		},
		{
			data: 'prescription_actions',
			name: 'prescription_actions',
			orderable: false,
			searchable: false
		},
		{
			data: 'action',
			name: 'action',
			orderable: false,
			searchable: false
		},
	],
	order: [
		[0, 'desc']
	],
	dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
	pageLength: 10,
	responsive: true,
	language: languages[language],
	buttons: [{
			extend: 'print',
			exportOptions: {
				columns: [0, 1, 2, 3, 4, 5, 6, 7]
			}
		},
		{
			extend: 'excel',
			text: 'Excel',
			title: 'Appointments Data',
			exportOptions: {
				columns: [0, 1, 2, 3, 4, 5, 6, 7]
			}
		},
		{
			extend: 'copy',
			exportOptions: {
				columns: [0, 1, 2, 3, 4, 5, 6, 7]
			}
		},
	],
	drawCallback: function() {
		$('.dataTables_paginate > .pagination').addClass(
			'pagination-rounded');
	}
});

// Apply and reset filters
$('#applyFilters').on('click', function() {
	table.ajax.reload();
});

$('#resetToday').on('click', function() {
	const today = new Date().toISOString().slice(0, 10);
	$('#filter_start_date').val(today);
	$('#filter_end_date').val(today);
	table.ajax.reload();
});

// ============================================
// CREATE APPOINTMENT - Initialize Select2
// ============================================
$('#create_patient_id, #create_doctor_profile_id').select2({
	dropdownParent: $('#createAppointmentModal')
});

// ============================================
// CREATE APPOINTMENT - Load Available Periods
// ============================================
$('#create_doctor_profile_id, #create_appointment_date').on('change', function() {
	loadAvailablePeriods(
		$('#create_doctor_profile_id').val(),
		$('#create_appointment_date').val(),
		'#create_period_id'
	);
});

// ============================================
// CREATE APPOINTMENT - Form Submission
// ============================================
$('#createAppointmentForm').on('submit', function(e) {
	e.preventDefault();

	let formData = new FormData(this);

	$.ajax({
		url: '{{ route("clinic.appointments.store") }}',
		method: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		success: function(response) {
			$('#createAppointmentModal').modal(
				'hide');
			$('#createAppointmentForm')[0].reset();
			$('#create_patient_id, #create_doctor_profile_id')
				.val(null).trigger('change');
			$('#create_period_id').empty().append(
				'<option value="">{{ __("Select Date & Doctor First") }}</option>'
			);
			table.ajax.reload();
			Swal.fire('{{ __("Success") }}',
				response.message,
				'success');
		},
		error: function(xhr) {
			handleFormErrors(xhr,
				'#createAppointmentForm'
			);
		}
	});
});

// ============================================
// EDIT APPOINTMENT - Open Modal
// ============================================
function editAppointment(id) {
	$.ajax({
		url: '{{ route("clinic.appointments.index") }}/' + id,
		method: 'GET',
		success: function(data) {
			// Set appointment ID
			$('#edit_appointment_id').val(data.id);

			// Display read-only information
			$('#edit_display_patient_name').text(data.patient?.user
				?.name || 'N/A');
			$('#edit_display_doctor_name').text(data.doctor_profile
				?.name || 'N/A');
			$('#edit_display_date').text(data.period?.date || 'N/A');
			$('#edit_display_time_slot').text(data.period ?
				`${data.period.start_time} - ${data.period.end_time}` :
				'N/A');

			// Populate editable fields
			$('#edit_status').val(data.status);
			$('#edit_visit_type').val(data.visit_type);
			$('#edit_cost_amount').val(data.cost_amount);
			$('#edit_payment_status').val(data.payment_status);
			$('#edit_patient_notes').val(data.patient_notes);
			$('#edit_doctor_notes').val(data.doctor_notes);

			// Show modal
			$('#editAppointmentModal').modal('show');
		},
		error: function(xhr) {
			Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message ||
				'{{ __("Failed to load appointment") }}',
				'error');
		}
	});
}

// ============================================
// EDIT APPOINTMENT - Form Submission
// ============================================
$('#editAppointmentForm').on('submit', function(e) {
	e.preventDefault();

	let appointmentId = $('#edit_appointment_id').val();
	let formData = new FormData(this);

	$.ajax({
		url: '{{ route("clinic.appointments.update", ":id") }}'
			.replace(':id', appointmentId),
		method: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		success: function(response) {
			$('#editAppointmentModal').modal(
				'hide');
			table.ajax.reload();
			Swal.fire('{{ __("Success") }}',
				response.message,
				'success');
		},
		error: function(xhr) {
			handleFormErrors(xhr,
				'#editAppointmentForm'
			);
		}
	});
});

// ============================================
// VIEW APPOINTMENT
// ============================================
function viewAppointment(id) {
	window.location.href = '{{ route("clinic.appointments.show", ":id") }}'.replace(':id', id);
}

// ============================================
// DELETE APPOINTMENT (SOFT DELETE)
// ============================================
function deleteAppointment(id) {
	Swal.fire({
		title: '{{ __("Are you sure?") }}',
		text: '{{ __("This will move the appointment to trash. You can restore it later.") }}',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: '{{ __("Yes, delete it!") }}',
		cancelButtonText: '{{ __("Cancel") }}'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: '{{ route("clinic.appointments.destroy", ":id") }}'
					.replace(':id', id),
				method: 'DELETE',
				headers: {
					'X-CSRF-TOKEN': $(
							'meta[name="csrf-token"]'
						)
						.attr('content')
				},
				success: function(response) {
					table.ajax.reload();
					Swal.fire('{{ __("Deleted!") }}',
						response
						.message,
						'success'
					);
				},
				error: function(xhr) {
					Swal.fire('{{ __("Error") }}',
						xhr
						.responseJSON
						?.message ||
						'{{ __("Failed to delete appointment") }}',
						'error'
					);
				}
			});
		}
	});
}

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Load available periods for a doctor on a specific date
 */
function loadAvailablePeriods(doctorId, date, targetSelector) {
	if (!doctorId || !date) {
		$(targetSelector).empty().append(
			'<option value="">{{ __("Select Date & Doctor First") }}</option>');
		return;
	}

	$.ajax({
		url: '{{ route("clinic.appointments.available-periods") }}',
		method: 'GET',
		data: {
			doctor_profile_id: doctorId,
			date: date
		},
		success: function(periods) {
			$(targetSelector).empty();
			if (periods.length > 0) {
				$(targetSelector).append(
					'<option value="">{{ __("Select a Time Slot") }}</option>'
				);
				periods.forEach(function(period) {
					let available =
						period
						.capacity -
						period
						.booked_count;
					$(targetSelector)
						.append(
							`<option value="${period.id}">${period.start_time} - ${period.end_time} (${available} {{ __("slots available") }})</option>`
						);
				});
			} else {
				$(targetSelector).append(
					'<option value="">{{ __("No available slots") }}</option>'
				);
			}
		},
		error: function() {
			Swal.fire('{{ __("Error") }}',
				'{{ __("Failed to load available periods") }}',
				'error');
		}
	});
}

/**
 * Handle form validation errors
 */
function handleFormErrors(xhr, formSelector) {
	if (xhr.status === 422) {
		let errors = xhr.responseJSON.errors || {};
		let messages = [];

		// Clear previous errors
		$(formSelector + ' .is-invalid').removeClass('is-invalid');
		$(formSelector + ' .invalid-feedback').text('');

		// Display errors
		Object.keys(errors).forEach(function(key) {
			messages.push(errors[key][0]);
			let $input = $(formSelector + ' [name="' + key + '"]');
			if ($input.length) {
				$input.addClass('is-invalid');
				$input.next('.invalid-feedback').text(errors[key][0]);
			}
		});

		Swal.fire({
			icon: 'error',
			title: '{{ __("Validation Errors") }}',
			html: messages.join('<br>')
		});
	} else {
		Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message || '{{ __("Something went wrong") }}',
			'error');
	}
}

// ============================================
// MODAL EVENTS
// ============================================

// Reset create form when modal is closed
$('#createAppointmentModal').on('hidden.bs.modal', function() {
	$('#createAppointmentForm')[0].reset();
	$('#create_patient_id, #create_doctor_profile_id').val(null).trigger('change');
	$('#create_period_id').empty().append(
		'<option value="">{{ __("Select Date & Doctor First") }}</option>');
	$('#createAppointmentForm .is-invalid').removeClass('is-invalid');
	$('#createAppointmentForm .invalid-feedback').text('');
});

// Reset edit form when modal is closed
$('#editAppointmentModal').on('hidden.bs.modal', function() {
	$('#editAppointmentForm')[0].reset();
	$('#editAppointmentForm .is-invalid').removeClass('is-invalid');
	$('#editAppointmentForm .invalid-feedback').text('');
});
</script>
@endpush
