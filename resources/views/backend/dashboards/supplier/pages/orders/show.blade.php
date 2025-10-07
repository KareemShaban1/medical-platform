@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Order Details'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('supplier.orders.index') }}">{{ __('Orders') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Order Details') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('Order Details') }} - #{{ $order->number }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Order Items') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Total') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                            <td>
                                                @php
                                                    $statusClass =
                                                        [
                                                            'pending' => 'warning',
                                                            'processing' => 'info',
                                                            'delivering' => 'primary',
                                                            'completed' => 'success',
                                                            'cancelled' => 'danger',
                                                        ][$item->status] ?? 'secondary';
                                                @endphp
                                                <span
                                                    class="badge bg-{{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">{{ __('Total') }}</th>
                                        <th>${{ number_format($order->items->sum(function ($item) {return $item->quantity * $item->price;}),2) }}
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Refunds -->
                @if ($order->refunds->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Refunds') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Reason') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->refunds as $refund)
                                            <tr>
                                                <td>${{ number_format($refund->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($refund->refund_type) }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass =
                                                            [
                                                                'pending' => 'warning',
                                                                'approved' => 'success',
                                                                'rejected' => 'danger',
                                                                'processed' => 'primary',
                                                            ][$refund->status] ?? 'secondary';
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $statusClass }}">{{ ucfirst($refund->status) }}</span>
                                                </td>
                                                <td>{{ $refund->reason ?? 'N/A' }}</td>
                                                <td>{{ $refund->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    @if ($refund->status === 'pending')
                                                        <button class="btn btn-sm btn-success"
                                                            onclick="updateRefundStatus({{ $refund->id }}, 'approved')"
                                                            title="{{ __('Approve') }}">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="updateRefundStatus({{ $refund->id }}, 'rejected')"
                                                            title="{{ __('Reject') }}">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    @elseif($refund->status === 'approved')
                                                        <button class="btn btn-sm btn-primary"
                                                            onclick="updateRefundStatus({{ $refund->id }}, 'processed')"
                                                            title="{{ __('Process') }}">
                                                            <i class="fa fa-check-circle"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">{{ __('No actions available') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Order Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Order Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>{{ __('Order Number') }}:</strong></td>
                                <td>{{ $order->number }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Status') }}:</strong></td>
                                <td>
                                    @php
                                        $supplierId = auth('supplier')->user()->supplier_id;
                                        $orderSupplier = $order
                                            ->suppliers()
                                            ->where('supplier_id', $supplierId)
                                            ->first();
                                        $status = $orderSupplier ? $orderSupplier->status : 'pending';
                                        $statusClass =
                                            [
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'delivering' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ][$status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Payment Status') }}:</strong></td>
                                <td>
                                    @php
                                        $paymentClass =
                                            [
                                                'pending' => 'warning',
                                                'paid' => 'success',
                                                'failed' => 'danger',
                                            ][$order->payment_status] ?? 'secondary';
                                    @endphp
                                    <span
                                        class="badge bg-{{ $paymentClass }}">{{ ucfirst($order->payment_status) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Payment Method') }}:</strong></td>
                                <td>{{ $order->payment_method == 0 ? 'COD' : 'Online' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Date') }}:</strong></td>
                                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Clinic Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Clinic Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>{{ __('Clinic') }}:</strong></td>
                                <td>{{ $order->clinic->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Ordered By') }}:</strong></td>
                                <td>{{ $order->clinicUser->name ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-info" onclick="updateOrderStatus({{ $order->id }})">
                                <i class="fa fa-edit"></i> {{ __('Update Status') }}
                            </button>
                            <button class="btn btn-warning" onclick="createRefund({{ $order->id }})">
                                <i class="fa fa-undo"></i> {{ __('Create Refund') }}
                            </button>
                            {{-- @if ($order->payment_method == 0)
                                <button class="btn btn-primary" onclick="updatePaymentStatus({{ $order->id }})">
                                    <i class="fa fa-credit-card"></i> {{ __('Update Payment') }}
                                </button>
                            @endif --}}
                            <a href="{{ route('supplier.orders.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> {{ __('Back to Orders') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include modals from index page -->
    @include('backend.dashboards.supplier.pages.orders.modals')
@endsection

@push('scripts')
    <script>
        let currentOrderId = {{ $order->id }};

        // Update Status Modal
        window.updateOrderStatus = function(orderId) {
            currentOrderId = orderId;

            // Load order items for individual status updates
            $.get(`{{ url('supplier/orders') }}/${orderId}/items`, function(items) {
                let itemsHtml = '';
                items.forEach(function(item) {
                    itemsHtml += `
                <div class="mb-2">
                    <label class="form-label">${item.product.name}</label>
                    <select class="form-select" name="item_statuses[${item.id}]">
                        <option value="pending" ${item.status === 'pending' ? 'selected' : ''}>{{ __('Pending') }}</option>
                        <option value="processing" ${item.status === 'processing' ? 'selected' : ''}>{{ __('Processing') }}</option>
                        <option value="delivering" ${item.status === 'delivering' ? 'selected' : ''}>{{ __('Delivering') }}</option>
                        <option value="completed" ${item.status === 'completed' ? 'selected' : ''}>{{ __('Completed') }}</option>
                        <option value="cancelled" ${item.status === 'cancelled' ? 'selected' : ''}>{{ __('Cancelled') }}</option>
                    </select>
                </div>
            `;
                });
                $('#itemStatuses').html(itemsHtml);
                $('#itemStatusesContainer').show();
            });

            $('#updateStatusModal').modal('show');
        };

        // Create Refund Modal
        window.createRefund = function(orderId) {
            currentOrderId = orderId;

            // Load order items for refund selection
            $.get(`{{ url('supplier/orders') }}/${orderId}/items`, function(items) {
                let itemsHtml = '<option value="">{{ __('Select Item') }}</option>';
                items.forEach(function(item) {
                    itemsHtml +=
                        `<option value="${item.id}">${item.product.name} - $${item.price}</option>`;
                });
                $('#order_item_id').html(itemsHtml);
            });

            $('#createRefundModal').modal('show');
        };

        // Handle refund type change
        $('#refund_type').change(function() {
            if ($(this).val() === 'partial') {
                $('#orderItemContainer').show();
            } else {
                $('#orderItemContainer').hide();
                $('#order_item_id').val('');
            }
        });

        // Update Status Form Submit
        $('#updateStatusForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: `{{ url('supplier/orders') }}/${currentOrderId}/update-status`,
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#updateStatusModal').modal('hide');
                    location.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error('{{ __('An error occurred') }}');
                }
            });
        });

        // Create Refund Form Submit
        $('#createRefundForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: `{{ url('supplier/orders') }}/${currentOrderId}/refund`,
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#createRefundModal').modal('hide');
                    $('#createRefundForm')[0].reset();
                    location.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error('{{ __('An error occurred') }}');
                }
            });
        });

        // Update Payment Status Modal
        window.updatePaymentStatus = function(orderId) {
            currentOrderId = orderId;
            $('#updatePaymentStatusModal').modal('show');
        };

        // Update Payment Status Form Submit
        $('#updatePaymentStatusForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: `{{ url('supplier/orders') }}/${currentOrderId}/update-payment-status`,
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#updatePaymentStatusModal').modal('hide');
                    location.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    let errorMessage = '{{ __('An error occurred') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                }
            });
        });

        // Update Refund Status
        window.updateRefundStatus = function(refundId, status) {
            // i wanna this confirm to be swal fire
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('Are you sure you want to update this refund status?') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('Yes, update it!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('supplier/orders/refund') }}/${refundId}/update-status`,
                        method: 'POST',
                        data: {
                            status: status,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: '{{ __('Updated!') }}',
                                text: '{{ __('The refund status has been updated successfully.') }}',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: '{{ __('Error!') }}',
                                text: '{{ __('Something went wrong while updating the status.') }}',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        };
    </script>
@endpush
