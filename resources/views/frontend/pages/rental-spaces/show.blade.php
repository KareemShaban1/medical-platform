@extends('frontend.layouts.app')

@section('title', $rentalSpace->name . ' - ' . __('Rental Space'))

@push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }

        .animation-delay-100 {
            animation-delay: 0.1s;
        }

        .animation-delay-200 {
            animation-delay: 0.2s;
        }

        .animation-delay-300 {
            animation-delay: 0.3s;
        }

        .animation-delay-400 {
            animation-delay: 0.4s;
        }

        .schedule-badge {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .schedule-badge:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .schedule-badge.available {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-color: #10b981;
        }

        .schedule-badge.unavailable {
            background: linear-gradient(135deg, #f9fafb, #f3f4f6);
            border-color: #d1d5db;
        }

        .amenity-tag {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .amenity-tag:hover {
            background: linear-gradient(135deg, #079184, #0aa896);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(7, 145, 132, 0.3);
        }

        .image-gallery-thumb {
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }

        .image-gallery-thumb:hover,
        .image-gallery-thumb.active {
            border-color: #079184;
            box-shadow: 0 4px 15px rgba(7, 145, 132, 0.3);
        }

        .main-image-container {
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
        }

        .main-image-container img {
            transition: transform 0.5s ease;
        }

        .main-image-container:hover img {
            transform: scale(1.05);
        }

        .feature-card {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 1px solid #e2e8f0;
        }

        .feature-card:hover {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-color: #10b981;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.15);
        }

        .contact-card {
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .contact-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .price-display {
            background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
            border: 2px solid #14b8a6;
        }

        .related-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .related-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .related-card:hover .related-card-image {
            transform: scale(1.1);
        }

        .related-card-image {
            transition: transform 0.5s ease;
        }

        .cta-button {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .listing-badge-sale {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .listing-badge-rent {
            background: linear-gradient(135deg, #10b981, #059669);
        }
    </style>
@endpush

@section('content')
    <section class="py-10 bg-gradient-to-b from-gray-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <nav class="mb-8 animate-fade-in">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-500 hover:text-primary transition-colors">
                            <i class="fas fa-home mr-1"></i> {{ __('Home') }}
                        </a>
                    </li>
                    <li class="text-gray-400"><i class="fas fa-chevron-right text-xs"></i></li>
                    <li>
                        <a href="{{ route('rental-spaces') }}" class="text-gray-500 hover:text-primary transition-colors">
                            {{ __('Rental Spaces') }}
                        </a>
                    </li>
                    <li class="text-gray-400"><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-primary font-medium">{{ Str::limit($rentalSpace->name, 30) }}</li>
                </ol>
            </nav>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Left Column - Images & Details (3 cols) -->
                <div class="lg:col-span-3 space-y-8">

                    <!-- Image Gallery -->
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in animation-delay-100">
                        @php
                            $allImages = array_filter(
                                array_merge([$rentalSpace->main_image], $rentalSpace->images ?? []),
                            );
                            $firstImage = count($allImages) ? $allImages[0] : null;
                        @endphp

                        <div class="main-image-container cursor-pointer" onclick="openLightbox('{{ $firstImage }}')">
                            @if ($firstImage)
                                <img id="mainImage" src="{{ $firstImage }}" alt="{{ $rentalSpace->name }}"
                                    class="w-full h-[450px] object-cover">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 hover:opacity-100 transition-opacity flex items-end justify-center pb-6">
                                    <span
                                        class="text-white text-sm font-medium px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full">
                                        <i class="fas fa-expand mr-2"></i> {{ __('Click to expand') }}
                                    </span>
                                </div>
                            @else
                                <div
                                    class="w-full h-[450px] flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <div class="text-center">
                                        <i class="fas fa-building text-7xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-400">{{ __('No image available') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if (count($allImages) > 1)
                            <div class="p-4 bg-gray-50">
                                <div class="flex gap-3 overflow-x-auto pb-2">
                                    @foreach ($allImages as $index => $img)
                                        <img src="{{ $img }}" alt=""
                                            class="image-gallery-thumb h-20 w-28 object-cover rounded-xl cursor-pointer flex-shrink-0 {{ $index === 0 ? 'active' : '' }}"
                                            onclick="changeMainImage('{{ $img }}', this)">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="bg-white rounded-3xl shadow-lg p-8 animate-fade-in animation-delay-200">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                <i class="fas fa-file-alt text-primary"></i>
                            </span>
                            {{ __('Description') }}
                        </h2>
                        <p class="text-gray-600 leading-relaxed text-lg">{{ $rentalSpace->description }}</p>
                    </div>

                    <!-- Amenities Section -->
                    @if ($rentalSpace->amenities && count($rentalSpace->amenities) > 0)
                        <div class="bg-white rounded-3xl shadow-lg p-8 animate-fade-in animation-delay-300">
                            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <span class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                    <i class="fas fa-star text-primary"></i>
                                </span>
                                {{ __('Amenities') }}
                            </h2>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($rentalSpace->amenities_labels as $amenity)
                                    <span
                                        class="amenity-tag px-5 py-3 bg-gray-50 text-gray-700 rounded-xl text-sm font-medium cursor-default">
                                        <i class="fas fa-check-circle text-primary mr-2"></i>{{ $amenity }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Weekly Schedule -->
                    @if ($rentalSpace->schedules->count() > 0)
                        <div class="bg-white rounded-3xl shadow-lg p-8 animate-fade-in animation-delay-400">
                            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <span class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </span>
                                {{ __('Weekly Availability') }}
                            </h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
                                @foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                    @php
                                        $schedule = $rentalSpace->schedules->firstWhere('day_of_week', $day);
                                        $isAvailable = $schedule && $schedule->is_available;
                                    @endphp
                                    <div
                                        class="schedule-badge text-center p-4 rounded-2xl border-2 {{ $isAvailable ? 'available' : 'unavailable' }}">
                                        <div
                                            class="font-bold {{ $isAvailable ? 'text-emerald-700' : 'text-gray-400' }} mb-2">
                                            {{ __(ucfirst(substr($day, 0, 3))) }}
                                        </div>
                                        @if ($isAvailable && $schedule)
                                            <div
                                                class="text-xs {{ $isAvailable ? 'text-emerald-600' : 'text-gray-400' }} space-y-1">
                                                <div class="font-semibold">
                                                    {{ date('g:i A', strtotime($schedule->start_time)) }}</div>
                                                <div class="text-gray-400">{{ __('to') }}</div>
                                                <div class="font-semibold">
                                                    {{ date('g:i A', strtotime($schedule->end_time)) }}</div>
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-400 mt-2">
                                                <i class="fas fa-times-circle mb-1"></i>
                                                <div>{{ __('Closed') }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Sidebar (2 cols) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Main Info Card -->
                    <div class="bg-white rounded-3xl shadow-xl p-8 sticky top-24 animate-fade-in animation-delay-100">
                        <!-- Listing Type Badge -->
                        <div class="mb-5">
                            @if ($rentalSpace->listing_type === 'sale')
                                <span
                                    class="listing-badge-sale inline-flex items-center px-5 py-2 text-white text-sm font-bold rounded-full shadow-lg">
                                    <i class="fas fa-tag mr-2"></i>{{ __('For Sale') }}
                                </span>
                            @else
                                <span
                                    class="listing-badge-rent inline-flex items-center px-5 py-2 text-white text-sm font-bold rounded-full shadow-lg">
                                    <i class="fas fa-key mr-2"></i>{{ __('For Rent') }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">{{ $rentalSpace->name }}</h1>

                        <div class="flex items-center text-gray-500 mb-6">
                            <i class="fas fa-map-marker-alt text-primary mr-3 text-lg"></i>
                            <span class="text-lg">{{ $rentalSpace->location }}</span>
                        </div>

                        <!-- Price Display -->
                        <div class="price-display rounded-2xl p-6 mb-6">
                            @if ($rentalSpace->listing_type === 'sale')
                                <div class="text-sm text-teal-600 mb-1 font-medium">{{ __('Sale Price') }}</div>
                                <div class="text-3xl font-bold text-teal-700">
                                    {{ $rentalSpace->sale_price ? number_format($rentalSpace->sale_price, 0) : __('Price on Request') }}
                                    @if ($rentalSpace->sale_price)
                                        <span class="text-lg font-normal text-teal-600">{{ __('EGP') }}</span>
                                    @endif
                                </div>
                            @else
                                @if ($rentalSpace->pricing)
                                    <div class="text-sm text-teal-600 mb-1 font-medium">{{ __('Rental Price') }}</div>
                                    <div class="text-3xl font-bold text-teal-700">
                                        {{ number_format($rentalSpace->pricing->price, 0) }}
                                        <span class="text-base font-normal text-teal-600">
                                            {{ __('EGP') }} /
                                            {{ __(ucfirst($rentalSpace->pricing->pricing_type ?? 'day')) }}
                                        </span>
                                    </div>
                                    @if ($rentalSpace->pricing->notes)
                                        <div class="text-sm text-teal-600 mt-3 flex items-center gap-2">
                                            <i class="fas fa-info-circle"></i> {{ $rentalSpace->pricing->notes }}
                                        </div>
                                    @endif
                                @else
                                    <div class="text-xl text-gray-400">{{ __('Price on Request') }}</div>
                                @endif
                            @endif
                        </div>

                        <!-- Features Grid -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            @if ($rentalSpace->capacity)
                                <div class="feature-card text-center p-4 rounded-2xl">
                                    <i class="fas fa-users text-2xl text-primary mb-2"></i>
                                    <div class="text-xl font-bold text-gray-800">{{ $rentalSpace->capacity }}</div>
                                    <div class="text-xs text-gray-500">{{ __('Persons') }}</div>
                                </div>
                            @endif
                            @if ($rentalSpace->area_sqm)
                                <div class="feature-card text-center p-4 rounded-2xl">
                                    <i class="fas fa-ruler-combined text-2xl text-primary mb-2"></i>
                                    <div class="text-xl font-bold text-gray-800">
                                        {{ number_format($rentalSpace->area_sqm, 0) }}</div>
                                    <div class="text-xs text-gray-500">{{ __('sqm') }}</div>
                                </div>
                            @endif
                            <div class="feature-card text-center p-4 rounded-2xl">
                                <i class="fas fa-calendar-check text-2xl text-primary mb-2"></i>
                                <div class="text-xl font-bold text-gray-800">
                                    {{ $rentalSpace->schedules->where('is_available', true)->count() }}</div>
                                <div class="text-xs text-gray-500">{{ __('Days') }}</div>
                            </div>
                        </div>

                        <!-- Contact Actions -->
                        @php
                            $clinic = $rentalSpace->clinic;
                            $clinicPhone = $clinic->phone ?? null;
                        @endphp

                        <div class="space-y-3">
                            @if ($clinicPhone)
                                <a href="tel:{{ $clinicPhone }}"
                                    class="cta-button w-full flex items-center justify-center gap-3 px-6 py-4 bg-gradient-primary text-white rounded-2xl font-bold text-lg shadow-lg hover:shadow-xl transition-all">
                                    <i class="fas fa-phone-alt"></i>
                                    <span>{{ __('Call Now') }}</span>
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $clinicPhone) }}" target="_blank"
                                    class="cta-button w-full flex items-center justify-center gap-3 px-6 py-4 text-white rounded-2xl font-bold text-lg shadow-lg hover:shadow-xl transition-all"
                                    style="background: linear-gradient(135deg, #22c55e, #16a34a);">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                    <span>{{ __('WhatsApp') }}</span>
                                </a>
                            @endif
                        </div>

                        <!-- Clinic Info -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
                                {{ __('Listed By') }}</h3>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-gradient-primary text-white flex items-center justify-center">
                                    <i class="fas fa-hospital-alt text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800">{{ $clinic->name ?? __('Clinic') }}</div>
                                    <div class="text-sm text-gray-500">{{ $clinic->address ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Spaces -->
            @if ($relatedSpaces->isNotEmpty())
                <div class="mt-16">
                    <div class="text-center mb-10">
                        <h2 class="text-3xl font-bold text-gray-800 mb-3">{{ __('More Spaces from This Clinic') }}</h2>
                        <p class="text-gray-500">{{ __('Explore other available spaces') }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($relatedSpaces as $space)
                            <a href="{{ route('rental-spaces.show', $space->id) }}"
                                class="related-card block bg-white rounded-3xl shadow-lg overflow-hidden group">
                                <div class="h-48 bg-gray-100 relative overflow-hidden">
                                    @if ($space->main_image)
                                        <img src="{{ $space->main_image }}" alt="{{ $space->name }}"
                                            class="related-card-image h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center">
                                            <i class="fas fa-building text-4xl text-gray-300"></i>
                                        </div>
                                    @endif
                                    <div class="absolute top-4 left-4">
                                        <span
                                            class="px-3 py-1.5 text-xs font-bold rounded-full text-white {{ $space->listing_type === 'sale' ? 'bg-amber-500' : 'bg-emerald-500' }}">
                                            {{ $space->listing_type === 'sale' ? __('Sale') : __('Rent') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3
                                        class="font-bold text-lg text-gray-800 truncate group-hover:text-primary transition-colors">
                                        {{ $space->name }}
                                    </h3>
                                    <div class="text-sm text-gray-500 truncate mt-1 flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        {{ $space->location }}
                                    </div>
                                    <div class="text-primary font-bold text-lg mt-3">
                                        @if ($space->listing_type === 'sale')
                                            {{ $space->sale_price ? number_format($space->sale_price, 0) . ' ' . __('EGP') : __('Contact') }}
                                        @else
                                            {{ optional($space->pricing)->price ? number_format($space->pricing->price, 0) . ' ' . __('EGP') : __('Contact') }}
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <div class="mt-12 text-center">
                <a href="{{ route('rental-spaces') }}"
                    class="inline-flex items-center gap-3 px-8 py-4 bg-gray-100 text-gray-700 rounded-2xl font-semibold hover:bg-gray-200 transition-colors shadow-md">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ __('Back to All Spaces') }}</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black/95 hidden items-center justify-center z-50 cursor-pointer"
        onclick="closeLightbox()">
        <button class="absolute top-6 right-6 text-white text-4xl hover:text-gray-300 transition-colors z-10">
            <i class="fas fa-times"></i>
        </button>
        <img id="lightboxImg" src="" alt=""
            class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl">
    </div>
@endsection

@push('scripts')
    <script>
        function changeMainImage(src, el) {
            const main = document.getElementById('mainImage');
            if (!main) return;
            main.src = src;

            document.querySelectorAll('.image-gallery-thumb').forEach(t => {
                t.classList.remove('active');
            });
            el.classList.add('active');
        }

        function openLightbox(src) {
            if (!src) return;
            const lb = document.getElementById('lightbox');
            const img = document.getElementById('lightboxImg');
            img.src = src;
            lb.classList.remove('hidden');
            lb.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lb = document.getElementById('lightbox');
            lb.classList.add('hidden');
            lb.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });
    </script>
@endpush
