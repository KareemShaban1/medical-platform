@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					@hasPermission('create doctor profiles')
					<a href="{{ route('clinic.doctor-profiles.create') }}"
						class="btn btn-primary">
						<i class="mdi mdi-plus"></i> {{ __('Create Profile') }}
					</a>
					@endhasPermission
					@hasPermission('view trash doctor profiles')
					<a href="{{ route('clinic.doctor-profiles.trash') }}"
						class="btn btn-secondary">
						<i class="fas fa-trash"></i> {{ __('Trash') }}
					</a>
					@endhasPermission
				</div>
				<h4 class="page-title">{{ __('Doctor Profile Management') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="doctor-profiles-table"
						class="table table-striped dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('Photo') }}</th>
								<th>{{ __('Name') }}</th>
								<th>{{ __('Email') }}</th>
								<th>{{ __('Phone') }}</th>
								<th>{{ __('Speciality') }}</th>
								<th>{{ __('Years Exp.') }}</th>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
	// Initialize DataTable
	var table = $('#doctor-profiles-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '{{ route("clinic.doctor-profiles.data") }}',
		columns: [{
				data: 'profile_photo',
				name: 'profile_photo',
				orderable: false,
				searchable: false
			},
			{
				data: 'name',
				name: 'name'
			},
			{
				data: 'email',
				name: 'email'
			},
			{
				data: 'phone',
				name: 'phone'
			},
			{
				data: 'speciality',
				name: 'speciality.name_en'
			},
			{
				data: 'years_experience',
				name: 'years_experience'
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
			}
		],
		order: [
			[1, 'asc']
		],
		dom: 'Bfrtip',
		buttons: [{
				extend: 'print',
				exportOptions: {
					columns: [1, 2, 3, 4,
						5,
						6
					]
				}
			},
			{
				extend: 'excel',
				exportOptions: {
					columns: [1, 2, 3, 4,
						5,
						6
					]
				}
			},
			'copy'
		]
	});
});

// ============================================
// SUBMIT PROFILE FOR REVIEW
// ============================================
function submitProfile(id) {
	Swal.fire({
		title: '{{ __("Submit Profile for Review?") }}',
		text: '{{ __("Your profile will be submitted to admins for review and approval.") }}',
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: '{{ __("Yes, submit it!") }}',
		cancelButtonText: '{{ __("Cancel") }}'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: '{{ route("clinic.doctor-profiles.submit", ":id") }}'
					.replace(':id', id),
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': $(
							'meta[name="csrf-token"]'
						)
						.attr('content')
				},
				success: function(response) {
					$('#doctor-profiles-table')
						.DataTable()
						.ajax
						.reload();
					Swal.fire('{{ __("Submitted!") }}',
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
						'{{ __("Something went wrong") }}',
						'error'
					);
				}
			});
		}
	});
}

// ============================================
// DELETE PROFILE (SOFT DELETE)
// ============================================
function deleteProfile(id) {
	Swal.fire({
		title: '{{ __("Are you sure?") }}',
		text: '{{ __("This will move the profile to trash. You can restore it later.") }}',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: '{{ __("Yes, delete it!") }}',
		cancelButtonText: '{{ __("Cancel") }}'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: '{{ route("clinic.doctor-profiles.destroy", ":id") }}'
					.replace(':id', id),
				method: 'DELETE',
				headers: {
					'X-CSRF-TOKEN': $(
							'meta[name="csrf-token"]'
						)
						.attr('content')
				},
				success: function(response) {
					$('#doctor-profiles-table')
						.DataTable()
						.ajax
						.reload();
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
						'{{ __("Failed to delete profile") }}',
						'error'
					);
				}
			});
		}
	});
}
</script>
@endpush