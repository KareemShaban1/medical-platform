  <!-- ========== Left Sidebar Start ========== -->
  <div class="leftside-menu">

  	<!-- LOGO -->
  	<a class="logo text-center" href="#">
  		<span class="logo-lg">
  			<i class="fas fa-laptop-code"></i> <span
  				class="logo-text">{{ config('app.name') }}</span>
  		</span>
  		<span class="logo-sm">
  			<i class="fas fa-laptop-code"></i>
  		</span>
  	</a>

  	<!-- LOGO -->
  	<a class="logo text-center logo-dark">
  		<span class="logo-lg text-white">
  			{{ config('app.name') }}
  			<!-- <img src="{{asset('backend/assets/images/logo-dark.png')}}" alt="" height="16"> -->
  		</span>
  		<span class="logo-sm text-white">
  			{{ config('app.name') }}
  			<!-- <img src="{{asset('backend/assets/images/logo_sm_dark.png')}}" alt="" height="16"> -->
  		</span>
  	</a>

  	<div class="h-100" id="leftside-menu-container" data-simplebar="">

  		<div class="px-3 py-2">
  			<input type="text" id="sidebar-search" class="form-control"
  				placeholder="{{ __('Search menu...') }}" autocomplete="off">
  		</div>

  		<!--- Sidemenu -->
  		<ul class="side-nav">

  			<!-- <li class="side-nav-title side-nav-item">Navigation</li> -->


  			@hasPermission('view dashboard')
  			<li class="side-nav-item">
  				<a href="{{ route('clinic.dashboard') }}" class="side-nav-link">
  					<i class="uil-home-alt"></i>
  					<span>
  						{{__('Clinic Dashboard')}}
  					</span>
  				</a>
  			</li>
  			@endhasPermission
  			@hasPermission('view clinic info')
  			<!-- Clinic Info -->
  			<li class="side-nav-item">
  				<a href="{{ route('clinic.settings.clinic-info') }}" class="side-nav-link">
  					<i class="uil-cog"></i>
  					<span> {{ __('Clinic Info') }} </span>
  				</a>
  			</li>
  			@endhasPermission



  			@hasPermission('view roles')
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarRoles" aria-expanded="false"
  					aria-controls="sidebarRoles" class="side-nav-link">
  					<i class="uil-shield"></i>
  					<span> {{__('Roles & Permissions')}} </span>
  					<span class="menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarRoles">
  					<ul class="side-nav-second-level">
  						@hasPermission('view roles')
  						<li>
  							<a href="{{ route('clinic.roles.index') }}">
  								<span> {{__('Roles')}} </span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view roles')
  						<li>
  							<a href="{{ route('clinic.roles.trash') }}">
  								<span> {{__('Trash Roles')}} </span>
  							</a>
  						</li>
  						@endhasPermission

  					</ul>
  				</div>
  			</li>
  			@endhasPermission


  			@hasAnyPermission('view users', 'view doctor profiles')
  			<!-- HR Management -->
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarUsers" aria-expanded="false"
  					aria-controls="sidebarUsers" class="side-nav-link">
  					<i class="uil-users-alt"></i>
  					<span> {{__('HR Management')}} </span>
  					<span class="menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarUsers">
  					<ul class="side-nav-second-level">
  						@hasPermission('view users')
  						<li>
  							<a href="{{ route('clinic.users.index') }}">
  								<span> {{__('Employees')}} </span>
  							</a>
  						</li>
  						@endhasPermission
  						@if(auth('clinic')->user()->isDoctor() &&
  						hasPermission('view doctor profiles'))
  						<li>
  							<a
  								href="{{ route('clinic.doctor-profiles.my-profile') }}">
  								<span> {{__('My Doctor Profile')}}
  								</span>
  							</a>
  						</li>
  						@endif
  						@hasPermission('view doctor profiles')
  						<li>
  							<a
  								href="{{ route('clinic.doctor-profiles.index') }}">
  								<span> {{__('Doctor Profile Management')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission

  						@hasPermission('view salary contracts')
  						<li>
  							<a
  								href="{{ route('clinic.salary-contracts.index') }}">
  								<span> {{__('Salary Contracts')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view payslips')
  						<li>
  							<a href="{{ route('clinic.payslips.index') }}">
  								<span> {{__('Payslip')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view working hours')
  						<li>
  							<a
  								href="{{ route('clinic.working-hours.index') }}">
  								<span> {{ __('Working Hours') }}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view attendance')
  						<!-- attendance -->
  						<li>
  							<a
  								href="{{ route('clinic.attendance.index') }}">
  								<span> {{__('Attendance')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  					</ul>
  				</div>
  			</li>
  			@endhasAnyPermission


  			@hasAnyPermission('view clinic inventories', 'view rental spaces', 'view jobs', 'view
  			purchase requests', 'view orders', 'view course enrollments')
  			<!-- Clinic Management -->
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarClinicinventories"
  					aria-expanded="false" aria-controls="sidebarClinicinventories"
  					class="side-nav-link">
  					<!-- inventories icon -->
  					<i class="uil-file-medical"></i>
  					<span> {{__('Clinic Management')}} </span>
  					<span class=" menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarClinicinventories">
  					<ul class="side-nav-second-level">
  						@hasPermission('view clinic inventories')
  						<li>
  							<a
  								href="{{ route('clinic.clinic-inventories.index') }}">
  								<span> {{__('Clinic Inventory')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view rental spaces')
  						<li>
  							<a
  								href="{{ route('clinic.rental-spaces.index') }}">
  								<span> {{__('Rental Space')}} </span>
  							</a>
  						</li>
  						@endhasPermission
  						<!-- jobs -->
  						@hasPermission('view jobs')
  						<li>
  							<a href="{{ route('clinic.jobs.index') }}">
  								<span> {{__('Jobs')}} </span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view purchase requests')
  						<li>
  							<a href="{{ route('clinic.requests.index') }}">
  								<span> {{__('My Requests')}} </span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view orders')
  						<li>
  							<a href="{{ route('clinic.orders.index') }}">
  								<span> {{ __('My Orders') }} </span>
  							</a>
  						</li>
  						@endhasPermission
  						<!-- Course Enrollments -->
  						@hasPermission('view course enrollments')
  						<li class="side-nav-item">
  							<a href="{{ route('clinic.course-enrollments.index') }}"
  								class="side-nav-link">
  								<i class="uil-book-open"></i>
  								<span> {{ __('Course Enrollments') }}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  					</ul>
  				</div>
  			</li>
  			@endhasAnyPermission


  			<!-- Appointments Management -->
  			@hasAnyPermission('view appointments', 'view availability overrides', 'view daily
  			periods', 'view appointments' ,'view medical records')
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarAppointments" aria-expanded="false"
  					aria-controls="sidebarAppointments" class="side-nav-link">
  					<i class="uil-calendar-alt"></i>
  					<span> {{__('Appointments')}} </span>
  					<span class="menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarAppointments">
  					<ul class="side-nav-second-level">
  						@hasPermission('view appointments')
  						<li>
  							<a
  								href="{{ route('clinic.appointments.index') }}">
  								<span> {{__('All Appointments')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view availability overrides')
  						<li>
  							<a
  								href="{{ route('clinic.availability-overrides.index') }}">
  								<span> {{__('Availability Overrides')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view daily periods')
  						<li>
  							<a
  								href="{{ route('clinic.daily-periods.index') }}">
  								<span> {{__('Daily Periods')}} </span>
  							</a>
  						</li>
  						@endhasPermission
  						<!-- Medical Records -->
  						@hasPermission('view medical records')
  						<li class="side-nav-item">
  							<a href="{{ route('clinic.medical-records.index') }}"
  								class="side-nav-link">
  								<i class="uil-notes"></i>
  								<span> {{ __('Medical Records') }}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  					</ul>
  				</div>
  			</li>
  			@endhasAnyPermission


  			<!-- Patients Management -->
  			@hasAnyPermission('view patients', 'view trash patients')
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarPatients" aria-expanded="false"
  					aria-controls="sidebarPatients" class="side-nav-link">
  					<i class="uil-user-square"></i>
  					<span> {{__('Patients')}} </span>
  					<span class="menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarPatients">
  					<ul class="side-nav-second-level">
  						@hasPermission('view patients')
  						<li>
  							<a href="{{ route('clinic.patients.index') }}">
  								<span> {{__('All Patients')}} </span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view trash patients')
  						<li>
  							<a href="{{ route('clinic.patients.trash') }}">
  								<span> {{__('Trash Patients')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  					</ul>
  				</div>
  			</li>
  			@endhasAnyPermission


  			@hasAnyPermission('view lab orders', 'create lab orders')
  			<!-- Lab Orders -->
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarLabOrders" aria-expanded="false"
  					aria-controls="sidebarLabOrders" class="side-nav-link">
  					<i class="uil-flask"></i>
  					<span> {{__('Lab Orders')}} </span>
  					<span class="menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarLabOrders">
  					<ul class="side-nav-second-level">
  						@hasPermission('view lab orders')
  						<li><a href="{{ route('clinic.lab-orders.index') }}"><span>
  									{{__('All Lab Orders')}}
  								</span></a></li>
  						@endhasPermission
  						@hasPermission('create lab orders')
  						<li><a href="{{ route('clinic.lab-orders.create') }}"><span>
  									{{__('Create Lab Order')}}
  								</span></a></li>
  						@endhasPermission
  					</ul>
  				</div>
  			</li>
  			@endhasAnyPermission





  			<!-- Expense Categories Management -->
  			@hasAnyPermission('view expense categories', 'view expenses', 'view invoices')
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarExpenseCategories"
  					aria-expanded="false" aria-controls="sidebarExpenseCategories"
  					class="side-nav-link">
  					<i class="uil-file-medical"></i>
  					<span> {{__('Expenses')}} </span>
  					<span class="menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarExpenseCategories">
  					<ul class="side-nav-second-level">
  						@hasPermission('view expense categories')
  						<li>
  							<a
  								href="{{ route('clinic.expense-categories.index') }}">
  								<span> {{__('Expense Categories')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view expenses')
  						<li> <a href="{{ route('clinic.expenses.index') }}">
  								<span> {{__('Expenses')}}
  								</span>
  							</a>
  						</li>
  						@endhasPermission
  						@hasPermission('view invoices')
  						<!-- Invoices -->
  						<li class="side-nav-item">
  							<a href="{{ route('clinic.invoices.index') }}"
  								class="side-nav-link">
  								<i class="uil-receipt-alt"></i>
  								<span> {{ __('Invoices') }} </span>
  							</a>
  						</li>
  						@endhasPermission
  					</ul>
  				</div>
  			</li>
  			@endhasAnyPermission

  			<!-- My Subscription -->
  			@hasPermission('view subscriptions')
  			<li class="side-nav-item">
  				<a href="{{ route('clinic.subscriptions.index') }}" class="side-nav-link">
  					<i class="uil-credit-card"></i>
  					<span> {{__('My Subscription')}} </span>
  				</a>
  			</li>
  			@endhasPermission
  			<!-- Notifications -->
  			@hasPermission('view notifications')
  			<li class="side-nav-item">
  				<a href="{{ route('clinic.notifications.index') }}" class="side-nav-link">
  					<i class="uil-bell"></i>
  					<span> {{__('Notifications')}} </span>
  					<span class="badge bg-danger rounded-pill"
  						id="sidebar-notification-count"
  						style="display: none;">0</span>
  				</a>
  			</li>
  			@endhasPermission
  		</ul>

  		<!-- End Sidebar -->

  		<div class="clearfix"></div>

  	</div>

  	<!-- Sidebar -left -->
  </div>
  <!-- Left Sidebar End -->
