@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<a href="{{ route('clinic.clinic-user-salaries.create') }}"
						class="btn btn-primary">
						<i class="mdi mdi-plus"></i>
						{{ __('Add Clinic User Salary') }}
					</a>
				</div>
				<h4 class="page-title">{{ __('Clinic User Salaries') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="clinic-user-salary-table"
						class="table dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('User') }}</th>
								<th>{{ __('Amount') }}</th>
								<th>{{ __('Salary Frequency') }}</th>
								<th>{{ __('Amount per Salary Frequency') }}
								</th>
								<th>{{ __('Start Date') }}</th>
								<th>{{ __('End Date') }}</th>
								<th>{{ __('Paid') }}</th>
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
let table = $('#clinic-user-salary-table').DataTable({
	processing: true,
	serverSide: true,
	ajax: '{{ route("clinic.clinic-user-salaries.data") }}',
	columns: [{
			data: 'id',
			name: 'id'
		},
		{
			data: 'user',
			name: 'user'
		},
		{
			data: 'amount',
			name: 'amount'
		}, {
			data: 'salary_frequency',
			name: 'salary_frequency'
		}, {
			data: 'amount_per_salary_frequency',
			name: 'amount_per_salary_frequency'
		},
		{
			data: 'start_date',
			name: 'start_date'
		},
		{
			data: 'end_date',
			name: 'end_date'
		},
		{
			data: 'paid',
			name: 'paid'
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
			title: 'Clinic User Salaries Data',
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
function deleteClinicUserSalary(id) {
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
				url: '{{ route("clinic.clinic-user-salaries.destroy", ":id") }}'
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