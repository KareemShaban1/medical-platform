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
					<i class="fas fa-heartbeat text-yellow-200"></i>
					{{ __('legal.about.hero_card.title') }}
				</h3>
				<p class="text-white/90">{{ __('legal.about.hero_card.text') }}</p>
				<div class="mt-4 grid grid-cols-3 gap-2 text-center">
					<div class="bg-white/15 rounded-xl p-3">
						<p class="text-2xl font-bold">{{ __('legal.about.hero_card.metric1.value') }}</p>
						<p class="text-xs text-white/80">{{ __('legal.about.hero_card.metric1.label') }}</p>
					</div>
					<div class="bg-white/15 rounded-xl p-3">
						<p class="text-2xl font-bold">{{ __('legal.about.hero_card.metric2.value') }}</p>
						<p class="text-xs text-white/80">{{ __('legal.about.hero_card.metric2.label') }}</p>
					</div>
					<div class="bg-white/15 rounded-xl p-3">
						<p class="text-2xl font-bold">{{ __('legal.about.hero_card.metric3.value') }}</p>
						<p class="text-xs text-white/80">{{ __('legal.about.hero_card.metric3.label') }}</p>
					</div>
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
			<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
				<div class="space-y-3">
					<p class="uppercase text-xs tracking-[4px] text-primary-600 font-semibold">{{ __('legal.about.mission.title') }}</p>
					<h2 class="text-2xl font-bold text-gray-900">{{ __('legal.about.mission.heading') }}</h2>
					<p class="text-gray-700 leading-relaxed">{{ __('legal.about.mission.body') }}</p>
					<ul class="space-y-2">
						@foreach(trans('legal.about.mission.points') as $point)
						<li class="flex items-start gap-2 text-gray-700">
							<span class="mt-1 text-primary-600"><i class="fas fa-check-circle"></i></span>
							<span>{{ $point }}</span>
						</li>
						@endforeach
					</ul>
				</div>
				<div class="bg-primary-gradient text-white rounded-2xl p-6 shadow-xl w-full lg:w-1/3">
					<h3 class="text-xl font-semibold mb-2">{{ __('legal.about.vision.title') }}</h3>
					<p class="text-white/90">{{ __('legal.about.vision.body') }}</p>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
			@foreach($pillars as $pillar)
			<div class="bg-white rounded-2xl shadow-lg p-7 border border-gray-100 flex flex-col gap-3">
				<div class="w-12 h-12 rounded-xl bg-primary-gradient text-white flex items-center justify-center text-xl shadow-md">
					<i class="fas {{ $pillar['icon'] }}"></i>
				</div>
				<h3 class="text-lg font-semibold text-gray-900">{{ $pillar['title'] }}</h3>
				<p class="text-gray-700">{{ $pillar['body'] }}</p>
			</div>
			@endforeach
		</div>

		<div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
			<div class="flex items-center justify-between flex-wrap gap-3 mb-6">
				<div>
					<p class="uppercase text-xs tracking-[4px] text-primary-600 font-semibold">{{ __('legal.about.how.title') }}</p>
					<h3 class="text-2xl font-bold text-gray-900">{{ __('legal.about.how.heading') }}</h3>
					<p class="text-gray-700">{{ __('legal.about.how.body') }}</p>
				</div>
				<div class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 bg-primary-50 px-4 py-2 rounded-full">
					<i class="fas fa-arrow-trend-up"></i> {{ __('legal.about.how.tag') }}
				</div>
			</div>
			<div class="grid md:grid-cols-3 gap-4">
				@foreach(trans('legal.about.how.steps') as $index => $step)
				<div class="border border-gray-100 rounded-xl p-5 shadow-sm bg-gray-50">
					<div class="w-10 h-10 rounded-full bg-primary-gradient text-white flex items-center justify-center font-bold mb-3">
						{{ $index + 1 }}
					</div>
					<p class="text-gray-800 font-semibold mb-1">{{ $step['title'] }}</p>
					<p class="text-gray-700 text-sm">{{ $step['body'] }}</p>
				</div>
				@endforeach
			</div>
		</div>

		<div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
			<p class="uppercase text-xs tracking-[4px] text-primary-600 font-semibold">{{ __('legal.about.milestones.title') }}</p>
			<h3 class="text-2xl font-bold text-gray-900 mb-6">{{ __('legal.about.milestones.heading') }}</h3>
			<div class="grid md:grid-cols-3 gap-4">
				@foreach($milestones as $milestone)
				<div class="relative p-6 rounded-xl border border-gray-100 shadow-sm bg-gray-50">
					<p class="text-sm font-semibold text-primary-700 mb-2">{{ $milestone['year'] }}</p>
					<h4 class="text-lg font-semibold text-gray-900">{{ $milestone['title'] }}</h4>
					<p class="text-gray-700 mt-2">{{ $milestone['body'] }}</p>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</section>

<section class="bg-white py-14">
	<div class="max-w-6xl mx-auto px-6">
		<div class="bg-primary-gradient text-white rounded-2xl p-8 md:p-10 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-6">
			<div class="space-y-2">
				<p class="uppercase text-xs tracking-[4px] text-yellow-200">{{ __('legal.cta.title') }}</p>
				<h3 class="text-2xl font-semibold">{{ __('legal.about.cta.heading') }}</h3>
				<p class="text-white/90">{{ __('legal.about.cta.text') }}</p>
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
