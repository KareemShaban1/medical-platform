<!--  jquery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Flowbite JS for dropdowns -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.2/flowbite.min.js"></script>

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
@stack('scripts')
