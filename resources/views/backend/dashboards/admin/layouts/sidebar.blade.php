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
                                                            {{__('Admin Dashboard')}}
                                                  </span>
                                        </a>
                              </li>


                              <li class="side-nav-item">
                                        <a data-bs-toggle="collapse" href="#sidebarCategories" aria-expanded="false"
                                                  aria-controls="sidebarEventsReport" class="side-nav-link">
                                                  <i class="uil-money-withdraw"></i>
                                                  <span> {{__('Categories')}} </span>
                                                  <span class="menu-arrow"></span>
                                        </a>
                                        <div class="collapse" id="sidebarCategories">
                                                  <ul class="side-nav-second-level">
                                                            <li>

                                                                      <a href="{{ route('admin.categories.index') }}">
                                                                                <span> {{__('Categories')}} </span>
                                                                      </a>
                                                            </li>
                                                            <li>
                                                                      <a href="#">
                                                                                <span> {{__('Trash Categories')}}
                                                                                </span>
                                                                      </a>
                                                            </li>

                                                  </ul>
                                        </div>
                              </li>

                              <li class="side-nav-item">
                                        <a data-bs-toggle="collapse" href="#sidebarRoles" aria-expanded="false"
                                                  aria-controls="sidebarRoles" class="side-nav-link">
                                                  <i class="uil-users-alt"></i>
                                                  <span> {{__('Roles & Permissions')}} </span>
                                                  <span class="menu-arrow"></span>
                                        </a>
                                        <div class="collapse" id="sidebarRoles">
                                                  <ul class="side-nav-second-level">
                                                            <li>
                                                                      <a href="{{ route('admin.roles.index') }}">
                                                                                <span> {{__('Roles')}} </span>
                                                                      </a>
                                                            </li>
                                                            <li>
                                                                      <a href="{{ route('admin.roles.trash') }}">
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
