@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					@hasPermission('create expense')
					<a href="{{ route('clinic.expenses.create') }}"
						class="btn btn-primary">
						<i class="mdi mdi-plus"></i>
						{{ __('Add Expense') }}
					</a>
					@endhasPermission
					@hasPermission('view expenses')
					<a href="{{ route('clinic.expenses.analytics') }}"
						class="btn btn-info text-white">
						<i class="mdi mdi-chart-line"></i>
						{{ __('Expenses Analytics') }}
					</a>
					@endhasPermission
				</div>
				<h4 class="page-title">{{ __('Expenses') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="expenses-table" class="table dt-responsive nowrap w-100">
						<thead>

							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('Category') }}</th>
								<th>{{ __('Amount') }}</th>
								<th>{{ __('Expense Date') }}</th>
								<th>{{ __('Actions') }}</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection

@push('scripts')
<script>
let table = $('#expenses-table').DataTable({
	processing: true,
	serverSide: true,
	ajax: '{{ route("clinic.expenses.data") }}',
	columns: [{
			data: 'id',
			name: 'id'
		},
		{
			data: 'category',
			name: 'category'
		},
		{
			data: 'amount',
			name: 'amount'
		}, {
			data: 'expense_date',
			name: 'expense_date'
		}, {
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
			title: 'Expenses Data',
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


// Delete
function deleteExpense(id) {
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
				url: '{{ route("clinic.expenses.destroy", ":id") }}'
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
					Swal.fire('Error!', 'Something went wrong',
						'error'
					);
				}
			});
		}
	});
}
</script>
@endpush
