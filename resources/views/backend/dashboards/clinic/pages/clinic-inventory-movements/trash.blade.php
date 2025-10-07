@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">

				</div>
				<h4 class="page-title">{{ __('Trash') }}</h4>
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
								<th>{{ __('Quantity') }}</th>
								<th>{{ __('Type') }}</th>
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
let trashTable = $('#clinic-inventory-movement-table').DataTable({
	processing: true,
	serverSide: true,
	ajax: '{{ route("clinic.clinic-inventory-movements.trash.data") }}',
	columns: [{
			data: 'id',
			name: 'id'
		},
		{
			data: 'clinic_inventory',
			name: 'clinic_inventory'
		},
		{
			data: 'quantity',
			name: 'quantity'
		},
		{
			data: 'type',
			name: 'type'
		},
		{
			data: 'movement_date',
			name: 'movement_date'
		},
		{
			data: 'trash_action',
			name: 'trash_action',
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
				columns: [0, 1, 2, 3, 4]
			}
		},
		{
			extend: 'excel',
			text: 'Excel',
			title: 'Clinic Inventory Movements Data',
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


// Restore Product
function restore(id) {
	Swal.fire({
		title: 'Are you sure?',
		text: "Do you want to restore this clinic inventory movement?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, restore it!'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: '{{ route("clinic.clinic-inventory-movements.restore", ":id") }}'
					.replace(':id', id),
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': $(
							'meta[name="csrf-token"]'
						)
						.attr('content')
				},
				success: function(response) {
					trashTable.ajax
						.reload();
					Swal.fire('Restored!',
						response
						.message,
						'success'
					);
				}
			});
		}
	});
}

// Force Delete Product
function forceDelete(id) {
	Swal.fire({
		title: 'Are you sure?',
		text: "This will permanently delete the clinic inventory movement. You won't be able to revert this!",
		icon: 'error',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		cancelButtonColor: '#3085d6',
		confirmButtonText: 'Yes, delete permanently!'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: '{{ route("clinic.clinic-inventory-movements.force-delete", ":id") }}'
					.replace(':id', id),
				method: 'DELETE',
				headers: {
					'X-CSRF-TOKEN': $(
							'meta[name="csrf-token"]'
						)
						.attr('content')
				},
				success: function(response) {
					trashTable.ajax
						.reload();
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
</script>
@endpush