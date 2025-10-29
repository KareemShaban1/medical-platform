@props([
'paginator' => null,
'containerClass' => 'mt-12',
'showInfo' => true,
'infoClass' => 'text-center mt-4 text-sm text-gray-500',
'previousText' => 'previous',
'nextText' => 'next',
'maxPages' => null,
'showFirstLast' => false,
'firstText' => 'first',
'lastText' => 'last',
])

@if($paginator && $paginator->hasPages())
<div id="paginationContainer" class="{{ $containerClass }}">
	<div class="flex flex-wrap justify-center items-center gap-2">
		{{-- First Page Link --}}
		@if($showFirstLast && !$paginator->onFirstPage())
		<a href="{{ $paginator->url(1) }}"
			class="px-4 py-2 bg-white hover:bg-blue-50 text-[var(--primary-color)] rounded-lg shadow-sm transition-all duration-200 hover:scale-105">
			<i class="fas fa-angle-double-left mr-1"></i> {{ $firstText }}
		</a>
		@endif

		{{-- Previous Page Link --}}
		@if ($paginator->onFirstPage())
		<span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">

			@if(App::getLocale() == 'ar')
			{{ __($previousText) }} <i class="fas fa-chevron-right mr-1"></i>
			@else
			<i class="fas fa-chevron-left ml-1"></i> {{ __($previousText) }}
			@endif
		</span>
		@else
		<a href="{{ $paginator->previousPageUrl() }}"
			class="px-4 py-2 bg-white hover:bg-blue-50 text-[var(--primary-color)] rounded-lg shadow-sm transition-all duration-200 hover:scale-105">

			<!-- if language is arabic -->
			@if(App::getLocale() == 'ar')
			{{ __($previousText) }} <i class="fas fa-chevron-right mr-1"></i>
			@else
			<i class="fas fa-chevron-left ml-1"></i> {{ __($previousText) }}
			@endif
		</a>
		@endif

		{{-- Pagination Elements --}}
		<div class="flex items-center gap-1">
			@php
			$currentPage = $paginator->currentPage();
			$lastPage = $paginator->lastPage();

			// Calculate page range
			if ($maxPages && $lastPage > $maxPages) {
			$startPage = max(1, $currentPage - floor($maxPages / 2));
			$endPage = min($lastPage, $startPage + $maxPages - 1);

			// Adjust start page if we're near the end
			if ($endPage - $startPage < $maxPages - 1) { $startPage=max(1, $endPage - $maxPages + 1);
				} } else { $startPage=1; $endPage=$lastPage; } @endphp @if($startPage> 1)
				<a href="{{ $paginator->url(1) }}"
					class="w-10 h-10 flex items-center justify-center bg-white hover:bg-blue-50 text-gray-600 hover:text-[var(--primary-color)] rounded-lg shadow-sm transition-all duration-200 hover:scale-105">
					1
				</a>
				@if($startPage > 2)
				<span
					class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>
				@endif
				@endif

				@for($page = $startPage; $page <= $endPage; $page++) @if ($page==$currentPage)
					<span
					class="w-10 h-10 flex items-center justify-center bg-[var(--primary-color)] text-white rounded-lg font-bold shadow-md transform scale-110">
					{{ $page }}
					</span>
					@else
					<a href="{{ $paginator->url($page) }}"
						class="w-10 h-10 flex items-center justify-center bg-white hover:bg-blue-50 text-gray-600 hover:text-[var(--primary-color)] rounded-lg shadow-sm transition-all duration-200 hover:scale-105">
						{{ $page }}
					</a>
					@endif
					@endfor

					@if($endPage < $lastPage) @if($endPage < $lastPage - 1) <span
						class="w-10 h-10 flex items-center justify-center text-gray-400">
						...</span>
						@endif
						<a href="{{ $paginator->url($lastPage) }}"
							class="w-10 h-10 flex items-center justify-center bg-white hover:bg-blue-50 text-gray-600 hover:text-[var(--primary-color)] rounded-lg shadow-sm transition-all duration-200 hover:scale-105">
							{{ __($lastPage) }}
						</a>
						@endif
		</div>

		{{-- Next Page Link --}}
		@if ($paginator->hasMorePages())
		<a href="{{ $paginator->nextPageUrl() }}"
			class="px-4 py-2 bg-white hover:bg-blue-50 text-[var(--primary-color)] rounded-lg shadow-sm transition-all duration-200 hover:scale-105">
			@if(App::getLocale() == 'ar')
			{{ __($nextText) }} <i class="fas fa-chevron-left mr-1"></i>
			@else
			<i class="fas fa-chevron-right ml-1"></i> {{ __($nextText) }}
			@endif
		</a>
		@else
		<span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
			@if(App::getLocale() == 'ar')
			{{ __($nextText) }} <i class="fas fa-chevron-left mr-1"></i>
			@else
			<i class="fas fa-chevron-right ml-1"></i> {{ __($nextText) }}
			@endif
		</span>
		@endif

		{{-- Last Page Link --}}
		@if($showFirstLast && $paginator->hasMorePages())
		<a href="{{ $paginator->url($paginator->lastPage()) }}"
			class="px-4 py-2 bg-white hover:bg-blue-50 text-[var(--primary-color)] rounded-lg shadow-sm transition-all duration-200 hover:scale-105">
			{{ __($lastText) }} <i class="fas fa-angle-double-right ml-1"></i>
		</a>
		@endif
	</div>

	@if($showInfo)
	<div class="{{ $infoClass }}">
		{{ __('showing') }} {{ $paginator->firstItem() }} {{ __('to') }} {{ $paginator->lastItem() }}
		{{ __('of') }} {{ $paginator->total() }} {{ __('results') }}
	</div>
	@endif
</div>
@endif