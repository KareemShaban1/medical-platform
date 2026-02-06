<head>
	<meta charset="utf-8">
	<title>@yield('title')</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
	<meta content="Coderthemes" name="author">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- App favicon -->
	<link rel="shortcut icon" href="{{asset('frontend/images/favicon/favicon-96x96.png')}}">

	<!-- third party css -->
	<link href="{{asset('backend/assets/css/vendor/jquery-jvectormap-1.2.2.css')}}" rel="stylesheet"
		type="text/css">
	<!-- third party css end -->

	<!-- App css -->
	<link href="{{asset('backend/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css">
	@if (App::getLocale() == 'en')
	<link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
	@else
	<link href="{{ asset('backend/assets/css/rtl_style.css') }}" rel="stylesheet">
	@endif

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
	<!-- Toastr CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<!-- Global Assets -->
	@include('backend.dashboards.clinic.layouts.global-assets')

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
			/* Accent color */
		}

		.logo-sm {
			display: none;
			/* Default hidden on large screens */
		}

		/* Responsive: show small version on small devices */
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

	<!-- jQuery -->
	<!-- <script src="{{ asset('plugins/jquery/jquery.min.js') }}" defer></script> -->

	<style>
         /* Sidebar search highlight */
         #leftside-menu-container .sidebar-highlight {
           background-color:rgb(223, 158, 38); /* bootstrap warning-100 */
		   color: white;
           border-radius: 4px;
           transition: background-color 0.2s ease;
         }

        </style>

	<style>
		@media (max-width: 768px) {
			.page-title-box {
				display: block !important;
				overflow: visible !important;
			}

			.page-title-right {
				float: none !important;
				width: 100%;
				display: flex !important;
				flex-wrap: wrap;
				gap: 0.6rem;
				justify-content: flex-start;
				margin-top: 0.5rem;
			}

			.page-title-right .btn {
				width: 100%;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				white-space: nowrap;
				border-radius: 10px;
				padding: 0.65rem 1rem;
				font-weight: 600;
				box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
				transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
			}

			.page-title-right .btn i {
				margin-inline-end: 0.4rem;
				font-size: 0.95em;
			}

			.page-title-right .btn:active {
				transform: translateY(1px);
				box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
			}

			.page-title-right .btn:focus {
				outline: 0;
				box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2), 0 6px 14px rgba(0, 0, 0, 0.08);
			}

			.page-title-right .btn-primary {
				background-image: linear-gradient(135deg, #0d6efd, #3b82f6);
				border: none;
			}

			.page-title-right .btn-info {
				background-image: linear-gradient(135deg, #0dcaf0, #22d3ee);
				border: none;
				color: #fff;
			}

			.page-title-right .btn-secondary {
				background-image: linear-gradient(135deg, #6c757d, #94a3b8);
				border: none;
				color: #fff;
			}
		}
	</style>

	<!-- Custom CSS -->
	@stack('styles')
</head>
