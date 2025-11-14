@forelse($clinics as $clinic)
<div class="clinic-card card overflow-hidden"
	data-specialization="{{ $clinic->specialization ?? 'general' }}"
	data-location="{{ $clinic->location ?? 'downtown' }}"
	data-rating="{{ $clinic->rating ?? rand(3, 5) }}"
	data-name="{{ $clinic->name }}">
	<div class="h-48 bg-gray-200 flex items-center justify-center">
		@php
			$clinicImages = $clinic->getMedia('clinic_images');
			$primaryImage = $clinicImages->first()
				? $clinicImages->first()->getUrl()
				: 'https://ui-avatars.com/api/?name=' . urlencode($clinic->name) . '&size=512&background=0D8ABC&color=fff';
		@endphp
		<img src="{{ $primaryImage }}" alt="{{ $clinic->name }}" class="w-full h-full object-cover">
	</div>
	<div class="p-4">
		<a href="{{ route('clinics.show', $clinic->id) }}" class="font-semibold text-lg mb-2">{{ $clinic->name }}</a>
		<p class="text-gray-600 text-sm mb-2">{{ $clinic->specialization->name ?? 'Specialized medical services' }}</p>

		<div class="flex items-center text-sm text-gray-500 mb-2">
			<i class="fas fa-map-marker-alt mx-2"></i>
			<span>{{ Str::limit($clinic->address ?? 'Location not specified', 40) }}</span>
		</div>

		<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-phone mx-2"></i>
			<span>{{ $clinic->phone ?? 'Contact not available' }}</span>
		</div>


		<a href="{{ route('clinics.show', $clinic->id) }}" class="btn-primary w-full">
			{{ __('view details') }}
		</a>
	</div>
</div>
@empty
<div class="col-span-full text-center py-12">
	<div class="text-gray-500">
		<i class="fas fa-search text-4xl mb-4"></i>
		<h3 class="text-lg font-semibold mb-2">{{ __('no clinics found') }}</h3>
		<p>{{ __('try adjusting your search criteria or filters') }}</p>
	</div>
</div>
@endforelse
