@extends('frontend.layouts.app')

@push('styles')
<style>
/* Blog Details Page Styles */
.blog-header {
	background: linear-gradient(135deg, #1e40af, #3b82f6);
	color: white;
	padding: 40px 0;
	margin-bottom: 32px;
}

.blog-title {
	font-size: 36px;
	font-weight: 700;
	margin-bottom: 16px;
	line-height: 1.2;
}

.blog-meta {
	display: flex;
	flex-wrap: wrap;
	gap: 24px;
	margin-bottom: 24px;
}

.blog-meta-item {
	display: flex;
	align-items: center;
	gap: 8px;
}

.blog-meta-icon {
	font-size: 16px;
	opacity: 0.8;
}

.blog-content {
	display: grid;
	grid-template-columns: 2fr 1fr;
	gap: 32px;
	margin-bottom: 48px;
}

.blog-main {
	background: white;
	border-radius: 12px;
	padding: 32px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.blog-sidebar {
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

.blog-image {
	width: 100%;
	height: 400px;
	object-fit: cover;
	border-radius: 12px;
	margin-bottom: 32px;
}

.blog-content-text {
	color: #374151;
	line-height: 1.8;
	font-size: 16px;
}

.blog-content-text h2 {
	font-size: 24px;
	font-weight: 600;
	color: #111827;
	margin: 32px 0 16px 0;
}

.blog-content-text h3 {
	font-size: 20px;
	font-weight: 600;
	color: #111827;
	margin: 24px 0 12px 0;
}

.blog-content-text p {
	margin-bottom: 16px;
}

.blog-content-text ul,
.blog-content-text ol {
	margin: 16px 0;
	padding-left: 24px;
}

.blog-content-text li {
	margin-bottom: 8px;
}

.blog-content-text blockquote {
	border-left: 4px solid #3b82f6;
	padding: 16px 24px;
	margin: 24px 0;
	background: #f8fafc;
	border-radius: 0 8px 8px 0;
	font-style: italic;
	color: #4b5563;
}

.blog-tags {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
	margin: 24px 0;
}

.tag {
	background: #e0f2fe;
	color: #0369a1;
	padding: 4px 12px;
	border-radius: 20px;
	font-size: 14px;
	font-weight: 500;
}

.blog-actions {
	display: flex;
	gap: 12px;
	margin: 32px 0;
	padding: 24px;
	background: #f8fafc;
	border-radius: 12px;
}

.blog-action-btn {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 12px 20px;
	border-radius: 8px;
	text-decoration: none;
	font-weight: 500;
	transition: all 0.3s ease;
}

.btn-like {
	background: #fef2f2;
	color: #dc2626;
	border: 1px solid #fecaca;
}

.btn-like:hover {
	background: #dc2626;
	color: white;
}

.btn-share {
	background: #f0f9ff;
	color: #0369a1;
	border: 1px solid #bae6fd;
}

.btn-share:hover {
	background: #0369a1;
	color: white;
}

.btn-bookmark {
	background: #f0fdf4;
	color: #16a34a;
	border: 1px solid #bbf7d0;
}

.btn-bookmark:hover {
	background: #16a34a;
	color: white;
}

.related-posts {
	margin-top: 48px;
}

.section-title {
	font-size: 24px;
	font-weight: 700;
	color: #111827;
	margin-bottom: 24px;
	text-align: center;
}

.posts-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
	gap: 24px;
	margin-bottom: 20px;
}

.post-card {
	background: white;
	border-radius: 12px;
	overflow: hidden;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
	transition: all 0.3s ease;
	cursor: pointer;
}

.post-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
}

.post-card-image {
	width: 100%;
	height: 200px;
	object-fit: cover;
}

.post-card-content {
	padding: 20px;
}

.post-card-title {
	font-size: 18px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 8px;
	line-height: 1.4;
}

.post-card-meta {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 12px;
	font-size: 14px;
	color: #6b7280;
}

.post-card-description {
	color: #6b7280;
	font-size: 14px;
	line-height: 1.5;
	margin-bottom: 16px;
}

.post-card-category {
	background: #e0f2fe;
	color: #0369a1;
	padding: 4px 8px;
	border-radius: 12px;
	font-size: 12px;
	font-weight: 500;
}

.categories-list {
	list-style: none;
	padding: 0;
}

.categories-list li {
	margin-bottom: 8px;
}

.categories-list a {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 8px 12px;
	border-radius: 8px;
	text-decoration: none;
	color: #374151;
	transition: all 0.3s ease;
}

.categories-list a:hover {
	background: #f3f4f6;
	color: #1e40af;
}

.category-count {
	background: #e5e7eb;
	color: #6b7280;
	padding: 2px 8px;
	border-radius: 12px;
	font-size: 12px;
}

.recent-posts-list {
	list-style: none;
	padding: 0;
}

.recent-posts-list li {
	display: flex;
	gap: 12px;
	margin-bottom: 16px;
	padding-bottom: 16px;
	border-bottom: 1px solid #e5e7eb;
}

.recent-posts-list li:last-child {
	border-bottom: none;
	margin-bottom: 0;
	padding-bottom: 0;
}

.recent-post-image {
	width: 60px;
	height: 60px;
	object-fit: cover;
	border-radius: 8px;
}

.recent-post-content h4 {
	font-size: 14px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 4px;
	line-height: 1.3;
}

.recent-post-content p {
	font-size: 12px;
	color: #6b7280;
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
	.blog-content {
		grid-template-columns: 1fr;
		gap: 24px;
	}

	.blog-meta {
		flex-direction: column;
		gap: 12px;
	}

	.blog-actions {
		flex-direction: column;
	}

	.posts-grid {
		grid-template-columns: 1fr;
	}

	.blog-title {
		font-size: 28px;
	}

	.blog-image {
		height: 250px;
	}
}

/* Loading States */
.loading {
	opacity: 0.6;
	pointer-events: none;
}

/* Author Info */
.author-info {
	display: flex;
	align-items: center;
	gap: 16px;
	margin-bottom: 24px;
	padding: 20px;
	background: #f8fafc;
	border-radius: 12px;
}

.author-avatar {
	width: 60px;
	height: 60px;
	border-radius: 50%;
	object-fit: cover;
}

.author-details h4 {
	font-size: 18px;
	font-weight: 600;
	color: #111827;
	margin-bottom: 4px;
}

.author-details p {
	color: #6b7280;
	font-size: 14px;
}

/* Social Share */
.social-share {
	display: flex;
	gap: 8px;
	margin-top: 16px;
}

.social-btn {
	width: 40px;
	height: 40px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	text-decoration: none;
	color: white;
	font-size: 16px;
	transition: all 0.3s ease;
}

.social-btn:hover {
	transform: scale(1.1);
}

.social-facebook {
	background: #1877f2;
}

.social-twitter {
	background: #1da1f2;
}

.social-linkedin {
	background: #0077b5;
}

.social-whatsapp {
	background: #25d366;
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
				<a href="{{ route('blogs') }}" class="hover:text-primary">Blog</a>
			</li>
			<li class="breadcrumb-separator">/</li>
			<li class="breadcrumb-item active">{{ $blogPost->title }}</li>
		</ol>
	</div>
</nav>

<!-- Blog Header -->
<section class="blog-header">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="text-center">
			<h1 class="blog-title">{{ $blogPost->title }}</h1>

			<div class="blog-meta">
				<div class="blog-meta-item">
					<i class="fas fa-calendar blog-meta-icon"></i>
					<span>{{ $blogPost->created_at->format('M d, Y') }}</span>
				</div>
				<div class="blog-meta-item">
					<i class="fas fa-user blog-meta-icon"></i>
					<span>Medical Team</span>
				</div>
				<div class="blog-meta-item">
					<i class="fas fa-folder blog-meta-icon"></i>
					<span>{{ app()->getLocale() == 'ar' ? $blogPost->blogCategory->name_ar : $blogPost->blogCategory->name_en }}</span>
				</div>
				<div class="blog-meta-item">
					<i class="fas fa-clock blog-meta-icon"></i>
					<span>5 min read</span>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Blog Content -->
<section class="py-8">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="blog-content">
			<!-- Main Content -->
			<div class="blog-main">
				<!-- Blog Image -->
				<img src="{{ $blogPost->main_image }}" alt="{{ $blogPost->title }}"
					class="blog-image">



				<!-- Blog Content -->
				<div class="blog-content-text">
					{!! $blogPost->content !!}
				</div>



				<!-- Actions -->
				<div class="blog-actions">
					<a href="#" class="blog-action-btn btn-like" onclick="likePost()">
						<i class="fas fa-heart"></i>
						<span>Like</span>
					</a>
					<a href="#" class="blog-action-btn btn-share" onclick="sharePost()">
						<i class="fas fa-share"></i>
						<span>Share</span>
					</a>
					<a href="#" class="blog-action-btn btn-bookmark"
						onclick="bookmarkPost()">
						<i class="fas fa-bookmark"></i>
						<span>Save</span>
					</a>
				</div>
			</div>

			<!-- Sidebar -->
			<div class="blog-sidebar">
				<!-- Categories -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Categories</h3>
					<ul class="categories-list">
						@foreach($categories as $category)
						<li>
							<a
								href="{{ route('blogs') }}?category={{ $category->id }}">
								<span>{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}</span>
								<span
									class="category-count">{{ rand(5, 25) }}</span>
							</a>
						</li>
						@endforeach
					</ul>
				</div>

				<!-- Recent Posts -->
				<div class="sidebar-card">
					<h3 class="sidebar-title">Recent Posts</h3>
					<ul class="recent-posts-list">
						@foreach($recentPosts as $recentPost)
						<li
							onclick="window.location.href='{{ route('blogs.show', $recentPost->id) }}'">
							<img src="{{ $recentPost->main_image }}"
								alt="{{ $recentPost->title }}"
								class="recent-post-image">
							<div class="recent-post-content">
								<h4>{{ $recentPost->title }}</h4>
								<p>{{ $recentPost->created_at->format('M d, Y') }}
								</p>
							</div>
						</li>
						@endforeach
					</ul>
				</div>


			</div>
		</div>
	</div>
</section>

<!-- Related Posts -->
@if($relatedPosts->count() > 0)
<section class="related-posts">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<h2 class="section-title">Related Articles</h2>
		<div class="posts-grid">
			@foreach($relatedPosts as $relatedPost)
			<div class="post-card"
				onclick="window.location.href='{{ route('blogs.show', $relatedPost->id) }}'">
				<img src="{{ $relatedPost->main_image }}" alt="{{ $relatedPost->title }}"
					class="post-card-image">
				<div class="post-card-content">
					<span
						class="post-card-category">{{ app()->getLocale() == 'ar' ? $relatedPost->blogCategory->name_ar : $relatedPost->blogCategory->name_en }}</span>
					<h3 class="post-card-title">{{ $relatedPost->title }}</h3>
					<div class="post-card-meta">
						<span><i class="fas fa-calendar"></i>
							{{ $relatedPost->created_at->format('M d, Y') }}</span>
						<span><i class="fas fa-user"></i> Medical Team</span>
					</div>
					<p class="post-card-description">
						{{ Str::limit(strip_tags($relatedPost->content), 120) }}</p>
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
// Blog Interaction Functions
function likePost() {
	const btn = event.target.closest('.btn-like');
	const icon = btn.querySelector('i');
	const text = btn.querySelector('span');

	if (icon.classList.contains('fa-heart')) {
		icon.classList.remove('fa-heart');
		icon.classList.add('fa-heart-solid');
		text.textContent = 'Liked';
		btn.style.background = '#dc2626';
		btn.style.color = 'white';
	} else {
		icon.classList.remove('fa-heart-solid');
		icon.classList.add('fa-heart');
		text.textContent = 'Like';
		btn.style.background = '#fef2f2';
		btn.style.color = '#dc2626';
	}
}

function sharePost() {
	if (navigator.share) {
		navigator.share({
			title: '{{ $blogPost->title }}',
			text: 'Check out this interesting article',
			url: window.location.href
		});
	} else {
		// Fallback for browsers that don't support Web Share API
		navigator.clipboard.writeText(window.location.href).then(function() {
			alert('Link copied to clipboard!');
		});
	}
}

function bookmarkPost() {
	const btn = event.target.closest('.btn-bookmark');
	const icon = btn.querySelector('i');
	const text = btn.querySelector('span');

	if (icon.classList.contains('fa-bookmark')) {
		icon.classList.remove('fa-bookmark');
		icon.classList.add('fa-bookmark-solid');
		text.textContent = 'Saved';
		btn.style.background = '#16a34a';
		btn.style.color = 'white';
	} else {
		icon.classList.remove('fa-bookmark-solid');
		icon.classList.add('fa-bookmark');
		text.textContent = 'Save';
		btn.style.background = '#f0fdf4';
		btn.style.color = '#16a34a';
	}
}

// Handle related post clicks
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.post-card').forEach(function(card) {
		card.addEventListener('click', function() {
			const postId = this
				.getAttribute(
					'data-post-id'
				);
			if (postId) {
				window.location
					.href =
					'/blog/' +
					postId;
			}
		});
	});
});
</script>
