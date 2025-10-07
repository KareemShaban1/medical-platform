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


 <!-- Datatable Init js -->
 <!-- <script src="{{asset('backend/assets/js/pages/demo.datatable-init.js')}}"></script> -->

 <!-- third party js ends -->

 <!-- demo app -->
 <!-- <script src="{{asset('backend/assets/js/pages/demo.dashboard.js')}}"></script> -->

 <!-- SweetAlert2 -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <!-- Toastr JS -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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

 <!-- Simple Toast Configuration -->
 <script>
$(document).ready(function() {
	// Configure Toastr
	toastr.options = {
		"closeButton": true,
		"debug": false,
		"newestOnTop": true,
		"progressBar": true,
		"positionClass": "toast-top-right",
		"preventDuplicates": false,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	};

	// Handle Laravel session messages
	@if(session('success'))
	toastr.success('{{ session('success') }}');
	@endif

	@if(session('error'))
	toastr.error('{{ session('error') }}');
	@endif

	@if(session('warning'))
	toastr.warning('{{ session('warning') }}');
	@endif

	@if(session('info'))
	toastr.info('{{ session('info') }}');
	@endif

	// Handle validation errors
	@if($errors->any())
	@foreach($errors->all() as $error)
	toastr.error('{{ $error }}');
	@endforeach
	@endif
});

// Global toast functions
function toast_success(message) {
	toastr.success(message);
}

function toast_error(message) {
	toastr.error(message);
}

function toast_warning(message) {
	toastr.warning(message);
}

function toast_info(message) {
	toastr.info(message);
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