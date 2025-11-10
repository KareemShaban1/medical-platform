@extends('frontend.layouts.app')

@push('styles')
<style>
	/* Custom Animations */
	@keyframes fadeInUp {
		from {
			opacity: 0;
			transform: translateY(30px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	@keyframes slideInRight {
		from {
			opacity: 0;
			transform: translateX(50px);
		}
		to {
			opacity: 1;
			transform: translateX(0);
		}
	}

	@keyframes pulse {
		0%, 100% {
			transform: scale(1);
		}
		50% {
			transform: scale(1.05);
		}
	}

	.animate-fade-in-up {
		animation: fadeInUp 0.8s ease-out forwards;
		opacity: 0;
	}

	.animate-slide-in-right {
		animation: slideInRight 0.6s ease-out forwards;
		opacity: 0;
	}

	/* Order Card Styles */
	.order-card {
		background: white;
		border-radius: 16px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
		transition: all 0.3s ease;
		position: relative;
		overflow: hidden;
		border: 1px solid #e5e7eb;
	}

	.order-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 4px;
		height: 100%;
		background: linear-gradient(135deg, #079184, #0aa896);
		transition: width 0.3s ease;
	}

	.order-card:hover::before {
		width: 6px;
	}

	.order-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 12px 24px rgba(7, 145, 132, 0.15);
		border-color: #079184;
	}

	/* Status Badges */
	.status-badge {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 6px 14px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.status-completed {
		background: linear-gradient(135deg, #10b981, #059669);
		color: white;
		box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
	}

	.status-pending {
		background: linear-gradient(135deg, #f59e0b, #d97706);
		color: white;
		box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
		animation: pulse 2s ease-in-out infinite;
	}

	.status-cancelled {
		background: linear-gradient(135deg, #ef4444, #dc2626);
		color: white;
		box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
	}

	.status-processing {
		background: linear-gradient(135deg, #3b82f6, #2563eb);
		color: white;
		box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
	}

	/* Empty State */
	.empty-state {
		background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
		border: 2px dashed #10b981;
		border-radius: 20px;
		padding: 60px 40px;
		text-align: center;
	}

	.empty-state-icon {
		width: 120px;
		height: 120px;
		margin: 0 auto 24px;
		background: linear-gradient(135deg, #10b981, #059669);
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 48px;
		box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
	}

	/* Modal Styles */
	.modal-overlay {
		background: rgba(0, 0, 0, 0.6);
		backdrop-filter: blur(4px);
	}

	.modal-content {
		background: white;
		border-radius: 24px;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
		max-height: 90vh;
		overflow-y: auto;
	}

	.modal-content::-webkit-scrollbar {
		width: 8px;
	}

	.modal-content::-webkit-scrollbar-track {
		background: #f1f1f1;
		border-radius: 10px;
	}

	.modal-content::-webkit-scrollbar-thumb {
		background: linear-gradient(135deg, #079184, #0aa896);
		border-radius: 10px;
	}

	/* Order Item Card */
	.order-item-card {
		background: #f9fafb;
		border-radius: 12px;
		padding: 16px;
		border-left: 4px solid #079184;
		transition: all 0.3s ease;
	}

	.order-item-card:hover {
		background: #f3f4f6;
		transform: translateX(4px);
	}

	/* Gradient Text */
	.gradient-text {
		background: linear-gradient(135deg, #079184, #0aa896);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		background-clip: text;
	}

	/* Stats Cards */
	.stat-card {
		background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
		border-radius: 16px;
		padding: 24px;
		border: 1px solid #d1fae5;
		transition: all 0.3s ease;
	}

	.stat-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 20px rgba(16, 185, 129, 0.2);
	}

	/* Loading Spinner */
	.spinner {
		width: 40px;
		height: 40px;
		border: 4px solid rgba(7, 145, 132, 0.3);
		border-top: 4px solid #079184;
		border-radius: 50%;
		animation: spin 1s linear infinite;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

	/* Stagger Animation */
	.stagger-animation > * {
		opacity: 0;
		transform: translateY(30px);
		animation: fadeInUp 0.6s ease-out forwards;
	}

	.stagger-animation > *:nth-child(1) { animation-delay: 0.1s; }
	.stagger-animation > *:nth-child(2) { animation-delay: 0.2s; }
	.stagger-animation > *:nth-child(3) { animation-delay: 0.3s; }
	.stagger-animation > *:nth-child(4) { animation-delay: 0.4s; }
	.stagger-animation > *:nth-child(5) { animation-delay: 0.5s; }
	.stagger-animation > *:nth-child(6) { animation-delay: 0.6s; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
	<div class="max-w-7xl mx-auto">
		<!-- Header Section -->
		<div class="mb-8 animate-fade-in-up">
			<div class="flex items-center justify-between mb-4">
				<div>
					<h1 class="text-4xl font-bold text-gray-900 mb-2">
						<span class="gradient-text">{{ __('My Orders') }}</span>
					</h1>
					<p class="text-gray-600">{{ __('View and manage all your orders') }}</p>
				</div>
				<a href="{{ route('doctor.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
					<i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
				</a>
			</div>

			<!-- Stats Cards -->
			@if($orders->count() > 0)
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
				<div class="stat-card">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-600 mb-1">{{ __('Total Orders') }}</p>
							<p class="text-2xl font-bold text-gray-900">{{ $orders->total() }}</p>
						</div>
						<div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
							<i class="fas fa-shopping-bag text-green-600 text-xl"></i>
						</div>
					</div>
				</div>
				<div class="stat-card">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-600 mb-1">{{ __('Completed') }}</p>
							<p class="text-2xl font-bold text-gray-900">{{ $orders->where('status', 'completed')->count() }}</p>
						</div>
						<div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
							<i class="fas fa-check-circle text-green-600 text-xl"></i>
						</div>
					</div>
				</div>
				<div class="stat-card">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-600 mb-1">{{ __('Pending') }}</p>
							<p class="text-2xl font-bold text-gray-900">{{ $orders->where('status', 'pending')->count() }}</p>
						</div>
						<div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
							<i class="fas fa-clock text-yellow-600 text-xl"></i>
						</div>
					</div>
				</div>
				<div class="stat-card">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-600 mb-1">{{ __('Total Spent') }}</p>
							<p class="text-2xl font-bold text-gray-900">{{ number_format($orders->sum('total'), 2) }} <span class="text-sm">EGP</span></p>
						</div>
						<div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
							<i class="fas fa-coins text-blue-600 text-xl"></i>
						</div>
					</div>
				</div>
			</div>
			@endif
		</div>

		<!-- Orders List -->
		@if($orders->isEmpty())
		<div class="empty-state animate-fade-in-up">
			<div class="empty-state-icon">
				<i class="fas fa-shopping-bag"></i>
			</div>
			<h3 class="text-2xl font-bold text-gray-900 mb-3">{{ __('No Orders Yet') }}</h3>
			<p class="text-gray-600 mb-6 max-w-md mx-auto">
				{{ __('You haven\'t placed any orders yet. Start shopping to see your orders here.') }}
			</p>
			<a href="{{ route('products') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-lg hover:shadow-xl">
				<i class="fas fa-shopping-cart"></i>
				<span>{{ __('Start Shopping') }}</span>
			</a>
		</div>
		@else
		<div class="space-y-6 stagger-animation">
			@foreach($orders as $order)
			<div class="order-card p-6">
				<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
					<!-- Order Info -->
					<div class="flex-1">
						<div class="flex items-start gap-4 mb-4">
							<div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg">
								<i class="fas fa-receipt"></i>
							</div>
							<div class="flex-1">
								<div class="flex items-center gap-3 mb-2">
									<h3 class="text-xl font-bold text-gray-900">
										{{ __('Order') }} #{{ $order->id }}
									</h3>
									<span class="status-badge status-{{ $order->status }}">
										@if($order->status === 'completed')
											<i class="fas fa-check-circle"></i>
										@elseif($order->status === 'pending')
											<i class="fas fa-clock"></i>
										@elseif($order->status === 'cancelled')
											<i class="fas fa-times-circle"></i>
										@else
											<i class="fas fa-spinner"></i>
										@endif
										<span>{{ ucfirst($order->status) }}</span>
									</span>
								</div>
								<div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
									<div>
										<p class="text-gray-500 mb-1">{{ __('Order Number') }}</p>
										<p class="font-semibold text-gray-900">{{ $order->number }}</p>
									</div>
									<div>
										<p class="text-gray-500 mb-1">{{ __('Date') }}</p>
										<p class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
									</div>
									<div>
										<p class="text-gray-500 mb-1">{{ __('Items') }}</p>
										<p class="font-semibold text-gray-900">{{ $order->items->count() }} {{ __('items') }}</p>
									</div>
									<div>
										<p class="text-gray-500 mb-1">{{ __('Total') }}</p>
										<p class="font-bold text-lg text-gray-900">{{ number_format($order->total, 2) }} <span class="text-sm">EGP</span></p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Action Button -->
					<div class="flex-shrink-0">
						<button onclick="openModal({{ $order->id }})"
							class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-teal-500 to-emerald-600 text-primary-gradient font-semibold rounded-lg hover:from-teal-600 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
							<i class="fas fa-eye"></i>
							<span>{{ __('View Details') }}</span>
						</button>
					</div>
				</div>
			</div>
			@endforeach
		</div>

		<!-- Pagination -->
		<div class="mt-8">
			{{ $orders->links() }}
		</div>
		@endif
	</div>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="fixed inset-0 modal-overlay hidden items-center justify-center z-50 p-4">
	<div class="modal-content w-full max-w-3xl animate-slide-in-right">
		<!-- Modal Header -->
		<div class="sticky top-0 bg-primary-gradient text-white p-6 rounded-t-2xl flex items-center justify-between">
			<h2 class="text-2xl font-bold">{{ __('Order Details') }}</h2>
			<button onclick="closeModal()" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition">
				<i class="fas fa-times"></i>
			</button>
		</div>

		<!-- Modal Body -->
		<div id="modalContent" class="p-6">
			<div class="text-center py-12">
				<div class="spinner mx-auto mb-4"></div>
				<p class="text-gray-600">{{ __('Loading order details...') }}</p>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openModal(orderId) {
	const modal = document.getElementById('orderModal');
	const content = document.getElementById('modalContent');
	modal.classList.remove('hidden');
	modal.classList.add('flex');
	content.innerHTML = `
		<div class="text-center py-12">
			<div class="spinner mx-auto mb-4"></div>
			<p class="text-gray-600">{{ __('Loading order details...') }}</p>
		</div>
	`;

	fetch(`{{ route('doctor.orders.show', ['id' => ':id']) }}`.replace(':id', orderId))
		.then(res => res.json())
		.then(order => {
			if (order.error) {
				Swal.fire('Error', order.error, 'error');
				closeModal();
				return;
			}

			let itemsHTML = order.items.map(item => `
				<div class="order-item-card mb-4">
					<div class="flex items-center justify-between">
						<div class="flex-1">
							<h4 class="font-semibold text-gray-900 mb-1">${item.product?.name || '{{ __("Product") }}'}</h4>
							<div class="flex items-center gap-4 text-sm text-gray-600">
								<span><i class="fas fa-hashtag mr-1"></i>${item.quantity} {{ __('items') }}</span>
								<span><i class="fas fa-tag mr-1"></i>${parseFloat(item.price).toFixed(2)} EGP {{ __('each') }}</span>
							</div>
						</div>
						<div class="text-right">
							<p class="text-lg font-bold text-gray-900">${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)} EGP</p>
						</div>
					</div>
				</div>
			`).join('');

			content.innerHTML = `
				<div class="space-y-6">
					<!-- Order Summary -->
					<div class="bg-gradient-to-r from-teal-50 to-emerald-50 rounded-xl p-6 border border-teal-100">
						<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
							<div>
								<p class="text-sm text-gray-600 mb-1">{{ __('Order ID') }}</p>
								<p class="font-bold text-gray-900">#${order.id}</p>
							</div>
							<div>
								<p class="text-sm text-gray-600 mb-1">{{ __('Order Number') }}</p>
								<p class="font-bold text-gray-900">${order.number}</p>
							</div>
							<div>
								<p class="text-sm text-gray-600 mb-1">{{ __('Date') }}</p>
								<p class="font-bold text-gray-900">${new Date(order.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
							</div>
							<div>
								<p class="text-sm text-gray-600 mb-1">{{ __('Status') }}</p>
								<span class="status-badge status-${order.status}">
									${order.status === 'completed' ? '<i class="fas fa-check-circle"></i>' :
									  order.status === 'pending' ? '<i class="fas fa-clock"></i>' :
									  order.status === 'cancelled' ? '<i class="fas fa-times-circle"></i>' :
									  '<i class="fas fa-spinner"></i>'}
									<span>${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span>
								</span>
							</div>
						</div>
					</div>

					<!-- Order Items -->
					<div>
						<h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
							<i class="fas fa-shopping-bag text-teal-600"></i>
							<span>{{ __('Order Items') }}</span>
						</h3>
						${itemsHTML}
					</div>

					<!-- Order Total -->
					<div class="bg-gray-50 rounded-xl p-6 border-t-4 border-teal-500">
						<div class="flex items-center justify-between">
							<span class="text-lg font-semibold text-gray-700">{{ __('Total Amount') }}</span>
							<span class="text-3xl font-bold gradient-text">${parseFloat(order.total).toFixed(2)} <span class="text-lg">EGP</span></span>
						</div>
					</div>
				</div>
			`;
		})
		.catch(error => {
			console.error('Error:', error);
			content.innerHTML = `
				<div class="text-center py-12">
					<i class="fas fa-exclamation-triangle text-red-500 text-5xl mb-4"></i>
					<p class="text-gray-600">{{ __('Failed to load order details. Please try again.') }}</p>
				</div>
			`;
		});
}

function closeModal() {
	const modal = document.getElementById('orderModal');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
}

// Close modal on outside click
document.getElementById('orderModal')?.addEventListener('click', function(e) {
	if (e.target === this) {
		closeModal();
	}
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape') {
		closeModal();
	}
});
</script>
@endpush
@endsection
