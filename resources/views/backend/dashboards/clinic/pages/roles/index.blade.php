@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					@hasPermission('create role')
					<button type="button" class="btn btn-primary" data-bs-toggle="modal"
						data-bs-target="#rolesModal" onclick="resetForm()">
						<i class="mdi mdi-plus"></i> {{ __('Add Role') }}
					</button>
					@endhasPermission
				</div>
				<h4 class="page-title">{{ __('Roles Management') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="roles-table" class="table dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('Name') }}</th>
								<th>{{ __('Permissions Count') }}</th>
								<th>{{ __('Created At') }}</th>
								<th>{{ __('Actions') }}</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="rolesModal" tabindex="-1" role="dialog" aria-labelledby="rolesModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="rolesModalLabel">{{ __('Add Role') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="rolesForm" method="POST">
					@csrf
					<input type="hidden" id="rolesId">
					<div class="row">
						<div class="col-12 mb-3">
							<label for="name"
								class="form-label">{{ __('Role Name') }}</label>
							<input type="text" class="form-control" id="name"
								name="name" required>
							<div class="invalid-feedback"></div>
						</div>

						<div class="col-12 mb-3">
							<label
								class="form-label">{{ __('Permissions') }}</label>

							<div class="mb-3">
								<div class="row align-items-end">
									<div class="col-md-6">
										<input type="text"
											class="form-control"
											id="permissionSearch"
											placeholder="{{ __('Search permissions...') }}">
									</div>
									<div class="col-md-3">
										<div
											class="form-check">
											<input class="form-check-input"
												type="checkbox"
												id="selectAllPermissions">
											<label class="form-check-label"
												for="selectAllPermissions">
												<strong>{{ __('Select All') }}</strong>
											</label>
										</div>
									</div>
									<div class="col-md-3">
										<div
											class="form-check">
											<input class="form-check-input"
												type="checkbox"
												id="expandAllGroups">
											<label class="form-check-label"
												for="expandAllGroups">
												<strong>{{ __('Expand All') }}</strong>
											</label>
										</div>
									</div>
								</div>
							</div>

							<div id="permissionsContainer"
								style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 15px;">
								@php
								$groupedPermissions = [];

								foreach($permissions as $permission) {
									$group = $permission->group ?? 'Other';
									if (!isset($groupedPermissions[$group])) {
										$groupedPermissions[$group] = [];
									}
									$groupedPermissions[$group][] = $permission;
								}

								ksort($groupedPermissions);
								@endphp

								@foreach($groupedPermissions as $group => $perms)
								<div class="permission-group mb-4"
									data-resource="{{ strtolower($group) }}">
									<h6
										class="mb-2 text-primary border-bottom pb-2">
										<i
											class="mdi mdi-folder-outline"></i>
										{{ $group }}
										<button type="button"
											class="btn btn-sm btn-link p-0 ms-2 toggle-group"
											data-group="{{ $group }}"
											style="font-size: 0.75rem;">
											<span
												class="expand-text">{{ __('Expand') }}</span>
											<span
												class="collapse-text d-none">{{ __('Collapse') }}</span>
										</button>
									</h6>
									<div class="row group-permissions"
										data-group="{{ $group }}"
										style="display: none;">
										@foreach($perms as $permission)
										<div class="col-md-6 col-lg-4 mb-2 permission-item"
											data-name="{{ strtolower($permission->name) }}">
											<div
												class="form-check">
												<input class="form-check-input permission-check group-{{ str_replace(' ', '-', strtolower($group)) }}"
													type="checkbox"
													name="permissions[]"
													value="{{ $permission->id }}"
													id="permission_{{ $permission->id }}"
													data-resource="{{ strtolower($group) }}">
												<label class="form-check-label"
													for="permission_{{ $permission->id }}">
													{{ $permission->name }}
												</label>
											</div>
										</div>
										@endforeach
									</div>
								</div>
								@endforeach
							</div>
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
let table = $('#roles-table').DataTable({
	ajax: '{{ route("clinic.roles.data") }}',
	columns: [{
			data: 'id',
			name: 'id'
		},
		{
			data: 'name',
			name: 'name'
		},
		{
			data: 'permissions_count',
			name: 'permissions_count'
		},
		{
			data: 'created_at',
			name: 'created_at'
		},
		{
			data: 'actions',
			name: 'actions',
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
				columns: [0, 1, 2, 3]
			}
		},
		{
			extend: 'excel',
			text: 'Excel',
			title: 'Roles Data',
			exportOptions: {
				columns: [0, 1, 2, 3]
			}
		},
		{
			extend: 'copy',
			exportOptions: {
				columns: [0, 1, 2, 3]
			}
		},
	],
	drawCallback: function() {
		$('.dataTables_paginate > .pagination').addClass(
			'pagination-rounded');
	}
});

function resetForm() {
	$('#rolesForm')[0].reset();
	$('#rolesForm').attr('action', '{{ route("clinic.roles.store") }}');
	$('#rolesId').val('');
	$('#rolesModal .modal-title').text('{{ __("Add Role") }}');
	$('.permission-check').prop('checked', false);
	$('#permissionSearch').val('');
	$('#selectAllPermissions').prop('checked', false);
	$('#expandAllGroups').prop('checked', false);
	$('.permission-item').show();
	$('.permission-group').show();
}

function performPermissionSearch() {
	let searchTerm = $('#permissionSearch').val().toLowerCase();

	if (searchTerm === '') {
		$('.permission-item').show();
		$('.permission-group').show();
		$('.group-permissions').each(function() {
			let group = $(this).data('group');
			let toggleBtn = $('.toggle-group[data-group="' + group + '"]');
			if ($(this).is(':visible')) {
				toggleBtn.find('.expand-text').addClass('d-none');
				toggleBtn.find('.collapse-text').removeClass('d-none');
			} else {
				toggleBtn.find('.expand-text').removeClass('d-none');
				toggleBtn.find('.collapse-text').addClass('d-none');
			}
		});
	} else {
		$('.group-permissions').show();
		$('.expand-text').addClass('d-none');
		$('.collapse-text').removeClass('d-none');

		$('.permission-item').each(function() {
			let permissionName = $(this).data('name') || '';
			let permissionText = $(this).find('label').text().toLowerCase();

			if (permissionName.includes(searchTerm) || permissionText.includes(
					searchTerm)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});

		$('.permission-group').each(function() {
			let visibleItems = $(this).find('.permission-item:visible').length;
			if (visibleItems === 0) {
				$(this).hide();
			} else {
				$(this).show();
			}
		});
	}

	updateSelectAllState();
	updateExpandAllState();
}

$(document).on('input keyup', '#permissionSearch', function() {
	performPermissionSearch();
});

$('#selectAllPermissions').on('change', function() {
	let isChecked = $(this).prop('checked');
	$('.permission-check:visible').prop('checked', isChecked);
	updateGroupSelectAllStates();
});

$(document).on('change', '.permission-check', function() {
	updateSelectAllState();
	updateGroupSelectAllStates();
});

function updateSelectAllState() {
	let visibleChecks = $('.permission-check:visible');
	let checkedVisible = $('.permission-check:visible:checked');

	if (visibleChecks.length === 0) {
		$('#selectAllPermissions').prop('checked', false);
		$('#selectAllPermissions').prop('indeterminate', false);
	} else if (checkedVisible.length === visibleChecks.length) {
		$('#selectAllPermissions').prop('checked', true);
		$('#selectAllPermissions').prop('indeterminate', false);
	} else if (checkedVisible.length > 0) {
		$('#selectAllPermissions').prop('checked', false);
		$('#selectAllPermissions').prop('indeterminate', true);
	} else {
		$('#selectAllPermissions').prop('checked', false);
		$('#selectAllPermissions').prop('indeterminate', false);
	}
}

function updateGroupSelectAllStates() {
	$('.permission-group').each(function() {
		let group = $(this);
		let groupChecks = group.find('.permission-check:visible');
		let checkedGroup = group.find('.permission-check:visible:checked');
	});
}

function expandAllGroups() {
	$('.group-permissions').slideDown(200);
	$('.expand-text').addClass('d-none');
	$('.collapse-text').removeClass('d-none');
	$('#expandAllGroups').prop('checked', true);
}

function collapseAllGroups() {
	$('.group-permissions').slideUp(200);
	$('.expand-text').removeClass('d-none');
	$('.collapse-text').addClass('d-none');
	$('#expandAllGroups').prop('checked', false);
}

function updateExpandAllState() {
	let visibleGroups = $('.group-permissions:visible').length;
	let totalGroups = $('.group-permissions').length;

	if (visibleGroups === 0) {
		$('#expandAllGroups').prop('checked', false);
		$('#expandAllGroups').prop('indeterminate', false);
	} else if (visibleGroups === totalGroups) {
		$('#expandAllGroups').prop('checked', true);
		$('#expandAllGroups').prop('indeterminate', false);
	} else {
		$('#expandAllGroups').prop('checked', false);
		$('#expandAllGroups').prop('indeterminate', true);
	}
}

$('#expandAllGroups').on('change', function() {
	let isChecked = $(this).prop('checked');
	if (isChecked) {
		expandAllGroups();
	} else {
		collapseAllGroups();
	}
});

$(document).on('click', '.toggle-group', function(e) {
	e.preventDefault();
	let group = $(this).data('group');
	let groupPermissions = $('.group-permissions[data-group="' + group + '"]');
	let expandText = $(this).find('.expand-text');
	let collapseText = $(this).find('.collapse-text');

	if (groupPermissions.is(':visible')) {
		groupPermissions.slideUp(200);
		expandText.removeClass('d-none');
		collapseText.addClass('d-none');
	} else {
		groupPermissions.slideDown(200);
		expandText.addClass('d-none');
		collapseText.removeClass('d-none');
	}

	updateExpandAllState();
});

$('#rolesModal').on('shown.bs.modal', function() {
	$('#permissionSearch').val('');
	$('.permission-item').show();
	$('.permission-group').show();
	$('.group-permissions').hide();
	$('.expand-text').removeClass('d-none');
	$('.collapse-text').addClass('d-none');
	$('#expandAllGroups').prop('checked', false);
	updateSelectAllState();
	updateExpandAllState();
});

$('#rolesForm').on('submit', function(e) {
	e.preventDefault();
	let id = $('#rolesId').val();
	let url = id ?
		'{{ route("clinic.roles.update", ":id") }}'.replace(':id', id) :
		'{{ route("clinic.roles.store") }}';
	let method = id ? 'PUT' : 'POST';

	$.ajax({
		url: url,
		method: method,
		data: $(this).serialize(),
		success: function(response) {
			$('#rolesModal').modal('hide');
			table.ajax.reload();
			Swal.fire('Success', response.message ||
				'Operation completed successfully',
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
						let nameSelector =
							'[name="' +
							key +
							'"]';
						let $input =
							$(
								nameSelector
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
				Swal.fire('Error', 'Something went wrong',
					'error');
			}
		}
	});
});

function editRole(id, name, permissions) {
	$('#rolesId').val(id);
	$('#name').val(name);

	$('.permission-check').prop('checked', false);

	if (permissions && permissions.length > 0) {
		permissions.forEach(function(permissionId) {
			$('#permission_' + permissionId).prop('checked', true);
		});
	}

	$('#permissionSearch').val('');
	$('.permission-item').show();
	$('.permission-group').show();
	$('#expandAllGroups').prop('checked', false);
	updateSelectAllState();
	updateExpandAllState();

	$('#rolesForm').attr('action', '{{ route("clinic.roles.update", ":id") }}'.replace(':id', id));
	$('#rolesModal .modal-title').text('{{ __("Edit Role") }}');
	$('#rolesModal').modal('show');
}

function deleteRole(id) {
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
				url: '{{ route("clinic.roles.index") }}/' +
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
						.message ||
						'Role deleted successfully',
						'success'
					);
				}
			});
		}
	});
}
</script>
@endpush