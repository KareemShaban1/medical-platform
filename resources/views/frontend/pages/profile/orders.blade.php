@extends('frontend.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
	<h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">My Orders</h1>

	@if($orders->isEmpty())
	<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
		<p class="text-gray-600 dark:text-gray-400">You don’t have any orders yet.</p>
		<a href="{{ route('shop.index') }}"
			class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
			Start Shopping
		</a>
	</div>
	@else
	<!-- Responsive table -->
	<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
		<table class="w-full hidden md:table text-sm text-left text-gray-700 dark:text-gray-300">
			<thead
				class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm uppercase">
				<tr>
					<th class="px-6 py-3">Order #</th>
					<th class="px-6 py-3">Order Number</th>
					<th class="px-6 py-3">Items</th>
					<th class="px-6 py-3">Date</th>
					<th class="px-6 py-3">Status</th>
					<th class="px-6 py-3">Total</th>
					<th class="px-6 py-3 text-center">Actions</th>
				</tr>
			</thead>
			<tbody>
				@foreach($orders as $order)
				<tr
					class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
					<td class="px-6 py-4 font-medium">#{{ $order->id }}</td>
					<td class="px-6 py-4">{{ $order->number }}</td>
					<td class="px-6 py-4">{{ $order->items->count() }}</td>
					<td class="px-6 py-4">{{ $order->created_at->format('M d, Y') }}</td>
					<td class="px-6 py-4">
						<span class="px-3 py-1 rounded-full text-xs font-medium
								{{ $order->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
								($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
								($order->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
								'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200')) }}">
							{{ ucfirst($order->status) }}
						</span>
					</td>
					<td class="px-6 py-4 font-semibold">
						{{ number_format($order->total, 2) }} EGP</td>
					<td class="px-6 py-4 text-center">
						<button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition"
							onclick="openModal({{ $order->id }})">
							View Details
						</button>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<!-- Mobile card layout -->
		<div class="space-y-4 md:hidden p-2">
			@foreach($orders as $order)
			<div
				class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-600">
				<div class="flex justify-between items-center mb-3">
					<h3 class="font-semibold text-gray-900 dark:text-white">
						#{{ $order->id }}</h3>
					<span class="px-3 py-1 text-xs rounded-full font-medium
							{{ $order->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
							($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
							($order->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
							'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200')) }}">
						{{ ucfirst($order->status) }}
					</span>
				</div>

				<div class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
					<p><span class="font-medium">Order Number:</span>
						{{ $order->number }}</p>
					<p><span class="font-medium">Items:</span>
						{{ $order->items->count() }}</p>
					<p><span class="font-medium">Date:</span>
						{{ $order->created_at->format('M d, Y') }}</p>
					<p><span class="font-medium">Total:</span>
						{{ number_format($order->total, 2) }} EGP</p>
				</div>

				<div class="mt-4 text-right">
					<button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition"
						onclick="openModal({{ $order->id }})">
						View Details
					</button>
				</div>
			</div>
			@endforeach
		</div>
	</div>
	@endif
</div>

<!-- Details Modal -->
<div id="orderModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto py-6">
	<div
		class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl relative mx-4 max-h-[90vh] flex flex-col overflow-hidden">
		<button onclick="closeModal()"
			class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl">✕</button>
		<div class="p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
			<h2 class="text-2xl font-bold text-gray-900 dark:text-white">Order Details</h2>
		</div>
		<div id="modalContent" class="p-6 overflow-y-auto flex-1">
			<p class="text-center text-gray-500 dark:text-gray-400">Loading...</p>
		</div>
	</div>
</div>

<script>
function openModal(orderId) {
	const modal = document.getElementById('orderModal');
	const content = document.getElementById('modalContent');
	modal.classList.remove('hidden');
	modal.classList.add('flex');
	content.innerHTML = `<p class="text-gray-500 dark:text-gray-400">Loading order details...</p>`;

	fetch(`{{ route('profile.order-details', ['id' => ':id']) }}`.replace(':id', orderId))
		.then(res => res.json())
		.then(order => {
			let itemsHTML = order.items.map(item => `
				<div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 py-3">
					<div>
						<p class="font-medium text-gray-800 dark:text-gray-200">${item.product.name}</p>
						<p class="text-sm text-gray-500 dark:text-gray-400">Qty: ${item.quantity}</p>
					</div>
					<p class="font-semibold text-gray-900 dark:text-white">${(item.price * item.quantity).toFixed(2)} EGP</p>
				</div>
			`).join('');

			content.innerHTML = `
				<h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Order #${order.id}</h2>
				<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
					Placed on ${new Date(order.created_at).toLocaleDateString()}<br>
					Status: <span class="capitalize font-medium">${order.status}</span>
				</p>
				<div class="divide-y divide-gray-100 dark:divide-gray-700 mb-4">
					${itemsHTML}
				</div>
				<div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
					<span class="text-gray-600 dark:text-gray-300 font-medium">Total</span>
					<span class="text-xl font-bold text-gray-900 dark:text-white">${order.total} EGP</span>
				</div>
			`;
		});
}

function closeModal() {
	const modal = document.getElementById('orderModal');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
}
</script>
@endsection