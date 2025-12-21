@extends('frontend.layouts.app')

@section('title' , __('Doctor Details'))
@push('styles')
<style>
/* Modern Doctor Profile Page */
* {
	box-sizing: border-box;
}

.doctor-hero {
	background: linear-gradient(135deg, #059669 0%, #10b981 100%);
	padding: 60px 0;
	position: relative;
	overflow: hidden;
}
.doctor-hero.pro {
	background: radial-gradient(circle at 20% 20%, rgba(245,158,11,0.18), transparent 40%), linear-gradient(135deg, #0f172a 0%, #0ea5e9 55%, #f59e0b 100%);
}

.doctor-hero::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
	background-size: cover;
	opacity: 0.3;
}

.doctor-hero-content {
	position: relative;
	z-index: 1;
}

.doctor-profile-card {
	background: linear-gradient(145deg, #ffffff, #f8fafc);
	border-radius: 20px;
	padding: 40px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
	border: 1px solid rgba(245, 158, 11, 0.35);
	display: flex;
	gap: 40px;
	align-items: flex-start;
	margin-top: -80px;
	position: relative;
	z-index: 10;
}

.doctor-avatar-section {
	flex-shrink: 0;
}

.doctor-avatar {
	width: 200px;
	height: 200px;
	border-radius: 20px;
	object-fit: cover;
	border: 5px solid #f0fdf4;
	box-shadow: 0 10px 30px rgba(5, 150, 105, 0.2);
}

.doctor-avatar-placeholder {
	width: 200px;
	height: 200px;
	border-radius: 20px;
	background: linear-gradient(135deg, #059669, #10b981);
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 64px;
	font-weight: 700;
	border: 5px solid #f0fdf4;
	box-shadow: 0 10px 30px rgba(5, 150, 105, 0.2);
}

.doctor-info-section {
	flex: 1;
}

.doctor-name {
	font-size: 36px;
	font-weight: 700;
	color: #111827;
	margin-bottom: 8px;
	display: flex;
	align-items: center;
	gap: 12px;
}

.featured-badge {
	background: linear-gradient(135deg, #fbbf24, #f59e0b);
	color: white;
	padding: 6px 16px;
	border-radius: 20px;
	font-size: 14px;
	font-weight: 600;
	display: inline-flex;
	align-items: center;
	gap: 6px;
}

.doctor-specialization {
	color: #059669;
	font-size: 20px;
	font-weight: 600;
	margin-bottom: 20px;
}

.doctor-stats {
	display: flex;
	gap: 32px;
	margin-bottom: 24px;
	flex-wrap: wrap;
}

.stat-item {
	display: flex;
	align-items: center;
	gap: 12px;
}

.stat-icon {
	width: 48px;
	height: 48px;
	border-radius: 12px;
	background: linear-gradient(135deg, #dcfce7, #bbf7d0);
	display: flex;
	align-items: center;
	justify-content: center;
	color: #059669;
	font-size: 20px;
}

.stat-details h4 {
	font-size: 24px;
	font-weight: 700;
	color: #111827;
	margin: 0;
}

.stat-details p {
	font-size: 14px;
	color: #6b7280;
	margin: 0;
}

.doctor-social {
	display: flex;
	gap: 12px;
	margin-top: 20px;
}

.social-link {
	width: 40px;
	height: 40px;
	border-radius: 10px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: #f3f4f6;
	color: #6b7280;
	text-decoration: none;
	transition: all 0.3s;
}

.pro-chip {
	display: inline-flex;
	align-items: center;
	gap: 4px;
	padding: 0;
	border-radius: 9999px;
	background: transparent;
	color: #1d9bf0;
	font-weight: 700;
	font-size: 14px;
}

.pro-chip i {
	color: #1d9bf0;
}

.contact-actions {
	display: flex;
	align-items: center;
	gap: 16px;
	flex-wrap: wrap;
}

.share-pill {
	display: inline-flex;
	align-items: center;
	gap: 10px;
	padding: 12px 18px;
	border-radius: 9999px;
	background: linear-gradient(135deg, #111827, #f59e0b);
	color: #fff;
	font-weight: 700;
	text-decoration: none;
	transition: transform 0.2s ease, box-shadow 0.2s ease;
	box-shadow: 0 10px 24px rgba(245, 158, 11, 0.25);
	border: 1px solid rgba(255,255,255,0.1);
}

.share-pill:hover {
	transform: translateY(-2px);
	box-shadow: 0 12px 30px rgba(245, 158, 11, 0.35);
	color: #fff;
}

.social-link:hover {
	background: #059669;
	color: white;
	transform: translateY(-2px);
}

.content-section {
	background: white;
	border-radius: 16px;
	padding: 32px;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
	margin-bottom: 24px;
}

.section-title {
	font-size: 24px;
	font-weight: 700;
	color: #111827;
	margin-bottom: 20px;
	display: flex;
	align-items: center;
	gap: 12px;
}

.section-title-icon {
	width: 40px;
	height: 40px;
	border-radius: 10px;
	background: linear-gradient(135deg, #dcfce7, #bbf7d0);
	display: flex;
	align-items: center;
	justify-content: center;
	color: #059669;
}

.experience-timeline {
	position: relative;
	padding-left: 40px;
}

.experience-timeline::before {
	content: '';
	position: absolute;
	left: 15px;
	top: 0;
	bottom: 0;
	width: 2px;
	background: linear-gradient(to bottom, #10b981, #dcfce7);
}

.experience-item {
	position: relative;
	margin-bottom: 24px;
}

.experience-item::before {
	content: '';
	position: absolute;
	left: -32px;
	top: 8px;
	width: 12px;
	height: 12px;
	border-radius: 50%;
	background: #10b981;
	border: 3px solid white;
	box-shadow: 0 0 0 2px #10b981;
}

.experience-role {
	font-size: 18px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 4px;
}

.experience-company {
	color: #059669;
	font-weight: 500;
	margin-bottom: 4px;
}

.experience-period {
	font-size: 14px;
	color: #6b7280;
	margin-bottom: 8px;
}

.education-item,
.service-item {
	padding: 16px;
	background: #f9fafb;
	border-radius: 12px;
	margin-bottom: 12px;
	border-left: 4px solid #10b981;
}

.specialty-tags {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
}

.specialty-tag {
	background: linear-gradient(135deg, #dcfce7, #bbf7d0);
	color: #059669;
	padding: 10px 20px;
	border-radius: 20px;
	font-weight: 600;
	font-size: 14px;
}

/* Booking Day Cards Section */
.booking-section {
	background: white;
	border-radius: 16px;
	padding: 40px;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
	margin-top: 32px;
}

.booking-header {
	text-align: center;
	margin-bottom: 40px;
}

.booking-header h2 {
	font-size: 32px;
	font-weight: 700;
	color: #111827;
	margin-bottom: 12px;
}

.booking-header p {
	color: #6b7280;
	font-size: 16px;
}

.days-slider-container {
	position: relative;
	padding: 0 60px;
}

.days-slider {
	overflow-x: auto;
	scroll-behavior: smooth;
	-ms-overflow-style: none;
	scrollbar-width: none;
	padding: 20px 0;
}

.days-slider::-webkit-scrollbar {
	display: none;
}

.days-grid {
	display: flex;
	gap: 20px;
	min-width: min-content;
}

.day-card {
	flex: 0 0 320px;
	background: white;
	border: 2px solid #e5e7eb;
	border-radius: 16px;
	padding: 24px;
	transition: all 0.3s;
	cursor: pointer;
	position: relative;
}

.day-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
	border-color: #10b981;
}

.day-card.available {
	border-color: #10b981;
	background: linear-gradient(to bottom, white, #f0fdf4);
}

.day-card.blocked {
	background: #f9fafb;
	border-color: #e5e7eb;
	cursor: not-allowed;
}

.day-card.blocked:hover {
	transform: none;
	box-shadow: none;
}

.day-card-header {
	text-align: center;
	margin-bottom: 20px;
	padding-bottom: 16px;
	border-bottom: 2px solid #e5e7eb;
}

.day-name {
	font-size: 18px;
	font-weight: 700;
	color: #111827;
	margin-bottom: 4px;
}

.day-date {
	font-size: 24px;
	font-weight: 700;
	color: #059669;
}

.availability-status {
	text-align: center;
	padding: 20px;
}

.status-icon {
	width: 60px;
	height: 60px;
	border-radius: 50%;
	margin: 0 auto 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 28px;
}

.status-icon.available {
	background: #dcfce7;
	color: #059669;
}

.status-icon.blocked {
	background: #fee2e2;
	color: #dc2626;
}

.status-text {
	font-weight: 600;
	font-size: 16px;
	margin-bottom: 8px;
}

.status-text.available {
	color: #059669;
}

.status-text.blocked {
	color: #dc2626;
}

.slots-count {
	color: #6b7280;
	font-size: 14px;
}

.day-card-footer {
	margin-top: 20px;
	padding-top: 16px;
	border-top: 2px solid #e5e7eb;
}

.book-btn {
	width: 100%;
	padding: 14px;
	border-radius: 10px;
	border: none;
	font-weight: 600;
	font-size: 16px;
	cursor: pointer;
	transition: all 0.3s;
}

.book-btn.available {
	background: linear-gradient(135deg, #059669, #10b981);
	color: white;
}

.book-btn.available:hover {
	background: linear-gradient(135deg, #047857, #059669);
	transform: translateY(-2px);
	box-shadow: 0 8px 16px rgba(5, 150, 105, 0.3);
}

.book-btn.blocked {
	background: #e5e7eb;
	color: #9ca3af;
	cursor: not-allowed;
}

.slider-nav {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	width: 48px;
	height: 48px;
	border-radius: 50%;
	background: white;
	border: 2px solid #e5e7eb;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all 0.3s;
	z-index: 10;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.slider-nav:hover {
	background: #059669;
	color: white;
	border-color: #059669;
}

.slider-nav.prev {
	left: 0;
}

.slider-nav.next {
	right: 0;
}

.time-slots-modal {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.5);
	z-index: 1000;
	align-items: center;
	justify-content: center;
	padding: 20px;
}

.time-slots-modal.active {
	display: flex;
}

.modal-content {
	background: white;
	border-radius: 20px;
	padding: 40px;
	max-width: 800px;
	width: 100%;
	max-height: 90vh;
	overflow-y: auto;
	position: relative;
}

.modal-close {
	position: absolute;
	top: 20px;
	right: 20px;
	width: 40px;
	height: 40px;
	border-radius: 50%;
	background: #f3f4f6;
	border: none;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all 0.3s;
}

.modal-close:hover {
	background: #dc2626;
	color: white;
}

.time-slots-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
	gap: 16px;
	margin-top: 24px;
}

.time-slot {
	padding: 16px;
	border: 2px solid #e5e7eb;
	border-radius: 12px;
	text-align: center;
	cursor: pointer;
	transition: all 0.3s;
	background: white;
}

.time-slot:hover:not(.past) {
	border-color: #10b981;
	background: #f0fdf4;
}

.time-slot.selected {
	background: linear-gradient(135deg, #059669, #10b981);
	color: white;
	border-color: #059669;
}

.time-slot.full {
	background: #fff7ed;
	color: #ea580c;
	border-color: #fdba74;
}

.time-slot.past {
	background: #f9fafb;
	color: #9ca3af;
	cursor: not-allowed;
	border-color: #e5e7eb;
	opacity: 0.6;
}

.booking-form {
	margin-top: 32px;
	padding-top: 32px;
	border-top: 2px solid #e5e7eb;
}

.form-group {
	margin-bottom: 20px;
}

.form-label {
	display: block;
	font-weight: 600;
	color: #374151;
	margin-bottom: 8px;
}

.form-control {
	width: 100%;
	padding: 12px 16px;
	border: 2px solid #e5e7eb;
	border-radius: 10px;
	font-size: 16px;
	transition: all 0.3s;
}

.form-control:focus {
	outline: none;
	border-color: #10b981;
	box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

@media (max-width: 768px) {
	.doctor-profile-card {
		flex-direction: column;
		padding: 24px;
	}

	.doctor-avatar,
	.doctor-avatar-placeholder {
		width: 150px;
		height: 150px;
		margin: 0 auto;
	}

	.doctor-name {
		font-size: 28px;
	}

	.doctor-stats {
		gap: 20px;
	}

	.days-slider-container {
		padding: 0 20px;
	}

	.day-card {
		flex: 0 0 280px;
	}
}
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('click', function(e) {
	const btn = e.target.closest('.copy-share-link');
	if (!btn) return;
	const url = btn.dataset.url;
	if (!url) return;
	const done = () => showToast();

	if (navigator.clipboard && navigator.clipboard.writeText) {
		navigator.clipboard.writeText(url).then(done);
	} else {
		const el = document.createElement('textarea');
		el.value = url;
		document.body.appendChild(el);
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
		done();
	}
});

function showToast() {
	const toast = document.createElement('div');
	toast.textContent = '{{ __("Link copied!") }}';
	Object.assign(toast.style, {
		position: 'fixed',
		bottom: '24px',
		right: '24px',
		padding: '12px 16px',
		background: 'linear-gradient(135deg, #111827, #f59e0b)',
		color: '#fff',
		borderRadius: '9999px',
		boxShadow: '0 12px 30px rgba(0,0,0,0.25)',
		zIndex: '9999',
		transition: 'opacity .3s ease'
	});
	document.body.appendChild(toast);
	setTimeout(() => {
		toast.style.opacity = '0';
		setTimeout(() => toast.remove(), 300);
	}, 1500);
}
</script>
@endpush

@section('content')
@php
	$proService = app(\App\Services\ProfessionalBioService::class);
	$hasProBio = $proService->hasForDoctor($doctor);
	$shareUrl = $hasProBio ? $proService->getShareUrl($doctor, 'doctor') : null;
@endphp

<!-- Doctor Hero Section -->
<section class="doctor-hero {{ $hasProBio ? 'pro' : '' }}">
	<div class="doctor-hero-content">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<!-- Profile Card -->
			<div class="doctor-profile-card" style="position: relative;">
				<!-- Avatar -->
				<div class="doctor-avatar-section">
					@if($doctor->profile_photo_url)
					<img src="{{ $doctor->profile_photo_url }}" alt="{{ $doctor->name }}"
						class="doctor-avatar">
					@else
					<div class="doctor-avatar-placeholder">
						{{ substr($doctor->name, 0, 1) }}
					</div>
					@endif
				</div>

				<!-- Info -->
				<div class="doctor-info-section">
					<div class="doctor-name">
						{{ $doctor->name }}
						@if($doctor->is_featured)
						<span class="featured-badge">
							<i class="fas fa-star"></i> {{ __('Featured') }}
						</span>
						@endif
						@if($hasProBio)
							<span class="pro-chip">
								<i class="fas fa-check-circle"></i>
							</span>
						@endif
					</div>
					<div class="doctor-specialization">
						<i class="fas fa-user-md"></i>
						@if($doctor->speciality)
							{{ App::getLocale() == 'ar' ? $doctor->speciality->name_ar : $doctor->speciality->name_en }}
						@else
							{{ __('General Practice') }}
						@endif
					</div>

					<!-- Stats -->
					<div class="doctor-stats">
						@if($doctor->years_experience)
						<div class="stat-item">
							<div class="stat-icon">
								<i class="fas fa-briefcase"></i>
							</div>
							<div class="stat-details">
								<h4>{{ $doctor->years_experience }}+
								</h4>
								<p>{{ __('Years Experience') }}</p>
							</div>
						</div>
						@endif

					</div>

					<!-- Contact -->
					<div
						class="contact-actions" style="margin-top: 20px;">
						@if($doctor->email)
						<a href="mailto:{{ $doctor->email }}"
							style="color: #6b7280; text-decoration: none;">
							<i class="fas fa-envelope"></i>
							{{ $doctor->email }}
						</a>
						@endif
						@if($doctor->phone)
						<a href="tel:{{ $doctor->phone }}"
							style="color: #6b7280; text-decoration: none;">
							<i class="fas fa-phone"></i> {{ $doctor->phone }}
						</a>
						@endif
						@if($hasProBio && $shareUrl)
						<button type="button" class="share-pill copy-share-link" data-url="{{ $shareUrl }}">
							<i class="fas fa-link"></i> {{ __('Copy signature link') }}
						</button>
						@endif
					</div>

					<!-- Social Links -->
					@if($doctor->facebook_link || $doctor->twitter_link ||
					$doctor->linkedin_link || $doctor->instagram_link)
					<div class="doctor-social">
						@if($doctor->facebook_link)
						<a href="{{ $doctor->facebook_link }}" target="_blank"
							class="social-link">
							<i class="fab fa-facebook-f"></i>
						</a>
						@endif
						@if($doctor->twitter_link)
						<a href="{{ $doctor->twitter_link }}" target="_blank"
							class="social-link">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/></svg>
						</a>
						@endif
						@if($doctor->linkedin_link)
						<a href="{{ $doctor->linkedin_link }}" target="_blank"
							class="social-link">
							<i class="fab fa-linkedin-in"></i>
						</a>
						@endif
						@if($doctor->instagram_link)
						<a href="{{ $doctor->instagram_link }}" target="_blank"
							class="social-link">
							<i class="fab fa-instagram"></i>
						</a>
						@endif
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Main Content -->
<section style="padding: 60px 0; background: #f9fafb;">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
			<!-- Left Column -->
			<div>
				<!-- About -->
				@if($doctor->bio)
				<div class="content-section">
					<h3 class="section-title">
						<span class="section-title-icon"><i
								class="fas fa-info-circle"></i></span>
						{{ __('About Dr.') }} {{ $doctor->name }}
					</h3>
					<p style="color: #6b7280; line-height: 1.8; font-size: 16px;">
						{{ $doctor->bio }}</p>
				</div>
				@endif

				<!-- Experience -->
				@if($doctor->experience && is_array($doctor->experience) &&
				count($doctor->experience) > 0)
				<div class="content-section">
					<h3 class="section-title">
						<span class="section-title-icon"><i
								class="fas fa-briefcase"></i></span>
						{{ __('Experience') }}
					</h3>
					<div class="experience-timeline">
						@foreach($doctor->experience as $exp)
						<div class="experience-item">
							<div class="experience-role">
								{{ is_array($exp) ? ($exp['title'] ?? $exp['role'] ?? '') : $exp }}
							</div>
							@if(is_array($exp) && isset($exp['organization']))
							<div class="experience-company">
								{{ $exp['organization'] }}</div>
							@endif
							@if(is_array($exp) && isset($exp['period']))
							<div class="experience-period">
								{{ $exp['period'] }}</div>
							@endif
							@if(is_array($exp) && isset($exp['description']))
							<p style="color: #6b7280; margin-top: 8px;">
								{{ $exp['description'] }}</p>
							@endif
						</div>
						@endforeach
					</div>
				</div>
				@endif

				<!-- Education -->
				@if($doctor->education && is_array($doctor->education) &&
				count($doctor->education) > 0)
				<div class="content-section">
					<h3 class="section-title">
						<span class="section-title-icon"><i
								class="fas fa-graduation-cap"></i></span>
						{{ __('Education') }}
					</h3>
					@foreach($doctor->education as $edu)
					<div class="education-item">
						<i class="fas fa-graduation-cap"
							style="color: #059669; margin-right: 12px;"></i>
						<strong>{{ is_array($edu) ? ($edu['degree'] ?? $edu['title'] ?? '') : $edu }}</strong>
						@if(is_array($edu) && isset($edu['institution']))
						<div style="color: #6b7280; margin-top: 4px;">
							{{ $edu['institution'] }}</div>
						@endif
						@if(is_array($edu) && isset($edu['year']))
						<div
							style="color: #9ca3af; font-size: 14px; margin-top: 2px;">
							{{ $edu['year'] }}</div>
						@endif
					</div>
					@endforeach
				</div>
				@endif
			</div>

			<!-- Right Column -->
			<div>
				<!-- Specialties -->
				@if($doctor->specialties && is_array($doctor->specialties) &&
				count($doctor->specialties) > 0)
				<div class="content-section">
					<h3 class="section-title">
						<span class="section-title-icon"><i
								class="fas fa-stethoscope"></i></span>
						{{ __('Specialties') }}
					</h3>
					<div class="specialty-tags">
						@foreach($doctor->specialties as $specialty)
						<span class="specialty-tag">{{ $specialty }}</span>
						@endforeach
					</div>
				</div>
				@endif

				<!-- Services -->
				@if($doctor->services_offered && is_array($doctor->services_offered) &&
				count($doctor->services_offered) > 0)
				<div class="content-section">
					<h3 class="section-title">
						<span class="section-title-icon"><i
								class="fas fa-hand-holding-medical"></i></span>
						{{ __('Services Offered') }}
					</h3>
					@foreach($doctor->services_offered as $service)
					<div class="service-item">
						<i class="fas fa-check-circle"
							style="color: #10b981; margin-right: 12px;"></i>
						{{ $service }}
					</div>
					@endforeach
				</div>
				@endif

				<!-- Research Links -->
				@if($doctor->research_links && is_array($doctor->research_links) &&
				count($doctor->research_links) > 0)
				<div class="content-section">
					<h3 class="section-title">
						<span class="section-title-icon"><i
								class="fas fa-microscope"></i></span>
						{{ __('Research & Publications') }}
					</h3>
					@foreach($doctor->research_links as $link)
					<a href="{{ is_array($link) ? ($link['url'] ?? '#') : $link }}"
						target="_blank"
						style="display: block; padding: 12px; background: #f9fafb; border-radius: 8px; margin-bottom: 8px; text-decoration: none; color: #059669; border-left: 3px solid #10b981;">
						<i class="fas fa-external-link-alt"></i>
						{{ is_array($link) ? ($link['title'] ?? __('Research Link')) : __('Research Link') }}
					</a>
					@endforeach
				</div>
				@endif
			</div>
		</div>

		<!-- Booking Section -->
		@if($doctor->clinicUser->clinic_id != null)
		<div class="booking-section">
			<div class="booking-header">
				<h2><i class="fas fa-calendar-alt" style="color: #059669;"></i>
					{{ __('Book Your Appointment') }}</h2>
				<p>{{ __('Select a date to view available time slots') }}</p>
			</div>

			<div class="days-slider-container">
				<div class="slider-nav prev" onclick="scrollDays('left')">
					<i class="fas fa-chevron-left"></i>
				</div>
				<div class="slider-nav next" onclick="scrollDays('right')">
					<i class="fas fa-chevron-right"></i>
				</div>

				<div class="days-slider" id="daysSlider">
					<div class="days-grid" id="daysGrid">
						<!-- Days will be dynamically loaded here -->
					</div>
				</div>
			</div>
		</div>
		@endif
	</div>
</section>

<!-- Time Slots Modal -->
<div class="time-slots-modal" id="timeSlotsModal">
	<div class="modal-content">
		<button class="modal-close" onclick="closeModal()">
			<i class="fas fa-times"></i>
		</button>

		<h3 style="font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 8px;">
			{{ __('Select Time Slot') }}
		</h3>
		<p id="selectedDateDisplay" style="color: #6b7280; margin-bottom: 24px;"></p>

		<div class="time-slots-grid" id="timeSlots">
			<!-- Time slots will be loaded here -->
		</div>

		<div class="booking-form" id="bookingForm" style="display: none;">
			<h4 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">
				{{ __('Your Information') }}</h4>
			<form id="appointmentForm">
				<input type="hidden" name="doctor_profile_id" value="{{ $doctor->id }}">
				<input type="hidden" name="period_id" id="selectedPeriodId">

				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
					<div class="form-group">
						<label class="form-label">{{ __('Name') }} *</label>
						<input type="text" name="name" class="form-control" required
							value="{{ isset($patient) && $patient ? ($patient->user?->name) : '' }}">
					</div>
					<div class="form-group">
						<label class="form-label">{{ __('Email') }} *</label>
						<input type="email" name="email" class="form-control"
							required
							value="{{ isset($patient) && $patient ? ($patient->user?->email) : '' }}">
					</div>
				</div>

				<div class="form-group">
					<label class="form-label">{{ __('Phone') }} *</label>
					<input type="tel" name="phone" class="form-control" required
						value="{{ isset($patient) && $patient ? ($patient->phone ?? $patient->user?->phone) : '' }}">
				</div>

				<div class="form-group">
					<label class="form-label">{{ __('Notes (Optional)') }}</label>
					<textarea name="patient_notes" class="form-control" rows="3"
						placeholder="{{ __('Any special notes or concerns...') }}"></textarea>
				</div>

				<button type="submit" class="book-btn available">
					<i class="fas fa-calendar-check"></i> {{ __('Confirm Booking') }}
				</button>
			</form>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script>
const doctorId = {
	{
		$doctor - > id
	}
};
let availableDaysData = {};
let selectedDate = null;
let selectedPeriod = null;

// Load next 30 days
function loadDays() {
	const today = new Date();
	const daysGrid = document.getElementById('daysGrid');
	daysGrid.innerHTML =
		'<div style="grid-column: 1/-1; text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #059669;"></i></div>';

	const startDate = today.toISOString().split('T')[0];
	const endDate = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

	fetch(`{{ route('doctors.available-days', $doctor->id) }}?start_date=${startDate}&end_date=${endDate}`)
		.then(response => response.json())
		.then(data => {
			if (data.status === 'success') {
				availableDaysData = data.days;
				renderDays();
			}
		})
		.catch(error => {
			console.error('Error:', error);
			daysGrid.innerHTML =
				'<div style="grid-column: 1/-1; text-align: center; color: #dc2626;">Error loading dates</div>';
		});
}

function renderDays() {
	const daysGrid = document.getElementById('daysGrid');
	const today = new Date();
	const dayNames = ['{{ __("Sunday") }}', '{{ __("Monday") }}', '{{ __("Tuesday") }}', '{{ __("Wednesday") }}',
		'{{ __("Thursday") }}', '{{ __("Friday") }}', '{{ __("Saturday") }}'
	];
	const monthNames = ['{{ __("Jan") }}', '{{ __("Feb") }}', '{{ __("Mar") }}', '{{ __("Apr") }}',
		'{{ __("May") }}', '{{ __("Jun") }}', '{{ __("Jul") }}', '{{ __("Aug") }}',
		'{{ __("Sep") }}', '{{ __("Oct") }}', '{{ __("Nov") }}', '{{ __("Dec") }}'
	];

	let html = '';

	for (let i = 0; i < 30; i++) {
		const date = new Date(today);
		date.setDate(today.getDate() + i);
		const dateStr = date.toISOString().split('T')[0];
		const isAvailable = availableDaysData.includes(dateStr);

		const dayName = dayNames[date.getDay()];
		const dayNum = date.getDate();
		const monthName = monthNames[date.getMonth()];

		html += `
			<div class="day-card ${isAvailable ? 'available' : 'blocked'}" onclick="selectDay('${dateStr}', ${isAvailable})">
				<div class="day-card-header">
					<div class="day-name">${dayName}</div>
					<div class="day-date">${dayNum} ${monthName}</div>
				</div>
				<div class="availability-status">
					<div class="status-icon ${isAvailable ? 'available' : 'blocked'}">
						<i class="fas fa-${isAvailable ? 'check-circle' : 'times-circle'}"></i>
					</div>
					<div class="status-text ${isAvailable ? 'available' : 'blocked'}">
						${isAvailable ? '{{ __("Available") }}' : '{{ __("No Appointments") }}'}
					</div>
					<div class="slots-count">
						${isAvailable ? '{{ __("View time slots") }}' : '{{ __("Not available") }}'}
					</div>
				</div>
				<div class="day-card-footer">
					<button class="book-btn ${isAvailable ? 'available' : 'blocked'}" ${!isAvailable ? 'disabled' : ''}>
						<i class="fas fa-calendar-${isAvailable ? 'check' : 'times'}"></i>
						${isAvailable ? '{{ __("Book") }}' : '{{ __("Blocked") }}'}
					</button>
				</div>
			</div>
		`;
	}

	daysGrid.innerHTML = html;
}

function scrollDays(direction) {
	const slider = document.getElementById('daysSlider');
	const scrollAmount = 340;
	slider.scrollBy({
		left: direction === 'left' ? -scrollAmount : scrollAmount,
		behavior: 'smooth'
	});
}

function selectDay(dateStr, isAvailable) {
	if (!isAvailable) return;

	selectedDate = dateStr;
	document.getElementById('selectedDateDisplay').textContent = new Date(dateStr).toLocaleDateString('en-US', {
		weekday: 'long',
		year: 'numeric',
		month: 'long',
		day: 'numeric'
	});

	openModal();
	loadTimeSlots(dateStr);
}

function openModal() {
	document.getElementById('timeSlotsModal').classList.add('active');
	document.body.style.overflow = 'hidden';
}

function closeModal() {
	document.getElementById('timeSlotsModal').classList.remove('active');
	document.body.style.overflow = '';
	document.getElementById('bookingForm').style.display = 'none';
}

function loadTimeSlots(date) {
	const timeSlotsGrid = document.getElementById('timeSlots');
	timeSlotsGrid.innerHTML =
		'<div style="grid-column: 1/-1; text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #059669;"></i></div>';

	fetch(`{{ route('doctors.available-periods', $doctor->id) }}?date=${date}`)
		.then(response => response.json())
		.then(data => {
			if (data.status === 'success' && data.periods.length > 0) {
				let html = '';
				data.periods.forEach(period => {
					const isPast = period.is_past || false;
					const remaining = period
						.remaining_capacity || 0;
					const confirmedBookings = period
						.confirmed_bookings || 0;
					const capacity = period.capacity || 0;
					const atCapacity = confirmedBookings >=
						capacity;

					let slotClass = '';
					let statusText = '';
					let statusColor = '#059669';
					let clickable = true;

					if (isPast) {
						slotClass = 'past';
						statusText =
							'{{ __("Expired") }}';
						statusColor = '#9ca3af';
						clickable = false;
					} else if (atCapacity) {
						slotClass = 'full';
						statusText =
							`{{ __("At capacity") }} (${confirmedBookings}/${capacity})`;
						statusColor = '#ea580c';
						clickable =
						true; // Still allow booking
					} else {
						statusText =
							`${remaining} {{ __("spots available") }}`;
						statusColor = '#059669';
						clickable = true;
					}

					html += `
						<div class="time-slot ${slotClass}"
							${clickable ? `onclick="selectTimeSlot(${period.id}, '${period.start_time} - ${period.end_time}')"` : ''}>
							<div style="font-weight: 700; font-size: 18px; margin-bottom: 8px;">
								${period.start_time} - ${period.end_time}
							</div>
							<div style="font-size: 14px; color: ${statusColor};">
								${statusText}
							</div>
						</div>
					`;
				});
				timeSlotsGrid.innerHTML = html;
			} else {
				timeSlotsGrid.innerHTML =
					'<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #6b7280;">{{ __("No available time slots for this date.") }}</div>';
			}
		})
		.catch(error => {
			console.error('Error:', error);
			timeSlotsGrid.innerHTML =
				'<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #dc2626;">{{ __("Error loading time slots.") }}</div>';
		});
}

function selectTimeSlot(periodId, timeRange) {
	selectedPeriod = periodId;
	document.getElementById('selectedPeriodId').value = periodId;

	document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));
	event.currentTarget.classList.add('selected');

	document.getElementById('bookingForm').style.display = 'block';
	document.getElementById('bookingForm').scrollIntoView({
		behavior: 'smooth',
		block: 'nearest'
	});
}

// Form submission
document.getElementById('appointmentForm').addEventListener('submit', function(e) {
	e.preventDefault();

	const formData = new FormData(this);
	const data = Object.fromEntries(formData);

	fetch('{{ route("appointments.book") }}', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			body: JSON.stringify(data)
		})
		.then(response => response.json())
		.then(data => {
			if (data.status === 'success') {
				Swal.fire({
					title: '{{ __("Booking Successful!") }}',
					html: `{{ __("Your confirmation code is:") }}<br><strong style="font-size: 32px; color: #059669; display: block; margin: 20px 0;">${data.confirmation_code}</strong>{{ __("Please save this code for confirmation.") }}`,
					icon: 'success',
					confirmButtonText: '{{ __("OK") }}',
					confirmButtonColor: '#059669'
				}).then(() => {
					closeModal();
					loadDays();
				});
			} else {
				Swal.fire('{{ __("Error") }}', data.message,
					'error');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			Swal.fire('{{ __("Error") }}',
				'{{ __("An error occurred while booking") }}',
				'error');
		});
});

// Close modal on outside click
document.getElementById('timeSlotsModal').addEventListener('click', function(e) {
	if (e.target === this) {
		closeModal();
	}
});

// Initialize
loadDays();
</script>
@endpush
