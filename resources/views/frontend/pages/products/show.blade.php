@extends('frontend.layouts.app')

@push('styles')
<style>
/* Product Details Page Styles */
.product-gallery {
	position: relative;
	overflow: hidden;
	border-radius: 12px;
	background: #f8f9fa;
}

.main-image {
	width: 100%;
	height: 500px;
	object-fit: cover;
	border-radius: 12px;
	transition: transform 0.3s ease;
}

.main-image:hover {
	transform: scale(1.05);
}

.thumbnail-container {
	display: flex;
	gap: 8px;
	margin-top: 12px;
	overflow-x: auto;
	padding: 4px 0;
}

.thumbnail {
	width: 80px;
	height: 80px;
	object-fit: cover;
	border-radius: 8px;
	cursor: pointer;
	transition: all 0.3s ease;
	border: 2px solid transparent;
}

.thumbnail:hover,
.thumbnail.active {
	border-color: #079184;
	transform: scale(1.05);
}

.product-info {
	background: white;
	border-radius: 12px;
	padding: 24px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.product-title {
	font-size: 28px;
	font-weight: 700;
	color: #111827;
	margin-bottom: 12px;
	line-height: 1.3;
}

.product-price {
	display: flex;
	align-items: center;
	gap: 16px;
	margin-bottom: 20px;
}

.current-price {
	font-size: 32px;
	font-weight: 700;
	color: #059669;
}

.original-price {
	font-size: 20px;
	color: #6b7280;
	text-decoration: line-through;
}

.discount-badge {
	background: linear-gradient(135deg, #ef4444, #dc2626);
	color: white;
	padding: 4px 12px;
	border-radius: 20px;
	font-size: 14px;
	font-weight: 600;
}

.product-meta {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 16px;
	margin-bottom: 24px;
}

.meta-item {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 12px;
	background: #f9fafb;
	border-radius: 8px;
}

.meta-icon {
	color: #079184;
	font-size: 18px;
}

.meta-text {
	color: #374151;
	font-weight: 500;
}

.product-description {
	color: #6b7280;
	line-height: 1.6;
	margin-bottom: 24px;
	font-size: 16px;
}

.product-features {
	margin-bottom: 24px;
}

.features-title {
	font-size: 18px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 12px;
}

.features-list {
	list-style: none;
	padding: 0;
}

.features-list li {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 8px 0;
	color: #374151;
}

.feature-icon {
	color: #10b981;
	font-size: 16px;
}

.quantity-selector {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 24px;
}

.quantity-input {
	width: 80px;
	text-align: center;
	border: 2px solid #e5e7eb;
	border-radius: 8px;
	padding: 8px;
	font-weight: 600;
}

.quantity-btn {
	width: 36px;
	height: 36px;
	border: 2px solid #e5e7eb;
	background: white;
	border-radius: 8px;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all 0.3s ease;
}

.quantity-btn:hover {
	border-color: #079184;
	color: #079184;
}

.action-buttons {
	display: flex;
	gap: 12px;
	margin-bottom: 24px;
}

.btn-add-cart {
	background: linear-gradient(135deg, #079184, #0aa896);
	color: white;
	border: none;
	padding: 14px 28px;
	border-radius: 8px;
	font-weight: 600;
	font-size: 16px;
	cursor: pointer;
	transition: all 0.3s ease;
	flex: 1;
}

.btn-add-cart:hover {
	transform: translateY(-2px);
	box-shadow: 0 8px 25px rgba(7, 145, 132, 0.3);
}

.btn-wishlist {
	background: white;
	border: 2px solid #e5e7eb;
	color: #6b7280;
	padding: 14px;
	border-radius: 8px;
	cursor: pointer;
	transition: all 0.3s ease;
}

.btn-wishlist:hover {
	border-color: #ef4444;
	color: #ef4444;
}

.stock-info {
	display: flex;
	align-items: center;
	gap: 8px;
	margin-bottom: 16px;
}

.stock-badge {
	padding: 6px 12px;
	border-radius: 20px;
	font-size: 14px;
	font-weight: 600;
}

.stock-in {
	background: #dcfce7;
	color: #166534;
}

.stock-low {
	background: #fef3c7;
	color: #92400e;
}

.stock-out {
	background: #fee2e2;
	color: #991b1b;
}

.supplier-info {
	background: #f8fafc;
	border-radius: 8px;
	padding: 16px;
	margin-bottom: 24px;
}

.supplier-title {
	font-size: 16px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 8px;
}

.supplier-name {
	color: #079184;
	font-weight: 500;
}

.product-tabs {
	background: white;
	border-radius: 12px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
	margin-top: 32px;
}

.tab-nav {
	display: flex;
	border-bottom: 1px solid #e5e7eb;
}

.tab-btn {
	flex: 1;
	padding: 16px 24px;
	background: none;
	border: none;
	color: #6b7280;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.3s ease;
	border-bottom: 3px solid transparent;
}

.tab-btn.active {
	color: #079184;
	border-bottom-color: #079184;
	background: #f0fdfa;
}

.tab-content {
	padding: 24px;
}

.tab-panel {
	display: none;
}

.tab-panel.active {
	display: block;
}

.specifications-table {
	width: 100%;
	border-collapse: collapse;
}

.specifications-table th,
.specifications-table td {
	padding: 12px 16px;
	text-align: left;
	border-bottom: 1px solid #e5e7eb;
}

.specifications-table th {
	background: #f9fafb;
	font-weight: 600;
	color: #374151;
	width: 30%;
}

.specifications-table td {
	color: #6b7280;
}

.related-products {
	margin-top: 48px;
}

.section-title {
	font-size: 24px;
	font-weight: 700;
	color: #111827;
	margin-bottom: 24px;
	text-align: center;
}

.products-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
	gap: 24px;
}

.product-card {
	background: white;
	border-radius: 12px;
	overflow: hidden;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
	transition: all 0.3s ease;
	cursor: pointer;
}

.product-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
}

.product-card-image {
	width: 100%;
	height: 200px;
	object-fit: cover;
}

.product-card-content {
	padding: 16px;
}

.product-card-title {
	font-size: 16px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 8px;
	line-height: 1.4;
}

.product-card-price {
	display: flex;
	align-items: center;
	gap: 8px;
	margin-bottom: 12px;
}

.product-card-current {
	font-size: 18px;
	font-weight: 700;
	color: #059669;
}

.product-card-original {
	font-size: 14px;
	color: #6b7280;
	text-decoration: line-through;
}

.product-card-btn {
	width: 100%;
	background: #079184;
	color: white;
	border: none;
	padding: 10px;
	border-radius: 6px;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.3s ease;
}

.product-card-btn:hover {
	background: #0aa896;
	transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
	.product-title {
		font-size: 24px;
	}

	.current-price {
		font-size: 28px;
	}

	.product-meta {
		grid-template-columns: 1fr;
	}

	.action-buttons {
		flex-direction: column;
	}

	.tab-nav {
		flex-direction: column;
	}

	.tab-btn {
		text-align: left;
		border-bottom: 1px solid #e5e7eb;
		border-right: none;
	}

	.tab-btn.active {
		border-bottom-color: #e5e7eb;
		border-right: 3px solid #079184;
	}

	.products-grid {
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 16px;
	}
}

/* Loading States */
.loading {
	opacity: 0.6;
	pointer-events: none;
}

/* Image Zoom Effect */
.image-zoom-container {
	position: relative;
	overflow: hidden;
}

.zoom-lens {
	position: absolute;
	border: 2px solid #079184;
	background: rgba(7, 145, 132, 0.1);
	pointer-events: none;
	display: none;
}

.zoom-result {
	position: absolute;
	top: 0;
	right: -100%;
	width: 100%;
	height: 100%;
	background: white;
	border: 1px solid #e5e7eb;
	border-radius: 8px;
	display: none;
	z-index: 10;
}

/* Breadcrumb Styles */
.breadcrumb {
	background: #f8fafc;
	padding: 12px 0;
	margin-bottom: 24px;
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
				<a href="{{ route('products') }}" class="hover:text-primary">Products</a>
			</li>
			<li class="breadcrumb-separator">/</li>
			@if($product->categories->count() > 0)
			<li class="breadcrumb-item">
				<a href="{{ route('products.category', $product->categories->first()->id) }}"
					class="hover:text-primary">
					{{ app()->getLocale() == 'ar' ? $product->categories->first()->name_ar : $product->categories->first()->name_en }}
				</a>
			</li>
			<li class="breadcrumb-separator">/</li>
			@endif
			<li class="breadcrumb-item active">{{ $product->name }}</li>
		</ol>
	</div>
</nav>

<!-- Product Details Section -->
<section class="py-8">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
			<!-- Product Gallery -->
			<div class="product-gallery">
				<div class="image-zoom-container">
					<img id="mainImage" src="{{ $product->first_image }}"
						alt="{{ $product->name }}" class="main-image">
					<div class="zoom-lens" id="zoomLens"></div>
					<div class="zoom-result" id="zoomResult"></div>
				</div>

				@if(count($product->images) > 1)
				<div class="thumbnail-container">
					@foreach($product->images as $index => $image)
					<img src="{{ $image }}" alt="{{ $product->name }}"
						class="thumbnail {{ $index === 0 ? 'active' : '' }}"
						onclick="changeMainImage('{{ $image }}', this)">
					@endforeach
				</div>
				@endif
			</div>

			<!-- Product Information -->
			<div class="product-info">
				<h1 class="product-title">{{ $product->name }}</h1>

				<div class="product-price">
					<span class="current-price">${{ $product->price_after }}</span>
					@if($product->price_before > $product->price_after)
					<span class="original-price">${{ $product->price_before }}</span>
					@php
					$discountPercentage = round((($product->price_before -
					$product->price_after) / $product->price_before) * 100);
					@endphp
					<span class="discount-badge">-{{ $discountPercentage }}%</span>
					@endif
				</div>

				

				<!-- <div class="product-meta">
					<div class="meta-item">
						<i class="fas fa-barcode meta-icon"></i>
						<span class="meta-text">SKU: {{ $product->sku }}</span>
					</div>
					<div class="meta-item">
						<i class="fas fa-truck meta-icon"></i>
						<span class="meta-text">Free Shipping</span>
					</div>
					<div class="meta-item">
						<i class="fas fa-shield-alt meta-icon"></i>
						<span class="meta-text">Quality Assured</span>
					</div>
					<div class="meta-item">
						<i class="fas fa-headset meta-icon"></i>
						<span class="meta-text">24/7 Support</span>
					</div>
				</div> -->

				<div class="product-description">
					{{ $product->description }}
				</div>

				
				<div class="supplier-info">
					<h3 class="supplier-title">Supplied by:</h3>
					<p class="supplier-name">
						{{ $product->supplier->name ?? 'Medical Supplier' }}</p>
				</div>

				<!-- <div class="product-features">
					<h3 class="features-title">Key Features</h3>
					<ul class="features-list">
						<li><i class="fas fa-check feature-icon"></i> High-quality
							medical grade materials</li>
						<li><i class="fas fa-check feature-icon"></i> Certified and
							approved by medical authorities</li>
						<li><i class="fas fa-check feature-icon"></i> Easy to use
							and maintain</li>
						<li><i class="fas fa-check feature-icon"></i> Long-lasting
							durability</li>
						<li><i class="fas fa-check feature-icon"></i> Professional
							medical standards</li>
					</ul>
				</div> -->

				<div class="stock-info">
					@if($product->stock > 10)
					<span class="stock-badge stock-in">
						<i class="fas fa-check-circle mr-1"></i>In Stock
						({{ $product->stock }} available)
					</span>
					@elseif($product->stock > 0)
					<span class="stock-badge stock-low">
						<i class="fas fa-exclamation-triangle mr-1"></i>Low Stock
						({{ $product->stock }} left)
					</span>
					@else
					<span class="stock-badge stock-out">
						<i class="fas fa-times-circle mr-1"></i>Out of Stock
					</span>
					@endif
				</div>

				<div class="quantity-selector">
					<label for="quantity"
						class="text-sm font-medium text-gray-700">Quantity:</label>
					<button class="quantity-btn" onclick="decreaseQuantity()">-</button>
					<input type="number" id="quantity" class="quantity-input" value="1"
						min="1" max="{{ $product->stock }}">
					<button class="quantity-btn" onclick="increaseQuantity()">+</button>
				</div>

				<div class="action-buttons">
					<button class="btn-add-cart" onclick="addToCart()">
						<i class="fas fa-cart-plus mr-2"></i>Add to Cart
					</button>
					<!-- <button class="btn-wishlist" onclick="toggleWishlist()">
						<i class="fas fa-heart"></i>
					</button> -->
				</div>

			</div>
		</div>

		<!-- Product Tabs -->
		<div class="product-tabs">
			<div class="tab-nav">
				<button class="tab-btn active"
					onclick="switchTab('description')">Description</button>
				
				<button class="tab-btn" onclick="switchTab('shipping')">Shipping &
					Returns</button>
			</div>

			<div class="tab-content">
				<div id="description" class="tab-panel active">
					<h3 class="text-lg font-semibold mb-4">Product Description</h3>
					<div class="prose max-w-none">
						{{ $product->description }}
					</div>
					<div class="mt-6">
						<h4 class="font-semibold mb-3">Additional Information</h4>
						<p class="text-gray-600">This product meets all medical
							standards and is suitable for professional use in
							healthcare facilities. It has been tested and
							approved by medical authorities.</p>
					</div>
				</div>



				<div id="shipping" class="tab-panel">
					<h3 class="text-lg font-semibold mb-4">Shipping & Returns</h3>
					<div class="space-y-4">
						<div>
							<h4 class="font-semibold mb-2">Shipping
								Information</h4>
							<ul
								class="list-disc list-inside text-gray-600 space-y-1">
								<li>Free shipping on orders over $100
								</li>
								<li>Standard delivery: 3-5 business days
								</li>
								<li>Express delivery: 1-2 business days
								</li>
								<li>International shipping available
								</li>
							</ul>
						</div>
						<div>
							<h4 class="font-semibold mb-2">Return Policy</h4>
							<ul
								class="list-disc list-inside text-gray-600 space-y-1">
								<li>30-day return policy</li>
								<li>Items must be in original condition
								</li>
								<li>Free return shipping</li>
								<li>Full refund within 5-7 business days
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Related Products -->
@if($relatedProducts->count() > 0)
<section class="related-products">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<h2 class="section-title">Related Products</h2>
		<div class="products-grid">
			@foreach($relatedProducts as $relatedProduct)
			<div class="product-card" data-product-id="{{ $relatedProduct->id }}">
				<img src="{{ $relatedProduct->first_image }}" alt="{{ $relatedProduct->name }}"
					class="product-card-image">
				<div class="product-card-content">
					<h3 class="product-card-title">{{ $relatedProduct->name }}</h3>
					<div class="product-card-price">
						<span
							class="product-card-current">${{ $relatedProduct->price_after }}</span>
						@if($relatedProduct->price_before >
						$relatedProduct->price_after)
						<span
							class="product-card-original">${{ $relatedProduct->price_before }}</span>
						@endif
					</div>
					<button class="product-card-btn">View Details</button>
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
// Product Gallery Functions
function changeMainImage(imageSrc, thumbnail) {
	document.getElementById('mainImage').src = imageSrc;

	// Update active thumbnail
	document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
	thumbnail.classList.add('active');
}

// Quantity Functions
function increaseQuantity() {
	const quantityInput = document.getElementById('quantity');
	const maxStock = parseInt('{{ $product->stock }}');
	const currentValue = parseInt(quantityInput.value);

	if (currentValue < maxStock) {
		quantityInput.value = currentValue + 1;
	}
}

function decreaseQuantity() {
	const quantityInput = document.getElementById('quantity');
	const currentValue = parseInt(quantityInput.value);

	if (currentValue > 1) {
		quantityInput.value = currentValue - 1;
	}
}

// Tab Functions
function switchTab(tabName) {
	// Hide all tab panels
	document.querySelectorAll('.tab-panel').forEach(panel => {
		panel.classList.remove('active');
	});

	// Remove active class from all tab buttons
	document.querySelectorAll('.tab-btn').forEach(btn => {
		btn.classList.remove('active');
	});

	// Show selected tab panel
	document.getElementById(tabName).classList.add('active');

	// Add active class to clicked button
	event.target.classList.add('active');
}

// Cart Functions
function addToCart() {
	const quantity = document.getElementById('quantity').value;
	const productId = parseInt('{{ $product->id }}');

	// Show loading state
	const btn = event.target;
	const originalText = btn.innerHTML;
	btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
	btn.disabled = true;

	// Simulate API call
	setTimeout(function() {
		btn.innerHTML = '<i class="fas fa-check mr-2"></i>Added to Cart';
		btn.style.background = '#10b981';

		setTimeout(function() {
			btn.innerHTML = originalText;
			btn.disabled = false;
			btn.style.background = '';
		}, 2000);
	}, 1000);
}

// Wishlist Functions
function toggleWishlist() {
	const btn = event.target;
	const icon = btn.querySelector('i');

	if (icon.classList.contains('fas')) {
		icon.classList.remove('fas');
		icon.classList.add('far');
		btn.style.color = '#6b7280';
		btn.style.borderColor = '#e5e7eb';
	} else {
		icon.classList.remove('far');
		icon.classList.add('fas');
		btn.style.color = '#ef4444';
		btn.style.borderColor = '#ef4444';
	}
}

// Image Zoom Effect
document.addEventListener('DOMContentLoaded', function() {
	const mainImage = document.getElementById('mainImage');
	const zoomLens = document.getElementById('zoomLens');
	const zoomResult = document.getElementById('zoomResult');

	if (mainImage && zoomLens && zoomResult) {
		mainImage.addEventListener('mousemove', function(e) {
			const rect = mainImage.getBoundingClientRect();
			const x = e.clientX - rect.left;
			const y = e.clientY - rect.top;

			zoomLens.style.left = (x - 25) + 'px';
			zoomLens.style.top = (y - 25) + 'px';
			zoomLens.style.display = 'block';
			zoomResult.style.display = 'block';
		});

		mainImage.addEventListener('mouseleave', function() {
			zoomLens.style.display = 'none';
			zoomResult.style.display = 'none';
		});
	}

	// Handle related product clicks
	document.querySelectorAll('.product-card').forEach(function(card) {
		card.addEventListener('click', function() {
			const productId = this
				.getAttribute(
					'data-product-id'
				);
			window.location.href =
				'/products/' +
				productId;
		});
	});
});
</script>
@endpush