@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('My Orders'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('My Orders') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('My Orders') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="orders-table" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>{{ __('Order Number') }}</th>
                                        <th>{{ __('Clinic') }}</th>
                                        <th>{{ __('Clinic User') }}</th>
                                        <th>{{ __('Items Count') }}</th>
                                        <th>{{ __('My Total') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Payment Status') }}</th>
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

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">{{ __('Update Order Status') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Order Status') }}</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="processing">{{ __('Processing') }}</option>
                                <option value="delivering">{{ __('Delivering') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                                <option value="cancelled">{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                        <div id="itemStatusesContainer" style="display: none;">
                            <h6>{{ __('Update Individual Items') }}</h6>
                            <div id="itemStatuses"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Status') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Refund Modal -->
    <div class="modal fade" id="createRefundModal" tabindex="-1" aria-labelledby="createRefundModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRefundModalLabel">{{ __('Create Refund') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createRefundForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="refund_type" class="form-label">{{ __('Refund Type') }}</label>
                            <select class="form-select" id="refund_type" name="refund_type" required>
                                <option value="partial">{{ __('Partial Refund') }}</option>
                                <option value="full">{{ __('Full Refund') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ __('Refund Amount') }}</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                                min="0" required>
                        </div>
                        <div class="mb-3" id="orderItemContainer" style="display: none;">
                            <label for="order_item_id" class="form-label">{{ __('Select Item (Optional)') }}</label>
                            <select class="form-select" id="order_item_id" name="order_item_id">
                                <option value="">{{ __('Select Item') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">{{ __('Reason') }}</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-warning">{{ __('Create Refund') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Payment Status Modal -->
    <div class="modal fade" id="updatePaymentStatusModal" tabindex="-1" aria-labelledby="updatePaymentStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePaymentStatusModalLabel">{{ __('Update Payment Status') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updatePaymentStatusForm">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            {{ __('Payment status can only be updated for COD orders.') }}
                        </div>
                        <div class="mb-3">
                            <label for="payment_status" class="form-label">{{ __('Payment Status') }}</label>
                            <select class="form-select" id="payment_status" name="payment_status" required>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="paid">{{ __('Paid') }}</option>
                                <option value="failed">{{ __('Failed') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Payment Status') }}</button>
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
                ajax: '{{ route('supplier.orders.data') }}',
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
                        data: 'items_count',
                        name: 'items_count'
                    },
                    {
                        data: 'supplier_total',
                        name: 'supplier_total'
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
                        ordersTable.ajax.reload();
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
                        ordersTable.ajax.reload();
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
        });
    </script>
@endpush
