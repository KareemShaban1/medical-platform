@extends('backend.dashboards.clinic.layouts.app')
@section('title', __('Availability Overrides'))

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					@hasPermission('create availability override')
					<button type="button" class="btn btn-primary" data-bs-toggle="modal"
						data-bs-target="#overrideModal" onclick="resetForm()">
						<i class="mdi mdi-plus"></i> {{ __('Add Override') }}
					</button>
					@endhasPermission
				</div>
				<h4 class="page-title">{{ __('Availability Overrides') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="overrides-table" class="table dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('Doctor') }}</th>
								<th>{{ __('Date') }}</th>
								<th>{{ __('Time Range') }}</th>
								<th>{{ __('Type') }}</th>
								<th>{{ __('Note') }}</th>
								<th>{{ __('Actions') }}</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="overrideModal" tabindex="-1" role="dialog" aria-labelledby="overrideModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="overrideModalLabel">{{ __('Add Override') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="overrideForm" method="POST">
					@csrf
					<input type="hidden" id="overrideId">
					<div class="row">
						<div class="col-12 col-md-6 mb-3">
							<label for="doctor_profile_id"
								class="form-label">{{ __('Doctor') }}</label>
							<select class="form-control select2"
								id="doctor_profile_id"
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
							<label for="date"
								class="form-label">{{ __('Date') }}</label>
							<input type="date" class="form-control" id="date"
								name="date" required>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="start_time"
								class="form-label">{{ __('Start Time') }}</label>
							<input type="time" class="form-control"
								id="start_time" name="start_time">
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="end_time"
								class="form-label">{{ __('End Time') }}</label>
							<input type="time" class="form-control"
								id="end_time" name="end_time">
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<label for="type"
								class="form-label">{{ __('Type') }}</label>
							<select class="form-control" id="type" name="type"
								required>
								<option value="">{{ __('Select Type') }}
								</option>
								<option value="blocked">
									{{ __('Blocked') }}</option>
								<option value="opened">
									{{ __('Opened') }}</option>
							</select>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 mb-3">
							<label for="note"
								class="form-label">{{ __('Note') }}</label>
							<textarea class="form-control" id="note"
								name="note" rows="3"></textarea>
							<div class="invalid-feedback"></div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-light"
							data-bs-dismiss="modal">{{ __('Close') }}</button>
						<button type="submit"
							class="btn btn-primary">{{ __('Save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
let table = $('#overrides-table').DataTable({
	processing: true,
	serverSide: true,
	ajax: {
		url: '{{ route("clinic.availability-overrides.data") }}',
		data: function(d) {
			// Add filters here if needed
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
			data: 'date',
			name: 'date'
		},
		{
			data: 'time_range',
			name: 'time_range'
		},
		{
			data: 'type',
			name: 'type'
		},
		{
			data: 'note',
			name: 'note'
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
				columns: [0, 1, 2, 3, 4, 5]
			}
		},
		{
			extend: 'excel',
			text: 'Excel',
			title: 'Availability Overrides Data',
			exportOptions: {
				columns: [0, 1, 2, 3, 4, 5]
			}
		},
		{
			extend: 'copy',
			exportOptions: {
				columns: [0, 1, 2, 3, 4, 5]
			}
		},
	],
	drawCallback: function() {
		$('.dataTables_paginate > .pagination').addClass(
			'pagination-rounded');
	}
});

// Initialize select2
$('.select2').select2({
	dropdownParent: $('#overrideModal')
});

// Reset form
function resetForm() {
	$('#overrideForm')[0].reset();
	$('#overrideForm').attr('action', '{{ route("clinic.availability-overrides.store") }}');
	$('#overrideId').val('');
	$('#overrideModal .modal-title').text('{{ __("Add Override") }}');
	$('.is-invalid').removeClass('is-invalid');
	$('.invalid-feedback').text('');
}

// Handle Add/Edit Form Submission
$('#overrideForm').on('submit', function(e) {
	e.preventDefault();
	let id = $('#overrideId').val();
	let url = id ?
		'{{ route("clinic.availability-overrides.update", ":id") }}'.replace(':id',
			id) :
		'{{ route("clinic.availability-overrides.store") }}';
	let method = id ? 'PUT' : 'POST';

	let formData = new FormData(this);
	if (method === 'PUT') {
		formData.append('_method', 'PUT');
	}

	$.ajax({
		url: url,
		method: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		success: function(response) {
			$('#overrideModal').modal('hide');
			table.ajax.reload();
			Swal.fire('Success', response.message,
				'success');
		},
		error: function(xhr) {
			if (xhr.status === 422) {
				let errors = xhr.responseJSON
					.errors || {};
				let messages = [];
				Object.keys(errors).forEach(
					function(
						key
					) {
						messages.push(errors[
								key
							]
							[
								0
							]
						);
						let $input =
							$('[name="' +
								key +
								'"]'
							);
						if ($input
							.length
						) {
							$input.addClass(
								'is-invalid'
							);
							$input.next(
									'.invalid-feedback'
								)
								.text(errors[
										key
									]
									[
										0
									]
								);
						}
					});
				Swal.fire({
					icon: 'error',
					title: 'Validation Errors',
					html: messages
						.join(
							'<br>'
						)
				});
			} else {
				Swal.fire('Error', xhr
					.responseJSON
					?.message ||
					'Something went wrong',
					'error');
			}
		}
	});
});

// Edit
function editOverride(id) {
	$.get('{{ route("clinic.availability-overrides.index") }}/' + id, function(data) {
		$('#overrideId').val(data.id);
		$('#doctor_profile_id').val(data.doctor_profile_id).trigger('change');
		$('#date').val(data.date);
		$('#start_time').val(data.start_time);
		$('#end_time').val(data.end_time);
		$('#type').val(data.type);
		$('#note').val(data.note);

		$('#overrideForm').attr('action',
			'{{ route("clinic.availability-overrides.update", ":id") }}'
			.replace(':id', id));
		$('#overrideModal .modal-title').text('{{ __("Edit Override") }}');
		$('#overrideModal').modal('show');
	});
}

// Delete
function deleteOverride(id) {
	Swal.fire({
		title: 'Are you sure?',
		text: "You won't be able to revert this!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: '{{ route("clinic.availability-overrides.destroy", ":id") }}'
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
					Swal.fire('Deleted!',
						response
						.message,
						'success'
					);
				},
				error: function(xhr) {
					Swal.fire('Error!', xhr
						.responseJSON
						?.message ||
						'Something went wrong',
						'error'
					);
				}
			});
		}
	});
}
</script>
@endpush
