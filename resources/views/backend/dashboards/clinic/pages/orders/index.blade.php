@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row mb-3">
		<div class="col-12 d-flex justify-content-between align-items-center">
			<h4 class="page-title">{{ __('My Orders') }}</h4>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					@if($orders->isEmpty())
					<div class="alert alert-info mb-0">
						{{ __('You have not placed any orders yet.') }}
					</div>
					@else
					<div class="table-responsive">
						<table class="table table-striped align-middle mb-0">
							<thead>
								<tr>
									<th>#</th>
									<th>{{ __('Order Number') }}
									</th>
									<th>{{ __('Items') }}</th>
									<th>{{ __('Date') }}</th>
									<th>{{ __('Status') }}</th>
									<th>{{ __('Payment Status') }}
									</th>
									<th>{{ __('Total') }}</th>
									<th class="text-center">
										{{ __('Actions') }}
									</th>
								</tr>
							</thead>
							<tbody>
								@foreach($orders as $order)
								<tr>
									<td>#{{ $order->id }}</td>
									<td>{{ $order->number }}</td>
									<td>{{ $order->items->count() }}
									</td>
									<td>{{ $order->created_at->format('Y-m-d H:i') }}
									</td>
									<td>
										@php
										$status =
										$order->status;
										$statusBadge =
										match($status) {
										'completed' =>
										'success',
										'pending' =>
										'warning',
										'cancelled' =>
										'danger',
										'processing' =>
										'info',
										default =>
										'secondary',
										};
										@endphp
										<span
											class="badge bg-{{ $statusBadge }}">{{ ucfirst($status) }}</span>
									</td>
									<td>
										@php
										$payStatus =
										$order->payment_status;
										$payBadge =
										match($payStatus) {
										'paid' => 'success',
										'failed' =>
										'danger',
										'pending' =>
										'warning',
										default =>
										'secondary',
										};
										@endphp
										<span
											class="badge bg-{{ $payBadge }}">{{ ucfirst($payStatus) }}</span>
									</td>
									<td><strong>{{ number_format($order->total, 2) }}
											EGP</strong>
									</td>
									<td class="text-center">
										<button type="button"
											class="btn btn-sm btn-primary"
											onclick="openOrderModal({{ $order->id }})">
											{{ __('View Details') }}
										</button>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					<div class="mt-3">
						{{ $orders->links() }}
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ __('Order Details') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<div class="modal-body" id="order-details-body">
				<p class="text-muted mb-0">{{ __('Loading order details...') }}</p>
			</div>
		</div>
	</div>
</div>

@include('backend.dashboards.clinic.pages.orders.scripts.index-scripts')
@endsection


