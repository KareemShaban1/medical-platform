<head>
	<meta charset="utf-8">
	<title>@yield('title')</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
	<meta content="Coderthemes" name="author">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="shortcut icon" href="{{asset('frontend/images/favicon/favicon-96x96.png')}}">

	<link href="{{asset('backend/assets/css/vendor/jquery-jvectormap-1.2.2.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('backend/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css">
	@if (App::getLocale() == 'en')
	<link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
	@else
	<link href="{{ asset('backend/assets/css/rtl_style.css') }}" rel="stylesheet">
	@endif

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

	@include('backend.dashboards.admin.layouts.global-assets')

	<style>
		.logo {
			display: inline-block;
			text-decoration: none;
		}

		.logo-lg,
		.logo-sm {
			font-family: 'Segoe UI', sans-serif;
			font-weight: 700;
			letter-spacing: 1px;
			color: white;
			background-color: #313a46;
			padding: 0px 20px;
			display: inline-block;
			transition: all 0.3s ease;
		}

		.logo-lg i,
		.logo-sm i {
			margin-right: 8px;
			color: #f0b400;
		}

		.logo-sm {
			display: none;
		}

		@media (max-width: 768px) {
			.logo-lg {
				display: none;
			}

			.logo-sm {
				display: inline-block;
			}
		}

		.logo:hover {
			opacity: 0.9;
		}
	</style>

	@stack('styles')
</head>
