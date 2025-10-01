@props([
'title' => '',
'description' => '',
'buttonText' => 'View All',
'buttonUrl' => '#',
'buttonIcon' => 'fas fa-arrow-right',
'buttonColor' => 'bg-blue-600 hover:bg-blue-700',
'titleColor' => 'text-gray-900',
'descriptionColor' => 'text-gray-600'
])

<div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-12 space-y-4 lg:space-y-0">
	<div class="text-center lg:text-left">
		<h2 class="text-2xl sm:text-3xl font-bold {{ $titleColor }} mb-4">
			{{ $title }}
		</h2>
		<p class="text-base sm:text-lg {{ $descriptionColor }}">
			{{ $description }}
		</p>
	</div>
	<div class="text-center">
		<a href="{{ $buttonUrl }}"
			class="animate-bounce focus:animate-none hover:animate-none inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 {{ $buttonColor }} text-white rounded hover:transition font-medium rounded-lg text-sm sm:text-base">
			{{ $buttonText }}

			<!-- Button icon -->
			<i class="{{ $buttonIcon }} mx-2"></i>
		</a>
	</div>
</div>
