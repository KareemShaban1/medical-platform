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

      <!--- Sidemenu -->
      <ul class="side-nav">

        <!-- <li class="side-nav-title side-nav-item">Navigation</li> -->

        <li class="side-nav-item">
          <a href="" class="side-nav-link">
            <i class="uil-home-alt"></i>
            <span>
              {{__('Clinic Dashboard')}}
            </span>
          </a>
        </li>


        <!-- Users Management -->
        <li class="side-nav-item">
          <a data-bs-toggle="collapse" href="#sidebarUsers" aria-expanded="false"
            aria-controls="sidebarUsers" class="side-nav-link">
            <i class="uil-users-alt"></i>
            <span> {{__('Users')}} </span>
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
                <a href="{{ route('clinic.users.trash') }}">
                  <span> {{__('Trash Users')}} </span>
                </a>
              </li>
            </ul>
          </div>
        </li>


        <!-- Doctor Profiles Management -->
        <li class="side-nav-item">
          <a data-bs-toggle="collapse" href="#sidebarDoctorProfiles" aria-expanded="false"
            aria-controls="sidebarDoctorProfiles" class="side-nav-link">
            <i class="uil-medical-square"></i> <!-- TODO: Change to medical icon -->
            <span> {{__('Doctor Profiles')}} </span>
            <span class="menu-arrow"></span>
          </a>
          <div class="collapse" id="sidebarDoctorProfiles">
            <ul class="side-nav-second-level">
              <li>
                <a href="{{ route('clinic.doctor-profiles.index') }}">
                  <span> {{__('Doctor Profiles')}} </span>
                </a>
              </li>
            </ul>
          </div>
        </li>




        <!-- Notifications -->
        <li class="side-nav-item">
          <a href="{{ route('clinic.notifications.index') }}" class="side-nav-link">
            <i class="uil-bell"></i>
            <span> {{__('Notifications')}} </span>
            <span class="badge bg-danger rounded-pill" id="sidebar-notification-count" style="display: none;">0</span>
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

        <!-- Rental Space Management -->
        <li class="side-nav-item">
          <a data-bs-toggle="collapse" href="#sidebarRentalSpace" aria-expanded="false"
            aria-controls="sidebarRentalSpace" class="side-nav-link">
            <i class="uil-users-alt"></i>
            <span> {{__('Rental Space')}} </span>
            <span class="menu-arrow"></span>
          </a>
          <div class="collapse" id="sidebarRentalSpace">
            <ul class="side-nav-second-level">
              <li>
                <a href="{{ route('clinic.rental-spaces.index') }}">
                  <span> {{__('Rental Space')}} </span>
                </a>
              </li>
              <li>
                <a href="{{ route('clinic.rental-spaces.trash') }}">
                  <span> {{__('Trash Rental Space')}} </span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Jobs Management -->
        <li class="side-nav-item">
          <a data-bs-toggle="collapse" href="#sidebarJobs" aria-expanded="false"
            aria-controls="sidebarJobs" class="side-nav-link">
            <i class="uil-medical-square"></i> <!-- TODO: Change to medical icon -->
            <span> {{__('Jobs')}} </span>
            <span class="menu-arrow"></span>
          </a>
          <div class="collapse" id="sidebarJobs">
            <ul class="side-nav-second-level">
              <li>
                <a href="{{ route('clinic.jobs.index') }}">
                  <span> {{__('Jobs')}} </span>
                </a>
              </li>
              <li>
                <a href="{{ route('clinic.jobs.trash') }}">
                  <span> {{__('Trash Jobs')}} </span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Requests Management (Tickets System) -->
        <li class="side-nav-item">
          <a data-bs-toggle="collapse" href="#sidebarRequests" aria-expanded="false"
            aria-controls="sidebarRequests" class="side-nav-link">
            <i class="uil-clipboard-notes"></i>
            <span> {{__('Purchase Requests')}} </span>
            <span class="menu-arrow"></span>
          </a>
          <div class="collapse" id="sidebarRequests">
            <ul class="side-nav-second-level">
              <li>
                <a href="{{ route('clinic.requests.index') }}">
                  <span> {{__('My Requests')}} </span>
                </a>
              </li>
              <li>
                <a href="{{ route('clinic.requests.create') }}">
                  <span> {{__('Create Request')}} </span>
                </a>
              </li>
            </ul>
          </div>
        </li>

      </ul>

      <!-- End Sidebar -->

      <div class="clearfix"></div>

    </div>

    <!-- Sidebar -left -->
  </div>
  <!-- Left Sidebar End -->
