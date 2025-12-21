   <!-- Topbar Start -->
   <div class="navbar-custom">
       <ul class="list-unstyled topbar-menu float-end mb-0">

           <li class="dropdown topbar-dropdown">
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
               <a class="nav-link dropdown-toggle arrow-none notification-bell" data-bs-toggle="dropdown" href="#"
                   role="button" aria-haspopup="false" aria-expanded="false" id="notification-bell">
                   <i class="dripicons-bell noti-icon"></i>
                   <span class="noti-icon-badge" id="notification-count" style="display: none;">0</span>
               </a>
               <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg notification-dropdown">

                   <div class="dropdown-item noti-title">
                       <h5 class="m-0">
                           <span class="float-end">
                               <a href="javascript: void(0);" class="text-dark" onclick="markAllAsRead()">
                                   <small>Clear All</small>
                               </a>
                           </span>{{ __('Notifications') }}
                       </h5>
                   </div>

                   <div id="notifications-list" class="notification-list-scroll" data-simplebar="">
                       <div class="text-center p-3" id="loading-state">
                           <i class="mdi mdi-loading mdi-spin"></i> {{ __('Loading notifications...') }}
                       </div>
                   </div>

                   <div class="notification-footer px-2 pb-2">
                       <button type="button" class="btn btn-sm btn-light w-100" id="notifications-load-more" style="display: none;">
                           {{ __('Load more') }}
                       </button>
                   </div>

                   <a href="{{ route('admin.notifications.index') }}" class="dropdown-item text-center text-primary notify-item notify-all">
                       {{ __('View All') }}
                   </a>

               </div>
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


                   <form method="POST" action="{{ route('admin.logout') }}">
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

/* Scrollbar styling */
.notification-list-scroll::-webkit-scrollbar {
    width: 6px;
}

.notification-list-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.notification-list-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.notification-list-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.notification-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
    position: relative;
    cursor: pointer;
    background: #ffffff;
}

.notification-item:hover {
    background-color: #f8fafc;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item .notify-details {
    flex: 1;
    min-width: 0;
    padding-right: 24px;
}

.notification-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.notification-unread {
    background: #f8fafc;
}

.notification-unread .notification-icon {
    background: #dbeafe;
    color: #2563eb;
}

.notification-title {
    font-weight: 600;
    color: #0f172a;
    line-height: 1.5;
    margin-bottom: 4px;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notification-message {
    color: #64748b;
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-time {
    color: #94a3b8;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.notification-time::before {
    content: '•';
    font-size: 8px;
}

.notification-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #3b82f6;
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
}

.dropdown-item.noti-title {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 20px;
    position: sticky;
    top: 0;
    z-index: 10;
}

.dropdown-item.noti-title h5 {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.dropdown-item.noti-title .float-end a {
    color: #3b82f6;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
}

.dropdown-item.noti-title .float-end a:hover {
    color: #2563eb;
}

.notification-footer {
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 12px 16px;
}

.notification-footer .btn {
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    padding: 10px 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
}

.notification-footer .btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.dropdown-item.notify-all {
    background: #3b82f6;
    color: white !important;
    text-align: center;
    font-weight: 600;
    font-size: 14px;
    padding: 14px;
    border: none;
    margin: 0;
}

.dropdown-item.notify-all:hover {
    background: #2563eb;
}

/* Empty state styling */
.text-center.p-3.text-muted {
    padding: 48px 24px !important;
}

.text-center.p-3.text-muted i.display-4 {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 12px;
}

.text-center.p-3.text-muted p {
    color: #64748b;
    font-size: 14px;
    font-weight: 500;
}

/* Loading state */
#loading-state {
    padding: 48px 24px !important;
    color: #64748b;
}

#loading-state i {
    font-size: 2rem;
    color: #3b82f6;
}

/* Badge styling */
.noti-icon-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #e11d48;
    color: #0f172a;
    font-size: 12px;
    font-weight: 800;
    padding: 0 6px;
    height: 20px;
    min-width: 20px;
    line-height: 1;
    border-radius: 999px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(225, 29, 72, 0.45);
    border: 2px solid #ffffff;
    z-index: 2;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transform: translate(50%, -50%);
}

.notification-bell {
    position: relative;
}

.notification-bell .noti-icon {
    position: relative;
    z-index: 1;
}

/* Icon color variations */
.mdi-account-plus.text-warning {
    color: #f59e0b !important;
}

.mdi-check-circle.text-success {
    color: #10b981 !important;
}

.mdi-close-circle.text-danger {
    color: #ef4444 !important;
}

.mdi-information.text-info {
    color: #3b82f6 !important;
}

.mdi-bell.text-secondary {
    color: #64748b !important;
}

/* Responsive design */
@media (max-width: 576px) {
    .notification-dropdown {
        min-width: 100vw;
        width: 100vw;
        left: 0 !important;
        right: 0 !important;
        margin: 0;
        border-radius: 0;
    }

    .notification-list-scroll {
        max-height: 60vh;
    }

    .notification-item {
        padding: 14px 16px;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
}
</style>
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

       $.get('{{ route("admin.notifications.latest") }}', {
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
       $.post('{{ route("admin.notifications.mark-as-read", ":id") }}'.replace(':id', notificationId), {
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
       $.post('{{ route("admin.notifications.mark-all-as-read") }}', {
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
