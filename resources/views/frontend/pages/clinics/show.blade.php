@extends('frontend.layouts.app')

@push('styles')
<style>
	/* Clinic Details Page Styles */
	.clinic-header {
		background: linear-gradient(135deg, #059669, #10b981);
		color: white;
		padding: 40px 0;
		margin-bottom: 32px;
	}

	.clinic-title {
		font-size: 32px;
		font-weight: 700;
		margin-bottom: 8px;
	}

	.clinic-specialization {
		font-size: 18px;
		opacity: 0.9;
		margin-bottom: 16px;
	}

	.clinic-meta {
		display: flex;
		flex-wrap: wrap;
		gap: 24px;
		margin-bottom: 24px;
	}

	.clinic-meta-item {
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.clinic-meta-icon {
		font-size: 18px;
		opacity: 0.8;
	}

	.clinic-actions {
		display: flex;
		gap: 12px;
		margin-top: 24px;
	}

	.btn-book {
		background: white;
		color: #059669;
		border: 2px solid white;
		padding: 12px 24px;
		border-radius: 8px;
		font-weight: 600;
		text-decoration: none;
		transition: all 0.3s ease;
	}

	.btn-book:hover {
		background: transparent;
		color: white;
	}

	.btn-contact {
		background: transparent;
		color: white;
		border: 2px solid white;
		padding: 12px 24px;
		border-radius: 8px;
		font-weight: 600;
		text-decoration: none;
		transition: all 0.3s ease;
	}

	.btn-contact:hover {
		background: white;
		color: #059669;
	}

	.clinic-content {
		display: grid;
		grid-template-columns: 2fr 1fr;
		gap: 32px;
		margin-bottom: 48px;
	}

	.clinic-main {
		background: white;
		border-radius: 12px;
		padding: 32px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
	}

	.clinic-sidebar {
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

	.clinic-image {
		width: 100%;
		height: 300px;
		object-fit: cover;
		border-radius: 12px;
		margin-bottom: 24px;
	}

	.clinic-description {
		margin-bottom: 32px;
	}

	.clinic-description h3 {
		font-size: 20px;
		font-weight: 600;
		color: #111827;
		margin-bottom: 16px;
	}

	.clinic-description p {
		color: #6b7280;
		line-height: 1.6;
		margin-bottom: 16px;
	}

	.services-list {
		list-style: none;
		padding: 0;
	}

	.services-list li {
		display: flex;
		align-items: center;
		gap: 8px;
		padding: 8px 0;
		color: #374151;
	}

	.service-icon {
		color: #10b981;
		font-size: 16px;
	}

	.contact-info {
		margin-bottom: 24px;
	}

	.contact-item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 12px 0;
		border-bottom: 1px solid #e5e7eb;
	}

	.contact-item:last-child {
		border-bottom: none;
	}

	.contact-icon {
		color: #059669;
		font-size: 18px;
		width: 20px;
	}

	.contact-text {
		color: #374151;
	}

	.working-hours {
		margin-bottom: 24px;
	}

	.hours-item {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 8px 0;
		border-bottom: 1px solid #e5e7eb;
	}

	.hours-item:last-child {
		border-bottom: none;
	}

	.day {
		font-weight: 500;
		color: #374151;
	}

	.time {
		color: #6b7280;
	}

	.rating-display {
		display: flex;
		align-items: center;
		gap: 8px;
		margin-bottom: 16px;
	}

	.rating-stars {
		color: #fbbf24;
		font-size: 18px;
	}

	.rating-value {
		font-size: 18px;
		font-weight: 600;
		color: #111827;
	}

	.rating-count {
		color: #6b7280;
		font-size: 14px;
	}

	.related-clinics {
		margin-top: 48px;
	}

	.section-title {
		font-size: 24px;
		font-weight: 700;
		color: #111827;
		margin-bottom: 24px;
		text-align: center;
	}

	.clinics-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 24px;
	}

	.clinic-card {
		background: white;
		border-radius: 12px;
		padding: 24px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
		transition: all 0.3s ease;
		cursor: pointer;
	}

	.clinic-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
	}

	.clinic-card-title {
		font-size: 18px;
		font-weight: 600;
		color: #111827;
		margin-bottom: 8px;
	}

	.clinic-card-specialization {
		color: #059669;
		font-weight: 500;
		margin-bottom: 12px;
	}

	.clinic-card-meta {
		display: flex;
		flex-wrap: wrap;
		gap: 12px;
		margin-bottom: 16px;
	}

	.clinic-card-meta-item {
		display: flex;
		align-items: center;
		gap: 4px;
		font-size: 14px;
		color: #6b7280;
	}

	.clinic-card-description {
		color: #6b7280;
		font-size: 14px;
		line-height: 1.5;
		margin-bottom: 16px;
	}

	.clinic-card-actions {
		display: flex;
		gap: 8px;
	}

	.clinic-card-btn {
		flex: 1;
		background: #059669;
		color: white;
		border: none;
		padding: 8px 16px;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.clinic-card-btn:hover {
		background: #10b981;
	}

	.clinic-card-btn-secondary {
		background: transparent;
		color: #059669;
		border: 1px solid #059669;
	}

	.clinic-card-btn-secondary:hover {
		background: #059669;
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

	/* Status Badges */
	.status-badge {
		padding: 4px 12px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 600;
		text-transform: uppercase;
	}

	.status-open {
		background: #dcfce7;
		color: #166534;
	}

	.status-closed {
		background: #fee2e2;
		color: #991b1b;
	}

	/* Map Styles */
	.map-container {
		height: 300px;
		border-radius: 12px;
		overflow: hidden;
		margin-bottom: 24px;
	}

	/* Responsive Design */
	@media (max-width: 768px) {
		.clinic-content {
			grid-template-columns: 1fr;
			gap: 24px;
		}

		.clinic-meta {
			flex-direction: column;
			gap: 12px;
		}

		.clinic-actions {
			flex-direction: column;
		}

		.clinics-grid {
			grid-template-columns: 1fr;
		}

		.clinic-title {
			font-size: 24px;
		}

		.clinic-specialization {
			font-size: 16px;
		}
	}

	/* Loading States */
	.loading {
		opacity: 0.6;
		pointer-events: none;
	}

	/* Gallery Styles */
	.clinic-gallery {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 12px;
		margin-bottom: 24px;
	}

	.gallery-item {
		height: 150px;
		border-radius: 8px;
		overflow: hidden;
		cursor: pointer;
		transition: transform 0.3s ease;
	}

	.gallery-item:hover {
		transform: scale(1.05);
	}

	.gallery-item img {
		width: 100%;
		height: 100%;
		object-fit: cover;
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
				<a href="{{ route('clinics') }}" class="hover:text-primary">Clinics</a>
			</li>
			<li class="breadcrumb-separator">/</li>
			<li class="breadcrumb-item active">{{ $clinic->name }}</li>
		</ol>
	</div>
</nav>

<!-- Clinic Header -->
<section class="clinic-header">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex items-center justify-between">
			<div>
				<h1 class="clinic-title">{{ $clinic->name }}</h1>
				<p class="clinic-specialization">{{ ucfirst($clinic->specialization) }} Clinic</p>

				<div class="clinic-meta">
					<div class="clinic-meta-item">
						<i class="fas fa-map-marker-alt clinic-meta-icon"></i>
						<span>{{ $clinic->address }}</span>
					</div>
					<div class="clinic-meta-item">
						<i class="fas fa-star clinic-meta-icon"></i>
						<span>{{ $clinic->rating ?? 4.5 }} Rating</span>
					</div>
					<div class="clinic-meta-item">
						<i class="fas fa-clock clinic-meta-icon"></i>
						<span>{{ $clinic->status ? 'Open Now' : 'Closed' }}</span>
					</div>
					<div class="clinic-meta-item">
						<i class="fas fa-phone clinic-meta-icon"></i>
						<span>{{ $clinic->phone ?? '+1 (555) 123-4567' }}</span>
					</div>
				</div>
			</div>

			<div class="clinic-actions">
				<a href="#" class="btn-book" onclick="bookAppointment()">
					<i class="fas fa-calendar-plus mr-2"></i>Book Appointment
				</a>
				<a href="#" class="btn-contact" onclick="contactClinic()">
					<i class="fas fa-phone mr-2"></i>Contact
				</a>
			</div>
		</div>
	</div>
</section>

<!-- Clinic Content -->
<section class="py-8">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="clinic-content">
			<!-- Main Content -->
			<div class="clinic-main">
				<!-- Clinic Image -->
				<img src="/images/clinics/clinic-{{ $clinic->id }}.jpg" alt="{{ $clinic->name }}" class="clinic-image">

				<!-- Clinic Description -->
				<div class="clinic-description">
					<h3>About Our Clinic</h3>
					<p>{{ $clinic->description ?? 'We are a leading healthcare provider committed to delivering exceptional patient care and professional medical services. Our clinic is equipped with state-of-the-art facilities and staffed by experienced medical professionals.' }}</p>
					<p>Our team of dedicated healthcare professionals is committed to providing comprehensive medical care in a comfortable and welcoming environment. We prioritize patient safety, comfort, and positive health outcomes.</p>
				</div>

				<!-- Services -->
				<div class="clinic-description">
					<h3>Our Services</h3>
					<ul class="services-list">
						<li><i class="fas fa-check service-icon"></i>General Medical Consultations</li>
						<li><i class="fas fa-check service-icon"></i>Specialized {{ ucfirst($clinic->specialization) }} Care</li>
						<li><i class="fas fa-check service-icon"></i>Diagnostic Services</li>
						<li><i class="fas fa-check service-icon"></i>Preventive Health Screenings</li>
						<li><i class="fas fa-check service-icon"></i>Emergency Medical Care</li>
						<li><i class="fas fa-check service-icon"></i>Follow-up Consultations</li>
						<li><i class="fas fa-check service-icon"></i>Health Education and Counseling</li>
						<li><i class="fas fa-check service-icon"></i>Telemedicine Services</li>
					</ul>
				</div>

				<!-- Gallery -->
				<div class="clinic-description">
					<h3>Clinic Gallery</h3>
					<div class="clinic-gallery">
						<div class="gallery-item">
							<img src="/images/clinics/gallery-1.jpg" alt="Clinic Interior">
						</div>
						<div class="gallery-item">
							<img src="/images/clinics/gallery-2.jpg" alt="Waiting Area">
						</div>
						<div class="gallery-item">
							<img src="/images/clinics/gallery-3.jpg" alt="Consultation Room">
						</div>
						<div class="gallery-item">
							<img src="/images/clinics/gallery-4.jpg" alt="Reception Area">
						</div>
					</div>
				</div>
			</div>

			<!-- Sidebar -->
			<div class="clinic-sidebar">
				<!-- Contact Information -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Contact Information</h3>
					<div class="contact-info">
						<div class="contact-item">
							<i class="fas fa-map-marker-alt contact-icon"></i>
							<span class="contact-text">{{ $clinic->address }}</span>
						</div>
						<div class="contact-item">
							<i class="fas fa-phone contact-icon"></i>
							<span class="contact-text">{{ $clinic->phone ?? '+1 (555) 123-4567' }}</span>
						</div>
						<div class="contact-item">
							<i class="fas fa-envelope contact-icon"></i>
							<span class="contact-text">{{ $clinic->email ?? 'info@clinic.com' }}</span>
						</div>
						<div class="contact-item">
							<i class="fas fa-globe contact-icon"></i>
							<span class="contact-text">{{ $clinic->website ?? 'www.clinic.com' }}</span>
						</div>
					</div>
				</div>

				<!-- Working Hours -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Working Hours</h3>
					<div class="working-hours">
						<div class="hours-item">
							<span class="day">Monday - Friday</span>
							<span class="time">8:00 AM - 6:00 PM</span>
						</div>
						<div class="hours-item">
							<span class="day">Saturday</span>
							<span class="time">9:00 AM - 4:00 PM</span>
						</div>
						<div class="hours-item">
							<span class="day">Sunday</span>
							<span class="time">Closed</span>
						</div>
						<div class="hours-item">
							<span class="day">Emergency</span>
							<span class="time">24/7 Available</span>
						</div>
					</div>
				</div>

				<!-- Rating & Reviews -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Patient Rating</h3>
					<div class="rating-display">
						<div class="rating-stars">
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
						</div>
						<span class="rating-value">{{ $clinic->rating ?? 4.5 }}</span>
						<span class="rating-count">({{ rand(50, 200) }} reviews)</span>
					</div>
					<p class="text-sm text-gray-600">Based on patient feedback and satisfaction surveys.</p>
				</div>

				<!-- Quick Book -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Book Appointment</h3>
					<p class="text-sm text-gray-600 mb-4">Schedule your visit with our medical professionals.</p>
					<button class="w-full bg-primary text-white py-3 px-4 rounded-lg font-semibold hover:bg-primary-dark transition-colors" onclick="bookAppointment()">
						<i class="fas fa-calendar-plus mr-2"></i>Book Now
					</button>
				</div>

				<!-- Map -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Location</h3>
					<div class="map-container">
						<!-- Placeholder for map -->
						<div class="w-full h-full bg-gray-200 flex items-center justify-center">
							<i class="fas fa-map text-4xl text-gray-400"></i>
						</div>
					</div>
					<p class="text-sm text-gray-600 mt-2">Click to view directions</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Related Clinics -->
@if($relatedClinics->count() > 0 || $nearbyClinics->count() > 0)
<section class="related-clinics">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<h2 class="section-title">Related Clinics</h2>
		<div class="clinics-grid">
			@foreach($relatedClinics->take(4) as $relatedClinic)
			<div class="clinic-card" onclick="window.location.href='{{ route('clinics.show', $relatedClinic->id) }}'">
				<h3 class="clinic-card-title">{{ $relatedClinic->name }}</h3>
				<p class="clinic-card-specialization">{{ ucfirst($relatedClinic->specialization) }} Clinic</p>
				<div class="clinic-card-meta">
					<div class="clinic-card-meta-item">
						<i class="fas fa-map-marker-alt"></i>
						<span>{{ $relatedClinic->address }}</span>
					</div>
					<div class="clinic-card-meta-item">
						<i class="fas fa-star"></i>
						<span>{{ $relatedClinic->rating ?? 4.5 }} Rating</span>
					</div>
					<div class="clinic-card-meta-item">
						<i class="fas fa-clock"></i>
						<span>{{ $relatedClinic->status ? 'Open' : 'Closed' }}</span>
					</div>
				</div>
				<p class="clinic-card-description">{{ Str::limit($relatedClinic->description ?? 'Professional medical services in a comfortable environment.', 100) }}</p>
				<div class="clinic-card-actions">
					<button class="clinic-card-btn">View Details</button>
					<button class="clinic-card-btn clinic-card-btn-secondary">Book</button>
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
	// Clinic Interaction Functions
	function bookAppointment() {
		// Show loading state
		const btn = event.target;
		const originalText = btn.innerHTML;
		btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
		btn.disabled = true;

		// Simulate API call
		setTimeout(function() {
			btn.innerHTML = '<i class="fas fa-check mr-2"></i>Appointment Booked';
			btn.style.background = '#10b981';

			setTimeout(function() {
				btn.innerHTML = originalText;
				btn.disabled = false;
				btn.style.background = '';
			}, 3000);
		}, 1500);
	}

	function contactClinic() {
		const btn = event.target;
		const originalText = btn.innerHTML;
		btn.innerHTML = '<i class="fas fa-phone mr-2"></i>Calling...';
		btn.disabled = true;

		// Simulate call
		setTimeout(function() {
			btn.innerHTML = originalText;
			btn.disabled = false;
			alert('Calling clinic...');
		}, 1000);
	}

	// Handle related clinic clicks
	document.addEventListener('DOMContentLoaded', function() {
		document.querySelectorAll('.clinic-card').forEach(function(card) {
			card.addEventListener('click', function(e) {
				// Don't navigate if clicking on buttons
				if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
					return;
				}

				const clinicId = this.getAttribute('data-clinic-id');
				if (clinicId) {
					window.location.href = '/clinics/' + clinicId;
				}
			});
		});
	});
</script>

