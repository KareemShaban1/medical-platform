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
               <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">

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

                   <div id="notifications-list" style="max-height: 230px;" data-simplebar="">
                       <div class="text-center p-3" id="loading-state">notifications
                           <i class="mdi mdi-loading mdi-spin"></i> {{ __('Loading ...') }}
                       </div>
                   </div>

                   <!-- All-->
                   <a href="{{ route('clinic.notifications.index') }}" class="dropdown-item text-center text-primary notify-item notify-all">
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
                   <a href="" class="dropdown-item notify-item">
                       <i class="mdi mdi-account-circle me-1"></i>
                       <span>My Account</span>
                   </a>

                   <div class="dropdown-divider"></div>

                   <form method="POST" action="{{ route('clinic.logout') }}">
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

   <script>
   // Notification functionality
   let notificationDropdownOpen = false;
   let notificationsLoaded = false;

   $(document).ready(function() {

    // Load notifications only once on page load
       loadNotifications();

       // Load notifications when dropdown is opened for the first time
       $('#notification-bell').on('click', function() {
           if (!notificationDropdownOpen && !notificationsLoaded) {
               loadNotifications();
               notificationDropdownOpen = true;
           }
       });

       // Reset dropdown state when closed
       $(document).on('click', function(e) {
           if (!$(e.target).closest('.notification-list').length) {
               notificationDropdownOpen = false;
           }
       });
   });

   function loadNotifications() {
       console.log('Loading notifications...');
       $.get('{{ route("clinic.notifications.latest") }}')
           .done(function(response) {
               console.log('Notifications loaded:', response);
               updateNotificationBadge(response.unread_count);
               displayNotifications(response.notifications);
               notificationsLoaded = true;
           })
           .fail(function(xhr) {
               console.error('Failed to load notifications:', xhr.status, xhr.responseText);
               $('#notifications-list').html(`
                   <div class="text-center p-3 text-muted">
                       <i class="mdi mdi-alert-circle display-4"></i>
                       <p class="mt-2 mb-0">{{ __('Failed to load notifications') }}</p>
                       <small class="d-block">Error: ${xhr.status}</small>
                   </div>
               `);
           });
   }

   function updateNotificationBadge(count) {
       const badge = $('#notification-count');
       const sidebarBadge = $('#sidebar-notification-count');

       if (count > 0) {
           const displayCount = count > 99 ? '99+' : count;
           badge.text(displayCount).show();
           sidebarBadge.text(displayCount).show();
       } else {
           badge.hide();
           sidebarBadge.hide();
       }
   }

   function displayNotifications(notifications) {
       const container = $('#notifications-list');
       console.log('Displaying notifications:', notifications);

       if (!notifications || notifications.length === 0) {
           container.html(`
               <div class="text-center p-3 text-muted">
                   <i class="mdi mdi-bell-off display-4"></i>
                   <p class="mt-2 mb-0">{{ __('No notifications') }}</p>
               </div>
           `);
           return;
       }

       let html = '';
       notifications.forEach(function(notification, index) {
           console.log('Processing notification:', index, notification);

           const typeIcons = {
               'profile_submitted': 'mdi-account-plus text-warning',
               'profile_approved': 'mdi-check-circle text-success',
               'profile_rejected': 'mdi-close-circle text-danger',
               'info': 'mdi-information text-info'
           };

           const icon = typeIcons[notification.type] || 'mdi-bell text-secondary';
           const readClass = notification.read_at ? 'text-muted' : '';
           const actionUrl = notification.action_url || '#';

           html += `
               <div class="dropdown-item notify-item ${readClass}" style="cursor: pointer;"
                    onclick="handleNotificationClick('${notification.id}', '${actionUrl}')">
                   <div class="notify-icon bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                       <i class="mdi ${icon}"></i>
                   </div>
                   <div class="notify-details">
                       <strong>${notification.title || 'Notification'}</strong><br>
                       <small class="text-muted">${notification.message || ''}</small><br>
                       <small class="text-muted">${notification.created_at || ''}</small>
                   </div>
               </div>
           `;
       });

       container.html(html);
   }

   function handleNotificationClick(notificationId, actionUrl) {
       // Mark notification as read
       $.post('{{ route("clinic.notifications.mark-as-read", ":id") }}'.replace(':id', notificationId), {
           _token: '{{ csrf_token() }}'
       }).done(function(response) {
           if (response.status === 'success') {
               // Refresh notifications to update the badge
               loadNotifications();

               // Redirect to the action URL
               if (actionUrl && actionUrl !== '#') {
                   window.location.href = actionUrl;
               }
           }
       });
   }

   function markAllAsRead() {
       $.post('{{ route("clinic.notifications.mark-all-as-read") }}', {
           _token: '{{ csrf_token() }}'
       }).done(function(response) {
           if (response.status === 'success') {
               loadNotifications();
               Swal.fire({
                   icon: 'success',
                   title: '{{ __("Success") }}',
                   text: '{{ __("All notifications marked as read") }}',
                   timer: 2000,
                   showConfirmButton: false
               });
           }
       });
   }
   </script>
