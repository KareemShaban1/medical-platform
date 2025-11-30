<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	@php($metaStack = trim($__env->yieldPushContent('meta')))
	@if($metaStack !== '')
		{!! $metaStack !!}
	@else
		@include('frontend.layouts.meta-default')
	@endif
	<title>@yield('title')</title>
	<!-- tailwind -->
	<script src="https://cdn.tailwindcss.com"></script>
	<!-- Swiper CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
		rel="stylesheet">
	<link rel="shortcut icon" href="{{asset('backend/assets/images/favicon.ico')}}">
	<!-- Theme CSS -->
	<link rel="stylesheet" href="{{ asset('frontend/css/theme.css') }}">

	<!-- Toastr CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


	<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap"
		rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

	<script src="//unpkg.com/alpinejs" defer></script>




	@if (app()->getLocale() == 'ar')
	<link rel="stylesheet" href="{{ asset('frontend/css/rtl.css') }}">
	@else
	<link rel="stylesheet" href="{{ asset('frontend/css/ltr.css') }}">
	@endif

	@stack('styles')
</head>
