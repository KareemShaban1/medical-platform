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
	background: var(--primary-color);
	color: white;
	border: none;
	padding: 10px;
	border-radius: 6px;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.3s ease;
}

.product-card-btn:hover {
	background: var(--primary-light);
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
