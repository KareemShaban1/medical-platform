@extends('frontend.layouts.app')


@section('title' , __('Products'))
@push('styles')
<style>
/* Custom Animations */
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

.animate-fade-in-up {
	animation: fadeInUp 0.8s ease-out forwards;
	opacity: 0;
}

.animation-delay-200 {
	animation-delay: 0.2s;
}

.animation-delay-400 {
	animation-delay: 0.4s;
}

/* Custom Spinner */
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

/* Enhanced Form Inputs */
.form-input {
	transition: all 0.3s ease;
	border: 2px solid #e5e7eb;
}

.form-input:focus {
	border-color: #079184;
	box-shadow: 0 0 0 3px rgba(7, 145, 132, 0.1);
	transform: scale(1.02);
}

/* Product Card Hover Effects */
.product-card {
	transition: all 0.3s ease;
}

.product-card:hover {
	transform: translateY(-8px);
	box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Gradient Text */
.text-gradient {
	background: linear-gradient(135deg, #079184, #0aa896);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
}

/* Sticky Filters */
.sticky {
	position: sticky;
	top: 0;
	z-index: 40;
}

#heroSearch::placeholder {
	color: white;
	opacity: 1;
	/* Ensures full visibility */
}

/* Filter Panel Animation */
#filtersPanel {
	transition: all 0.3s ease;
}

/* Active Filter Chips */
#activeFilters .bg-primary {
	background: linear-gradient(135deg, #079184, #0aa896);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
	width: 8px;
}

::-webkit-scrollbar-track {
	background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
	background: linear-gradient(135deg, #079184, #0aa896);
	border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
	background: linear-gradient(135deg, #056b5f, #079184);
}

/* Smooth scrolling */
html {
	scroll-behavior: smooth;
}

/* Loading states */
.loading {
	opacity: 0.6;
	pointer-events: none;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
	.animate-fade-in-up {
		animation-delay: 0s;
	}

	#filtersPanel {
		margin: 0 -1rem;
		border-radius: 0;
	}

	.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 {
		grid-template-columns: 1fr;
		gap: 1rem;
	}
}
</style>
@endpush

@section('content')

<!-- Hero Section with Advanced Animations -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden bg-gradient-primary">
	<!-- Animated Background Elements -->
	<div class="absolute inset-0">
		<div class="absolute top-20 left-10 w-32 h-32 bg-white/10 rounded-full animate-pulse"></div>
		<div class="absolute top-40 right-20 w-24 h-24 bg-white/5 rounded-full animate-bounce"></div>
		<div class="absolute bottom-32 left-1/4 w-16 h-16 bg-white/15 rounded-full animate-ping"></div>
		<div class="absolute top-1/3 right-1/3 w-12 h-12 bg-white/20 rounded-full animate-pulse"></div>
		<div class="absolute bottom-20 right-10 w-20 h-20 bg-white/8 rounded-full animate-bounce"></div>
	</div>

	<div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
		<h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-up">
			<span class="text-white bg-clip-text text-transparent">
				{{ __('medical products') }}
			</span>
		</h1>
		<p class="text-xl md:text-2xl mb-8 animate-fade-in-up animation-delay-200 opacity-90">
			{{ __('discover premium medical equipment and supplies') }}
		</p>

		<!-- Interactive Search Bar -->
		<div class="max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
			<div class="relative group">
				<input type="text" id="heroSearch"
					placeholder="{{ __('search for medical products...') }}"
					class="w-full px-6 py-4 pl-14 pr-6 text-white
                    rounded-full shadow-2xl focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-300 group-hover:scale-105">
				<i
					class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/4 text-white text-lg"></i>
				<!-- <button
					class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gradient-primary text-white px-6 py-2 rounded-full hover:scale-105 transition-transform duration-300">
					Search
				</button> -->
			</div>
		</div>
	</div>
</section>

<!-- Horizontal Filters Section -->
<section class="py-8 bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<!-- Filter Toggle Button -->
		<div class="flex items-center justify-between mb-6">
			<div class="flex items-center space-x-4">
				<button id="toggleFilters"
					class="group flex items-center space-x-3 px-6 py-3 bg-gradient-primary text-white rounded-xl hover:scale-105 transition-all duration-300 shadow-lg">
					<i class="fas fa-filter text-lg"></i>
					<span class="font-semibold">{{ __('filters') }}</span>
					<i id="filterChevron"
						class="fas fa-chevron-down transition-transform duration-300"></i>
				</button>

				<!-- Active Filters Count -->
				<div id="activeFiltersCount"
					class="hidden bg-primary text-white px-3 py-1 rounded-full text-sm font-semibold">
					<span id="filterCount">0</span> {{ __('filters') }}
				</div>
			</div>

			<!-- Results Count -->
			<div class="flex items-center space-x-4">
				<div class="bg-gray-100 rounded-xl px-4 py-2">
					<span class="text-gray-600">{{ __('showing') }} <span
							id="resultsCount"
							class="font-bold text-primary">{{ $products->total() }}</span>
						{{ __('products') }}</span>
				</div>

				<!-- View Toggle -->
				<div class="hidden md:flex items-center space-x-2">
					<span class="text-sm text-gray-500">{{ __('view') }}:</span>
					<button id="gridView"
						class="p-3 bg-gradient-primary text-white rounded-xl hover:scale-110 transition-transform duration-300 shadow-lg">
						<i class="fas fa-th"></i>
					</button>
					<button id="listView"
						class="p-3 bg-gray-200 text-gray-600 rounded-xl hover:scale-110 transition-transform duration-300">
						<i class="fas fa-list"></i>
					</button>
				</div>
			</div>
		</div>

		<!-- Collapsible Filters Panel -->
		<div id="filtersPanel" class="hidden bg-gray-50 rounded-2xl p-6 shadow-lg border border-gray-200">
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
				<!-- Search Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">
						{{ __('search products') }}
					</label>
					<div class="relative">
						<input type="text" id="search"
							placeholder="{{ __('search products...') }}"
							class="form-input w-full pl-10 pr-4 py-3 group-hover:scale-105 transition-transform duration-300">
						<i
							class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
					</div>
				</div>

				<!-- Category Filter -->
				<div class="group">
					<label
						class="block text-sm font-semibold text-gray-700 mb-3">{{ __('category') }}</label>
					<div class="relative">
						<select id="category"
							class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="">{{ __('all categories') }}
							</option>
							@foreach($categories as $category)
							<option value="{{ $category->id }}">
								{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}
							</option>
							@endforeach
						</select>

					</div>
				</div>

				<!-- Price Range Filter -->
				<div class="group">
					<label
						class="block text-sm font-semibold text-gray-700 mb-3">{{ __('price range') }}</label>
					<div class="relative">
						<select id="priceRange"
							class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="">{{ __('all prices') }}</option>
							<!-- highest price -->
							<option value="highest">{{ __('highest price') }}
							</option>
							<!-- lowest pice -->
							<option value="lowest">{{ __('lowest price') }}
							</option>

						</select>

					</div>
				</div>

				<!-- Sort Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">
						{{ __('sort by') }}
					</label>
					<div class="relative">
						<select id="sort"
							class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="name">{{ __('name a-z') }}</option>
							<option value="name-desc">{{ __('name z-a') }}
							</option>
							<option value="price">
								{{ __('price low to high') }}</option>
							<option value="price-desc">
								{{ __('price high to low') }}
							</option>
							<option value="newest">{{ __('newest first') }}
							</option>
						</select>

					</div>
				</div>
			</div>

			<!-- Filter Actions -->
			<div
				class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-6 border-t border-gray-200 gap-4">
				<div class="flex items-center space-x-4">
					<button id="clearFilters" class="btn-secondary group">
						<i
							class="fas fa-refresh mx-2 group-hover:rotate-180 transition-transform duration-500"></i>
						{{ __('clear all filters') }}
					</button>
					<button id="applyFilters" class="btn-primary">
						<i class="fas fa-check mx-2"></i>
						{{ __('apply filters') }}
					</button>
				</div>

				<!-- Active Filters Display -->
				<div id="activeFilters" class="flex flex-wrap gap-2">
					<!-- Active filters will be displayed here -->
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Products Grid Section -->
<section class="py-12 bg-gray-50 min-h-screen">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<!-- Loading Spinner -->
		<div id="loadingSpinner" class="hidden text-center py-12">
			<div class="inline-flex items-center space-x-3">
				<div class="spinner"></div>
				<span class="text-gray-600 font-medium">{{ __('loading products...') }}</span>
			</div>
		</div>

		<!-- Products Grid with Advanced Animations -->
		<div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
			@include('frontend.pages.products.partials.products-grid', ['products' => $products])
		</div>

		<!-- Enhanced Pagination -->
		<x-frontend.pagination :paginator="$products" container-class="mt-12" :show-info="true"
			:max-pages="7" :show-first-last="false" />
	</div>
</section>


@endsection

@include('frontend.pages.products.scripts.index-js')