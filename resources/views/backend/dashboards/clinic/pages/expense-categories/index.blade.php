@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<button type="button" class="btn btn-primary" data-bs-toggle="modal"
						data-bs-target="#expenseCategoriesModal"
						onclick="resetForm()">
						<i class="mdi mdi-plus"></i>
						{{ __('Add Expense Category') }}
					</button>
				</div>
				<h4 class="page-title">{{ __('Expense Categories') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="expenseCategories-table"
						class="table dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('Name') }}</th>
								<th>{{ __('Status') }}</th>
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
<div class="modal fade" id="expenseCategoriesModal" tabindex="-1" role="dialog"
	aria-labelledby="expenseCategoriesModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="expenseCategoriesModalLabel">
					{{ __('Add Expense Category') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="expenseCategoriesForm" method="POST">
					@csrf
					<input type="hidden" id="expenseCategoriesId">
					<div class="row">
						<div class="col-12 col-md-6 mb-3">
							<label for="name"
								class="form-label">{{ __('Name') }}</label>
							<input type="text" class="form-control" id="name"
								name="name">
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 col-md-6 mb-3">
							<div class="form-check form-switch mt-4">
								<input type="hidden" name="status"
									id="statusHidden" value="0">
								<input type="checkbox"
									class="form-check-input"
									id="statusToggle" value="1">
								<label class="form-check-label"
									for="statusToggle">{{ __('Status') }}</label>

							</div>
						</div>

						<!-- Notes -->
						<div class="col-12 mb-3">
							<label for="notes"
								class="form-label">{{ __('Notes') }}</label>
							<textarea class="form-control" id="notes"
								name="notes"></textarea>
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
let table = $('#expenseCategories-table').DataTable({
	processing: true,
	serverSide: true,
	ajax: '{{ route("clinic.expense-categories.data") }}',
	columns: [{
			data: 'id',
			name: 'id'
		},
		{
			data: 'name',
			name: 'name'
		},
		{
			data: 'status',
			name: 'status'
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
				columns: [0, 1, 2]
			}
		},
		{
			extend: 'excel',
			text: 'Excel',
			title: 'Expense Categories Data',
			exportOptions: {
				columns: [0, 1, 2]
			}
		},
		{
			extend: 'copy',
			exportOptions: {
				columns: [0, 1, 2]
			}
		},
	],
	drawCallback: function() {
		$('.dataTables_paginate > .pagination').addClass(
			'pagination-rounded');
	}
});

// Reset form
function resetForm() {
	$('#expenseCategoriesForm')[0].reset();
	$('#expenseCategoriesForm').attr('action', '{{ route("clinic.expense-categories.store") }}');
	$('#expenseCategoriesId').val('');
	$('#expenseCategoriesModal .modal-title').text('{{ __("Add Expense Category") }}');
}

// Handle Add/Edit Form Submission
$('#expenseCategoriesForm').on('submit', function(e) {
	e.preventDefault();
	let id = $('#expenseCategoriesId').val();
	let url = id ?
		'{{ route("clinic.expense-categories.update", ":id") }}'.replace(':id', id) :
		'{{ route("clinic.expense-categories.store") }}';
	let method = id ? 'PUT' : 'POST';

	$('#statusHidden').val($('#statusToggle').is(':checked') ? 1 : 0);


	$.ajax({
		url: url,
		method: method,
		data: $(this).serialize(),
		success: function(response) {
			$('#expenseCategoriesModal').modal(
				'hide');
			table.ajax.reload();
			Swal.fire('Success', response.message,
				'success');
		},
		error: function(xhr) {
			if (xhr.status === 422) {
				let errors = xhr.responseJSON
					.errors || {};
				// show inline feedback and aggregated alert
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
						// find input (supports nested names like items[0][price])
						let nameSelector =
							'[name="' +
							key +
							'"]';
						let $input =
							$(
								nameSelector
							);
						// fallback for inputs using array syntax:
						if (!$input
							.length
						) {
							// try ends-with matching
							$input = $(
									'#expenseCategoriesForm'
								)
								.find('[name^="' +
									key +
									'"], [name$="' +
									key +
									'"]'
								);
						}
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
				Swal.fire('Error', 'Something went wrong',
					'error');
			}
		}
	});
});

// Edit
function editExpenseCategory(id) {
	$.get('{{ route("clinic.expense-categories.index") }}/' + id, function(data) {
		$('#expenseCategoriesId').val(data.id);
		$('#name').val(data.name);
		$('#statusToggle').prop('checked', data.status);
		$('#notes').val(data.notes);

		$('#expenseCategoriesForm').attr('action',
			'{{ route("clinic.expense-categories.update", ":id") }}'
			.replace(
				':id', id));
		$('#expenseCategoriesModal .modal-title').text(
			'{{ __("Edit Expense Category") }}');
		$('#expenseCategoriesModal').modal('show');
	});
}


// Delete
function deleteExpenseCategory(id) {
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
				url: '{{ route("clinic.expense-categories.destroy", ":id") }}'
					.replace(':id', id),
				id,
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
				}
			});
		}
	});
}


// change status toggle
$(document).on('change', '.toggle-boolean', function(e) {
	let id = $(this).data('id');
	let field = $(this).data('field');
	let value = $(this).is(':checked') ? 1 : 0;

	let url = '{{ route("clinic.expense-categories.update-status", ":id") }}'.replace(':id',
		id);

	$.ajax({
		url: url,
		method: 'PUT',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
				'content')
		},
		data: {
			id: id,
			field: field,
			value: value
		},
		success: function(response) {
			table.ajax.reload(null,
				false
			); // reload but keep current page
			Swal.fire('Success!', response.message,
				'success');
		},
		error: function() {
			Swal.fire('Error!', 'Something went wrong',
				'error');
		}
	});
});
</script>
@endpush