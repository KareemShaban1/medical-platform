 <!-- bundle -->
 <script src="{{asset('backend/assets/js/vendor.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/app.min.js')}}"></script>

 <!-- third party js -->
 <!-- <script src="{{asset('backend/assets/js/vendor/apexcharts.min.js')}}"></script> -->
 <script src="{{asset('backend/assets/js/vendor/jquery-jvectormap-1.2.2.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/jquery-jvectormap-world-mill-en.js')}}"></script>

 <!-- Datatables js -->
 <script src="{{asset('backend/assets/js/vendor/jquery.dataTables.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/dataTables.bootstrap5.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/dataTables.responsive.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/responsive.bootstrap5.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/dataTables.buttons.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/buttons.bootstrap5.min.js')}}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
 <script src="{{asset('backend/assets/js/vendor/buttons.html5.min.js')}} "></script>
 <script src="{{asset('backend/assets/js/vendor/buttons.flash.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/buttons.print.min.js')}}"></script>
 <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
 <!-- Charts -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


 <!-- Datatable Init js -->
 <!-- <script src="{{asset('backend/assets/js/pages/demo.datatable-init.js')}}"></script> -->

 <!-- third party js ends -->

 <!-- demo app -->
 <!-- <script src="{{asset('backend/assets/js/pages/demo.dashboard.js')}}"></script> -->

 <!-- SweetAlert2 -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <script>
const languages = {
          @if(App::getLocale() == 'en')
          en: {
                    paginate: {
                              previous: "<i class='mdi mdi-chevron-left'></i> Previous",
                              next: "Next <i class='mdi mdi-chevron-right'></i>"
                    },
                    info: "Showing records _START_ to _END_ of _TOTAL_",
                    lengthMenu: "Display _MENU_ records",
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    zeroRecords: "No matching records found",
                    infoEmpty: "No records to display",
                    infoFiltered: "(filtered from _MAX_ total records)"
          },
          @else
          ar: {
                    paginate: {
                              previous: "<i class='mdi mdi-chevron-right'></i> السابق",
                              next: "التالي <i class='mdi mdi-chevron-left'></i>"
                    },
                    info: "عرض السجلات من _START_ إلى _END_ من إجمالي _TOTAL_ سجلات",
                    lengthMenu: "عرض _MENU_ سجلات",
                    search: "_INPUT_",
                    searchPlaceholder: "بحث...",
                    zeroRecords: "لا توجد سجلات مطابقة",
                    infoEmpty: "لا توجد سجلات للعرض",
                    infoFiltered: "(تمت التصفية من إجمالي _MAX_ سجلات)"
          }
          @endif
};

const language = '{{ App::getLocale() }}';
 </script>

 <!-- Supplier Notification System -->
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

     $.get('{{ route("supplier.notifications.latest") }}', {
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
             'new_request': 'mdi-clipboard-text text-info',
             'offer_accepted': 'mdi-check-circle text-success',
             'offer_declined': 'mdi-close-circle text-danger',
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
     $.post('{{ route("supplier.notifications.mark-as-read", ":id") }}'.replace(':id', notificationId), {
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
     $.post('{{ route("supplier.notifications.mark-all-as-read") }}', {
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

<script>
          document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('leftside-menu-container');
            const input = document.getElementById('sidebar-search');
            if (!container || !input) return;

            const getAllLinks = () => Array.from(container.querySelectorAll('.side-nav a, .side-nav-second-level a'));
            const getTogglerForCollapse = (collapseEl) =>
                container.querySelector(`a[data-bs-toggle="collapse"][href="#${collapseEl.id}"]`);

            const expandCollapse = (collapseEl) => {
             if (!collapseEl) return;
             if (!collapseEl.classList.contains('show')) {
               collapseEl.classList.add('show');
               collapseEl.dataset.openedBySearch = '1';
             }
             const toggler = getTogglerForCollapse(collapseEl);
             if (toggler) toggler.setAttribute('aria-expanded', 'true');
           };

           const collapseIfOpenedBySearch = (collapseEl) => {
             if (!collapseEl) return;
             if (collapseEl.dataset.openedBySearch === '1') {
               collapseEl.classList.remove('show');
               delete collapseEl.dataset.openedBySearch;
               const toggler = getTogglerForCollapse(collapseEl);
               if (toggler) toggler.setAttribute('aria-expanded', 'false');
             }
           };

           const clearHighlights = () => {
             getAllLinks().forEach(a => a.classList.remove('sidebar-highlight'));
           };

           const clearSearchState = () => {
             clearHighlights();
             // Collapse sections we expanded due to a previous search
             container.querySelectorAll('.collapse').forEach(collapseIfOpenedBySearch);
             // Show everything back
             container.querySelectorAll('.side-nav > li.side-nav-item').forEach(li => li.classList.remove('d-none'));
             container.querySelectorAll('.side-nav-second-level > li').forEach(li => li.classList.remove('d-none'));
             container.querySelectorAll('.collapse').forEach(col => col.classList.remove('d-none'));
           };

           input.addEventListener('input', function () {
             const q = (input.value || '').trim().toLowerCase();
             if (!q) {
               clearSearchState();
               return;
             }

             clearHighlights();
             let firstMatch = null;

             // Reset visibility: hide everything by default for search state
             container.querySelectorAll('.side-nav > li.side-nav-item').forEach(li => li.classList.add('d-none'));
             container.querySelectorAll('.side-nav-second-level > li').forEach(li => li.classList.add('d-none'));
             container.querySelectorAll('.collapse').forEach(col => col.classList.add('d-none'));

             // Track sections (collapse containers) that have any match
             const matchedCollapses = new Set();

             getAllLinks().forEach(link => {
               const text = (link.textContent || '').trim().toLowerCase();
               if (text && text.includes(q)) {
                 link.classList.add('sidebar-highlight');
                 if (!firstMatch) firstMatch = link;

                 // Show this link's LI
                 const linkLi = link.closest('li');
                 if (linkLi) linkLi.classList.remove('d-none');

                 // If it's inside a collapse, show and expand its collapse and show the toggler item
                 const parentCollapse = link.closest('.collapse');
                 if (parentCollapse) {
                   matchedCollapses.add(parentCollapse);
                   parentCollapse.classList.remove('d-none');
                   expandCollapse(parentCollapse);
                   const toggler = getTogglerForCollapse(parentCollapse);
                   if (toggler) {
                     const togglerLi = toggler.closest('li.side-nav-item');
                     if (togglerLi) togglerLi.classList.remove('d-none');
                   }
                 } else {
                   // Top-level direct link: show its parent li
                   const topLi = link.closest('li.side-nav-item');
                   if (topLi) topLi.classList.remove('d-none');
                 }
               }
             });

             // If no matches at all, reset to show everything
             const hasAny = !!firstMatch;
             if (!hasAny) {
               clearSearchState();
               return;
             }

             // Optionally scroll first match into view for better UX
             if (firstMatch) {
               try { firstMatch.scrollIntoView({ block: 'nearest' }); } catch (e) {}
             }
           });
         });
        </script>

 @stack('scripts')
