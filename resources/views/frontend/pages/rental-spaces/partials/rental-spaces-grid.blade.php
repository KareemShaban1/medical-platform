@forelse($rentalSpaces as $space)
    <div class="product-card bg-white rounded-3xl shadow-lg overflow-hidden relative group">
        <a href="{{ route('rental-spaces.show', $space->id) }}" class="absolute inset-0 z-10"
            aria-label="{{ $space->name }}"></a>

        <!-- Image Section -->
        <div class="h-56 bg-gray-100 relative overflow-hidden">
            @if ($space->main_image)
                <img src="{{ $space->main_image }}" alt="{{ $space->name }}"
                    class="card-image w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                    <i class="fas fa-building text-5xl text-gray-300"></i>
                </div>
            @endif

            <!-- Overlay Gradient -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            </div>

            <!-- Listing Type Badge -->
            <div class="absolute top-4 left-4">
                @if ($space->listing_type === 'sale')
                    <span
                        class="px-4 py-1.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1.5">
                        <i class="fas fa-tag"></i>{{ __('For Sale') }}
                    </span>
                @else
                    <span
                        class="px-4 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1.5">
                        <i class="fas fa-key"></i>{{ __('For Rent') }}
                    </span>
                @endif
            </div>

            <!-- Pricing Type Badge -->
            @if ($space->listing_type === 'rent' && optional($space->pricing)->pricing_type)
                <div class="absolute top-4 right-4">
                    <span
                        class="px-3 py-1.5 bg-white/95 backdrop-blur-sm text-gray-700 text-xs font-semibold rounded-full shadow-md">
                        {{ __(ucfirst($space->pricing->pricing_type)) }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <h3 class="font-bold text-lg mb-2 text-gray-800 truncate group-hover:text-primary transition-colors">
                {{ $space->name }}
            </h3>

            <p class="text-gray-500 text-sm mb-4 line-clamp-2 leading-relaxed">
                {{ Str::limit($space->description, 70) }}
            </p>

            <!-- Location -->
            <div class="flex items-center text-sm text-gray-500 mb-4">
                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                <span class="truncate">{{ Str::limit($space->location, 35) }}</span>
            </div>

            <!-- Features Row -->
            <div class="flex items-center gap-4 mb-4 text-xs text-gray-500">
                @if ($space->capacity)
                    <div class="flex items-center gap-1.5 bg-gray-100 px-2.5 py-1.5 rounded-lg">
                        <i class="fas fa-users text-primary"></i>
                        <span class="font-medium">{{ $space->capacity }}</span>
                    </div>
                @endif
                @if ($space->area_sqm)
                    <div class="flex items-center gap-1.5 bg-gray-100 px-2.5 py-1.5 rounded-lg">
                        <i class="fas fa-ruler-combined text-primary"></i>
                        <span class="font-medium">{{ number_format($space->area_sqm, 0) }} {{ __('sqm') }}</span>
                    </div>
                @endif

            </div>

            <!-- Price & CTA -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="text-left">
                    @if ($space->listing_type === 'sale')
                        @if ($space->sale_price)
                            <span
                                class="text-xl font-bold text-amber-600">{{ number_format($space->sale_price, 0) }}</span>
                            <span class="text-xs text-gray-500 ml-1">{{ __('EGP') }}</span>
                        @else
                            <span class="text-sm text-gray-400 font-medium">{{ __('Price on Request') }}</span>
                        @endif
                    @else
                        @if (optional($space->pricing)->price)
                            <span
                                class="text-xl font-bold text-primary">{{ number_format($space->pricing->price, 0) }}</span>
                            <span
                                class="text-xs text-gray-500 ml-1">{{ __('EGP') }}/{{ __(ucfirst(substr($space->pricing->pricing_type ?? 'day', 0, 3))) }}</span>
                        @else
                            <span class="text-sm text-gray-400 font-medium">{{ __('Price on Request') }}</span>
                        @endif
                    @endif
                </div>
                <div class="relative z-20">
                    <a href="{{ route('rental-spaces.show', $space->id) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-xl text-sm font-semibold hover:bg-primary hover:text-white transition-all">
                        {{ __('Details') }}
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-span-full">
        <div class="text-center py-20 bg-white rounded-3xl shadow-lg">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                <i class="fas fa-building text-5xl text-gray-300"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">{{ __('No Rental Spaces Found') }}</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                {{ __('Try adjusting your filters or check back later for new listings.') }}</p>
            <a href="{{ route('rental-spaces') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                <i class="fas fa-redo"></i>
                {{ __('Reset Filters') }}
            </a>
        </div>
    </div>
@endforelse
