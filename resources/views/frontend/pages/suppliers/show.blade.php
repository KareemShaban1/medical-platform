@extends('frontend.layouts.app')

@push('styles')
<style>
/* Supplier Details Page Styles */
.supplier-header {
	background: linear-gradient(135deg, #7c3aed, #a855f7);
	color: white;
	padding: 40px 0;
	margin-bottom: 32px;
}

.supplier-title {
	font-size: 32px;
	font-weight: 700;
	margin-bottom: 8px;
}

.supplier-category {
	font-size: 18px;
	opacity: 0.9;
	margin-bottom: 16px;
}

.supplier-meta {
	display: flex;
	flex-wrap: wrap;
	gap: 24px;
	margin-bottom: 24px;
}

.supplier-meta-item {
	display: flex;
	align-items: center;
	gap: 8px;
}

.supplier-meta-icon {
	font-size: 18px;
	opacity: 0.8;
}

.supplier-actions {
	display: flex;
	gap: 12px;
	margin-top: 24px;
}

.btn-contact {
	background: white;
	color: #7c3aed;
	border: 2px solid white;
	padding: 12px 24px;
	border-radius: 8px;
	font-weight: 600;
	text-decoration: none;
	transition: all 0.3s ease;
}

.btn-contact:hover {
	background: transparent;
	color: white;
}

.btn-quote {
	background: transparent;
	color: white;
	border: 2px solid white;
	padding: 12px 24px;
	border-radius: 8px;
	font-weight: 600;
	text-decoration: none;
	transition: all 0.3s ease;
}

.btn-quote:hover {
	background: white;
	color: #7c3aed;
}

.supplier-content {
	display: grid;
	grid-template-columns: 2fr 1fr;
	gap: 32px;
	margin-bottom: 48px;
}

.supplier-main {
	background: white;
	border-radius: 12px;
	padding: 32px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.supplier-sidebar {
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

.supplier-image {
	width: 100%;
	height: 300px;
	object-fit: cover;
	border-radius: 12px;
	margin-bottom: 24px;
}

.supplier-description {
	margin-bottom: 32px;
}

.supplier-description h3 {
	font-size: 20px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 16px;
}

.supplier-description p {
	color: #6b7280;
	line-height: 1.6;
	margin-bottom: 16px;
}

.specialties-list {
	list-style: none;
	padding: 0;
}

.specialties-list li {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 8px 0;
	color: #374151;
}

.specialty-icon {
	color: #7c3aed;
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
	color: #7c3aed;
	font-size: 18px;
	width: 20px;
}

.contact-text {
	color: #374151;
}

.certifications {
	margin-bottom: 24px;
}

.certification-item {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 12px 0;
	border-bottom: 1px solid #e5e7eb;
}

.certification-item:last-child {
	border-bottom: none;
}

.certification-icon {
	color: #10b981;
	font-size: 18px;
}

.certification-text {
	color: #374151;
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

.related-suppliers {
	margin-top: 48px;
}

.section-title {
	font-size: 24px;
	font-weight: 700;
	color: #111827;
	margin-bottom: 24px;
	text-align: center;
}

.suppliers-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
	gap: 24px;
}

.supplier-card {
	background: white;
	border-radius: 12px;
	padding: 24px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
	transition: all 0.3s ease;
	cursor: pointer;
}

.supplier-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
}

.supplier-card-title {
	font-size: 18px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 8px;
}

.supplier-card-category {
	color: #7c3aed;
	font-weight: 500;
	margin-bottom: 12px;
}

.supplier-card-meta {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	margin-bottom: 16px;
}

.supplier-card-meta-item {
	display: flex;
	align-items: center;
	gap: 4px;
	font-size: 14px;
	color: #6b7280;
}

.supplier-card-description {
	color: #6b7280;
	font-size: 14px;
	line-height: 1.5;
	margin-bottom: 16px;
}

.supplier-card-actions {
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.supplier-card-btn {
	flex: 1;
	background: #7c3aed;
	color: white;
	border: none;
	padding: 8px 16px;
	border-radius: 6px;
	font-size: 14px;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.3s ease;
}

.supplier-card-btn:hover {
	background: #a855f7;
}

.supplier-card-btn-secondary {
	background: transparent;
	color: #7c3aed;
	border: 1px solid #7c3aed;
}

.supplier-card-btn-secondary:hover {
	background: #7c3aed;
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

.status-active {
	background: #dcfce7;
	color: #166534;
}

.status-verified {
	background: #dbeafe;
	color: #1e40af;
}

.status-premium {
	background: #fef3c7;
	color: #92400e;
}

/* Experience Badge */
.experience-badge {
	background: linear-gradient(135deg, #7c3aed, #a855f7);
	color: white;
	padding: 8px 16px;
	border-radius: 20px;
	font-size: 14px;
	font-weight: 600;
	display: inline-flex;
	align-items: center;
	gap: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
	.supplier-content {
		grid-template-columns: 1fr;
		gap: 24px;
	}

	.supplier-meta {
		flex-direction: column;
		gap: 12px;
	}

	.supplier-actions {
		flex-direction: column;
	}

	.suppliers-grid {
		grid-template-columns: 1fr;
	}

	.supplier-title {
		font-size: 24px;
	}

	.supplier-category {
		font-size: 16px;
	}
}

/* Loading States */
.loading {
	opacity: 0.6;
	pointer-events: none;
}

/* Gallery Styles */
.supplier-gallery {
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
				<a href="{{ route('suppliers') }}" class="hover:text-primary">Suppliers</a>
			</li>
			<li class="breadcrumb-separator">/</li>
			<li class="breadcrumb-item active">{{ $supplier->name }}</li>
		</ol>
	</div>
</nav>

<!-- Supplier Header -->
<section class="supplier-header">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex items-center justify-between">
			<div>
				<h1 class="supplier-title">{{ $supplier->name }}</h1>
				<p class="supplier-category">{{ ucfirst($supplier->category) }} Supplier</p>

				<div class="supplier-meta">
					<div class="supplier-meta-item">
						<i class="fas fa-map-marker-alt supplier-meta-icon"></i>
						<span>{{ $supplier->address }}</span>
					</div>

					<div class="supplier-meta-item">
						<i class="fas fa-phone supplier-meta-icon"></i>
						<span>{{ $supplier->phone  }}
						</span>
					</div>

				</div>
			</div>


		</div>
	</div>
</section>

<!-- Supplier Content -->
<section class="py-8">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="supplier-content">
			<!-- Main Content -->
			<div class="supplier-main">


				<!-- Supplier Description -->
				<div class="supplier-description">
					<h3>About Our Company</h3>
					<p>{{ $supplier->description ?? 'We are a leading medical equipment and supplies supplier with years of experience in the healthcare industry. Our company is committed to providing high-quality products and exceptional service to healthcare professionals worldwide.' }}
					</p>
					<p>Our extensive product range includes medical equipment, diagnostic
						tools, surgical instruments, and pharmaceutical supplies. We
						maintain strict quality standards and are certified by major
						regulatory bodies.</p>
				</div>

				<!-- Specialties -->
				<div class="supplier-description">
					<h3>Our Specialties</h3>
					<ul class="specialties-list">
						<li><i class="fas fa-check specialty-icon"></i>Medical
							Equipment Supply</li>
						<li><i class="fas fa-check specialty-icon"></i>Diagnostic
							Tools & Instruments</li>
						<li><i class="fas fa-check specialty-icon"></i>Surgical
							Equipment</li>
						<li><i class="fas fa-check specialty-icon"></i>Pharmaceutical
							Products</li>
						<li><i class="fas fa-check specialty-icon"></i>Laboratory
							Equipment</li>
						<li><i class="fas fa-check specialty-icon"></i>Disposable
							Medical Supplies</li>
						<li><i class="fas fa-check specialty-icon"></i>Rehabilitation
							Equipment</li>
						<li><i class="fas fa-check specialty-icon"></i>Emergency
							Medical Supplies</li>
					</ul>
				</div>


			</div>

			<!-- Sidebar -->
			<div class="supplier-sidebar">
				<!-- Contact Information -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Contact Information</h3>
					<div class="contact-info">
						<div class="contact-item">
							<i class="fas fa-map-marker-alt contact-icon"></i>
							<span
								class="contact-text">{{ $supplier->address }}</span>
						</div>
						<div class="contact-item">
							<i class="fas fa-phone contact-icon"></i>
							<span
								class="contact-text">{{ $supplier->phone ?? '+1 (555) 123-4567' }}</span>
						</div>
						<div class="contact-item">
							<i class="fas fa-envelope contact-icon"></i>
							<span
								class="contact-text">{{ $supplier->email ?? 'info@supplier.com' }}</span>
						</div>
						<div class="contact-item">
							<i class="fas fa-globe contact-icon"></i>
							<span
								class="contact-text">{{ $supplier->website ?? 'www.supplier.com' }}</span>
						</div>
					</div>
				</div>



				<!-- Quick Contact -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Get in Touch</h3>
					<p class="text-sm text-gray-600 mb-4">Ready to discuss your medical
						supply needs?</p>
					<button class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-purple-700 transition-colors"
						onclick="contactSupplier()">
						<i class="fas fa-phone mr-2"></i>Contact Now
					</button>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Related Suppliers -->
@if($relatedSuppliers->count() > 0 || $similarSuppliers->count() > 0)
<section class="related-suppliers mb-4">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<h2 class="section-title">Related Suppliers</h2>
		<div class="suppliers-grid">
			@foreach($relatedSuppliers->take(4) as $relatedSupplier)
			<div class="supplier-card"
				onclick="window.location.href='{{ route('suppliers.show', $relatedSupplier->id) }}'">
				<h3 class="supplier-card-title">{{ $relatedSupplier->name }}</h3>
				<p class="supplier-card-category">{{ ucfirst($relatedSupplier->category) }}
					Supplier</p>
				<div class="supplier-card-meta">
					<div class="supplier-card-meta-item">
						<i class="fas fa-map-marker-alt"></i>
						<span
							class="line-clamp-1">{{ $relatedSupplier->address }}</span>
					</div>

				</div>
				<p class="supplier-card-description line-clamp-2">
					{{ Str::limit($relatedSupplier->description ?? 'Professional medical supplies and equipment supplier.', 100) }}
				</p>
				<div class="supplier-card-actions">
					<button class="supplier-card-btn">View Details</button>
					<button
						class="supplier-card-btn supplier-card-btn-secondary">Contact</button>
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
// Supplier Interaction Functions
function contactSupplier() {
	const btn = event.target;
	const originalText = btn.innerHTML;
	btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Connecting...';
	btn.disabled = true;

	// Simulate API call
	setTimeout(function() {
		btn.innerHTML = '<i class="fas fa-phone mr-2"></i>Contacted';
		btn.style.background = '#10b981';

		setTimeout(function() {
			btn.innerHTML = originalText;
			btn.disabled = false;
			btn.style.background = '';
		}, 3000);
	}, 1500);
}

function requestQuote() {
	const btn = event.target;
	const originalText = btn.innerHTML;
	btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
	btn.disabled = true;

	// Simulate API call
	setTimeout(function() {
		btn.innerHTML = '<i class="fas fa-check mr-2"></i>Quote Requested';
		btn.style.background = '#10b981';

		setTimeout(function() {
			btn.innerHTML = originalText;
			btn.disabled = false;
			btn.style.background = '';
		}, 3000);
	}, 1500);
}

// Handle related supplier clicks
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.supplier-card').forEach(function(card) {
		card.addEventListener('click', function(e) {
			// Don't navigate if clicking on buttons
			if (e.target.tagName ===
				'BUTTON' || e.target
				.closest('button')
			) {
				return;
			}

			const supplierId = this
				.getAttribute(
					'data-supplier-id'
				);
			if (supplierId) {
				window.location
					.href =
					'/suppliers/' +
					supplierId;
			}
		});
	});
});
</script>