   <!-- Topbar Start -->
   <div class="navbar-custom">
       <ul class="list-unstyled topbar-menu float-end mb-0">

           <li class="dropdown notification-list topbar-dropdown">
               <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#"
                   role="button" aria-haspopup="false" aria-expanded="false">
                   @if (App::getLocale() == 'ar')
                   {{ LaravelLocalization::getCurrentLocaleName() }}
                   <img src="{{ asset('backend/assets/images/flags/eg.png') }}" alt="">
                   @else
                   {{ LaravelLocalization::getCurrentLocaleName() }}
                   <img src="{{ asset('backend/assets/images/flags/us.png') }}" alt="">
                   @endif
               </a>

               <div
                   class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu">

                   @foreach (LaravelLocalization::getSupportedLocales() as $localeCode =>
                   $properties)
                   <a class="dropdown-item notify-item" rel="alternate"
                       hreflang="{{ $localeCode }}"
                       href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                       {{ $properties['native'] }}
                   </a>
                   @endforeach

               </div>
           </li>



           <!-- Notifications -->
           <li class="dropdown notification-list">
               <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#"
                   role="button" aria-haspopup="false" aria-expanded="false" id="notification-bell">
                   <i class="dripicons-bell noti-icon"></i>
                   <span class="noti-icon-badge" id="notification-count" style="display: none;">0</span>
               </a>
               <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg notification-dropdown">

                   <!-- item-->
                   <div class="dropdown-item noti-title">
                       <h5 class="m-0">
                           <span class="float-end">
                               <a href="javascript: void(0);" class="text-dark" onclick="markAllAsRead()">
                                   <small>Clear All</small>
                               </a>
                           </span>{{ __('Notifications') }}
                       </h5>
                   </div>

                   <div id="notifications-list" class="notification-list-scroll">
                       <div class="text-center p-3" id="loading-state">
                           <i class="mdi mdi-loading mdi-spin"></i> {{ __('Loading notifications...') }}
                       </div>
                   </div>

                   <div class="notification-footer text-center p-2">
                       <button type="button" class="btn btn-sm btn-light w-100" id="notifications-load-more" style="display: none;">
                           {{ __('Load more') }}
                       </button>
                   </div>

                   <!-- All-->
                   <a href="{{ route('supplier.notifications.index') }}" class="dropdown-item text-center text-primary notify-item notify-all">
                       {{ __('View All') }}
                   </a>

               </div>
           </li>


           <li class="notification-list">
               <a class="nav-link end-bar-toggle" href="javascript: void(0);">
                   <i class="dripicons-gear noti-icon"></i>
               </a>
           </li>

           <li class="dropdown notification-list">
               <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown"
                   href="#" role="button" aria-haspopup="false" aria-expanded="false">
                   <span class="account-user-avatar">
                       <img src="{{asset('backend/assets/images/users/user.png')}}"
                           alt="user-image" class="rounded-circle">
                   </span>
                   <span>
                       <span class="account-user-name">
                           {{ Auth::user()->name ?? 'Admin' }} </span>
                       <span
                           class="account-position">{{Auth::user()->roles[0]->name ?? 'Admin'}}</span>
                   </span>
               </a>
               <div
                   class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                   <!-- item-->
                   <div class=" dropdown-header noti-title">
                       <h6 class="text-overflow m-0">Welcome !</h6>
                   </div>

                   <!-- item-->
                   <a href="{{ route('home') }}" class="dropdown-item notify-item">
                       <i class="uil-home-alt me-1"></i>
                       <span>{{ __('Back to Home') }}</span>
                   </a>

                   <!-- item-->
                   <a href="" class="dropdown-item notify-item">
                       <i class="mdi mdi-account-circle me-1"></i>
                       <span>{{ __('My Account') }}</span>
                   </a>

                   <div class="dropdown-divider"></div>


                   <form method="POST" action="{{ route('supplier.logout') }}">
                       @csrf
                       <a class="dropdown-item" href="#"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                           <i class="mdi mdi-lock-outline me-1"></i>
                           {{ __('Logout') }}
                       </a>
                   </form>


               </div>
           </li>

       </ul>
       <button class="button-menu-mobile open-left">
           <i class="mdi mdi-menu"></i>
       </button>

   </div>
   <!-- end Topbar -->

<style>
.notification-dropdown {
    min-width: 380px;
    max-width: 420px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 16px;
    overflow: hidden;
}

.notification-list-scroll {
    max-height: 420px;
    overflow-y: auto;
    background: #ffffff;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: background 0.2s ease;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background: rgba(0, 0, 0, 0.03);
}

.notification-unread {
    background: rgba(13, 110, 253, 0.05);
}

.notification-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.notification-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
}

.notification-message {
    font-size: 13px;
    color: #6c757d;
    line-height: 1.3;
}

.notification-time {
    font-size: 12px;
    color: #9aa0a6;
    margin-top: 4px;
}

.notification-dot {
    width: 8px;
    height: 8px;
    background: #0d6efd;
    border-radius: 50%;
    margin-left: auto;
    margin-top: 6px;
}

.notification-footer {
    background: #f8f9fa;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

@media (max-width: 576px) {
    .notification-dropdown {
        min-width: 320px;
        max-width: 100%;
    }

    .notification-item {
        padding: 10px 12px;
    }

    .notification-icon {
        width: 32px;
        height: 32px;
        font-size: 16px;
    }
}
</style>
