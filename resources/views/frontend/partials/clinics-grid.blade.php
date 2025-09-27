@forelse($clinics as $clinic)
<div class="clinic-card card overflow-hidden"
	data-specialization="{{ $clinic->specialization ?? 'general' }}"
	data-location="{{ $clinic->location ?? 'downtown' }}"
	data-rating="{{ $clinic->rating ?? rand(3, 5) }}"
	data-name="{{ $clinic->name }}">
	<div class="h-48 bg-gray-200 flex items-center justify-center">
		@if($clinic->images && count($clinic->images) > 0)
		<img src="{{ $clinic->images[0] }}" alt="{{ $clinic->name }}" class="w-full h-full object-cover">
		@else
		<i class="fas fa-hospital text-4xl text-gray-400"></i>
		@endif
	</div>
	<div class="p-4">
		<h3 class="font-semibold text-lg mb-2">{{ $clinic->name }}</h3>
		<p class="text-gray-600 text-sm mb-2">Specialized medical services</p>
		<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-map-marker-alt mr-2"></i>
			<span>{{ $clinic->address ?? 'Location not specified' }}</span>
		</div>
		<div class="flex items-center mb-3">
			<div class="flex text-yellow-400">
				@for($i = 1; $i <= 5; $i++)
					<i class="fas fa-star {{ $i <= ($clinic->rating ?? 4) ? '' : 'text-gray-300' }}"></i>
					@endfor
			</div>
			<span class="text-sm text-gray-500 ml-2">({{ rand(20, 150) }} reviews)</span>
		</div>
		<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-clock mr-2"></i>
			<span>{{ $clinic->status ? 'Open 24/7' : 'Closed' }}</span>
		</div>
		<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-phone mr-2"></i>
			<span>{{ $clinic->phone ?? 'Contact not available' }}</span>
		</div>
		<button class="btn-primary w-full">
			View Details
		</button>
	</div>
</div>
@empty
<div class="col-span-full text-center py-12">
	<div class="text-gray-500">
		<i class="fas fa-search text-4xl mb-4"></i>
		<h3 class="text-lg font-semibold mb-2">No clinics found</h3>
		<p>Try adjusting your search criteria or filters.</p>
	</div>
</div>
@endforelse
