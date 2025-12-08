@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Subscriptions Management'))

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<div class="btn-group">
						@hasPermission('create subscription')
						<button class="btn btn-primary"
							onclick="createSubscription()">
							<i class="mdi mdi-plus"></i>
							{{ __('Create Subscription') }}
						</button>
						@endhasPermission
						<a href="{{ route('admin.subscriptions.analytics') }}"
							class="btn btn-outline-primary">
							<i class="mdi mdi-chart-line"></i>
							{{ __('Analytics') }}
						</a>
					</div>
				</div>
				<h4 class="page-title">{{ __('Subscriptions Management') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="row g-3 mb-3 align-items-end">
						<div class="col-md-3">
							<label
								class="form-label">{{ __('Filter by Status') }}</label>
							<select id="status-filter"
								class="form-select form-select-sm">
								<option value="all">
									{{ __('All Statuses') }}
								</option>
								<option value="active">
									{{ __('Active') }}</option>
								<option value="expired">
									{{ __('Expired') }}</option>
								<option value="pending">
									{{ __('Pending') }}</option>
								<option value="canceled">
									{{ __('Canceled') }}</option>
							</select>
						</div>
						<div class="col-md-3">
							<label
								class="form-label">{{ __('Filter by Plan Type') }}</label>
							<select id="plan-type-filter"
								class="form-select form-select-sm">
								<option value="all">
									{{ __('All Types') }}</option>
								<option value="doctor">
									{{ __('Doctor') }}</option>
								<option value="clinic">
									{{ __('Clinic') }}</option>
								<option value="supplier">
									{{ __('Supplier') }}</option>
							</select>
						</div>
						<div class="col-md-3">
							<label
								class="form-label">{{ __('Filter by Entity Type') }}</label>
							<select id="entity-type-filter"
								class="form-select form-select-sm">
								<option value="all">
									{{ __('All Entities') }}
								</option>
								<option value="App\Models\Clinic">
									{{ __('Clinics') }}</option>
								<option value="App\Models\ClinicUser">
									{{ __('Doctors') }}</option>
								<option value="App\Models\Supplier">
									{{ __('Suppliers') }}</option>
							</select>
						</div>
					</div>
					<div class="table-responsive">
						<table id="subscriptions-table"
							class="table table-hover dt-responsive nowrap w-100">
							<thead>
								<tr>
									<th>{{ __('ID') }}</th>
									<th>{{ __('Entity') }}</th>
									<th>{{ __('Type') }}</th>
									<th>{{ __('Plan') }}</th>
									<th>{{ __('Status') }}</th>
									<th>{{ __('Start Date') }}
									</th>
									<th>{{ __('End Date') }}</th>
									<th>{{ __('Days Remaining') }}
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
</div>

<div id="subscription-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>
<div id="extend-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
	const table = $('#subscriptions-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: '{{ route('admin.subscriptions.data') }}',
			data: function(d) {
				d.status = $('#status-filter')
					.val() || 'all';
				d.plan_type = $(
						'#plan-type-filter'
					)
					.val() || 'all';
				d.entity_type = $(
						'#entity-type-filter'
					)
					.val() || 'all';
			}
		},
		order: [
			[0, 'desc']
		],
		dom: '<"row align-items-center mb-3"<"col-md-6"l><"col-md-6 text-md-end"B>>rt<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
		buttons: [{
				extend: 'excel',
				className: 'btn btn-sm btn-light',
				text: '<i class="mdi mdi-file-excel"></i> {{ __('Export Excel') }}'
			},
			{
				extend: 'print',
				className: 'btn btn-sm btn-light',
				text: '<i class="mdi mdi-printer"></i> {{ __('Print') }}'
				Print ') }}'
			}
		],
		columns: [{
				data: 'id',
				name: 'id'
			},
			{
				data: 'entity_name',
				name: 'entity_name'
			},
			{
				data: 'entity_type',
				name: 'entity_type'
			},
			{
				data: 'plan_name',
				name: 'plan_name'
			},
			{
				data: 'status',
				name: 'status',
				orderable: false
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
				data: 'days_remaining',
				name: 'days_remaining',
				orderable: false
			},
			{
				data: 'action',
				name: 'action',
				orderable: false,
				searchable: false
			},
		]
	});

	$('#status-filter, #plan-type-filter, #entity-type-filter').on('change', function() {
		table.ajax.reload();
	});

	window.createSubscription = function() {
		$.get('{{ route('admin.subscriptions.create') }}',
			function(resp) {
				if (resp.success && resp.html) {
					$('#subscription-modal').html(
							resp.html)
						.modal('show');
				}
			});
	};

	window.extendSubscription = function(id) {
		Swal.fire({
			title: '{{ __('Extend Subscription') }}',
			html: `
                <div class="text-start">
                    <label class="form-label">{{ __('Number of Days') }}</label>
                    <input type="number" id="extend-days" class="form-control" min="1" value="30">
                </div>
            `,
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: '{{ __('Extend') }}',
			cancelButtonText: '{{ __('Cancel') }}',
			confirmButtonColor: '#079184',
			preConfirm: () => {
				const days =
					document
					.getElementById(
						'extend-days'
					)
					.value;
				if (!days || days <
					1) {
					Swal.showValidationMessage(
						'{{ __('Please enter a valid number of days') }}'
					);
				}
				return {
					days: parseInt(
						days
					)
				};
			}
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: '{{ route('admin.subscriptions.extend', ['id' => '__ID__']) }}'
					.replace('__ID__',
						id
					),
					type: 'POST',
					data: JSON.stringify(
						result
						.value
					),
					contentType: 'application/json',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					success: function(
						resp
					) {
						Swal.fire({
							title: '{{ __('Success!') }}',
							text: resp.message ||
								'{{ __('Subscription extended successfully') }}',
							icon: 'success',
							confirmButtonColor: '#079184',
						});
						table.ajax.reload(null,
							false
						);
					},
					error: function(
						xhr
					) {
						const error =
							xhr
							.responseJSON
							?.message ||
							'{{ __('Failed to extend subscription') }}';
						Swal.fire({
							title: '{{ __('Error') }}',
							text: error,
							icon: 'error',
							confirmButtonColor: '#079184',
						});
					}
				});
			}
		});
	};

	window.deleteSubscription = function(id) {
		Swal.fire({
			title: '{{ __('Are you sure?') }}',
			text : '{{ __('This will permanently delete the subscription. This action cannot be undone.') }}',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: '{{ __('Yes, delete it!') }}',
			cancelButtonText: '{{ __('Cancel') }}',
			Cancel ') }}',
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: '{{ route('admin.subscriptions.destroy', ['id' => '__ID__']) }}'
					.replace('__ID__',
						id
					),
					type: 'DELETE',
					data: {
						_token: '{{ csrf_token() }}'
					},
					success: function(
						resp
					) {
						Swal.fire({
							title: '{{ __('Deleted!') }}',
							text: resp.message ||
								'{{ __('Subscription deleted successfully') }}',
							icon: 'success',
							confirmButtonColor: '#079184',
						});
						table.ajax.reload(null,
							false
						);
					},
					error: function(
						xhr
					) {
						const error =
							xhr
							.responseJSON
							?.message ||
							'{{ __('Failed to delete subscription') }}';
						Swal.fire({
							title: '{{ __('Error') }}',
							text: error,
							icon: 'error',
							confirmButtonColor: '#079184',
						});
					}
				});
			}
		});
	};
});
</script>
@endpush
