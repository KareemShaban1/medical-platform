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
     $.get('{{ route("supplier.notifications.latest") }}')
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
             'new_request': 'mdi-clipboard-text text-info',
             'offer_accepted': 'mdi-check-circle text-success',
             'offer_declined': 'mdi-close-circle text-danger',
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
     $.post('{{ route("supplier.notifications.mark-as-read", ":id") }}'.replace(':id', notificationId), {
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
     $.post('{{ route("supplier.notifications.mark-all-as-read") }}', {
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

 @stack('scripts')
