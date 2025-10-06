<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
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
