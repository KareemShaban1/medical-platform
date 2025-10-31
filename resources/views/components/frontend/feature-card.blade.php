@props(['title' => '' , 'description' => '' , 'features' => [] , 'icon' => 'fas fa-hospital' , 'iconColor' =>
'text-indigo-600' , 'bgColor' => 'bg-indigo-50' , 'hoverBgColor' => 'group-hover:bg-indigo-600' , 'titleColor' =>
'text-gray-900' , 'svgIcon' => '' , 'hoverShadowColor' =>
'group-hover:shadow-indigo-400/50' , 'hoverShadow' => 'group-hover:shadow-2xl'])
<div
	class="relative w-full text-center max-w-sm group transition-all duration-500 hover:scale-105 shadow-lg p-4 py-8 border border-gray-200 rounded-lg">
	<div
		class="bg-indigo-50 rounded-2xl flex justify-center items-center mb-5 w-20 h-20 mx-auto cursor-pointer  transition-all duration-500 {{ $hoverBgColor }} {{ $hoverShadowColor }} group-hover:shadow-2xl relative z-10">

		@if($svgIcon)
		<img src="{{ $svgIcon }}" alt="{{ $title }}" class="w-full h-full object-cover">
		@else
		<i
			class="{{ $icon }} text-4xl {{ $iconColor }} transition-all duration-500 group-hover:text-white"></i>
		@endif
	</div>

	<h4 class="text-lg font-semibold text-gray-900 mb-3 capitalize transition-all duration-300 {{ $titleColor }}">
		{{ $title }}
	</h4>

	<p class="text-sm font-normal text-gray-500 transition-all duration-300 group-hover:text-gray-700">
		{{ $description }}
	</p>

	<ul class="mt-5 space-y-2 text-start mx-auto w-fit">
		 @foreach(array_slice($features, 0, 2) as $feature)
		<x-frontend.feature-item item="{{ $feature }}" icon="fas fa-check" iconColor="{{ $iconColor }}" />
		@endforeach
	</ul>

	<!-- Glow Effect (behind content) -->
	<!-- <div
		class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 bg-gradient-to-r from-indigo-400/10 to-blue-400/10 blur-xl transition-all duration-700 z-0">
	</div> -->
</div>