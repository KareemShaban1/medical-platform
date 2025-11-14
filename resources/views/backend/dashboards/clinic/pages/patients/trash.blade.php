@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<a href="{{ route('clinic.patients.index') }}" class="btn btn-secondary">
						<i class="mdi mdi-arrow-left"></i> {{ __('Back to Patients') }}
					</a>
				</div>
				<h4 class="page-title">{{ __('Trash Patients') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="patients-trash-table" class="table table-striped dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('Name') }}</th>
								<th>{{ __('Phone') }}</th>
								<th>{{ __('Email') }}</th>
								<th>{{ __('Assigned Doctors') }}</th>
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
let trashTable = $('#patients-trash-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("clinic.patients.trash.data") }}',
    columns: [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'phone', name: 'phone' },
        { data: 'email', name: 'email' },
        { data: 'assigned_doctors', name: 'assigned_doctors', orderable: false, searchable: false },
        { data: 'trash_action', name: 'trash_action', orderable: false, searchable: false },
    ],
    order: [[0, 'desc']],
    responsive: true,
    language: languages[language] || {},
    drawCallback: function() {
        $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
    }
});

function restore(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("Do you want to restore this patient to this clinic?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("Yes, restore it!") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.patients.restore", ":id") }}'.replace(':id', id),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    trashTable.ajax.reload();
                    Swal.fire('Restored!', response.message || '{{ __("Patient restored successfully") }}', 'success');
                },
                error: function() {
                    Swal.fire('Error!', '{{ __("Something went wrong") }}', 'error');
                }
            });
        }
    });
}

function forceDelete(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("This will permanently delete the assignment of this patient in this clinic.") }}',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '{{ __("Yes, delete permanently!") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.patients.force-delete", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    trashTable.ajax.reload();
                    Swal.fire('Deleted!', response.message || '{{ __("Patient assignment deleted permanently") }}', 'success');
                },
                error: function() {
                    Swal.fire('Error!', '{{ __("Something went wrong") }}', 'error');
                }
            });
        }
    });
}
</script>
@endpush

