@extends('frontend.layouts.app')

@section('title', $hero['title'] ?? '')

@section('content')
<section class="relative overflow-hidden bg-primary-gradient text-white">
	<div class="max-w-6xl mx-auto px-6 py-16">
		<p class="uppercase tracking-[4px] text-xs font-semibold text-yellow-200 flex items-center gap-2">
			<span class="h-[1px] w-8 bg-yellow-200/70 inline-block"></span>
			{{ __('legal.badge') }}
		</p>
		<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8 mt-4">
			<div class="space-y-4 max-w-3xl">
				<h1 class="text-3xl sm:text-4xl font-extrabold leading-tight">{{ $hero['title'] ?? '' }}</h1>
				<p class="text-lg text-white/90">{{ $hero['subtitle'] ?? '' }}</p>
			</div>
			<div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-6 w-full lg:max-w-sm shadow-2xl">
				<h3 class="text-xl font-semibold mb-2 flex items-center gap-3">
					<i class="fas fa-shield-alt text-yellow-200"></i>
					{{ __('legal.hero_card.title') }}
				</h3>
				<p class="text-white/90">{{ __('legal.hero_card.text') }}</p>
				<div class="mt-4 flex flex-wrap gap-2">
					<span class="px-3 py-1 rounded-full bg-white/20 text-sm flex items-center gap-2">
						<i class="fas fa-clipboard-check"></i> {{ __('legal.hero_card.tag1') }}
					</span>
					<span class="px-3 py-1 rounded-full bg-white/20 text-sm flex items-center gap-2">
						<i class="fas fa-lock"></i> {{ __('legal.hero_card.tag2') }}
					</span>
				</div>
			</div>
		</div>
	</div>

	<span class="absolute -left-10 -top-10 w-44 h-44 bg-white/10 rounded-full blur-3xl"></span>
	<span class="absolute -right-20 top-10 w-52 h-52 bg-yellow-200/20 rounded-full blur-3xl"></span>
</section>

<section class="bg-gray-50 py-14">
	<div class="max-w-6xl mx-auto px-6 space-y-8">
		<div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-100">
			<p class="text-gray-700 leading-relaxed text-lg">{{ $intro ?? '' }}</p>
			@if(!empty($updateNotice))
			<div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-primary-600 bg-primary-50 px-4 py-2 rounded-full">
				<i class="fas fa-clock"></i> {{ $updateNotice }}
			</div>
			@endif
		</div>

		@if(!empty($highlights))
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			@foreach($highlights as $highlight)
			<div class="bg-white shadow-md rounded-xl p-6 border border-gray-100 flex flex-col gap-3">
				<div class="w-12 h-12 rounded-xl bg-primary-gradient text-white flex items-center justify-center text-xl shadow-lg">
					<i class="fas {{ $highlight['icon'] }}"></i>
				</div>
				<h3 class="text-lg font-semibold text-gray-900">{{ $highlight['title'] }}</h3>
				<p class="text-gray-600">{{ $highlight['body'] }}</p>
			</div>
			@endforeach
		</div>
		@endif

		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			@foreach($sections as $section)
			<div class="bg-white rounded-2xl shadow-lg p-7 border border-gray-100 flex flex-col gap-3">
				<div class="flex items-start gap-3">
					<div class="w-11 h-11 rounded-xl bg-primary-gradient text-white flex items-center justify-center text-lg shadow-md shrink-0">
						<i class="fas {{ $section['accent'] ?? 'fa-circle' }}"></i>
					</div>
					<div class="space-y-2">
						<h3 class="text-xl font-semibold text-gray-900">{{ $section['title'] }}</h3>
						<p class="text-gray-700 leading-relaxed">{{ $section['body'] }}</p>
						@if(!empty($section['items']) && is_array($section['items']))
						<ul class="mt-2 space-y-2">
							@foreach($section['items'] as $item)
							<li class="flex items-start gap-2 text-gray-700">
								<span class="mt-1 text-primary-600"><i class="fas fa-check-circle"></i></span>
								<span>{{ $item }}</span>
							</li>
							@endforeach
						</ul>
						@endif
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</section>

<section class="bg-white py-14">
	<div class="max-w-6xl mx-auto px-6">
		<div class="bg-primary-gradient text-white rounded-2xl p-8 md:p-10 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-6">
			<div class="space-y-2">
				<p class="uppercase text-xs tracking-[4px] text-yellow-200">{{ __('legal.cta.title') }}</p>
				<h3 class="text-2xl font-semibold">{{ __('legal.cta.heading') }}</h3>
				<p class="text-white/90">{{ __('legal.cta.text') }}</p>
			</div>
			<div class="flex items-center gap-3">
				<a href="mailto:info@tebbplus.com"
				   class="inline-flex items-center gap-2 border border-white/60 text-white font-semibold px-5 py-3 rounded-xl hover:bg-white/10 transition">
					<i class="fas fa-envelope-open-text"></i>
					{{ __('legal.cta.button') }}
				</a>
				<a href="{{ route('home') }}#subscriptions-plans"
				   class="inline-flex items-center gap-2 text-white border border-white/40 px-4 py-3 rounded-xl hover:bg-white/10 transition">
					<i class="fas fa-arrow-right"></i>
					{{ __('legal.cta.alt') }}
				</a>
			</div>
		</div>
	</div>
</section>
@endsection
