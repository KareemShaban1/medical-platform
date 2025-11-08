@extends('frontend.layouts.app')

@push('styles')
<style>
@keyframes fadeInUp {from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
.animate-fade-in-up{animation:fadeInUp .8s ease-out forwards;opacity:0}
.animation-delay-200{animation-delay:.2s}.animation-delay-400{animation-delay:.4s}
.spinner{width:40px;height:40px;border:4px solid rgba(7,145,132,.3);border-top:4px solid #079184;border-radius:50%;animation:spin 1s linear infinite}
@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
.form-input{transition:all .3s ease;border:2px solid #e5e7eb}
.form-input:focus{border-color:#079184;box-shadow:0 0 0 3px rgba(7,145,132,.1);transform:scale(1.02)}
.product-card{transition:all .3s ease}
.product-card:hover{transform:translateY(-8px);box-shadow:0 20px 40px rgba(0,0,0,.1)}
.text-gradient{background:linear-gradient(135deg,#079184,#0aa896);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.sticky{position:sticky;top:0;z-index:40}
#heroSearch::placeholder{color:white;opacity:1}
#filtersPanel{transition:all .3s ease}
::-webkit-scrollbar{width:8px}::-webkit-scrollbar-track{background:#f1f1f1}::-webkit-scrollbar-thumb{background:linear-gradient(135deg,#079184,#0aa896);border-radius:4px}::-webkit-scrollbar-thumb:hover{background:linear-gradient(135deg,#056b5f,#079184)}html{scroll-behavior:smooth}
.loading{opacity:.6;pointer-events:none}
@media (max-width:768px){.animate-fade-in-up{animation-delay:0s}#filtersPanel{margin:0 -1rem;border-radius:0}.grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4{grid-template-columns:1fr;gap:1rem}}
</style>
@endpush

@section('content')

<!-- Hero Section matching Products -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden bg-gradient-primary">
  <div class="absolute inset-0">
    <div class="absolute top-20 left-10 w-32 h-32 bg-white/10 rounded-full animate-pulse"></div>
    <div class="absolute top-40 right-20 w-24 h-24 bg-white/5 rounded-full animate-bounce"></div>
    <div class="absolute bottom-32 left-1/4 w-16 h-16 bg-white/15 rounded-full animate-ping"></div>
    <div class="absolute top-1/3 right-1/3 w-12 h-12 bg-white/20 rounded-full animate-pulse"></div>
    <div class="absolute bottom-20 right-10 w-20 h-20 bg-white/8 rounded-full animate-bounce"></div>
  </div>
  <div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-up">
      <span class="text-white bg-clip-text text-transparent">{{ __('rental spaces') }}</span>
    </h1>
    <p class="text-xl md:text-2xl mb-8 animate-fade-in-up animation-delay-200 opacity-90">
      {{ __('discover available medical rooms and clinic spaces for rent') }}
    </p>
    <div class="max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
      <div class="relative group">
        <input type="text" id="heroSearch" placeholder="{{ __('search rental spaces...') }}" class="w-full px-6 py-4 pl-14 pr-6 text-white rounded-full shadow-2xl focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-300 group-hover:scale-105">
        <i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/4 text-white text-lg"></i>
      </div>
    </div>
  </div>
</section>

<!-- Sticky Filters Bar (matching Products) -->
<section class="py-8 bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-4">
        <button id="toggleFilters" class="group flex items-center space-x-3 px-6 py-3 bg-gradient-primary text-white rounded-xl hover:scale-105 transition-all duration-300 shadow-lg">
          <i class="fas fa-filter text-lg"></i>
          <span class="font-semibold">{{ __('filters') }}</span>
          <i id="filterChevron" class="fas fa-chevron-down transition-transform duration-300"></i>
        </button>
        <div id="activeFiltersCount" class="hidden bg-primary text-white px-3 py-1 rounded-full text-sm font-semibold">
          <span id="filterCount">0</span> {{ __('filters') }}
        </div>
      </div>
      <div class="flex items-center space-x-4">
        <div class="bg-gray-100 rounded-xl px-4 py-2">
          <span class="text-gray-600">{{ __('showing') }} <span id="resultsCount" class="font-bold text-primary">{{ $rentalSpaces->total() }}</span> {{ __('spaces') }}</span>
        </div>
        <div class="hidden md:flex items-center space-x-2">
          <span class="text-sm text-gray-500">{{ __('view') }}:</span>
          <button id="gridView" class="p-3 bg-gradient-primary text-white rounded-xl">
            <i class="fas fa-th"></i>
          </button>
          <button id="listView" class="p-3 bg-gray-200 text-gray-600 rounded-xl">
            <i class="fas fa-list"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Collapsible Filters Panel -->
    <div id="filtersPanel" class="hidden bg-gray-50 rounded-2xl p-6 shadow-lg border border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('search rental spaces') }}</label>
          <div class="relative">
            <input type="text" id="search" placeholder="{{ __('search rental spaces...') }}" class="form-input w-full pl-10 pr-4 py-3 group-hover:scale-105 transition-transform duration-300">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
        </div>
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('availability') }}</label>
          <div class="relative">
            <select id="availability" class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
              <option value="">{{ __('any') }}</option>
              <option value="hourly">{{ __('hourly') }}</option>
              <option value="daily">{{ __('daily') }}</option>
              <option value="weekly">{{ __('weekly') }}</option>
              <option value="monthly">{{ __('monthly') }}</option>
            </select>
          </div>
        </div>
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('price range') }}</label>
          <div class="relative">
            <select id="price" class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
              <option value="">{{ __('all prices') }}</option>
              <option value="0-500">0 - 500</option>
              <option value="500-1000">500 - 1000</option>
              <option value="1000+">1000+</option>
            </select>
          </div>
        </div>
        <div class="group">
          <label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('sort by') }}</label>
          <div class="relative">
            <select id="sort" class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
              <option value="name">{{ __('name a-z') }}</option>
              <option value="name-desc">{{ __('name z-a') }}</option>
              <option value="newest">{{ __('newest first') }}</option>
              <option value="oldest">{{ __('oldest first') }}</option>
            </select>
          </div>
        </div>
      </div>
      <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-6 border-t border-gray-200 gap-4">
        <div class="flex items-center space-x-4">
          <button id="clearFilters" class="btn-secondary group">
            <i class="fas fa-refresh mx-2 group-hover:rotate-180 transition-transform duration-500"></i>
            {{ __('clear all filters') }}
          </button>
        </div>
        <div id="activeFilters" class="flex flex-wrap gap-2"></div>
      </div>
    </div>
  </div>
</section>

<!-- Grid Section -->
<section class="py-12 bg-gray-50 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div id="loadingSpinner" class="hidden text-center py-12">
      <div class="inline-flex items-center space-x-3">
        <div class="spinner"></div>
        <span class="text-gray-600 font-medium">{{ __('loading rental spaces...') }}</span>
      </div>
    </div>
    <div id="rentalSpacesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
      @include('frontend.pages.rental-spaces.partials.rental-spaces-grid', ['rentalSpaces' => $rentalSpaces])
    </div>
    <!-- Enhanced Pagination -->
    <x-frontend.pagination :paginator="$rentalSpaces" container-class="mt-12" :show-info="true"
			:max-pages="7" :show-first-last="false"/>
  </div>
</section>

@endsection

@include('frontend.pages.rental-spaces.scripts.index-js')
