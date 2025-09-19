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
                                                              {{__('Supplier Dashboard')}}
                                                    </span>
                                          </a>
                                </li>





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
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#sidebarProducts" aria-expanded="false"
                                              aria-controls="sidebarProducts" class="side-nav-link">
                                              <i class="uil-money-withdraw"></i>
                                              <span> {{__('Products')}} </span>
                                              <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse" id="sidebarProducts">
                                              <ul class="side-nav-second-level">
                                                        <li>

                                                                  <a href="{{ route('supplier.products.index') }}">
                                                                            <span> {{__('Products')}} </span>
                                                                  </a>
                                                        </li>
                                                        <li>
                                                                  <a href="{{ route('supplier.products.trash') }}">
                                                                            <span> {{__('Trash Products')}}
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
                                                                        <a href="{{ route('supplier.roles.index') }}">
                                                                                  <span> {{__('Roles')}} </span>
                                                                        </a>
                                                              </li>
                                                              <li>
                                                                        <a href="{{ route('supplier.roles.trash') }}">
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
