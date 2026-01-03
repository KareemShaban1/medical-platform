@push('scripts')
<script>
function openOrderModal(orderId) {
	const modalEl = document.getElementById('orderDetailsModal');
	const bodyEl = document.getElementById('order-details-body');
	const modal = new bootstrap.Modal(modalEl);

	bodyEl.innerHTML = "<p class='text-muted mb-0'>{{ __('Loading order details...') }}</p>";
	modal.show();

	fetch("{{ route('clinic.orders.show', ':id') }}".replace(':id', orderId))
		.then(response => response.json())
		.then(order => {
			if (!order || order.error) {
				bodyEl.innerHTML = "<div class='alert alert-danger mb-0'>{{ __('Failed to load order details.') }}</div>";
				return;
			}

			let itemsHtml = '';
			if (order.items && order.items.length) {
				itemsHtml = order.items.map(item => `
                        <tr>
                            <td>${item.product ? item.product.name : '---'}</td>
                            <td>${item.supplier ? item.supplier.name : '-'}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.price).toFixed(2)} EGP</td>
                            <td><strong>${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)} EGP</strong></td>
                        </tr>
                    `).join('');
			} else {
				itemsHtml = `
                        <tr>
                            <td colspan="5" class="text-center text-muted">{{ __('No items found for this order.') }}</td>
                        </tr>
                    `;
			}

			const shipping = parseFloat(order.shipping || 0);
			const tax = parseFloat(order.tax || 0);
			const discount = parseFloat(order.discount || 0);
			const total = parseFloat(order.total || 0);
			const inferredSubtotal = total - (shipping + tax - discount);

			bodyEl.innerHTML = `
             <h5 class="mb-1">{{ __('Order') }} #${order.id}</h5>
                    <div class="row mb-3">
                        <div class="col-md-4">
                           
                            <p class="mb-0 text-muted">
                                {{ __('Order Number') }}: <strong>${order.number}</strong><br>
                                {{ __('Date') }}: <strong>${new Date(order.created_at).toLocaleString()}</strong><br>
                                {{ __('Status') }}: <strong>${order.status}</strong>
                            </p>
                        </div>
                        <div class="col-md-4 mt-3 mt-md-0">
                            <p class="mb-1 text-muted">
                                {{ __('Payment Method') }}:
                                <strong>${order.payment_method === 0 ? '{{ __('Cash on Delivery') }}' : '{{ __('Online Payment') }}'}</strong>
                            </p>
                            <p class="mb-1 text-muted">
                                {{ __('Payment Status') }}:
                                <strong>${order.payment_status}</strong>
                            </p>
                            <p class="mb-0 text-muted">
                                {{ __('Payment Gateway') }}:
                                <strong>${order.payment_gateway ?? '-'}</strong>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1 text-muted">
                                {{ __('Shipping Address') }}:
                                <strong>${order.shipping_address ?? '-'}</strong>
                            </p>
                            <p class="mb-1 text-muted">
                                {{ __('Phone') }}:
                                <strong>${order.phone ?? '-'}</strong>
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Supplier') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <p class="mb-1">
                                {{ __('Subtotal') }}:
                                <strong>${inferredSubtotal.toFixed(2)} EGP</strong>
                            </p>
                            <p class="mb-1">
                                {{ __('Shipping') }}:
                                <strong>${shipping.toFixed(2)} EGP</strong>
                            </p>
                            <p class="mb-1">
                                {{ __('Tax') }}:
                                <strong>${tax.toFixed(2)} EGP</strong>
                            </p>
                            <p class="mb-1">
                                {{ __('Discount') }}:
                                <strong>${discount.toFixed(2)} EGP</strong>
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end d-flex align-items-end justify-content-md-end mt-3 mt-md-0">
                            <h5 class="mb-0">{{ __('Total') }}: <strong>${total.toFixed(2)} EGP</strong></h5>
                        </div>
                    </div>
                `;
		})
		.catch(() => {
			bodyEl.innerHTML = "<div class='alert alert-danger mb-0'>{{ __('Failed to load order details.') }}</div>";
		});
}
</script>
@endpush