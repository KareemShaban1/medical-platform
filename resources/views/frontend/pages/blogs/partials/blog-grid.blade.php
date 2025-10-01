@forelse($blogPosts as $post)
<div class="blog-card card overflow-hidden" data-category="{{ $post->blogCategory->id ?? '' }}"
	data-author="{{ $post->author ?? 'dr-smith' }}" data-date="{{ $post->created_at->diffInDays(now()) }}"
	data-name="{{ $post->title }}">
	<div class="h-48 bg-gray-200 flex items-center justify-center">
		@if($post->main_image)
		<img src="{{ $post->main_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
		@else
		<i class="fas fa-newspaper text-4xl text-gray-400"></i>
		@endif
	</div>
	<div class="p-4">
		<div class="flex items-center text-sm text-gray-500 mb-2">
			@if($post->blogCategory)
			<span class="badge badge-primary mr-2">
				{{ $post->blogCategory->name }}
			</span>
			@endif
			<span>{{ $post->created_at->diffForHumans() }}</span>
		</div>
		<h3 class="font-semibold text-lg mb-2 line-clamp-1">
			<a href="{{ route('blogs.show', $post->id) }}">{{ $post->title }}</a>
		</h3>
		<p class="text-gray-600 text-sm mb-3 line-clamp-2">
			{{ Str::limit(strip_tags($post->content_en ?? $post->content_ar), 100) }}
		</p>
		<div class="flex items-center justify-between">
			<!-- <div class="flex items-center text-sm text-gray-500">
				<i class="fas fa-user mr-1"></i>
				<span>{{ ucfirst(str_replace('-', ' ', $post->author ?? 'Dr. Smith')) }}</span>
			</div> -->
			<div class="flex items-center text-sm text-gray-500">
				<i class="fas fa-eye mr-1"></i>
				<span>{{ $post->views ?? rand(100, 1000) }}</span>
			</div>
		</div>
	</div>
</div>
@empty
<div class="col-span-full text-center py-12">
	<div class="text-gray-500">
		<i class="fas fa-search text-4xl mb-4"></i>
		<h3 class="text-lg font-semibold mb-2">No blog posts found</h3>
		<p>Try adjusting your search criteria or filters.</p>
	</div>
</div>
@endforelse
