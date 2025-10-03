<!DOCTYPE html>
<html lang="en">



@include('backend.dashboards.admin.layouts.head')

<body class="loading"
	data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
	<!-- Begin page -->
	<div class="wrapper">

		@include('backend.dashboards.admin.layouts.sidebar')
		<!-- ============================================================== -->
		<!-- Start Page Content here -->
		<!-- ============================================================== -->

		<div class="content-page">
			<div class="content">


				@include('backend.dashboards.admin.layouts.top-bar')

				@yield('content')


			</div>
			<!-- content -->

			<!-- Footer Start -->
			<footer class="footer">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<script>
								document.write(new Date().getFullYear())
							</script> © Medical Platform
						</div>
					</div>
				</div>
			</footer>
			<!-- end Footer -->

		</div>

	</div>
	<!-- END wrapper -->



	<div class="rightbar-overlay"></div>
	<!-- /End-bar -->

	@include('backend.dashboards.admin.layouts.footer-scripts')
	<!-- end demo js-->
</body>

</html>
