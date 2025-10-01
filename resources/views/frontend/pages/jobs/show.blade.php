@extends('frontend.layouts.app')

@push('styles')
<style>
/* Job Details Page Styles */
.job-header {
	background: linear-gradient(135deg, #079184, #0aa896);
	color: white;
	padding: 40px 0;
	margin-bottom: 32px;
}

.job-title {
	font-size: 25px;
	font-weight: 700;
	margin-bottom: 8px;
}

.job-company {
	font-size: 20px;
	opacity: 0.9;
	margin-bottom: 16px;
}

.job-meta {
	display: flex;
	flex-wrap: wrap;
	gap: 24px;
	margin-bottom: 24px;
}

.job-meta-item {
	display: flex;
	align-items: center;
	gap: 8px;
}

.job-meta-icon {
	font-size: 18px;
	opacity: 0.8;
}

.job-actions {
	display: flex;
	gap: 12px;
	margin-top: 24px;
}

.btn-apply {
	background: white;
	color: #079184;
	border: 2px solid white;
	padding: 12px 24px;
	border-radius: 8px;
	font-weight: 600;
	text-decoration: none;
	transition: all 0.3s ease;
}

.btn-apply:hover {
	background: transparent;
	color: white;
}

.btn-save {
	background: transparent;
	color: white;
	border: 2px solid white;
	padding: 12px 24px;
	border-radius: 8px;
	font-weight: 600;
	text-decoration: none;
	transition: all 0.3s ease;
}

.btn-save:hover {
	background: white;
	color: #079184;
}

.job-content {
	display: grid;
	grid-template-columns: 2fr 1fr;
	gap: 32px;
	margin-bottom: 48px;
}

.job-main {
	background: white;
	border-radius: 12px;
	padding: 32px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.job-sidebar {
	display: flex;
	flex-direction: column;
	gap: 24px;
}

.sidebar-card {
	background: white;
	border-radius: 12px;
	padding: 24px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.sidebar-title {
	font-size: 18px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 16px;
}

.job-description {
	margin-bottom: 32px;
}

.job-description h3 {
	font-size: 20px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 16px;
}

.job-description p {
	color: #6b7280;
	line-height: 1.6;
	margin-bottom: 16px;
}

.requirements-list {
	list-style: none;
	padding: 0;
}

.requirements-list li {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 8px 0;
	color: #374151;
}

.requirement-icon {
	color: #10b981;
	font-size: 16px;
}

.benefits-list {
	list-style: none;
	padding: 0;
}

.benefits-list li {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 8px 0;
	color: #374151;
}

.benefit-icon {
	color: #3b82f6;
	font-size: 16px;
}

.company-info {
	display: flex;
	align-items: center;
	gap: 16px;
	margin-bottom: 16px;
}

.company-logo {
	width: 60px;
	height: 60px;
	border-radius: 8px;
	object-fit: cover;
	background: #f3f4f6;
}

.company-details h4 {
	font-size: 18px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 4px;
}

.company-details p {
	color: #6b7280;
	font-size: 14px;
}

.job-stats {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 16px;
	margin-bottom: 24px;
}

.stat-item {
	text-align: center;
	padding: 16px;
	background: #f8fafc;
	border-radius: 8px;
}

.stat-value {
	font-size: 24px;
	font-weight: 700;
	color: #079184;
	margin-bottom: 4px;
}

.stat-label {
	font-size: 14px;
	color: #6b7280;
}

.related-jobs {
	margin-top: 48px;
}

.section-title {
	font-size: 24px;
	font-weight: 700;
	color: #111827;
	margin-bottom: 24px;
	text-align: center;
}

.jobs-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
	gap: 24px;
	margin-bottom: 20px;
}

.job-card {
	background: white;
	border-radius: 12px;
	padding: 24px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
	transition: all 0.3s ease;
	cursor: pointer;
}

.job-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
}

.job-card-title {
	font-size: 18px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 8px;
}

.job-card-company {
	color: #079184;
	font-weight: 500;
	margin-bottom: 12px;
}

.job-card-meta {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	margin-bottom: 16px;
}

.job-card-meta-item {
	display: flex;
	align-items: center;
	gap: 4px;
	font-size: 14px;
	color: #6b7280;
}

.job-card-description {
	color: #6b7280;
	font-size: 14px;
	line-height: 1.5;
	margin-bottom: 16px;
}

.job-card-actions {
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.job-card-btn {
	flex: 1;
	background: #079184;
	color: white;
	border: none;
	padding: 8px 16px;
	border-radius: 6px;
	font-size: 14px;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.3s ease;
}

.job-card-btn:hover {
	background: #0aa896;
}

.job-card-btn-secondary {
	background: transparent;
	color: #079184;
	border: 1px solid #079184;
}

.job-card-btn-secondary:hover {
	background: #079184;
	color: white;
}

/* Breadcrumb Styles */
.breadcrumb {
	background: #f8fafc;
	padding: 12px 0;
	margin-bottom: 0;
}

.breadcrumb-list {
	display: flex;
	align-items: center;
	gap: 8px;
	list-style: none;
	padding: 0;
	margin: 0;
}

.breadcrumb-item {
	color: #6b7280;
	font-size: 14px;
}

.breadcrumb-item.active {
	color: #111827;
	font-weight: 500;
}

.breadcrumb-separator {
	color: #9ca3af;
}

/* Responsive Design */
@media (max-width: 768px) {
	.job-content {
		grid-template-columns: 1fr;
		gap: 24px;
	}

	.job-meta {
		flex-direction: column;
		gap: 12px;
	}

	.job-actions {
		flex-direction: column;
	}

	.job-stats {
		grid-template-columns: 1fr;
	}

	.jobs-grid {
		grid-template-columns: 1fr;
	}

	.job-title {
		font-size: 24px;
	}

	.job-company {
		font-size: 18px;
	}
}

/* Loading States */
.loading {
	opacity: 0.6;
	pointer-events: none;
}

/* Status Badges */
.status-badge {
	padding: 4px 12px;
	border-radius: 20px;
	font-size: 12px;
	font-weight: 600;
	text-transform: uppercase;
}

.status-active {
	background: #dcfce7;
	color: #166534;
}

.status-urgent {
	background: #fef3c7;
	color: #92400e;
}

.status-featured {
	background: #dbeafe;
	color: #1e40af;
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
				<a href="{{ route('jobs') }}" class="hover:text-primary">Jobs</a>
			</li>
			<li class="breadcrumb-separator">/</li>
			<li class="breadcrumb-item active">{{ $job->title }}</li>
		</ol>
	</div>
</nav>

<!-- Job Header -->
<section class="job-header">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex items-center justify-between">
			<div>
				<h1 class="job-title">{{ $job->title }}</h1>
				<p class="job-company">
					<i class="fas fa-hospital job-meta-icon"></i>
					{{ $job->clinic->name ?? 'Medical Clinic' }}
				</p>

				<div class="job-meta">
					<div class="job-meta-item">
						<i class="fas fa-map-marker-alt job-meta-icon"></i>
						<span>{{ $job->location }}</span>
					</div>
					<div class="job-meta-item">
						<i class="fas fa-briefcase job-meta-icon"></i>
						<span>{{ ucfirst($job->type) }}</span>
					</div>
					<div class="job-meta-item">
						<!-- <i class="fas fa-dollar-sign job-meta-icon"></i> -->
						<span class="text-[#ceeae7]">LE</span>
						<span>{{ $job->salary }} </span>
					</div>

				</div>
			</div>

			<div class="job-actions">
				<a href="#" class="btn-apply" onclick="applyForJob()">
					<i class="fas fa-paper-plane mr-2"></i>Apply Now
				</a>
				<a href="#" class="btn-save" onclick="saveJob()">
					<i class="fas fa-bookmark mr-2"></i>Save Job
				</a>
			</div>
		</div>
	</div>
</section>

<!-- Job Content -->
<section class="py-8">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="job-content">
			<!-- Main Content -->
			<div class="job-main">
				<!-- Job Description -->
				<div class="job-description">
					<h3>Job Description</h3>
					<p>{{ $job->description }}</p>
				</div>

				<!-- Requirements -->
				<div class="job-description">
					<h3>Requirements</h3>
					<ul class="requirements-list">
						<li><i class="fas fa-check requirement-icon"></i>Bachelor's
							degree in {{ ucfirst($job->specialization) }} or
							related field</li>
						<li><i class="fas fa-check requirement-icon"></i>{{ ucfirst($job->experience_level) }}
							level experience in healthcare</li>
						<li><i class="fas fa-check requirement-icon"></i>Valid
							professional license and certifications</li>
						<li><i class="fas fa-check requirement-icon"></i>Strong
							communication and interpersonal skills</li>
						<li><i class="fas fa-check requirement-icon"></i>Ability to
							work in a fast-paced environment</li>
						<li><i class="fas fa-check requirement-icon"></i>Commitment
							to patient care and safety</li>
					</ul>
				</div>

				<!-- Benefits -->
				<div class="job-description">
					<h3>Benefits & Perks</h3>
					<ul class="benefits-list">
						<li><i class="fas fa-heart benefit-icon"></i>Comprehensive
							health insurance</li>
						<li><i class="fas fa-graduation-cap benefit-icon"></i>Professional
							development opportunities</li>
						<li><i class="fas fa-calendar benefit-icon"></i>Flexible
							work schedule</li>
						<li><i class="fas fa-coffee benefit-icon"></i>On-site
							amenities and facilities</li>
						<li><i class="fas fa-users benefit-icon"></i>Collaborative
							team environment</li>
						<li><i class="fas fa-chart-line benefit-icon"></i>Career
							advancement opportunities</li>
					</ul>
				</div>
			</div>

			<!-- Sidebar -->
			<div class="job-sidebar">
				<!-- Company Info -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">About Clinic</h3>
					<div class="company-info">

						<div class="company-details">
							<h4>{{ $job->clinic->name ?? 'Medical Clinic' }}
							</h4>
							<p>{{ $job->location }}</p>
						</div>
					</div>
					<p class="text-sm text-gray-600 mt-4">
						We are a leading healthcare provider committed to delivering
						exceptional patient care and professional development
						opportunities.
					</p>
				</div>


				<!-- Quick Apply -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Quick Apply</h3>
					<p class="text-sm text-gray-600 mb-4">Ready to join our team? Apply
						now with your resume.</p>
					<button class="w-full bg-primary text-white py-3 px-4 rounded-lg font-semibold hover:bg-primary-dark transition-colors"
						onclick="applyForJob()">
						<i class="fas fa-paper-plane mr-2"></i>Apply for this
						Position
					</button>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Related Jobs -->
@if($relatedJobs->count() > 0 || $similarJobs->count() > 0)
<section class="related-jobs">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<h2 class="section-title">Related Jobs</h2>
		<div class="jobs-grid">
			@foreach($relatedJobs->take(4) as $relatedJob)
			<div class="job-card"
				onclick="window.location.href='{{ route('jobs.show', $relatedJob->id) }}'">
				<h3 class="job-card-title line-clamp-1"><a
						href="{{ route('jobs.show', $relatedJob->id) }}">{{ $relatedJob->title }}</a>
				</h3>
				<p class="job-card-company">{{ $relatedJob->clinic->name ?? 'Medical Clinic' }}
				</p>
				<div class="job-card-meta">
					<div class="job-card-meta-item">
						<i class="fas fa-map-marker-alt"></i>
						<span>{{ $relatedJob->location }}</span>
					</div>
					<div class="job-card-meta-item">
						<i class="fas fa-briefcase"></i>
						<span>{{ ucfirst($relatedJob->type) }}</span>
					</div>
					<div class="job-card-meta-item">
						<!-- <i class="fas fa-dollar-sign"></i> -->
						<span class="text-[#ceeae7]">LE</span>
						<span>{{ $relatedJob->salary }} </span>
					</div>
				</div>
				<p class="job-card-description line-clamp-2">
					{{ Str::limit($relatedJob->description, 120) }}
				</p>
				<div class="job-card-actions">
					<button class="job-card-btn">View Details</button>
					<button class="job-card-btn job-card-btn-secondary">Save</button>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</section>
@endif

@endsection

@push('scripts')
<script>
// Job Application Functions
function applyForJob() {
	// Show loading state
	const btn = event.target;
	const originalText = btn.innerHTML;
	btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
	btn.disabled = true;

	// Simulate API call
	setTimeout(function() {
		btn.innerHTML = '<i class="fas fa-check mr-2"></i>Application Submitted';
		btn.style.background = '#10b981';

		setTimeout(function() {
			btn.innerHTML = originalText;
			btn.disabled = false;
			btn.style.background = '';
		}, 3000);
	}, 1500);
}

// Save Job Functions
function saveJob() {
	const btn = event.target;
	const icon = btn.querySelector('i');

	if (icon.classList.contains('fa-bookmark')) {
		icon.classList.remove('fa-bookmark');
		icon.classList.add('fa-bookmark-filled');
		btn.innerHTML = '<i class="fas fa-bookmark-filled mr-2"></i>Saved';
		btn.style.background = '#10b981';
		btn.style.borderColor = '#10b981';
	} else {
		icon.classList.remove('fa-bookmark-filled');
		icon.classList.add('fa-bookmark');
		btn.innerHTML = '<i class="fas fa-bookmark mr-2"></i>Save Job';
		btn.style.background = '';
		btn.style.borderColor = '';
	}
}

// Handle related job clicks
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.job-card').forEach(function(card) {
		card.addEventListener('click', function(e) {
			// Don't navigate if clicking on buttons
			if (e.target.tagName ===
				'BUTTON' || e.target
				.closest('button')
			) {
				return;
			}

			const jobId = this
				.getAttribute(
					'data-job-id'
				);
			if (jobId) {
				window.location
					.href =
					'/jobs/' +
					jobId;
			}
		});
	});
});
</script>
@endpush