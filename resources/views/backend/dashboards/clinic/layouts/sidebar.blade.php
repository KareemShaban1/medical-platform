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





                                <!-- Events -->
                                <li class="side-nav-item">
                                          <a data-bs-toggle="collapse" href="#sidebarEventsReport" aria-expanded="false"
                                                    aria-controls="sidebarEventsReport" class="side-nav-link">
                                                    <i class="uil-money-withdraw"></i>
                                                    <span> {{__('Events')}} </span>
                                                    <span class="menu-arrow"></span>
                                          </a>
                                          <div class="collapse" id="sidebarEventsReport">
                                                    <ul class="side-nav-second-level">
                                                              @can('view events')
                                                              <li>
                                                                        <a href="#">
                                                                                  <span> {{__('Events')}} </span>
                                                                        </a>
                                                              </li>
                                                              @endcan
                                                              @can('view calendar')
                                                              <li>
                                                                        <a href="#">
                                                                                  <span> {{__('Calendar')}} </span>
                                                                        </a>
                                                              </li>
                                                              @endcan

                                                    </ul>
                                          </div>
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
                                                    <i class="uil-user-md"></i>
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

                      </ul>

                      <!-- End Sidebar -->

                      <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->
  </div>
  <!-- Left Sidebar End -->
