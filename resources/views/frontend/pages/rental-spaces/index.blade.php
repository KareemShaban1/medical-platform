@extends('frontend.layouts.app')

@section('title', __('Rental Spaces'))
@push('styles')
    <style>
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

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animation-delay-200 {
            animation-delay: 0.2s;
        }

        .animation-delay-400 {
            animation-delay: 0.4s;
        }

        .animation-delay-600 {
            animation-delay: 0.6s;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(7, 145, 132, 0.3);
            border-top: 4px solid #079184;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .form-input {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            background: white;
        }

        .form-input:focus {
            border-color: #079184;
            box-shadow: 0 0 0 4px rgba(7, 145, 132, 0.1);
            outline: none;
        }

        .product-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 1.5rem;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .product-card:hover .card-image {
            transform: scale(1.1);
        }

        .card-image {
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .filter-btn {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            border-radius: 9999px;
        }

        .filter-btn.active {
            border-color: #079184;
            background: linear-gradient(135deg, rgba(7, 145, 132, 0.15), rgba(10, 168, 150, 0.1));
            color: #079184;
            font-weight: 600;
        }

        .filter-btn:hover:not(.active) {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(7, 145, 132, 0.2);
            background: rgba(7, 145, 132, 0.05);
        }

        #heroSearch {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        #heroSearch:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.4);
        }

        #heroSearch::placeholder {
            color: white;
            opacity: 1;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .price-badge {
            background: linear-gradient(135deg, #079184, #0aa896);
            box-shadow: 0 4px 15px rgba(7, 145, 132, 0.3);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        html {
            scroll-behavior: smooth;
        }

        .loading {
            opacity: 0.5;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .animate-fade-in-up {
                animation-delay: 0s;
            }
        }
    </style>
@endpush

@section('content')

    <!-- Enhanced Hero Section -->
    <section class="relative min-h-[65vh] flex items-center justify-center overflow-hidden bg-gradient-primary">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-10 w-40 h-40 bg-white/10 rounded-full animate-float"></div>
            <div class="absolute top-60 right-16 w-32 h-32 bg-white/5 rounded-full animate-float animation-delay-200"></div>
            <div class="absolute bottom-20 left-1/4 w-24 h-24 bg-white/15 rounded-full animate-float animation-delay-400">
            </div>
            <div class="absolute top-1/3 right-1/4 w-16 h-16 bg-white/20 rounded-full animate-float animation-delay-600">
            </div>
            <div class="absolute bottom-40 right-10 w-28 h-28 bg-white/8 rounded-full animate-float"></div>
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/20"></div>
        </div>

        <div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
            <div class="mb-6 animate-fade-in-up">
                {{-- <span
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium">
                    <i class="fas fa-building text-emerald-300"></i>
                    {{ __('Premium Medical Spaces') }}
                </span> --}}
            </div>

            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 animate-fade-in-up animation-delay-200">
                {{ __('Rental Spaces') }}
            </h1>

            <p class="text-xl md:text-2xl mb-10 animate-fade-in-up animation-delay-400 opacity-90 max-w-3xl mx-auto">
                {{ __('Find the perfect medical room or clinic space for your practice') }}
            </p>

            <!-- Interactive Search Bar -->
            <div class="max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
                <div class="relative group">
                    <input type="text" id="heroSearch" placeholder="{{ __('Search by name or location...') }}"
                        class="w-full px-6 py-4 pl-14 pr-6 text-white rounded-full shadow-2xl focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-300 group-hover:scale-105">
                    <i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/4 text-white text-lg"></i>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-12 flex flex-wrap justify-center gap-6 animate-fade-in-up animation-delay-600">
                <div class="stats-card px-6 py-3 rounded-xl">
                    <span class="text-2xl font-bold">{{ $rentalSpaces->total() }}+</span>
                    <span class="text-sm opacity-80 ms-2">{{ __('Available Spaces') }}</span>
                </div>
                <div class="stats-card px-6 py-3 rounded-xl">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <span class="text-sm">{{ __('Multiple Locations') }}</span>
                </div>
                <div class="stats-card px-6 py-3 rounded-xl">
                    <i class="fas fa-shield-alt me-2"></i>
                    <span class="text-sm">{{ __('Verified Clinics') }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Filter Bar -->
    <section class="py-6 bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <!-- Listing Type Toggle -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-semibold text-gray-500 mr-2">{{ __('Type') }}:</span>
                    <button type="button" class="filter-btn active px-5 py-2.5 text-sm bg-gray-100"
                        data-filter="listing_type" data-value="">
                        <i class="fas fa-th-large mr-1"></i> {{ __('All') }}
                    </button>
                    <button type="button" class="filter-btn px-5 py-2.5 text-sm bg-gray-100" data-filter="listing_type"
                        data-value="rent">
                        <i class="fas fa-key mr-1 text-emerald-500"></i> {{ __('For Rent') }}
                    </button>
                    <button type="button" class="filter-btn px-5 py-2.5 text-sm bg-gray-100" data-filter="listing_type"
                        data-value="sale">
                        <i class="fas fa-tag mr-1 text-amber-500"></i> {{ __('For Sale') }}
                    </button>
                </div>

                <!-- Results & Filter Toggle -->
                <div class="flex items-center gap-4">
                    <div
                        class="hidden sm:flex items-center gap-2 bg-gradient-to-r from-primary/10 to-primary/5 rounded-xl px-5 py-2.5">
                        <i class="fas fa-building text-primary"></i>
                        <span class="text-gray-700 font-medium">
                            <span id="resultsCount"
                                class="font-bold text-primary text-lg">{{ $rentalSpaces->total() }}</span>
                            {{ __('spaces') }}
                        </span>
                    </div>
                    <button id="toggleFilters"
                        class="flex items-center gap-2 px-5 py-2.5 bg-gradient-primary text-white rounded-xl hover:opacity-90 transition-all shadow-md">
                        <i class="fas fa-sliders-h"></i>
                        <span class="font-medium">{{ __('Filters') }}</span>
                        <i id="filterChevron" class="fas fa-chevron-down transition-transform duration-300 text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Advanced Filters Panel -->
    <section id="filtersPanel" class="hidden bg-gray-50/80 border-b border-gray-200 glass-panel">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-search text-primary mr-1"></i> {{ __('Search') }}
                    </label>
                    <div class="relative">
                        <input type="text" id="search" placeholder="{{ __('Name or location...') }}"
                            class="form-input w-full pl-11">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Pricing Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock text-primary mr-1"></i> {{ __('Pricing') }}
                    </label>
                    <select id="pricing_type" class="form-input w-full">
                        <option value="">{{ __('All Pricing') }}</option>
                        <option value="hourly">{{ __('Hourly') }}</option>
                        <option value="daily">{{ __('Daily') }}</option>
                        <option value="weekly">{{ __('Weekly') }}</option>
                        <option value="monthly">{{ __('Monthly') }}</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-coins text-primary mr-1"></i> {{ __('Price Range') }}
                    </label>
                    <select id="price" class="form-input w-full">
                        <option value="">{{ __('All Prices') }}</option>
                        <option value="0-500">0 - 500 {{ __('EGP') }}</option>
                        <option value="500-1000">500 - 1,000 {{ __('EGP') }}</option>
                        <option value="1000-5000">1,000 - 5,000 {{ __('EGP') }}</option>
                        <option value="5000+">5,000+ {{ __('EGP') }}</option>
                    </select>
                </div>

                <!-- Available Day -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-check text-primary mr-1"></i> {{ __('Available Day') }}
                    </label>
                    <select id="available_day" class="form-input w-full">
                        <option value="">{{ __('Any Day') }}</option>
                        <option value="sunday">{{ __('Sunday') }}</option>
                        <option value="monday">{{ __('Monday') }}</option>
                        <option value="tuesday">{{ __('Tuesday') }}</option>
                        <option value="wednesday">{{ __('Wednesday') }}</option>
                        <option value="thursday">{{ __('Thursday') }}</option>
                        <option value="friday">{{ __('Friday') }}</option>
                        <option value="saturday">{{ __('Saturday') }}</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sort text-primary mr-1"></i> {{ __('Sort By') }}
                    </label>
                    <select id="sort" class="form-input w-full">
                        <option value="newest">{{ __('Newest First') }}</option>
                        <option value="oldest">{{ __('Oldest First') }}</option>
                        <option value="price_low">{{ __('Price: Low to High') }}</option>
                        <option value="price_high">{{ __('Price: High to Low') }}</option>
                        <option value="name">{{ __('Name A-Z') }}</option>
                    </select>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-center justify-between mt-6 pt-5 border-t border-gray-200">
                <button id="clearFilters"
                    class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-primary hover:bg-primary/5 rounded-lg transition-all">
                    <i class="fas fa-times-circle"></i>
                    <span class="font-medium">{{ __('Clear All Filters') }}</span>
                </button>
                <div id="activeFilters" class="flex flex-wrap gap-2"></div>
            </div>
        </div>
    </section>

    <!-- Grid Section -->
    <section class="py-16 bg-gradient-to-b from-gray-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">{{ __('Available Spaces') }}</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">
                    {{ __('Browse our curated selection of premium medical and clinic spaces') }}</p>
            </div>

            <div id="loadingSpinner" class="hidden text-center py-16">
                <div class="inline-flex flex-col items-center gap-4">
                    <div class="spinner"></div>
                    <span class="text-gray-600 font-medium">{{ __('Loading rental spaces...') }}</span>
                </div>
            </div>

            <div id="rentalSpacesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @include('frontend.pages.rental-spaces.partials.rental-spaces-grid', [
                    'rentalSpaces' => $rentalSpaces,
                ])
            </div>

            <!-- Pagination - Only show when there are results -->
            <div id="paginationContainer" class="mt-16">
                @if ($rentalSpaces->count() > 0)
                    <x-frontend.pagination :paginator="$rentalSpaces" container-class="" :show-info="true" :max-pages="7"
                        :show-first-last="false" />
                @endif
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleFilters = document.getElementById('toggleFilters');
            const filtersPanel = document.getElementById('filtersPanel');
            const filterChevron = document.getElementById('filterChevron');
            const heroSearch = document.getElementById('heroSearch');
            const searchInput = document.getElementById('search');
            const clearFilters = document.getElementById('clearFilters');
            const filterBtns = document.querySelectorAll('.filter-btn');

            // Toggle filters panel
            toggleFilters.addEventListener('click', function() {
                filtersPanel.classList.toggle('hidden');
                filterChevron.classList.toggle('rotate-180');
            });

            // Sync hero search with filter search
            heroSearch.addEventListener('input', function() {
                searchInput.value = this.value;
                applyFilters();
            });

            // Filter button clicks (listing type)
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;

                    // Update active state
                    document.querySelectorAll(`.filter-btn[data-filter="${filter}"]`).forEach(b => b
                        .classList.remove('active'));
                    this.classList.add('active');

                    applyFilters();
                });
            });

            // Filter inputs
            ['search', 'pricing_type', 'price', 'available_day', 'sort'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', applyFilters);
                    if (id === 'search') el.addEventListener('input', debounce(applyFilters, 400));
                }
            });

            // Clear filters
            clearFilters.addEventListener('click', function() {
                document.getElementById('search').value = '';
                document.getElementById('heroSearch').value = '';
                document.getElementById('pricing_type').value = '';
                document.getElementById('price').value = '';
                document.getElementById('available_day').value = '';
                document.getElementById('sort').value = 'newest';

                filterBtns.forEach(btn => {
                    if (btn.dataset.value === '') btn.classList.add('active');
                    else btn.classList.remove('active');
                });

                applyFilters();
            });

            function applyFilters() {
                const grid = document.getElementById('rentalSpacesGrid');
                const spinner = document.getElementById('loadingSpinner');

                grid.classList.add('loading');
                spinner.classList.remove('hidden');

                const params = new URLSearchParams();

                // Get listing type from active button
                const activeListingBtn = document.querySelector('.filter-btn[data-filter="listing_type"].active');
                if (activeListingBtn && activeListingBtn.dataset.value) {
                    params.append('listing_type', activeListingBtn.dataset.value);
                }

                const search = document.getElementById('search').value;
                const pricingType = document.getElementById('pricing_type').value;
                const price = document.getElementById('price').value;
                const availableDay = document.getElementById('available_day').value;
                const sort = document.getElementById('sort').value;

                if (search) params.append('search', search);
                if (pricingType) params.append('pricing_type', pricingType);
                if (price) params.append('price', price);
                if (availableDay) params.append('available_day', availableDay);
                if (sort) params.append('sort', sort);

                fetch(`{{ route('rental-spaces') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        grid.innerHTML = html;
                        grid.classList.remove('loading');
                        spinner.classList.add('hidden');
                        updateResultsCount();
                    })
                    .catch(err => {
                        console.error('Filter error:', err);
                        grid.classList.remove('loading');
                        spinner.classList.add('hidden');
                    });
            }

            function updateResultsCount() {
                const cards = document.querySelectorAll('#rentalSpacesGrid .product-card');
                const count = cards.length;
                document.getElementById('resultsCount').textContent = count;

                // Hide pagination when no results
                const paginationContainer = document.getElementById('paginationContainer');
                if (paginationContainer) {
                    paginationContainer.style.display = count > 0 ? 'block' : 'none';
                }
            }

            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }
        });
    </script>
@endpush
