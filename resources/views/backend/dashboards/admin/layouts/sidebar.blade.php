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

            <!-- Categories -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCategories" aria-expanded="false"
                    aria-controls="sidebarEventsReport" class="side-nav-link">

                    <i class="uil-list-ul"></i>
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



                    </ul>
                </div>
            </li>

            <!-- Roles Management -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRoles" aria-expanded="false"
                    aria-controls="sidebarRoles" class="side-nav-link">
                    <i class="uil-users-alt"></i>
                    <span> {{__('Roles & Permissions')}} </span>
                    <span class="menu-arrow"></span>
                </a>
            </li>
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

            <!-- Admin Users -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarAdminUsers" aria-expanded="false"
                    aria-controls="sidebarAdminUsers" class="side-nav-link">
                    <i class="uil-user"></i>
                    <span> {{__('Admin Users')}} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarAdminUsers">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.admin-users.index') }}">
                                <span> {{__('Admin Users')}} </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.admin-users.trash') }}">
                                <span> {{__('Trash Admin Users')}} </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Clinics -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarClinics" aria-expanded="false"
                    aria-controls="sidebarClinics" class="side-nav-link">
                    <i class="uil-building"></i>
                    <span> {{__('Clinics')}} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarClinics">
                    <ul class="side-nav-second-level">
                        <li>

                            <a href="{{ route('admin.clinics.index') }}">
                                <span> {{__('Clinics')}} </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.doctor-profiles.index') }}">
                                <span> {{__('Doctor Profiles')}} </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>

            <!-- Jobs -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarJobs" aria-expanded="false"
                    aria-controls="sidebarJobs" class="side-nav-link">
                    <i class="uil-user"></i>
                    <span> {{__('Jobs')}} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarJobs">
                    <ul class="side-nav-second-level">
                        <li>

                            <a href="{{ route('admin.jobs.index') }}">
                                <span> {{__('Jobs')}} </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.jobs.trash') }}">
                                <span> {{__('Trash Jobs')}} </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>




            <!-- Suppliers -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarSuppliers" aria-expanded="false"
                    aria-controls="sidebarEventsReport" class="side-nav-link">
                    <i class="uil-user"></i>
                    <span> {{__('Suppliers')}} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarSuppliers">
                    <ul class="side-nav-second-level">
                        <li>

                            <a href="{{ route('admin.suppliers.index') }}">
                                <span> {{__('Suppliers')}} </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>

            <!-- Notifications -->
            <li class="side-nav-item">
                <a href="{{ route('admin.notifications.index') }}" class="side-nav-link">
                    <i class="uil-bell"></i>
                    <span> {{__('Notifications')}} </span>
                    <span class="badge bg-danger rounded-pill" id="sidebar-notification-count" style="display: none;">0</span>
                </a>
            <!-- Rental Spaces -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRentalSpaces" aria-expanded="false"
                    aria-controls="sidebarEventsReport" class="side-nav-link">
                    <i class="uil-home-alt"></i>
                    <span> {{__('Rental Spaces')}} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRentalSpaces">
                    <ul class="side-nav-second-level">
                        <li>

                            <a href="{{ route('admin.rental-spaces.index') }}">
                                <span> {{__('Rental Spaces')}} </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.rental-spaces.trash') }}">
                                <span> {{__('Trash Rental Spaces')}} </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>

            <!-- Blogs -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarBlogs" aria-expanded="false"
                    aria-controls="sidebarBlogs" class="side-nav-link">
                    <i class="uil-book-alt"></i>
                    <span> {{__('Blogs')}} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarBlogs">
                    <ul class="side-nav-second-level">
                        <li>

                            <a href="{{ route('admin.blog-categories.index') }}">
                                <span> {{__('Blog Categories')}} </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.blog-categories.trash') }}">
                                <span> {{__('Trash Blog Categories')}} </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.blog-posts.index') }}">
                                <span> {{__('Blog Posts')}} </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.blog-posts.trash') }}">
                                <span> {{__('Trash Blog Posts')}} </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>


            <!-- Courses -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCourses" aria-expanded="false"
                    aria-controls="sidebarCourses" class="side-nav-link">
                    <i class="uil-book-open"></i>
                    <span> {{__('Courses')}} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCourses">
                    <ul class="side-nav-second-level">
                        <li>

                            <a href="{{ route('admin.courses.index') }}">
                                <span> {{__('Courses')}} </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.courses.trash') }}">
                                <span> {{__('Trash Courses')}} </span>
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
