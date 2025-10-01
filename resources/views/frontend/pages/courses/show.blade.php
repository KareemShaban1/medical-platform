@extends('frontend.layouts.app')

@push('styles')
<style>
	/* Course Details Page Styles */
	.course-hero {
		background: linear-gradient(135deg, #079184, #0aa896);
		position: relative;
		overflow: hidden;
	}

	.course-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
		opacity: 0.3;
	}

	.course-image {
		width: 100%;
		height: 400px;
		object-fit: cover;
		border-radius: 12px;
		transition: transform 0.3s ease;
	}

	.course-image:hover {
		transform: scale(1.02);
	}

	.course-info {
		background: white;
		border-radius: 12px;
		padding: 32px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
		margin-top: -50px;
		position: relative;
		z-index: 10;
	}

	.course-title {
		font-size: 32px;
		font-weight: 700;
		color: #111827;
		margin-bottom: 16px;
		line-height: 1.3;
	}

	.course-meta {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 16px;
		margin-bottom: 24px;
	}

	.meta-item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 16px;
		background: #f9fafb;
		border-radius: 8px;
		border-left: 4px solid #079184;
	}

	.meta-icon {
		color: #079184;
		font-size: 20px;
	}

	.meta-text {
		color: #374151;
		font-weight: 500;
	}

	.course-description {
		color: #6b7280;
		line-height: 1.6;
		margin-bottom: 24px;
		font-size: 16px;
	}

	.course-features {
		margin-bottom: 24px;
	}

	.features-title {
		font-size: 20px;
		font-weight: 600;
		color: #111827;
		margin-bottom: 16px;
	}

	.features-list {
		list-style: none;
		padding: 0;
	}

	.feature-item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 12px 0;
		border-bottom: 1px solid #f3f4f6;
	}

	.feature-item:last-child {
		border-bottom: none;
	}

	.feature-icon {
		color: #10b981;
		font-size: 16px;
	}

	.course-actions {
		display: flex;
		gap: 16px;
		margin-top: 32px;
		flex-wrap: wrap;
	}

	.btn-enroll {
		background: linear-gradient(135deg, #079184, #0aa896);
		color: white;
		padding: 16px 32px;
		border-radius: 8px;
		font-weight: 600;
		text-decoration: none;
		display: inline-flex;
		align-items: center;
		gap: 8px;
		transition: all 0.3s ease;
		border: none;
		cursor: pointer;
	}

	.btn-enroll:hover {
		transform: translateY(-2px);
		box-shadow: 0 8px 25px rgba(7, 145, 132, 0.3);
		color: white;
		text-decoration: none;
	}

	.btn-secondary {
		background: #f3f4f6;
		color: #374151;
		padding: 16px 32px;
		border-radius: 8px;
		font-weight: 600;
		text-decoration: none;
		display: inline-flex;
		align-items: center;
		gap: 8px;
		transition: all 0.3s ease;
		border: 2px solid #e5e7eb;
	}

	.btn-secondary:hover {
		background: #e5e7eb;
		color: #111827;
		text-decoration: none;
	}

	.course-tabs {
		margin-top: 48px;
	}

	.tab-nav {
		display: flex;
		border-bottom: 2px solid #e5e7eb;
		margin-bottom: 32px;
		overflow-x: auto;
	}

	.tab-btn {
		background: none;
		border: none;
		padding: 16px 24px;
		font-weight: 600;
		color: #6b7280;
		cursor: pointer;
		transition: all 0.3s ease;
		border-bottom: 3px solid transparent;
		white-space: nowrap;
	}

	.tab-btn:hover {
		color: #079184;
	}

	.tab-btn.active {
		color: #079184;
		border-bottom-color: #079184;
	}

	.tab-panel {
		display: none;
	}

	.tab-panel.active {
		display: block;
	}

	.related-courses {
		background: #f9fafb;
		border-radius: 12px;
		padding: 32px;
		margin-top: 48px;
	}

	.related-title {
		font-size: 24px;
		font-weight: 700;
		color: #111827;
		margin-bottom: 24px;
	}

	.course-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 24px;
	}

	.course-card {
		background: white;
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
		transition: all 0.3s ease;
	}

	.course-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
	}

	.course-card-image {
		width: 100%;
		height: 200px;
		object-fit: cover;
	}

	.course-card-content {
		padding: 20px;
	}

	.course-card-title {
		font-size: 18px;
		font-weight: 600;
		color: #111827;
		margin-bottom: 8px;
		line-height: 1.4;
	}

	.course-card-description {
		color: #6b7280;
		font-size: 14px;
		line-height: 1.5;
		margin-bottom: 16px;
	}

	.course-card-meta {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 16px;
	}

	.course-card-duration {
		color: #6b7280;
		font-size: 14px;
		display: flex;
		align-items: center;
		gap: 4px;
	}

	.course-card-level {
		background: #079184;
		color: white;
		padding: 4px 12px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 600;
	}

	.course-card-link {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		color: #079184;
		text-decoration: none;
		font-weight: 600;
		transition: all 0.3s ease;
	}

	.course-card-link:hover {
		color: #0aa896;
		text-decoration: none;
	}

	.breadcrumb {
		background: #f9fafb;
		padding: 16px 0;
		border-bottom: 1px solid #e5e7eb;
	}

	.breadcrumb-list {
		display: flex;
		align-items: center;
		gap: 8px;
		list-style: none;
		margin: 0;
		padding: 0;
	}

	.breadcrumb-item a {
		color: #6b7280;
		text-decoration: none;
		transition: color 0.3s ease;
	}

	.breadcrumb-item a:hover {
		color: #079184;
	}

	.breadcrumb-separator {
		color: #9ca3af;
	}

	.breadcrumb-item.active {
		color: #111827;
		font-weight: 500;
	}

	/* Status Badges */
	.status-badge {
		padding: 6px 16px;
		border-radius: 20px;
		font-size: 14px;
		font-weight: 600;
		display: inline-flex;
		align-items: center;
		gap: 6px;
	}

	.status-upcoming {
		background: #dbeafe;
		color: #1e40af;
	}

	.status-ongoing {
		background: #dcfce7;
		color: #166534;
	}

	.status-completed {
		background: #f3f4f6;
		color: #374151;
	}

	/* Responsive Design */
	@media (max-width: 768px) {
		.course-info {
			margin-top: -30px;
			padding: 24px;
		}

		.course-title {
			font-size: 24px;
		}

		.course-meta {
			grid-template-columns: 1fr;
		}

		.course-actions {
			flex-direction: column;
		}

		.btn-enroll,
		.btn-secondary {
			width: 100%;
			justify-content: center;
		}

		.tab-nav {
			flex-direction: column;
		}

		.tab-btn {
			text-align: left;
			border-bottom: 1px solid #e5e7eb;
			border-right: none;
		}

		.course-grid {
			grid-template-columns: 1fr;
		}
	}

	/* Animation Classes */
	.fade-in {
		animation: fadeIn 0.6s ease-out;
	}

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

	.slide-in-left {
		animation: slideInLeft 0.6s ease-out;
	}

	@keyframes slideInLeft {
		from {
			opacity: 0;
			transform: translateX(-30px);
		}

		to {
			opacity: 1;
			transform: translateX(0);
		}
	}

	.slide-in-right {
		animation: slideInRight 0.6s ease-out;
	}

	@keyframes slideInRight {
		from {
			opacity: 0;
			transform: translateX(30px);
		}

		to {
			opacity: 1;
			transform: translateX(0);
		}
	}
</style>
@endpush

@section('content')

<!-- Breadcrumb -->
<nav class="breadcrumb">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<ol class="breadcrumb-list">
			<li class="breadcrumb-item">
				<a href="{{ route('home') }}" class="hover:text-primary">Home</a>
			</li>
			<li class="breadcrumb-separator">/</li>
			<li class="breadcrumb-item">
				<a href="{{ route('courses') }}" class="hover:text-primary">Courses</a>
			</li>
			<li class="breadcrumb-separator">/</li>
			<li class="breadcrumb-item active">{{ $course->title }}</li>
		</ol>
	</div>
</nav>

<!-- Course Hero Section -->
<section class="course-hero py-16 relative">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
		<div class="text-center text-white">
			<h1 class="text-4xl md:text-6xl font-bold mb-6 fade-in">
				{{ $course->title }}
			</h1>
			<p class="text-xl md:text-2xl mb-8 fade-in" style="animation-delay: 0.2s;">
				{{ app()->getLocale() == 'ar' ? $course->description_ar : $course->description_en }}
			</p>

			<!-- Course Status Badge -->
			@php
			$now = now();
			$startDate = $course->start_date;
			$endDate = $course->end_date;

			if ($startDate > $now) {
			$status = 'upcoming';
			$statusText = 'Upcoming';
			$statusIcon = 'fas fa-clock';
			} elseif ($endDate >= $now) {
			$status = 'ongoing';
			$statusText = 'Ongoing';
			$statusIcon = 'fas fa-play';
			} else {
			$status = 'completed';
			$statusText = 'Completed';
			$statusIcon = 'fas fa-check';
			}
			@endphp

			<div class="inline-flex items-center gap-4 fade-in" style="animation-delay: 0.4s;">
				<span class="status-badge status-{{ $status }}">
					<i class="{{ $statusIcon }}"></i>
					{{ $statusText }}
				</span>
			</div>
		</div>
	</div>
</section>

<!-- Course Details -->
<section class="py-12 bg-gray-50">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
			<!-- Main Content -->
			<div class="lg:col-span-2">
				<!-- Course Image -->
				<div class="mb-8 slide-in-left">
					@if($course->main_image)
					<img src="{{ $course->main_image }}" alt="{{ $course->title }}" class="course-image">
					@else
					<div class="course-image bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
						<i class="fas fa-graduation-cap text-6xl text-blue-400"></i>
					</div>
					@endif
				</div>

				<!-- Course Information -->
				<div class="course-info slide-in-right">
					<h2 class="course-title">{{ $course->title }}</h2>

					<div class="course-meta">
						<div class="meta-item">
							<i class="fas fa-clock meta-icon"></i>
							<span class="meta-text">{{ $course->duration ?? 'N/A' }} weeks duration</span>
						</div>
						<div class="meta-item">
							<i class="fas fa-calendar-alt meta-icon"></i>
							<span class="meta-text">
								@if($course->start_date)
								{{ $course->start_date->format('M d, Y') }}
								@else
								TBA
								@endif
							</span>
						</div>
						<div class="meta-item">
							<i class="fas fa-calendar-check meta-icon"></i>
							<span class="meta-text">
								@if($course->end_date)
								{{ $course->end_date->format('M d, Y') }}
								@else
								TBA
								@endif
							</span>
						</div>
						<div class="meta-item">
							<i class="fas fa-globe meta-icon"></i>
							<span class="meta-text">Online Course</span>
						</div>
					</div>

					<div class="course-description">
						{{ app()->getLocale() == 'ar' ? $course->description_ar : $course->description_en }}
					</div>

					<div class="course-features">
						<h3 class="features-title">What You'll Learn</h3>
						<ul class="features-list">
							<li class="feature-item">
								<i class="fas fa-check feature-icon"></i>
								<span>Comprehensive medical knowledge and best practices</span>
							</li>
							<li class="feature-item">
								<i class="fas fa-check feature-icon"></i>
								<span>Hands-on practical experience and case studies</span>
							</li>
							<li class="feature-item">
								<i class="fas fa-check feature-icon"></i>
								<span>Expert instruction from medical professionals</span>
							</li>
							<li class="feature-item">
								<i class="fas fa-check feature-icon"></i>
								<span>Certification upon successful completion</span>
							</li>
							<li class="feature-item">
								<i class="fas fa-check feature-icon"></i>
								<span>Access to course materials and resources</span>
							</li>
						</ul>
					</div>

					<div class="course-actions">
						<button class="btn-enroll" onclick="enrollCourse()">
							<i class="fas fa-user-plus"></i>
							Enroll Now
						</button>
						<a href="#" class="btn-secondary" onclick="shareCourse()">
							<i class="fas fa-share"></i>
							Share Course
						</a>
						<a href="#" class="btn-secondary" onclick="addToFavorites()">
							<i class="fas fa-heart"></i>
							Add to Favorites
						</a>
					</div>
				</div>

				<!-- Course Tabs -->
				<div class="course-tabs">
					<div class="tab-nav">
						<button class="tab-btn active" onclick="switchTab('overview')">Overview</button>
						<button class="tab-btn" onclick="switchTab('curriculum')">Curriculum</button>
						<button class="tab-btn" onclick="switchTab('instructor')">Instructor</button>
						<button class="tab-btn" onclick="switchTab('reviews')">Reviews</button>
					</div>

					<div class="tab-content">
						<div id="overview" class="tab-panel active">
							<h3 class="text-lg font-semibold mb-4">Course Overview</h3>
							<div class="prose max-w-none">
								<p>{{ app()->getLocale() == 'ar' ? $course->description_ar : $course->description_en }}</p>
								<p class="mt-4">This comprehensive course is designed for medical professionals who want to enhance their knowledge and skills. The course covers essential topics and provides practical insights that you can apply in your medical practice.</p>
							</div>
						</div>

						<div id="curriculum" class="tab-panel">
							<h3 class="text-lg font-semibold mb-4">Course Curriculum</h3>
							<div class="space-y-4">
								<div class="border border-gray-200 rounded-lg p-4">
									<h4 class="font-semibold text-gray-900 mb-2">Module 1: Introduction</h4>
									<p class="text-gray-600 text-sm">Basic concepts and overview of the course material.</p>
								</div>
								<div class="border border-gray-200 rounded-lg p-4">
									<h4 class="font-semibold text-gray-900 mb-2">Module 2: Core Concepts</h4>
									<p class="text-gray-600 text-sm">Deep dive into the fundamental principles and theories.</p>
								</div>
								<div class="border border-gray-200 rounded-lg p-4">
									<h4 class="font-semibold text-gray-900 mb-2">Module 3: Practical Applications</h4>
									<p class="text-gray-600 text-sm">Hands-on exercises and real-world case studies.</p>
								</div>
								<div class="border border-gray-200 rounded-lg p-4">
									<h4 class="font-semibold text-gray-900 mb-2">Module 4: Assessment</h4>
									<p class="text-gray-600 text-sm">Final evaluation and certification process.</p>
								</div>
							</div>
						</div>

						<div id="instructor" class="tab-panel">
							<h3 class="text-lg font-semibold mb-4">Meet Your Instructor</h3>
							<div class="flex items-start space-x-4">
								<div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center">
									<i class="fas fa-user text-2xl text-gray-400"></i>
								</div>
								<div>
									<h4 class="font-semibold text-gray-900">Dr. Medical Expert</h4>
									<p class="text-gray-600 mb-2">Senior Medical Professional</p>
									<p class="text-sm text-gray-600">With over 15 years of experience in medical practice and education, our instructor brings extensive knowledge and practical insights to help you succeed in your medical career.</p>
								</div>
							</div>
						</div>

						<div id="reviews" class="tab-panel">
							<h3 class="text-lg font-semibold mb-4">Student Reviews</h3>
							<div class="space-y-4">
								<div class="border border-gray-200 rounded-lg p-4">
									<div class="flex items-center mb-2">
										<div class="flex text-yellow-400">
											<i class="fas fa-star"></i>
											<i class="fas fa-star"></i>
											<i class="fas fa-star"></i>
											<i class="fas fa-star"></i>
											<i class="fas fa-star"></i>
										</div>
										<span class="ml-2 text-sm text-gray-600">5.0</span>
									</div>
									<p class="text-gray-700">"Excellent course with comprehensive content and practical examples. Highly recommended for medical professionals."</p>
									<p class="text-sm text-gray-500 mt-2">- Dr. Sarah Johnson</p>
								</div>
								<div class="border border-gray-200 rounded-lg p-4">
									<div class="flex items-center mb-2">
										<div class="flex text-yellow-400">
											<i class="fas fa-star"></i>
											<i class="fas fa-star"></i>
											<i class="fas fa-star"></i>
											<i class="fas fa-star"></i>
											<i class="fas fa-star"></i>
										</div>
										<span class="ml-2 text-sm text-gray-600">5.0</span>
									</div>
									<p class="text-gray-700">"Great learning experience with expert instruction and valuable insights."</p>
									<p class="text-sm text-gray-500 mt-2">- Dr. Michael Chen</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Sidebar -->
			<div class="lg:col-span-1">
				<!-- Course Quick Info -->
				<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
					<h3 class="text-lg font-semibold mb-4">Course Details</h3>
					<div class="space-y-3">
						<div class="flex justify-between">
							<span class="text-gray-600">Duration:</span>
							<span class="font-medium">{{ $course->duration ?? 'N/A' }} weeks</span>
						</div>
						<div class="flex justify-between">
							<span class="text-gray-600">Start Date:</span>
							<span class="font-medium">
								@if($course->start_date)
								{{ $course->start_date->format('M d, Y') }}
								@else
								TBA
								@endif
							</span>
						</div>
						<div class="flex justify-between">
							<span class="text-gray-600">End Date:</span>
							<span class="font-medium">
								@if($course->end_date)
								{{ $course->end_date->format('M d, Y') }}
								@else
								TBA
								@endif
							</span>
						</div>
						<div class="flex justify-between">
							<span class="text-gray-600">Language:</span>
							<span class="font-medium">English</span>
						</div>
						<div class="flex justify-between">
							<span class="text-gray-600">Level:</span>
							<span class="font-medium">Intermediate</span>
						</div>
					</div>
				</div>

				<!-- Course URL -->
				@if($course->url)
				<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
					<h3 class="text-lg font-semibold mb-4">Course Link</h3>
					<a href="{{ $course->url }}" target="_blank" class="btn-enroll w-full text-center">
						<i class="fas fa-external-link-alt"></i>
						Visit Course Page
					</a>
				</div>
				@endif
			</div>
		</div>
	</div>
</section>

<!-- Related Courses -->
@if($relatedCourses->count() > 0)
<section class="py-12 bg-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="related-courses">
			<h2 class="related-title">Related Courses</h2>
			<div class="course-grid">
				@foreach($relatedCourses as $relatedCourse)
				<div class="course-card">
					@if($relatedCourse->main_image)
					<img src="{{ $relatedCourse->main_image }}" alt="{{ $relatedCourse->title }}" class="course-card-image">
					@else
					<div class="course-card-image bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
						<i class="fas fa-graduation-cap text-3xl text-blue-400"></i>
					</div>
					@endif
					<div class="course-card-content">
						<h3 class="course-card-title">{{ $relatedCourse->title }}</h3>
						<p class="course-card-description">
							{{ Str::limit(app()->getLocale() == 'ar' ? $relatedCourse->description_ar : $relatedCourse->description_en, 100) }}
						</p>
						<div class="course-card-meta">
							<span class="course-card-duration">
								<i class="fas fa-clock"></i>
								{{ $relatedCourse->duration ?? 'N/A' }} weeks
							</span>
							<span class="course-card-level">Intermediate</span>
						</div>
						<a href="{{ route('courses.show', $relatedCourse->id) }}" class="course-card-link">
							View Details
							<i class="fas fa-arrow-right"></i>
						</a>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</section>
@endif

@endsection

@push('scripts')
<script>
	// Tab switching functionality
	function switchTab(tabName) {
		// Hide all tab panels
		const panels = document.querySelectorAll('.tab-panel');
		panels.forEach(panel => panel.classList.remove('active'));

		// Remove active class from all tab buttons
		const buttons = document.querySelectorAll('.tab-btn');
		buttons.forEach(button => button.classList.remove('active'));

		// Show selected panel
		document.getElementById(tabName).classList.add('active');

		// Add active class to clicked button
		event.target.classList.add('active');
	}

	// Course enrollment
	function enrollCourse() {
		// Add your enrollment logic here
		alert('Enrollment functionality will be implemented soon!');
	}

	// Share course
	function shareCourse() {
		if (navigator.share) {
			navigator.share({
				title: '{{ $course->title }}',
				text: '{{ app()->getLocale() == "ar" ? $course->description_ar : $course->description_en }}',
				url: window.location.href
			});
		} else {
			// Fallback for browsers that don't support Web Share API
			navigator.clipboard.writeText(window.location.href).then(() => {
				alert('Course link copied to clipboard!');
			});
		}
	}

	// Add to favorites
	function addToFavorites() {
		// Add your favorites logic here
		alert('Added to favorites!');
	}

	// Smooth scrolling for anchor links
	document.querySelectorAll('a[href^="#"]').forEach(anchor => {
		anchor.addEventListener('click', function(e) {
			e.preventDefault();
			document.querySelector(this.getAttribute('href')).scrollIntoView({
				behavior: 'smooth'
			});
		});
	});

	// Animation on scroll
	const observerOptions = {
		threshold: 0.1,
		rootMargin: '0px 0px -50px 0px'
	};

	const observer = new IntersectionObserver((entries) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				entry.target.style.opacity = '1';
				entry.target.style.transform = 'translateY(0)';
			}
		});
	}, observerOptions);

	// Observe elements for animation
	document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right').forEach(el => {
		observer.observe(el);
	});
</script>
@endpush

