@extends('backend.dashboards.admin.layouts.app')

@section('title', __('All Orders'))

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<div class="d-flex align-items-center gap-2">
						@hasPermission('view orders')
						<a href="{{ route('admin.orders.analytics') }}"
							class="btn btn-info">
							<i class="mdi mdi-chart-box"></i>
							{{ __('Analytics Dashboard') }}
						</a>
						@endhasPermission
						<ol class="breadcrumb m-0">
							<li class="breadcrumb-item"><a
									href="javascript: void(0);">{{ __('Dashboard') }}</a>
							</li>
							<li class="breadcrumb-item active">
								{{ __('All Orders') }}</li>
						</ol>
					</div>
				</div>
				<h4 class="page-title">{{ __('All Orders') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="orders-table"
							class="table table-striped dt-responsive nowrap w-100">
							<thead>
								<tr>
									<th>{{ __('Order Number') }}
									</th>
									<th>{{ __('Clinic') }}</th>
									<th>{{ __('Clinic User') }}
									</th>
									<th>{{ __('Suppliers') }}</th>
									<th>{{ __('Items') }}</th>
									<th>{{ __('Total') }}</th>
									<th>{{ __('Status') }}</th>
									<th>{{ __('Payment Status') }}
									</th>
									<th>{{ __('Date') }}</th>
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

<div class="modal fade" id="updatePaymentStatusModal" tabindex="-1" aria-labelledby="updatePaymentStatusModalLabel"
	aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updatePaymentStatusModalLabel">
					{{ __('Update Payment Status') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form id="updatePaymentStatusForm">
				<div class="modal-body">
					<div class="mb-3">
						<label for="payment_status"
							class="form-label">{{ __('Payment Status') }}</label>
						<select class="form-select" id="payment_status"
							name="payment_status" required>
							<option value="pending">{{ __('Pending') }}
							</option>
							<option value="paid">{{ __('Paid') }}</option>
							<option value="failed">{{ __('Failed') }}</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary"
						data-bs-dismiss="modal">{{ __('Close') }}</button>
					<button type="submit"
						class="btn btn-primary">{{ __('Update') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
	let ordersTable = $('#orders-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '{{ route('admin.orders.data') }}',
		columns: [{
				data: 'number',
				name: 'number'
			},
			{
				data: 'clinic_name',
				name: 'clinic_name'
			},
			{
				data: 'clinic_user',
				name: 'clinic_user'
			},
			{
				data: 'suppliers_count',
				name: 'suppliers_count'
			},
			{
				data: 'items_count',
				name: 'items_count'
			},
			{
				data: 'total',
				name: 'total'
			},
			{
				data: 'status',
				name: 'status'
			},
			{
				data: 'payment_status',
				name: 'payment_status'
			},
			{
				data: 'created_at',
				name: 'created_at'
			},
			{
				data: 'action',
				name: 'action',
				orderable: false,
				searchable: false
			}
		]
	});

	let currentOrderId = null;

	window.updatePaymentStatus = function(orderId) {
		currentOrderId = orderId;
		$('#updatePaymentStatusModal').modal('show');
	};

	$('#updatePaymentStatusForm').submit(function(e) {
		e.preventDefault();

		$.ajax({
			url: `{{ url('admin/orders') }}/${currentOrderId}/update-payment-status`,
			method: 'POST',
			data: $(this).serialize(),
			headers: {
				'X-CSRF-TOKEN': $(
						'meta[name="csrf-token"]'
					)
					.attr(
						'content'
					)
			},
			success: function(response) {
				$('#updatePaymentStatusModal')
					.modal(
						'hide'
					);
				ordersTable
					.ajax
					.reload();
				if (window
					.toastr
				) {
					toastr.success(response
						.message
					);
				}
			},
			error: function(xhr) {
				const message =
					xhr
					.responseJSON &&
					xhr
					.responseJSON
					.message ?
					xhr
					.responseJSON
					.message :
					'{{ __('An error occurred') }}';
				if (window
					.toastr
				) {
					toastr.error(
						message
					);
				}
			}
		});
	});
});
</script>
@endpush
