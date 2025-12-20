<!DOCTYPE html>
<html lang="en">

@include('backend.dashboards.affiliate.layouts.head')

<body class="loading"
	data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
	<div class="wrapper">
		@include('backend.dashboards.affiliate.layouts.sidebar')

		<div class="content-page">
			<div class="content">
				@include('backend.dashboards.affiliate.layouts.top-bar')

				@yield('content')
			</div>

			<footer class="footer">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<script>
							document.write(new Date().getFullYear())
							</script> Ac tebbplus
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>

	<div class="rightbar-overlay"></div>

	@include('backend.dashboards.affiliate.layouts.footer-scripts')
</body>

</html>
