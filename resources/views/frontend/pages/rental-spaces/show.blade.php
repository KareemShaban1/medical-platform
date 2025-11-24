@extends('frontend.layouts.app')

@section('title' , __('Rental Space Details'))
@section('content')
<section class="py-12 bg-gray-50">
	<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="bg-white rounded-lg shadow overflow-hidden">
			<div class="grid grid-cols-1 lg:grid-cols-2">
				<div class="bg-gray-100">
					@php
					$allImages = array_filter(array_merge([$rentalSpace->main_image],
					$rentalSpace->images ?? []));
					$firstImage = count($allImages) ? $allImages[0] : null;
					@endphp

					@if($firstImage)
					<img id="mainImage" src="{{ $firstImage }}" alt=""
						class="w-full h-96 object-cover cursor-zoom-in"
						onclick="openLightbox(this.src)">
					@else
					<div
						class="w-full h-96 flex items-center justify-center text-gray-400">
						<i class="fas fa-building text-5xl"></i>
					</div>
					@endif

					@if(count($allImages) > 1)
					<div class="grid grid-cols-5 gap-2 p-4">
						@foreach($allImages as $index => $img)
						<img src="{{ $img }}" alt=""
							class="h-20 w-full object-cover rounded cursor-pointer border-2 {{ $index === 0 ? 'border-primary' : 'border-transparent' }}"
							onclick="changeMainImage('{{ $img }}', this)">
						@endforeach
					</div>
					@endif
				</div>
				<div class="p-6">
					<h1 class="text-2xl font-bold mb-2">{{ $rentalSpace->name }}</h1>
					<div class="text-gray-600 mb-4 flex items-center">
						<i class="fas fa-map-marker-alt mr-2"></i>
						<span>{{ $rentalSpace->location }}</span>
					</div>
					<p class="text-gray-700 mb-6">{{ $rentalSpace->description }}</p>

					<div class="space-y-2 mb-6">
						<div class="text-gray-700">
							<i class="fas fa-calendar-alt mr-2"></i>
							<span>{{ __('Availability') }}:
								{{ optional($rentalSpace->availability)->type }}</span>
						</div>
						<div class="text-gray-700">
							<i class="fas fa-dollar-sign mr-2"></i>
							<span>{{ __('Price') }}:
								{{ optional($rentalSpace->pricing)->price ? __('EGP') . ' ' . number_format(optional($rentalSpace->pricing)->price, 2) : __('Not specified') }}</span>
						</div>
					</div>

					<div class="flex items-center gap-3">
						<a href="{{ route('rental-spaces') }}"
							class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">{{ __('Back to list') }}</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Lightbox Modal -->
		<div id="lightbox" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50"
			onclick="closeLightbox()">
			<img id="lightboxImg" src="" alt="" class="max-h-[90vh] max-w-[90vw] object-contain">
		</div>

		<!-- Contact & Reservation Section -->
		@php
		$clinic = $rentalSpace->clinic;
		$clinicPhone = $clinic->phone ?? null;
		$clinicAddress = $clinic->address ?? null;
		// $clinicEmail = optional($clinic->clinicUsers->first())->email;
		$mapsUrl = $clinicAddress ? 'https://www.google.com/maps/search/?api=1&query=' .
		urlencode($clinicAddress) : null;
		@endphp

		<div class="mt-8 bg-white rounded-lg shadow p-6">
			<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
				<div>
					<h2 class="text-xl font-semibold">
						{{ __('Contact Clinic to Reserve') }}</h2>
					<p class="text-gray-600">
						{{ __('Reach out to the clinic to check availability and make a reservation for this space.') }}
					</p>
				</div>
				<div class="flex flex-wrap items-center gap-3">
					@if($clinicPhone)
					<a href="tel:{{ $clinicPhone }}"
						class="inline-flex items-center gap-2 px-4 py-2 bg-primary-gradient text-white rounded hover:opacity-90">
						<i class="fas fa-phone"></i>
						<span>{{ __('Call Now') }}</span>
					</a>
					@endif
					{{-- @if($clinicEmail)
                    <a href="mailto:{{ $clinicEmail }}?subject={{ urlencode(__('Rental Space Inquiry') . ' - ' . $rentalSpace->name) }}"
					class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
					rounded hover:bg-blue-700">
					<i class="fas fa-envelope"></i>
					<span>{{ __('Email') }}</span>
					</a>
					@endif --}}
					{{-- @if($mapsUrl)
                    <a href="{{ $mapsUrl }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2
					bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
					<i class="fas fa-map-marker-alt"></i>
					<span>{{ __('View on Map') }}</span>
					</a>
					@endif --}}
				</div>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
				<div class="flex items-start gap-3">
					<div
						class="w-10 h-10 rounded-full bg-primary-gradient text-white flex items-center justify-center">
						<i class="fas fa-phone"></i>
					</div>
					<div>
						<div class="text-sm text-gray-500">{{ __('Phone') }}</div>
						<div class="font-medium">
							{{ $clinicPhone ?? __('Not available') }}</div>
					</div>
				</div>
				{{-- <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('Email') }}
			</div>
			<div class="font-medium">{{ $clinicEmail ?? __('Not available') }}</div>
		</div>
	</div> --}}
	<div class="flex items-start gap-3">
		<div class="w-10 h-10 rounded-full bg-gray-700 text-white flex items-center justify-center">
			<i class="fas fa-map-marker-alt"></i>
		</div>
		<div>
			<div class="text-sm text-gray-500">{{ __('Address') }}</div>
			<div class="font-medium">{{ $clinicAddress ?? __('Not available') }}</div>
		</div>
	</div>
	</div>
	</div>

	@if($relatedSpaces->isNotEmpty())
	<div class="mt-10">
		<h2 class="text-xl font-semibold mb-4">{{ __('Other spaces in this clinic') }}</h2>
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
			@foreach($relatedSpaces as $space)
			<a href="{{ route('rental-spaces.show', $space->id) }}"
				class="block bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
				<div class="h-36 bg-gray-100 flex items-center justify-center">
					@if($space->main_image)
					<img src="{{ $space->main_image }}" alt=""
						class="h-full w-full object-cover">
					@else
					<i class="fas fa-building text-3xl text-gray-400"></i>
					@endif
				</div>
				<div class="p-4">
					<h3 class="font-semibold truncate">{{ $space->name }}</h3>
					<div class="text-sm text-gray-500 truncate">{{ $space->location }}
					</div>
				</div>
			</a>
			@endforeach
		</div>
	</div>
	@endif
	</div>
</section>
@endsection

@push('scripts')
<script>
function changeMainImage(src, el) {
	const main = document.getElementById('mainImage');
	if (!main) return;
	main.src = src;
	// highlight selected thumbnail
	document.querySelectorAll('[onclick^="changeMainImage"]').forEach(t => t.classList.remove('border-primary'));
	el.classList.add('border-primary');
}

function openLightbox(src) {
	const lb = document.getElementById('lightbox');
	const img = document.getElementById('lightboxImg');
	img.src = src;
	lb.classList.remove('hidden');
	lb.classList.add('flex');
}

function closeLightbox() {
	const lb = document.getElementById('lightbox');
	lb.classList.add('hidden');
	lb.classList.remove('flex');
}
</script>
@endpush
