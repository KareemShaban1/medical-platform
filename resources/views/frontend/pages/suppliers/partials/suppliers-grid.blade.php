@forelse($suppliers as $supplier)
<div class="supplier-card card overflow-hidden"
	data-name="{{ $supplier->name }}">
	<div class="h-48 bg-gray-200 flex items-center justify-center">
		@if($supplier->images && count($supplier->images) > 0)
		<img src="{{ $supplier->images[0] }}" alt="{{ $supplier->name }}" class="w-full h-full object-cover">
		@else
		<i class="fas fa-truck text-4xl text-gray-400"></i>
		@endif
	</div>
	<div class="p-4">
		<h3 class="font-semibold text-lg mb-2">{{ $supplier->name }}</h3>
		<p class="text-gray-600 text-sm mb-2">Medical equipment supplier</p>
		<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-map-marker-alt mr-2"></i>
			<span>{{ $supplier->address ?? 'Location not specified' }}</span>
		</div>
		<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-phone mr-2"></i>
			<span>{{ $supplier->phone ?? 'Contact not available' }}</span>
		</div>
		<button class="btn-primary w-full">
			Contact Supplier
		</button>
	</div>
</div>
@empty
<div class="col-span-full text-center py-12">
	<div class="text-gray-500">
		<i class="fas fa-search text-4xl mb-4"></i>
		<h3 class="text-lg font-semibold mb-2">No suppliers found</h3>
		<p>Try adjusting your search criteria or filters.</p>
	</div>
</div>
@endforelse
