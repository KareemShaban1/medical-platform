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
   let notificationsPage = 1;
   let notificationsPerPage = 8;
   let notificationsHasMore = true;
   let notificationsLoading = false;

   $(document).ready(function() {
       // Load notifications only once on page load
       loadNotifications({ reset: true });

       // Load notifications when dropdown is opened for the first time
       $('#notification-bell').on('click', function() {
           if (!notificationDropdownOpen && !notificationsLoaded) {
               loadNotifications({ reset: true });
               notificationDropdownOpen = true;
           }
       });

       // Reset dropdown state when closed
       $(document).on('click', function(e) {
           if (!$(e.target).closest('.notification-list').length) {
               notificationDropdownOpen = false;
           }
       });

       $('#notifications-load-more').on('click', function() {
           if (!notificationsHasMore || notificationsLoading) {
               return;
           }
           loadNotifications({ append: true });
       });
   });

   function loadNotifications(options) {
       const settings = options || {};
       const reset = !!settings.reset;
       const append = !!settings.append;

       if (notificationsLoading) {
           return;
       }
       notificationsLoading = true;

       console.log('Loading notifications...');
       if (reset) {
           notificationsPage = 1;
           notificationsHasMore = true;
           $('#notifications-load-more').hide();
           $('#notifications-list').html(`
               <div class="text-center p-3" id="loading-state">
                   <i class="mdi mdi-loading mdi-spin"></i> {{ __('Loading notifications...') }}
               </div>
           `);
       }

       if (append) {
           setLoadMoreState(true);
       }

       $.get('{{ route("clinic.notifications.latest") }}', {
               page: notificationsPage,
               per_page: notificationsPerPage
           })
           .done(function(response) {
               console.log('Notifications loaded:', response);
               updateNotificationBadge(response.unread_count);
               displayNotifications(response.notifications, {
                   append: append && !reset
               });
               notificationsHasMore = !!response.has_more;
               if (response.next_page) {
                   notificationsPage = response.next_page;
               }
               toggleLoadMore();
               notificationsLoaded = true;
           })
           .fail(function(xhr) {
               console.error('Failed to load notifications:', xhr.status, xhr.responseText);
               if (!append) {
                   $('#notifications-list').html(`
                       <div class="text-center p-3 text-muted">
                           <i class="mdi mdi-alert-circle display-4"></i>
                           <p class="mt-2 mb-0">{{ __('Failed to load notifications') }}</p>
                           <small class="d-block">Error: ${xhr.status}</small>
                       </div>
                   `);
               }
               toggleLoadMore();
           })
           .always(function() {
               if (append) {
                   setLoadMoreState(false);
               }
               notificationsLoading = false;
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

   function displayNotifications(notifications, options) {
       const container = $('#notifications-list');
       console.log('Displaying notifications:', notifications);
       const settings = options || {};
       const append = !!settings.append;

       if (!notifications || notifications.length === 0) {
           if (!append) {
               container.html(`
                   <div class="text-center p-3 text-muted">
                       <i class="mdi mdi-bell-off display-4"></i>
                       <p class="mt-2 mb-0">{{ __('No notifications') }}</p>
                   </div>
               `);
           }
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
           const isUnread = !notification.read_at;
           const actionUrl = notification.action_url || '#';
           const encodedActionUrl = encodeURIComponent(actionUrl);
           const title = escapeHtml(notification.title || 'Notification');
           const message = escapeHtml(notification.message || '');
           const createdAt = escapeHtml(notification.created_at || '');

           html += `
               <div class="dropdown-item notify-item notification-item ${isUnread ? 'notification-unread' : ''}"
                    role="button"
                    onclick="handleNotificationClick('${notification.id}', '${encodedActionUrl}')">
                   <div class="notification-icon">
                       <i class="mdi ${icon}"></i>
                   </div>
                   <div class="notify-details">
                       <div class="notification-title">${title}</div>
                       ${message ? `<div class="notification-message">${message}</div>` : ''}
                       <div class="notification-time">${createdAt}</div>
                   </div>
                   ${isUnread ? '<span class="notification-dot"></span>' : ''}
               </div>
           `;
       });

       if (append) {
           container.append(html);
       } else {
           container.html(html);
       }
   }

   function handleNotificationClick(notificationId, actionUrl) {
       const decodedUrl = decodeURIComponent(actionUrl || '');
       let redirected = false;
       // Mark notification as read
       $.post('{{ route("clinic.notifications.mark-as-read", ":id") }}'.replace(':id', notificationId), {
           _token: '{{ csrf_token() }}'
       }).done(function(response) {
           if (response && response.status === 'success') {
               // Refresh notifications to update the badge
               loadNotifications({ reset: true });
           }

           const targetUrl = (response && response.action_url) ? response.action_url : decodedUrl;
           if (!redirected && targetUrl && targetUrl !== '#') {
               redirected = true;
               window.location.href = targetUrl;
           }
       }).fail(function() {
           if (!redirected && decodedUrl && decodedUrl !== '#') {
               redirected = true;
               window.location.href = decodedUrl;
           }
       });
   }

   function markAllAsRead() {
       $.post('{{ route("clinic.notifications.mark-all-as-read") }}', {
           _token: '{{ csrf_token() }}'
       }).done(function(response) {
           if (response.status === 'success') {
               loadNotifications({ reset: true });
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

   function toggleLoadMore() {
       const button = $('#notifications-load-more');
       if (notificationsHasMore) {
           button.show();
       } else {
           button.hide();
       }
   }

   function setLoadMoreState(isLoading) {
       const button = $('#notifications-load-more');
       if (isLoading) {
           button.prop('disabled', true).text('{{ __("Loading...") }}');
       } else {
           button.prop('disabled', false).text('{{ __("Load more") }}');
       }
   }

   function escapeHtml(value) {
       return String(value)
           .replace(/&/g, '&amp;')
           .replace(/</g, '&lt;')
           .replace(/>/g, '&gt;')
           .replace(/"/g, '&quot;')
           .replace(/'/g, '&#39;');
   }
   </script>

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
