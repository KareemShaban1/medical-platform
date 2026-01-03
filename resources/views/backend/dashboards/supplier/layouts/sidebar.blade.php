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
              <input type="text" id="sidebar-search" class="form-control" placeholder="{{ __('Search menu...') }}"
                  autocomplete="off">
          </div>

          <!--- Sidemenu -->
          <ul class="side-nav">

              <!-- <li class="side-nav-title side-nav-item">Navigation</li> -->

              @hasPermission('view dashboard')
                  <li class="side-nav-item">

                      <a href="{{ route('supplier.dashboard') }}" class="side-nav-link">
                          <i class="uil-home-alt"></i>
                          <span>
                              {{ __('Supplier Dashboard') }}
                          </span>
                      </a>
                  </li>
              @endhasPermission


              @hasPermission('view supplier info')
                  <!-- Supplier Info -->
                  <li class="side-nav-item">
                      <a href="{{ route('supplier.settings.supplier-info') }}" class="side-nav-link">
                          <i class="uil-cog"></i>
                          <span> {{ __('Supplier Info') }} </span>
                      </a>
                  </li>
              @endhasPermission



              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarProducts" aria-expanded="false"
                      aria-controls="sidebarProducts" class="side-nav-link">
                      <i class="uil-money-withdraw"></i>
                      <span> {{ __('Products') }} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarProducts">
                      <ul class="side-nav-second-level">
                          @hasPermission('view products')
                              <li>
                                  <a href="{{ route('supplier.products.index') }}">
                                      <span> {{ __('Products') }} </span>
                                  </a>
                              </li>
                          @endhasPermission
                          @hasPermission('view trash products')
                              <li>
                                  <a href="{{ route('supplier.products.trash') }}">
                                      <span> {{ __('Trash Products') }}
                                      </span>
                                  </a>
                              </li>
                          @endhasPermission

                      </ul>
                  </div>
              </li>

              <!-- Users Management -->
              @hasAnyPermission('view users')
                  <li class="side-nav-item">
                      <a data-bs-toggle="collapse" href="#sidebarUsers" aria-expanded="false" aria-controls="sidebarUsers"
                          class="side-nav-link">
                          <i class="uil-users-alt"></i>
                          <span> {{ __('Users Management') }} </span>
                          <span class="menu-arrow"></span>
                      </a>
                      <div class="collapse" id="sidebarUsers">
                          <ul class="side-nav-second-level">
                              @hasPermission('view users')
                                  <li>
                                      <a href="{{ route('supplier.users.index') }}">
                                          <span> {{ __('Users') }} </span>
                                      </a>
                                  </li>
                              @endhasPermission
                              @hasPermission('view roles')
                                  <li>
                                      <a href="{{ route('supplier.roles.index') }}">
                                          <span> {{ __('Roles') }} </span>
                                      </a>
                                  </li>
                              @endhasPermission

                          </ul>
                      </div>
                  </li>
              @endhasAnyPermission

              @hasAnyPermission('view specialized categories')
                  <!-- Specialized Categories Management -->
                  <li class="side-nav-item">
                      <a data-bs-toggle="collapse" href="#sidebarCategories" aria-expanded="false"
                          aria-controls="sidebarCategories" class="side-nav-link">
                          <i class="uil-tag"></i>
                          <span> {{ __('Specialized Categories') }} </span>
                          <span class="menu-arrow"></span>
                      </a>
                      <div class="collapse" id="sidebarCategories">
                          <ul class="side-nav-second-level">
                              @hasPermission('view specialized categories')
                                  <li>
                                      <a href="{{ route('supplier.specialized-categories.index') }}">
                                          <span> {{ __('My Categories') }}
                                          </span>
                                      </a>
                                  </li>
                              @endhasPermission
                          </ul>
                      </div>
                  </li>
              @endhasAnyPermission

              @hasAnyPermission('view available requests')
                  <!-- Available Requests -->
                  <li class="side-nav-item">
                      <a data-bs-toggle="collapse" href="#sidebarAvailableRequests" aria-expanded="false"
                          aria-controls="sidebarAvailableRequests" class="side-nav-link">
                          <i class="uil-clipboard-notes"></i>
                          <span> {{ __('Available Requests') }} </span>
                          <span class="menu-arrow"></span>
                      </a>
                      <div class="collapse" id="sidebarAvailableRequests">
                          <ul class="side-nav-second-level">
                              @hasPermission('view available requests')
                                  <li>
                                      <a href="{{ route('supplier.available-requests.index') }}">
                                          <span> {{ __('Browse Requests') }}
                                          </span>
                                      </a>
                                  </li>
                              @endhasPermission
                          </ul>
                      </div>
                  </li>
              @endhasAnyPermission

              @hasAnyPermission('view offers')
                  <!-- Offers Management -->
                  <li class="side-nav-item">
                      <a data-bs-toggle="collapse" href="#sidebarOffers" aria-expanded="false" aria-controls="sidebarOffers"
                          class="side-nav-link">
                          <i class="uil-money-withdraw"></i>
                          <span> {{ __('My Offers') }} </span>
                          <span class="menu-arrow"></span>
                      </a>
                      <div class="collapse" id="sidebarOffers">
                          <ul class="side-nav-second-level">
                              @hasPermission('view offers')
                                  <li>
                                      <a href="{{ route('supplier.offers.index') }}">
                                          <span> {{ __('All Offers') }} </span>
                                      </a>
                                  </li>
                              @endhasPermission
                          </ul>
                      </div>
                  </li>
              @endhasAnyPermission

              @hasAnyPermission('view orders')
                  <!-- Orders Management -->
                  <li class="side-nav-item">
                      <a data-bs-toggle="collapse" href="#sidebarOrders" aria-expanded="false"
                          aria-controls="sidebarOrders" class="side-nav-link">
                          <i class="uil-store"></i>
                          <span> {{ __('Orders') }} </span>
                          <span class="menu-arrow"></span>
                      </a>
                      <div class="collapse" id="sidebarOrders">
                          <ul class="side-nav-second-level">
                              @hasPermission('view orders')
                                  <li>
                                      <a href="{{ route('supplier.orders.index') }}">
                                          <span> {{ __('My Orders') }} </span>
                                      </a>
                                  </li>
                              @endhasPermission
                          </ul>
                      </div>
                  </li>
              @endhasAnyPermission

              @hasPermission('view subscriptions')
                  <!-- My Subscription -->
                  <li class="side-nav-item">
                      <a href="{{ route('supplier.subscriptions.index') }}" class="side-nav-link">
                          <i class="uil-credit-card"></i>
                          <span> {{ __('My Subscription') }} </span>
                      </a>
                  </li>
              @endhasPermission

              @hasPermission('view payouts')
                  <!-- Payouts -->
                  <li class="side-nav-item">
                      <a href="{{ route('supplier.payouts.index') }}" class="side-nav-link">
                          <i class="uil-money-withdraw"></i>
                          <span> {{ __('Payouts') }} </span>
                      </a>
                  </li>
              @endhasPermission

              @hasPermission('view notifications')
                  <!-- Notifications -->
                  <li class="side-nav-item">
                      <a href="{{ route('supplier.notifications.index') }}" class="side-nav-link">
                          <i class="uil-bell"></i>
                          <span> {{ __('Notifications') }} </span>
                          <span class="badge bg-danger rounded-pill" id="sidebar-notification-count"
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
