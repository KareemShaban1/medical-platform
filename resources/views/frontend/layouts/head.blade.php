<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- tailwind -->
	<script src="https://cdn.tailwindcss.com"></script>
	<!-- Swiper CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<!-- Theme CSS -->
	<link rel="stylesheet" href="{{ asset('frontend/css/theme.css') }}">

		<!-- Toastr CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


	<title>Medical Platform - Home</title>

	@if (app()->getLocale() == 'ar')
	<link rel="stylesheet" href="{{ asset('frontend/css/rtl.css') }}">
	@else
	<link rel="stylesheet" href="{{ asset('frontend/css/ltr.css') }}">
	@endif

	@stack('styles')
</head>
