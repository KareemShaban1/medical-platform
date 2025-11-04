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

  			<li class="side-nav-item">
  				<a href="{{ route('clinic.dashboard') }}" class="side-nav-link">
  					<i class="uil-home-alt"></i>
  					<span>
  						{{__('Clinic Dashboard')}}
  					</span>
  				</a>
  			</li>

  			<!-- Clinic Info -->
  			<li class="side-nav-item">
  				<a href="{{ route('clinic.settings.clinic-info') }}" class="side-nav-link">
  					<i class="uil-cog"></i>
  					<span> {{ __('Clinic Info') }} </span>
  				</a>
  			</li>


  			<!-- Roles Management -->
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarRoles" aria-expanded="false"
  					aria-controls="sidebarRoles" class="side-nav-link">
  					<i class="uil-shield"></i>
  					<span> {{__('Roles & Permissions')}} </span>
  					<span class="menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarRoles">
  					<ul class="side-nav-second-level">
  						<li>
  							<a href="{{ route('clinic.roles.index') }}">
  								<span> {{__('Roles')}} </span>
  							</a>
  						</li>
  						<li>
  							<a href="{{ route('clinic.roles.trash') }}">
  								<span> {{__('Trash Roles')}} </span>
  							</a>
  						</li>
  					</ul>
  				</div>
  			</li>


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
  						<li>
  							<a href="{{ route('clinic.users.index') }}">
  								<span> {{__('Users')}} </span>
  							</a>
  						</li>
  						<li>
  							<a
  								href="{{ route('clinic.doctor-profiles.index') }}">
  								<span> {{__('Doctor Profiles')}}
  								</span>
  							</a>
  						</li>
  						<li>
  							<a
  								href="{{ route('clinic.salary-contracts.index') }}">
  								<span> {{__('Salary Contracts')}}
  								</span>
  							</a>
  						</li>
  						<li>
  							<a href="{{ route('clinic.payslips.index') }}">
  								<span> {{__('Payslip')}}
  								</span>
  							</a>
  						</li>

  						<a href="{{ route('clinic.working-hours.index') }}"
  							class="side-nav-link">
  							<i class="uil-schedule"></i>
  							<span> {{ __('Working Hours') }} </span>
  						</a>


  					</ul>
  				</div>
  			</li>


  			<!-- Clinic Management -->
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarClinicInventory"
  					aria-expanded="false" aria-controls="sidebarClinicInventory"
  					class="side-nav-link">
  					<!-- inventory icon -->
  					<i class="uil-file-medical"></i>
  					<span> {{__('Clinic Management')}} </span>
  					<span class=" menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarClinicInventory">
  					<ul class="side-nav-second-level">
  						<li>
  							<a
  								href="{{ route('clinic.clinic-inventories.index') }}">
  								<span> {{__('Clinic Inventory')}}
  								</span>
  							</a>
  						</li>

  						<li>
  							<a
  								href="{{ route('clinic.rental-spaces.index') }}">
  								<span> {{__('Rental Space')}} </span>
  							</a>
  						</li>

  						<!-- jobs -->
  						<li>
  							<a href="{{ route('clinic.jobs.index') }}">
  								<span> {{__('Jobs')}} </span>
  							</a>
  						</li>

  						<li>
  							<a href="{{ route('clinic.requests.index') }}">
  								<span> {{__('My Requests')}} </span>
  							</a>
  						</li>
  					</ul>
  				</div>
  			</li>



  			<!-- Appointments Management -->
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarAppointments" aria-expanded="false"
  					aria-controls="sidebarAppointments" class="side-nav-link">
  					<i class="uil-calendar-alt"></i>
  					<span> {{__('Appointments')}} </span>
  					<span class="menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarAppointments">
  					<ul class="side-nav-second-level">
  						<li>
  							<a
  								href="{{ route('clinic.appointments.index') }}">
  								<span> {{__('All Appointments')}}
  								</span>
  							</a>
  						</li>
  						<li>
  							<a
  								href="{{ route('clinic.availability-overrides.index') }}">
  								<span> {{__('Availability Overrides')}}
  								</span>
  							</a>
  						</li>
  						<li>
  							<a
  								href="{{ route('clinic.daily-periods.index') }}">
  								<span> {{__('Daily Periods')}} </span>
  							</a>
  						</li>

  					</ul>
  				</div>
  			</li>

  			<!-- Patients Management -->
  			<li class="side-nav-item">
  				<a data-bs-toggle="collapse" href="#sidebarPatients" aria-expanded="false"
  					aria-controls="sidebarPatients" class="side-nav-link">
  					<i class="uil-user-square"></i>
  					<span> {{__('Patients')}} </span>
  					<span class="menu-arrow"></span>
  				</a>
  				<div class="collapse" id="sidebarPatients">
  					<ul class="side-nav-second-level">
  						<li>
  							<a href="{{ route('clinic.patients.index') }}">
  								<span> {{__('All Patients')}} </span>
  							</a>
  						</li>

  					</ul>
  				</div>
  			</li>






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
  						<li><a href="{{ route('clinic.lab-orders.index') }}"><span>
  									{{__('All Lab Orders')}}
  								</span></a></li>
  						<li><a href="{{ route('clinic.lab-orders.create') }}"><span>
  									{{__('Create Lab Order')}}
  								</span></a></li>
  					</ul>
  				</div>
  			</li>

  			<!-- Medical Records -->
  			<li class="side-nav-item">
  				<a href="{{ route('clinic.medical-records.index') }}" class="side-nav-link">
  					<i class="uil-notes"></i>
  					<span> {{ __('Medical Records') }} </span>
  				</a>
  			</li>


  			<!-- Expense Categories Management -->
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
  						<li>
  							<a
  								href="{{ route('clinic.expense-categories.index') }}">
  								<span> {{__('Expense Categories')}}
  								</span>
  							</a>
  						</li>
  						<li> <a href="{{ route('clinic.expenses.index') }}">
  								<span> {{__('Expenses')}}
  								</span>
  							</a>
  						</li>
  					</ul>
  				</div>
  			</li>
  			</li>

  			<!-- Notifications -->
  			<li class="side-nav-item">
  				<a href="{{ route('clinic.notifications.index') }}" class="side-nav-link">
  					<i class="uil-bell"></i>
  					<span> {{__('Notifications')}} </span>
  					<span class="badge bg-danger rounded-pill"
  						id="sidebar-notification-count"
  						style="display: none;">0</span>
  				</a>
  			</li>

  		</ul>

  		<!-- End Sidebar -->

  		<div class="clearfix"></div>

  	</div>

  	<!-- Sidebar -left -->
  </div>
  <!-- Left Sidebar End -->