@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<a href="{{ route('clinic.clinic-inventory-movements.create', $id) }}"
						class="btn btn-primary">
						<i class="mdi mdi-plus"></i>
						{{ __('Add Clinic Inventory Movement') }}
					</a>
					<!-- trash -->
					<a href="{{ route('clinic.clinic-inventory-movements.trash') }}"
						class="btn btn-danger">
						<i class="mdi mdi-trash-can"></i>
						{{ __('Trash') }}
					</a>
				</div>
				<h4 class="page-title">{{ __('Clinic Inventory Movements') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="clinic-inventory-movement-table"
						class="table dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('Clinic Inventory') }}</th>
								<th>{{ __('Type') }}</th>
								<th>{{ __('Quantity') }}</th>
								<th>{{ __('Movement Date') }}</th>
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
let table = $('#clinic-inventory-movement-table').DataTable({
	processing: true,
	serverSide: true,
	ajax: '{{ route("clinic.clinic-inventory-movements.data", ":id") }}'.replace(':id',
		'{{ $id }}'),
	columns: [{
			data: 'id',
			name: 'id'
		},
		{
			data: 'clinic_inventory',
			name: 'clinic_inventory'
		},
		{
			data: 'type',
			name: 'type'
		}, {
			data: 'quantity',
			name: 'quantity'
		}, {
			data: 'movement_date',
			name: 'movement_date'
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
			title: 'Clinic Inventories Data',
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
function deleteClinicInventoryMovement(id) {
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
				url: '{{ route("clinic.clinic-inventory-movements.destroy", ":id") }}'
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