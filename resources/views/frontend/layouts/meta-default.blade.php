@php
    $metaTitle = $metaTitle ?? trim($__env->yieldContent('title')) ?: config('app.name');
    $metaDescription = $metaDescription ?? __('footer tagline');
    $metaImage = $metaImage ?? asset('frontend/images/logo.png');
    $metaUrl = $metaUrl ?? url()->current();
@endphp

<meta name="description" content="{{ strip_tags($metaDescription) }}">
<link rel="canonical" href="{{ $metaUrl }}">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ strip_tags($metaDescription) }}">
<meta property="og:url" content="{{ $metaUrl }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ strip_tags($metaDescription) }}">
<meta name="twitter:image" content="{{ $metaImage }}">
