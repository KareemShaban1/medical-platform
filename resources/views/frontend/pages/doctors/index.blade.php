@extends('frontend.layouts.app')

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

	/* Doctor Card Hover Effects */
	.doctor-card {
		transition: all 0.3s ease;
		position: relative;
		overflow: hidden;
	}

	.doctor-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
		transition: left 0.5s ease;
	}

	.doctor-card:hover::before {
		left: 100%;
	}

	.doctor-card:hover {
		transform: translateY(-8px) scale(1.02);
		box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
	}

	/* Featured Badge - Like Verified Badge */
	.featured-badge {
		position: absolute;
		top: 12px;
		right: 12px;
		background: linear-gradient(135deg, #fbbf24, #f59e0b);
		color: white;
		padding: 6px 12px;
		border-radius: 20px;
		font-size: 11px;
		font-weight: 700;
		display: inline-flex;
		align-items: center;
		gap: 4px;
		box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
		z-index: 10;
		animation: pulse 2s ease-in-out infinite;
	}

	.featured-badge i {
		font-size: 12px;
	}

	/* Verified Badge Style */
	.verified-badge {
		display: inline-flex;
		align-items: center;
		gap: 4px;
		background: linear-gradient(135deg, #3b82f6, #2563eb);
		color: white;
		padding: 4px 10px;
		border-radius: 12px;
		font-size: 11px;
		font-weight: 600;
		margin-left: 8px;
	}

	/* Clinic Badge */
	.clinic-badge {
		display: inline-flex;
		align-items: center;
		gap: 4px;
		background: #10b981;
		color: white;
		padding: 4px 10px;
		border-radius: 12px;
		font-size: 11px;
		font-weight: 600;
	}

	/* Loading Spinner */
	.spinner {
		width: 40px;
		height: 40px;
		border: 4px solid rgba(7, 145, 132, 0.3);
		border-top: 4px solid #079184;
		border-radius: 50%;
		animation: spin 1s linear infinite;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

	/* Staggered Animation */
	.stagger-animation>* {
		opacity: 0;
		transform: translateY(30px);
		animation: fadeInUp 0.6s ease-out forwards;
	}

	.stagger-animation>*:nth-child(1) { animation-delay: 0.1s; }
	.stagger-animation>*:nth-child(2) { animation-delay: 0.2s; }
	.stagger-animation>*:nth-child(3) { animation-delay: 0.3s; }
	.stagger-animation>*:nth-child(4) { animation-delay: 0.4s; }
	.stagger-animation>*:nth-child(5) { animation-delay: 0.5s; }
	.stagger-animation>*:nth-child(6) { animation-delay: 0.6s; }

	/* Specialization Tag */
	.specialization-tag {
		background: linear-gradient(135deg, #3b82f6, #1d4ed8);
		color: white;
		padding: 0.25rem 0.75rem;
		border-radius: 9999px;
		font-size: 0.75rem;
		font-weight: 600;
	}

	/* Form Inputs */
	.form-input {
		transition: all 0.3s ease;
		border: 2px solid #e5e7eb;
	}

	.form-input:focus {
		border-color: #079184;
		box-shadow: 0 0 0 3px rgba(7, 145, 132, 0.1);
		transform: scale(1.02);
	}
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden bg-gradient-primary">
	<!-- Animated Background Elements -->
	<div class="absolute inset-0">
		<div class="absolute top-20 left-10 w-32 h-32 bg-white/10 rounded-full animate-pulse"></div>
		<div class="absolute top-40 right-20 w-24 h-24 bg-white/5 rounded-full animate-bounce"></div>
		<div class="absolute bottom-32 left-1/4 w-16 h-16 bg-white/15 rounded-full animate-ping"></div>
	</div>

	<div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
		<h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-up">
			<span class="text-white bg-clip-text text-transparent">
				{{ __('Our Doctors') }}
			</span>
		</h1>
		<p class="text-xl md:text-2xl mb-8 animate-fade-in-up animation-delay-200 opacity-90">
			{{ __('Find the best doctors and medical professionals') }}
		</p>

		<!-- Interactive Search Bar -->
		<div class="max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
			<div class="relative group">
				<input type="text" id="heroSearch" placeholder="{{ __('search for doctors...') }}"
					class="w-full px-6 py-4 pl-14 pr-6 text-gray-900 rounded-full shadow-2xl focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-300 group-hover:scale-105">
				<i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/4 text-white text-lg"></i>
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
					<span class="text-gray-600">{{ __('showing') }} <span id="resultsCount"
							class="font-bold text-primary">{{ $doctors->total() }}</span>
						{{ __('doctors') }}</span>
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
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
				<!-- Search Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('search doctors') }}</label>
					<div class="relative">
						<input type="text" id="search" placeholder="{{ __('search doctors...') }}"
							class="form-input w-full pl-10 pr-4 py-3 group-hover:scale-105 transition-transform duration-300">
						<i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/4 text-gray-400"></i>
					</div>
				</div>

				<!-- Speciality Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('speciality') }}</label>
					<div class="relative">
						<select id="speciality_id" class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="">{{ __('all specialities') }}</option>
							@foreach($specialities as $spec)
								<option value="{{ $spec->id }}" {{ request('speciality_id') == $spec->id ? 'selected' : '' }}>
									{{ $spec->name_en }}
								</option>
							@endforeach
						</select>
					</div>
				</div>

				<!-- Featured Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('featured doctors') }}</label>
					<div class="relative">
						<select id="featured" class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="">{{ __('all doctors') }}</option>
							<option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>{{ __('featured only') }}</option>
						</select>
					</div>
				</div>

				<!-- Clinic Type Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('doctor type') }}</label>
					<div class="relative">
						<select id="has_clinic" class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="">{{ __('all types') }}</option>
							<option value="1" {{ request('has_clinic') == '1' ? 'selected' : '' }}>{{ __('with clinic') }}</option>
							<option value="0" {{ request('has_clinic') == '0' ? 'selected' : '' }}>{{ __('standalone') }}</option>
						</select>
					</div>
				</div>

				<!-- Sort Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">{{ __('sort by') }}</label>
					<div class="relative">
						<select id="sort" class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>{{ __('featured first') }}</option>
							<option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('name a-z') }}</option>
							<option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('newest first') }}</option>
							<option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('oldest first') }}</option>
							<option value="experience" {{ request('sort') == 'experience' ? 'selected' : '' }}>{{ __('most experience') }}</option>
						</select>
					</div>
				</div>
			</div>

			<div class="mt-6 flex items-center space-x-4">
				<button id="clearFilters" class="btn-secondary group">
					<i class="fas fa-refresh mr-2 group-hover:rotate-180 transition-transform duration-500"></i>
					{{ __('clear all filters') }}
				</button>
			</div>
		</div>
	</div>
</section>

<!-- Doctors Grid Section -->
<section class="py-12 bg-gray-50 min-h-screen">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<!-- Loading Spinner -->
		<div id="loadingSpinner" class="hidden text-center py-12">
			<div class="inline-flex items-center space-x-3">
				<div class="spinner"></div>
				<span class="text-gray-600 font-medium">{{ __('loading doctors...') }}</span>
			</div>
		</div>

		<!-- Doctors Grid with Advanced Animations -->
		<div id="doctorsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation">
			@include('frontend.pages.doctors.partials.doctors-grid', ['doctors' => $doctors])
		</div>

		<!-- Enhanced Pagination -->
		<div id="paginationContainer" class="mt-12">
			<x-frontend.pagination :paginator="$doctors" container-class="mt-12" :show-info="true" :max-pages="7"
				:show-first-last="false" />
		</div>
	</div>
</section>

@endsection

@include('frontend.pages.doctors.scripts.index-js')

