\
<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu">

	<!-- LOGO -->
	<a class="logo text-center" href="#">
		<span class="logo-lg">
			<i class="fas fa-laptop-code"></i> <span class="logo-text">{{ config('app.name') }}</span>
		</span>
		<span class="logo-sm">
			<i class="fas fa-laptop-code"></i>
		</span>
	</a>

	<!-- LOGO -->
	<a class="logo text-center logo-dark">
		<span class="logo-lg text-white">
			{{ config('app.name') }}
			<!-- <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="16"> -->
		</span>
		<span class="logo-sm text-white">
			{{ config('app.name') }}
			<!-- <img src="{{ asset('backend/assets/images/logo_sm_dark.png') }}" alt="" height="16"> -->
		</span>
	</a>

	<div class="h-100" id="leftside-menu-container" data-simplebar="">

		<div class="px-3 py-2">
			<input type="text" id="sidebar-search" class="form-control"
				placeholder="{{ __('Search menu...') }}" autocomplete="off">
		</div>



		<ul class="side-nav">


			<!-- @hasPermission('view dashboard')
			<li class="side-nav-item">
				<a href="{{ route('admin.dashboard') }}" class="side-nav-link">
					<i class="uil-home-alt"></i>
					<span>
						{{ __('Admin Dashboard') }}
					</span>
				</a>
			</li>
			@endhasPermission -->

			<!-- Contact Messages -->
			@hasPermission('view contact messages')
			<li class="side-nav-item">
				<a href="{{ route('admin.contact-messages.index') }}" class="side-nav-link">
					<i class="uil-envelope"></i>
					<span> {{ __('Contact Messages') }} </span>
					@php
					$newMessagesCount = \App\Models\ContactMessage::where('status',
					'new')->count();
					@endphp
					@if($newMessagesCount > 0)
					<span
						class="badge bg-danger rounded-pill ms-1">{{ $newMessagesCount }}</span>
					@endif
				</a>
			</li>
			@endhasPermission

			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarClinicsManagement"
					aria-expanded="false" aria-controls="sidebarClinicsManagement"
					class="side-nav-link">
					<i class="uil-users-alt"></i>
					<span> {{ __('Clinics Management') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarClinicsManagement">
					<ul class="side-nav-second-level">
						@hasPermission('view system clinics')
						<li>
							<a
								href="{{ route('admin.users-management.clinics') }}">
								<span> {{__('Clinics Info')}} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view system clinic users')
						<li>
							<a
								href="{{ route('admin.users-management.clinic-users') }}">
								<span> {{__('Clinic Users')}} </span>
							</a>
						</li>
						@endhasPermission
						{{-- @hasPermission('view system doctor profiles')
						<li>
							<a
								href="{{ route('admin.users-management.doctor-profiles') }}">
								<span> {{__('Doctor Profiles Management')}}
								</span>
							</a>
						</li>
						@endhasPermission --}}

						@hasPermission('view clinics')
						<li>

							<a href="{{ route('admin.clinics.index') }}">
								<span> {{ __('Clinics Management') }}
								</span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view clinic doctor profiles')
						<li>
							<a
								href="{{ route('admin.doctor-profiles.index') }}">
								<span> {{ __('Doctor Profiles') }}
								</span>
							</a>
						</li>
						@endhasPermission

						@hasPermission('view specialities')
						<li>
							<a href="{{ route('admin.specialities.index') }}">
								<span> {{ __('Doctor Specialites') }}
								</span>
							</a>
						</li>
						@endhasPermission

						@hasPermission('view orders')
						<li>
							<a href="{{ route('admin.orders.index') }}">
								<span> {{ __('All Orders') }} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view rental spaces')
						<li>
							<a
								href="{{ route('admin.rental-spaces.index') }}">
								<span> {{ __('Rental Spaces') }} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view jobs')
						<li>
							<a href="{{ route('admin.jobs.index') }}">
								<span> {{ __('Jobs') }} </span>
							</a>
						</li>
						@endhasPermission
					</ul>
				</div>
			</li>
			@hasPermission('view patients')
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarPatientsManagement"
					aria-expanded="false" aria-controls="sidebarPatientsManagement"
					class="side-nav-link">
					<i class="uil-users-alt"></i>
					<span> {{ __('Patients Management') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarPatientsManagement">
					<ul class="side-nav-second-level">
						@hasPermission('view patients')
						<li>
							<a
								href="{{ route('admin.users-management.patients') }}">
								<span> {{__('Patients')}} </span>
							</a>
						</li>
						@endhasPermission
					</ul>
				</div>
			</li>
			@endhasPermission
			<!-- suppliers management -->
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarSuppliersManagement"
					aria-expanded="false" aria-controls="sidebarSuppliersManagement"
					class="side-nav-link">
					<i class="uil-users-alt"></i>
					<span> {{ __('Suppliers Management') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarSuppliersManagement">
					<ul class="side-nav-second-level">
						@hasPermission('view system suppliers')
						<li>
							<a
								href="{{ route('admin.users-management.suppliers') }}">
								<span> {{__('Suppliers Info')}} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view system supplier users')
						<li>
							<a
								href="{{ route('admin.users-management.supplier-users') }}">
								<span> {{__('Supplier Users')}} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view suppliers')
						<li>
							<a href="{{ route('admin.suppliers.index') }}">
								<span> {{ __('Suppliers Management') }}
								</span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view supplier products')
						<li>
							<a
								href="{{ route('admin.supplier-products.index') }}">
								<span> {{ __('Supplier Products') }}
								</span>
							</a>
						</li>
						@endhasPermission
					</ul>
				</div>
			</li>

			<!-- Users Management -->
			@hasAnyPermission('view system management', 'view categories', 'view announcements', 'view
			translations')
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarUsersManagement"
					aria-expanded="false" aria-controls="sidebarUsersManagement"
					class="side-nav-link">
					<i class="uil-users-alt"></i>
					<span> {{ __('System Data') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarUsersManagement">
					<ul class="side-nav-second-level">

						@hasPermission('view system management')
						<li>
							<a
								href="{{ route('admin.users-management.index') }}">
								<span> {{__('Overview')}} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view categories')
						<li>

							<a href="{{ route('admin.categories.index') }}">
								<span> {{ __('Categories') }} </span>
							</a>
						</li>
						@endhasPermission

						<!-- Announcements -->
						@hasPermission('view announcements')
						<li class="side-nav-item">
							<a href="{{ route('admin.announcements.index') }}"
								class="side-nav-link">
								<i class="uil-megaphone"></i>
								<span> {{ __('Announcements') }} </span>
							</a>
						</li>
						@endhasPermission

						@hasPermission('view translations')
						<!-- Translations Management -->
						<li class="side-nav-item">
							<a href="{{ route('admin.translations.index') }}"
								class="side-nav-link">
								<i class="uil-language"></i>
								<span> {{ __('Translations') }} </span>
							</a>
						</li>
						@endhasPermission
					</ul>
				</div>
			</li>
			@endhasPermission

			<!-- location Management -->
			@hasAnyPermission('view governorates', 'view cities', 'view areas')
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarLocation" aria-expanded="false"
					aria-controls="sidebarLocation" class="side-nav-link">
					<i class="uil-location"></i>
					<span> {{ __('Location') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarLocation">
					<ul class="side-nav-second-level">
						@hasPermission('view governorates')
						<li>
							<a href="{{ route('admin.governorates.index') }}">
								<span> {{ __('Governorates') }} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view cities')
						<li>
							<a href="{{ route('admin.cities.index') }}">
								<span> {{ __('Cities') }} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view areas')
						<li>
							<a href="{{ route('admin.areas.index') }}">
								<span> {{ __('Areas') }} </span>
							</a>
						</li>
						@endhasPermission
					</ul>
				</div>
			</li>
			@endhasPermission



			<!-- Admin Users -->
			@hasAnyPermission('view admin users', 'view roles')
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarAdminUsers" aria-expanded="false"
					aria-controls="sidebarAdminUsers" class="side-nav-link">
					<i class="uil-user"></i>
					<span> {{ __('Users Management') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarAdminUsers">
					<ul class="side-nav-second-level">
						@hasPermission('view admin users')
						<li>
							<a href="{{ route('admin.admin-users.index') }}">
								<span> {{ __('Admin Users') }} </span>
							</a>
						</li>
						@endhasPermission
						<!-- <li>
							<a href="{{ route('admin.admin-users.trash') }}">
								<span> {{ __('Trash Admin Users') }}
								</span>
							</a>
						</li> -->
						@hasPermission('view roles')
						<li>
							<a href="{{ route('admin.roles.index') }}">
								<span> {{ __('Roles') }} </span>
							</a>
						</li>
						@endhasPermission
						<!-- <li>
						<a href="{{ route('admin.roles.trash') }}">
							<span> {{ __('Trash Roles') }} </span>
						</a>
					</li> -->
					</ul>
				</div>
			</li>
			@endhasPermission




			<!-- Purchase Requests & Offers -->
			@hasAnyPermission('view purchase requests')
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarPurchaseRequests"
					aria-expanded="false" aria-controls="sidebarPurchaseRequests"
					class="side-nav-link">
					<i class="uil-file-alt"></i>
					<span> {{ __('Purchase & Offers') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarPurchaseRequests">
					<ul class="side-nav-second-level">
						@hasPermission('view purchase requests')
						<li>
							<a
								href="{{ route('admin.purchase-requests.index') }}">
								<span> {{ __('Purchase Requests') }}
								</span>
							</a>
						</li>
						@endhasPermission
					</ul>
				</div>
			</li>
			@endhasAnyPermission


			<!-- Notifications -->
			@hasPermission('view notifications')
			<li class="side-nav-item">
				<a href="{{ route('admin.notifications.index') }}" class="side-nav-link">
					<i class="uil-bell"></i>
					<span> {{ __('Notifications') }} </span>
					<span class="badge bg-danger rounded-pill"
						id="sidebar-notification-count"
						style="display: none;">0</span>
				</a>
			</li>
			@endhasPermission

			<!-- Blogs -->
			@hasAnyPermission('view blog categories', 'view blog posts', 'view blog categories trash',
			'view blog posts trash')
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarBlogs" aria-expanded="false"
					aria-controls="sidebarBlogs" class="side-nav-link">
					<i class="uil-book-alt"></i>
					<span> {{ __('Blogs') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarBlogs">
					<ul class="side-nav-second-level">
						@hasPermission('view blog categories')
						<li>

							<a
								href="{{ route('admin.blog-categories.index') }}">
								<span> {{ __('Blog Categories') }}
								</span>
							</a>
						</li>
						@endhasPermission

						@hasPermission('view blog posts')
						<li>
							<a href="{{ route('admin.blog-posts.index') }}">
								<span> {{ __('Blog Posts') }} </span>
							</a>
						</li>
						@endhasPermission


					</ul>
				</div>
			</li>
			@endhasAnyPermission


			<!-- Courses -->
			@hasAnyPermission('view courses', 'view course enrollments')
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarCourses" aria-expanded="false"
					aria-controls="sidebarCourses" class="side-nav-link">
					<i class="uil-book-open"></i>
					<span> {{ __('Courses') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarCourses">
					<ul class="side-nav-second-level">
						@hasPermission('view courses')
						<li>

							<a href="{{ route('admin.courses.index') }}">
								<span> {{ __('Courses') }} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view course enrollments')
						<li>
							<a
								href="{{ route('admin.course-enrollments.index') }}">
								<span> {{ __('Course Enrollments') }}
								</span>
							</a>
						</li>
						@endhasPermission


					</ul>
				</div>
			</li>
			@endhasAnyPermission


			<!-- Tickets -->
			@hasAnyPermission('view tickets')
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarTickets" aria-expanded="false"
					aria-controls="sidebarTickets" class="side-nav-link">
					<i class="uil-ticket"></i>
					<span> {{ __('Tickets') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarTickets">
					<ul class="side-nav-second-level">
						@hasPermission('view tickets')
						<li>
							<a href="{{ route('admin.tickets.index') }}">
								<span> {{ __('All Tickets') }} </span>
							</a>
						</li>
						@endhasPermission
					</ul>
				</div>
			</li>
			@endhasAnyPermission

			<!-- Subscriptions -->
			@hasAnyPermission('view plans', 'view features', 'view subscriptions')
			<li class="side-nav-item">
				<a data-bs-toggle="collapse" href="#sidebarSubscriptions" aria-expanded="false"
					aria-controls="sidebarSubscriptions" class="side-nav-link">
					<i class="uil-money-bill"></i>
					<span> {{ __('Subscriptions') }} </span>
					<span class="menu-arrow"></span>
				</a>
				<div class="collapse" id="sidebarSubscriptions">
					<ul class="side-nav-second-level">

						@hasPermission('view plans')
						<li>
							<a href="{{ route('admin.plans.index') }}">
								<span> {{ __('Plans') }} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view features')
						<li>
							<a href="{{ route('admin.features.index') }}">
								<span> {{ __('Features') }} </span>
							</a>
						</li>
						@endhasPermission
						@hasPermission('view subscriptions')
						<li>
							<a
								href="{{ route('admin.subscriptions.index') }}">
								<span> {{ __('Subscriptions') }} </span>
							</a>
						</li>
						@endhasPermission
					</ul>
				</div>
			</li>
			@endhasAnyPermission


		</ul>

		<!-- End Sidebar -->

		<div class="clearfix"></div>



	</div>
	<!-- Sidebar -left -->
</div>
<!-- Left Sidebar End -->
