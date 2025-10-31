@extends('frontend.layouts.app')

@push('styles')
@include('frontend.pages.products.styles.show_styles')
@endpush

@section('content')

<!-- Breadcrumb -->
<nav class="breadcrumb">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<ol class="breadcrumb-list">
			<li class="breadcrumb-item">
				<a href="{{ route('home') }}" class="hover:text-primary">{{ __('home') }}</a>
			</li>
			<li class="breadcrumb-separator">/</li>
			<li class="breadcrumb-item">
				<a href="{{ route('products') }}"
					class="hover:text-primary">{{ __('products') }}</a>
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



				<div class="product-description">
					{{ $product->description }}
				</div>


				<div class="supplier-info">
					<h3 class="supplier-title">{{ __('supplier') }}:</h3>
					<p class="supplier-name">
						{{ $product->supplier->name ?? 'Medical Supplier' }}</p>
				</div>


				<!-- <div class="stock-info">
					@if($product->stock > 10)
					<span class="stock-badge stock-in">
						<i class="fas fa-check-circle mr-1"></i>{{ __('in_stock') }}
						({{ $product->stock }} {{ __('available') }})
					</span>
					@elseif($product->stock > 0)
					<span class="stock-badge stock-low">
						<i
							class="fas fa-exclamation-triangle mr-1"></i>{{ __('low_stock') }}
						({{ $product->stock }} {{ __('left') }})
					</span>
					@else
					<span class="stock-badge stock-out">
						<i
							class="fas fa-times-circle mr-1"></i>{{ __('out_of_stock') }}
					</span>
					@endif
				</div> -->

				<div class="quantity-selector">
					<label for="quantity"
						class="text-sm font-medium text-gray-700">{{ __('quantity') }}:</label>
					<button class="quantity-btn" onclick="decreaseQuantity()">-</button>
					<input type="number" id="quantity" class="quantity-input" value="1"
						min="1" max="{{ $product->stock }}">
					<button class="quantity-btn" onclick="increaseQuantity()">+</button>
				</div>

				<div class="action-buttons">
					<button data-add-to-cart data-product-id="{{ $product->id }}"
						data-supplier-id="{{ $product->supplier_id }}"
						data-quantity="1"
						class="bg-primary-gradient text-white px-4 py-2 rounded-full hover:bg-blue-700 transition">
						{{ __('add to cart') }}
						<i class="fas fa-cart-plus"></i>
					</button>
				</div>

			</div>
		</div>


	</div>
</section>

<!-- Related Products -->
@if($relatedProducts->count() > 0)
<section class="related-products">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<h2 class="section-title">{{ __('related products') }}</h2>
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
					<button class="product-card-btn">{{ __('view details') }}</button>
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
