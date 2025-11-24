@foreach($rentalSpaces as $space)
<div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow relative">
    <a href="{{ route('rental-spaces.show', $space->id) }}" class="absolute inset-0" aria-label="{{ $space->name }}"></a>
    <div class="h-48 bg-gray-200 flex items-center justify-center">
        @if($space->main_image)
            <img src="{{ $space->main_image }}" alt="Rental Space Image" class="w-full h-full object-cover">
        @else
            <i class="fas fa-building text-4xl text-gray-400"></i>
        @endif
    </div>
    <div class="p-4">
        <h3 class="font-semibold text-lg mb-2 truncate">
            <a href="{{ route('rental-spaces.show', $space->id) }}">
                {{ $space->name }}
            </a>
        </h3>
        <p class="text-gray-600 text-sm mb-2 line-clamp-2">
            {{ $space->description }}
        </p>
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <i class="fas fa-map-marker-alt mr-2"></i>
            <span class="truncate">{{ $space->location }}</span>
        </div>
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                <i class="fas fa-calendar-alt mr-2"></i>
                <span>{{ optional($space->availability)->type }}</span>
            </div>
            <div class="text-md text-blue-600 font-semibold">
                <i class="fas fa-dollar-sign mr-1"></i>
                <span>
					{{ optional($space->pricing)->price ? __('EGP') . ' ' . number_format(optional($space->pricing)->price, 2) : __('Not specified') }}
				</span>
            </div>
        </div>
    </div>
</div>
@endforeach
