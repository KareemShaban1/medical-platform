@props(['banner'])

@php
// Get position styles
if (!function_exists('getBannerPositionStyles')) {
function getBannerPositionStyles($position) {
$positions = [
'top-left' => ['top' => '10%', 'left' => '5%', 'transform' => 'translate(0, 0)'],
'top-center' => ['top' => '10%', 'left' => '50%', 'transform' => 'translate(-50%, 0)'],
'top-right' => ['top' => '10%', 'right' => '5%', 'left' => 'auto', 'transform' => 'translate(0, 0)'],
'center-left' => ['top' => '50%', 'left' => '5%', 'transform' => 'translate(0, -50%)'],
'center' => ['top' => '50%', 'left' => '50%', 'transform' => 'translate(-50%, -50%)'],
'center-right' => ['top' => '50%', 'right' => '5%', 'left' => 'auto', 'transform' => 'translate(0, -50%)'],
'bottom-left' => ['bottom' => '10%', 'left' => '5%', 'top' => 'auto', 'transform' => 'translate(0, 0)'],
'bottom-center' => ['bottom' => '10%', 'left' => '50%', 'top' => 'auto', 'transform' => 'translate(-50%, 0)'],
'bottom-right' => ['bottom' => '10%', 'right' => '5%', 'left' => 'auto', 'top' => 'auto', 'transform' => 'translate(0,
0)']
];
return $positions[$position] ?? $positions['center'];
}
}

$textPosition = $banner->text_position ?? 'center';
$textPosStyles = $textPosition === 'custom' && $banner->text_position_custom
? $banner->text_position_custom
: getBannerPositionStyles($textPosition);

$buttonPosition = $banner->button_position ?? 'below-text';
$buttonPosStyles = [];

if ($buttonPosition === 'custom' && $banner->button_position_custom) {
$buttonPosStyles = $banner->button_position_custom;
} else {
// Calculate relative to text position
$textTop = isset($textPosStyles['top']) ? (float)str_replace('%', '', $textPosStyles['top']) : 50;
switch($buttonPosition) {
case 'below-text':
$buttonPosStyles = ['top' => ($textTop + 20) . '%', 'left' => $textPosStyles['left'] ?? '50%', 'transform' =>
$textPosStyles['transform'] ?? 'translate(-50%, 0)'];
break;
case 'above-text':
$buttonPosStyles = ['bottom' => (100 - $textTop + 20) . '%', 'left' => $textPosStyles['left'] ?? '50%', 'transform' =>
$textPosStyles['transform'] ?? 'translate(-50%, 0)'];
break;
case 'left-of-text':
$buttonPosStyles = ['top' => $textPosStyles['top'] ?? '50%', 'left' => '5%', 'transform' => 'translate(0, -50%)'];
break;
case 'right-of-text':
$buttonPosStyles = ['top' => $textPosStyles['top'] ?? '50%', 'right' => '5%', 'left' => 'auto', 'transform' =>
'translate(0, -50%)'];
break;
default:
$buttonPosStyles = ['top' => '80%', 'left' => '50%', 'transform' => 'translate(-50%, -50%)'];
}
}

// Text styling
$textColor = $banner->text_color ?? '#ffffff';
$textBgColor = $banner->text_background_color ?? '';
$textBgOpacity = $banner->text_background_opacity ?? 0;
$textAlignment = $banner->text_alignment ?? 'left';

// Button styling
$buttonColor = $banner->button_color ?? '#007bff';
$buttonTextColor = $banner->button_text_color ?? '#ffffff';

// Convert hex to rgba for background
$textBgRgba = '';
if ($textBgColor && $textBgOpacity > 0) {
$hex = str_replace('#', '', $textBgColor);
$r = hexdec(substr($hex, 0, 2));
$g = hexdec(substr($hex, 2, 2));
$b = hexdec(substr($hex, 4, 2));
$textBgRgba = "rgba({$r}, {$g}, {$b}, " . ($textBgOpacity / 100) . ")";
}

// Build text container style
$textContainerStyle = 'position: absolute; ';
foreach ($textPosStyles as $key => $value) {
$textContainerStyle .= "{$key}: {$value}; ";
}
$textContainerStyle .= "text-align: {$textAlignment}; ";
if ($textBgRgba) {
$textContainerStyle .= "background-color: {$textBgRgba}; padding: 15px; border-radius: 4px; ";
} else {
$textContainerStyle .= "padding: 15px; ";
}
$textContainerStyle .= "z-index: 10; ";

// Build button container style
$buttonContainerStyle = 'position: absolute; ';
foreach ($buttonPosStyles as $key => $value) {
$buttonContainerStyle .= "{$key}: {$value}; ";
}
$buttonContainerStyle .= "padding: 15px; z-index: 10; ";

$bannerHeight = $banner->image_height ? (int) $banner->image_height . 'px' : null;
$containerStyle = 'position: relative; width: 100%; overflow: hidden; ';
if ($bannerHeight) {
	$containerStyle .= "height: {$bannerHeight}; ";
}
$imagePosX = $banner->image_position_x ?? 50;
$imagePosY = $banner->image_position_y ?? 50;
$imageStyle = $bannerHeight
	? "width: 100%; height: 100%; object-fit: cover; object-position: {$imagePosX}% {$imagePosY}%; display: block;"
	: "width: 100%; height: auto; object-fit: cover; object-position: {$imagePosX}% {$imagePosY}%; display: block;";
@endphp

<div class="banner-container" style="{{ $containerStyle }}">
	<div class="banner-image-container" style="position: relative; width: 100%; height: 100%;">
		@if($banner->image)
		<img src="{{ $banner->image }}" alt="{{ $banner->title ?? '' }}" class="banner-image"
			style="{{ $imageStyle }}" @if(!$bannerHeight) onload="this.style.height='auto';" @endif>
		@endif

		<!-- Overlay Content -->
		<div class="banner-overlay"
			style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; pointer-events: none;">
			@if($banner->title || $banner->description || $banner->content)
			<div class="banner-text-container" style="{{ $textContainerStyle }}">
				@if($banner->title)
				<h4 class="banner-title"
					style="color: {{ $textColor }}; margin-bottom: 10px; font-weight: bold; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
					{{ $banner->title }}
				</h4>
				@endif

				@if($banner->description)
				<p class="banner-description"
					style="color: {{ $textColor }}; margin-bottom: 10px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
					{{ $banner->description }}
				</p>
				@endif

				@if($banner->content)
				<div class="banner-content"
					style="color: {{ $textColor }}; margin-bottom: 10px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
					{!! $banner->content !!}
				</div>
				@endif
			</div>
			@endif

			@if($banner->link_url)
			<div class="banner-button-container" style="{{ $buttonContainerStyle }}">
				<a href="{{ $banner->link_url }}" class="banner-link-button"
					data-banner-id="{{ $banner->id }}" @if($banner->open_in_new_tab)
					target="_blank" rel="noopener noreferrer" @endif
					style="display: inline-block; padding: 8px 20px; background-color:
					{{ $buttonColor }}; color: {{ $buttonTextColor }}; text-decoration:
					none; border-radius: 4px; pointer-events: auto; transition: opacity
					0.3s;"
					onmouseover="this.style.opacity='0.9'"
					onmouseout="this.style.opacity='1'"
					onclick="trackBannerClick({{ $banner->id }}, event,
					'{{ $banner->link_url }}',
					{{ $banner->open_in_new_tab ? 'true' : 'false' }});
					@if(!$banner->open_in_new_tab) return false; @endif">
					{{ $banner->link_text ?: __('Click Here') }}
				</a>
			</div>
			@endif
		</div>
	</div>
</div>
