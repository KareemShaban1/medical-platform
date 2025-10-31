@props(['item' => '' , 'icon' => 'fas fa-check' , 'iconColor' => 'text-primary-600'])
<li class="flex gap-4 justify-center items-center">
	<i class="{{ $icon }} {{ $iconColor }}"></i>
	{{ $item }}
</li>
