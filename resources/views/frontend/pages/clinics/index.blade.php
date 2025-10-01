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

	@keyframes slideInLeft {
		from {
			opacity: 0;
			transform: translateX(-50px);
		}

		to {
			opacity: 1;
			transform: translateX(0);
		}
	}

	@keyframes slideInRight {
		from {
			opacity: 0;
			transform: translateX(50px);
		}

		to {
			opacity: 1;
			transform: translateX(0);
		}
	}

	@keyframes float {

		0%,
		100% {
			transform: translateY(0px);
		}

		50% {
			transform: translateY(-10px);
		}
	}

	@keyframes pulse {

		0%,
		100% {
			transform: scale(1);
		}

		50% {
			transform: scale(1.05);
		}
	}

	@keyframes rotate {
		from {
			transform: rotate(0deg);
		}

		to {
			transform: rotate(360deg);
		}
	}

	@keyframes bounce {

		0%,
		20%,
		53%,
		80%,
		100% {
			transform: translate3d(0, 0, 0);
		}

		40%,
		43% {
			transform: translate3d(0, -8px, 0);
		}

		70% {
			transform: translate3d(0, -4px, 0);
		}

		90% {
			transform: translate3d(0, -2px, 0);
		}
	}

	.animate-fade-in-up {
		animation: fadeInUp 0.8s ease-out forwards;
		opacity: 0;
	}

	.animate-slide-in-left {
		animation: slideInLeft 0.8s ease-out forwards;
		opacity: 0;
	}

	.animate-slide-in-right {
		animation: slideInRight 0.8s ease-out forwards;
		opacity: 0;
	}

	.animate-float {
		animation: float 3s ease-in-out infinite;
	}

	.animate-pulse-custom {
		animation: pulse 2s ease-in-out infinite;
	}

	.animate-rotate {
		animation: rotate 2s linear infinite;
	}

	.animate-bounce-custom {
		animation: bounce 2s ease-in-out infinite;
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

	/* Clinic Card Hover Effects */
	.clinic-card {
		transition: all 0.3s ease;
		position: relative;
		overflow: hidden;
	}

	.clinic-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
		transition: left 0.5s ease;
	}

	.clinic-card:hover::before {
		left: 100%;
	}

	.clinic-card:hover {
		transform: translateY(-8px) scale(1.02);
		box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
	}

	/* Filter Panel Animation */
	.filter-panel {
		transition: all 0.3s ease;
	}

	.filter-panel:hover {
		transform: translateY(-2px);
		box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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
		0% {
			transform: rotate(0deg);
		}

		100% {
			transform: rotate(360deg);
		}
	}

	/* Gradient Text */
	.text-gradient {
		background: linear-gradient(135deg, #079184, #0aa896);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		background-clip: text;
	}

	/* Interactive Elements */
	.interactive-element {
		transition: all 0.3s ease;
		cursor: pointer;
	}

	.interactive-element:hover {
		transform: scale(1.05);
	}

	/* Staggered Animation */
	.stagger-animation>* {
		opacity: 0;
		transform: translateY(30px);
		animation: fadeInUp 0.6s ease-out forwards;
	}

	.stagger-animation>*:nth-child(1) {
		animation-delay: 0.1s;
	}

	.stagger-animation>*:nth-child(2) {
		animation-delay: 0.2s;
	}

	.stagger-animation>*:nth-child(3) {
		animation-delay: 0.3s;
	}

	.stagger-animation>*:nth-child(4) {
		animation-delay: 0.4s;
	}

	.stagger-animation>*:nth-child(5) {
		animation-delay: 0.5s;
	}

	.stagger-animation>*:nth-child(6) {
		animation-delay: 0.6s;
	}

	/* Rating Stars */
	.rating-stars {
		display: flex;
		gap: 2px;
	}

	.rating-star {
		color: #fbbf24;
		transition: all 0.3s ease;
	}

	.rating-star:hover {
		transform: scale(1.2);
		color: #f59e0b;
	}

	/* Status Badge */
	.status-badge {
		transition: all 0.3s ease;
		position: relative;
		overflow: hidden;
	}

	.status-badge::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
		transition: left 0.3s ease;
	}

	.status-badge:hover::before {
		left: 100%;
	}

	.status-badge:hover {
		transform: scale(1.1);
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
	}

	/* Specialization Tag */
	.specialization-tag {
		background: linear-gradient(135deg, #3b82f6, #1d4ed8);
		color: white;
		padding: 0.25rem 0.75rem;
		border-radius: 9999px;
		font-size: 0.75rem;
		font-weight: 600;
		animation: pulse 2s ease-in-out infinite;
	}

	/* Clinic Logo Animation */
	.clinic-logo {
		transition: all 0.3s ease;
	}

	.clinic-logo:hover {
		transform: scale(1.1) rotate(5deg);
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

	/* Sticky Filters */
	.sticky {
		position: sticky;
		top: 0;
		z-index: 40;
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
		<div class="absolute top-1/2 left-1/2 w-8 h-8 bg-white/25 rounded-full animate-ping"></div>
		<div class="absolute top-1/4 left-1/3 w-6 h-6 bg-white/30 rounded-full animate-bounce"></div>
		<div class="absolute bottom-1/4 right-1/3 w-10 h-10 bg-white/12 rounded-full animate-pulse"></div>
		<div class="absolute top-3/4 left-1/4 w-14 h-14 bg-white/18 rounded-full animate-bounce"></div>
		<div class="absolute top-1/6 right-1/6 w-18 h-18 bg-white/14 rounded-full animate-ping"></div>
	</div>

	<div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
		<h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-up">
			<span class="bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
				Medical Clinics
			</span>
		</h1>
		<p class="text-xl md:text-2xl mb-8 animate-fade-in-up animation-delay-200 opacity-90">
			Find the best medical clinics near you
		</p>

		<!-- Interactive Search Bar -->
		<div class="max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
			<div class="relative group">
				<input type="text" id="heroSearch" placeholder="Search for clinics..."
					class="w-full px-6 py-4 pl-14 pr-6 text-gray-900 rounded-full shadow-2xl focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-300 group-hover:scale-105">
				<i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
				<!-- <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gradient-primary text-white px-6 py-2 rounded-full hover:scale-105 transition-transform duration-300">
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
					<span class="font-semibold">Filters</span>
					<i id="filterChevron"
						class="fas fa-chevron-down transition-transform duration-300"></i>
				</button>

				<!-- Active Filters Count -->
				<div id="activeFiltersCount"
					class="hidden bg-primary text-white px-3 py-1 rounded-full text-sm font-semibold">
					<span id="filterCount">0</span> filters
				</div>
			</div>

			<!-- Results Count -->
			<div class="flex items-center space-x-4">
				<div class="bg-gray-100 rounded-xl px-4 py-2">
					<span class="text-gray-600">Showing <span id="resultsCount"
							class="font-bold text-primary">{{ $clinics->total() }}</span>
						clinics</span>
				</div>

				<!-- View Toggle -->
				<div class="hidden md:flex items-center space-x-2">
					<span class="text-sm text-gray-500">View:</span>
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
					<label class="block text-sm font-semibold text-gray-700 mb-3">Search
						Clinics</label>
					<div class="relative">
						<input type="text" id="search" placeholder="Search clinics..."
							class="form-input w-full pl-10 pr-4 py-3 group-hover:scale-105 transition-transform duration-300">
						<i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
					</div>
				</div>

				<!-- Sort Filter -->
				<div class="group">
					<label class="block text-sm font-semibold text-gray-700 mb-3">Sort By</label>
					<div class="relative">
						<select id="sort" class="form-input w-full px-3 py-3 group-hover:scale-105 transition-transform duration-300">
							<option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
							<option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
							<option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
						</select>
						<i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
					</div>
				</div>

				<div class="group flex items-center space-x-4">
					<button id="clearFilters" class="btn-secondary group">
						<i class="fas fa-refresh mr-2 group-hover:rotate-180 transition-transform duration-500"></i>
						Clear All Filters
					</button>
				</div>
			</div>

			
		</div>
	</div>
</section>

<!-- Clinics Grid Section -->
<section class="py-12 bg-gray-50 min-h-screen">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<!-- Loading Spinner -->
		<div id="loadingSpinner" class="hidden text-center py-12">
			<div class="inline-flex items-center space-x-3">
				<div class="spinner"></div>
				<span class="text-gray-600 font-medium">Loading clinics...</span>
			</div>
		</div>

		<!-- Clinics Grid with Advanced Animations -->
		<div id="clinicsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-animation">
			@include('frontend.pages.clinics.partials.clinics-grid', ['clinics' => $clinics])
		</div>

		<!-- Enhanced Pagination -->
		<div id="paginationContainer" class="mt-12">
			{{ $clinics->links() }}
		</div>
	</div>
</section>

<!-- Interactive Features Section -->
<section class="py-16 bg-gradient-primary relative overflow-hidden">
	<div class="absolute inset-0 bg-black/20"></div>
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
		<div class="text-center mb-12">
			<h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Why Choose Our Clinics?</h2>
			<p class="text-xl text-white/90 max-w-3xl mx-auto">Trusted healthcare providers with exceptional patient care</p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
			<!-- Feature 1 -->
			<div class="group text-center">
				<div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 animate-float">
					<i class="fas fa-user-md text-3xl text-white"></i>
				</div>
				<h3 class="text-xl font-bold text-white mb-4">Expert Doctors</h3>
				<p class="text-white/80">Board-certified medical professionals</p>
			</div>

			<!-- Feature 2 -->
			<div class="group text-center">
				<div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 animate-float animation-delay-200">
					<i class="fas fa-hospital text-3xl text-white"></i>
				</div>
				<h3 class="text-xl font-bold text-white mb-4">Modern Facilities</h3>
				<p class="text-white/80">State-of-the-art medical equipment</p>
			</div>

			<!-- Feature 3 -->
			<div class="group text-center">
				<div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 animate-float animation-delay-400">
					<i class="fas fa-heart text-3xl text-white"></i>
				</div>
				<h3 class="text-xl font-bold text-white mb-4">Patient Care</h3>
				<p class="text-white/80">Compassionate and personalized treatment</p>
			</div>
		</div>
	</div>
</section>

@endsection

@include('frontend.pages.clinics.scripts.index-js')
