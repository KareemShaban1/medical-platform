<!--  jquery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Flowbite JS for dropdowns -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.2/flowbite.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- Cart functionality -->
<script src="{{ asset('js/cart.js') }}"></script>

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
	toastr.success(@json(session('success')));
	@endif

	@if(session('error'))
	toastr.error(@json(session('error')));
	@endif

	@if(session('warning'))
	toastr.warning(@json(session('warning')));
	@endif

	@if(session('info'))
	toastr.info(@json(session('info')));
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

<!-- Cart Dropdown Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
	@auth('clinic')
	loadCartData();

	// Reload cart data when dropdown is opened
	const cartButton = document.querySelector('[data-dropdown-toggle="cart-dropdown"]');
	if (cartButton) {
		cartButton.addEventListener('click', function() {
			loadCartData();
		});
	}
	@endauth

	function loadCartData() {
		fetch('{{ route("cart.data") }}')
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					updateCartDropdown(data);
				}
			})
			.catch(error => console.error('Error loading cart:', error));
	}

	function updateCartDropdown(data) {
		const cartCountBadge = document.getElementById('cart-count-badge');
		const emptyMessage = document.getElementById('empty-cart-message');
		const cartItemsList = document.getElementById('cart-items-list');
		const cartFooter = document.getElementById('cart-footer');
		const cartSubtotal = document.getElementById('cart-subtotal');

		// Update cart count
		if (cartCountBadge) {
			cartCountBadge.textContent = data.total_items;
			cartCountBadge.style.display = data.total_items > 0 ? 'flex' : 'none';
		}

		if (data.items.length === 0) {
			emptyMessage.classList.remove('hidden');
			cartItemsList.classList.add('hidden');
			cartFooter.classList.add('hidden');
		} else {
			emptyMessage.classList.add('hidden');
			cartItemsList.classList.remove('hidden');
			cartFooter.classList.remove('hidden');

			// Build cart items HTML
			let itemsHTML = '';
			data.items.forEach(item => {
				const imageUrl = item.product_image ?
					`/storage/${item.product_image}` :
					'';
				itemsHTML += `
                    <li class="px-4 py-3 hover:bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 flex-shrink-0">
                                ${imageUrl ? `<img src="${imageUrl}" alt="${item.product_name}" class="w-full h-full object-cover rounded">` :
                                `<div class="w-full h-full bg-gray-200 rounded"></div>`}
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="text-sm font-medium text-black truncate">${item.product_name}</p>
                                <p class="text-xs text-gray-500">${item.quantity} ×  ${parseFloat(item.price).toFixed(2)} {{ __('EGP') }}</p>
                            </div>
                            <div class="text-sm font-semibold text-black">
                              ${parseFloat(item.total).toFixed(2)}   {{ __('EGP') }} 
                            </div>
                        </div>
                    </li>
                `;
            });

            cartItemsList.innerHTML = itemsHTML;
            cartSubtotal.textContent = parseFloat(data.subtotal).toFixed(2) + " {{ __('EGP') }} " ;
        }
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Select all dropdown toggle buttons
	const dropdownButtons = document.querySelectorAll('[data-dropdown-toggle]');

	dropdownButtons.forEach(button => {
		button.addEventListener('click', function(e) {
			e.stopPropagation();

			const dropdownId = this
				.getAttribute(
					'data-dropdown-toggle'
					);
			const dropdown = document
				.getElementById(
					dropdownId
					);

			// Close all other dropdowns
			document.querySelectorAll(
				'[id$="Dropdown"]'
				).forEach(
				el => {
					if (el !==
						dropdown) {
						el.classList
							.add(
								'hidden');
					}
				});

			// Toggle this dropdown
			dropdown.classList.toggle(
				'hidden');
		});
	});

	// Close all dropdowns when clicking outside
	document.addEventListener('click', function(e) {
		if (!e.target.closest('[data-dropdown-toggle]') && !e.target
			.closest('[id$="Dropdown"]')) {
			document.querySelectorAll('[id$="Dropdown"]')
				.forEach(el => {
					el.classList.add(
						'hidden');
				});
		}
	});
});
</script>


<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

@if(session('subscription_prompt'))
<script>
document.addEventListener('DOMContentLoaded', function() {
	toastr.info(@json(session('subscription_prompt')), @json(__('Subscription Required')), {
		timeOut: 6000,
		closeButton: true,
		progressBar: true,
		onclick: function() {
			window.location.href = '{{ route('home') }}#subscriptions-plans';
		}
	});
});
</script>
@endif


@stack('scripts')

<!-- Banner Click Tracking -->
<script>
function trackBannerClick(bannerId, event, linkUrl, openInNewTab) {
	const url = '{{ url('/api/banners') }}/' + bannerId + '/track-click';
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
	
	// Use fetch with keepalive for reliable tracking (works even if page navigates)
	// keepalive ensures the request completes even if the page unloads
	fetch(url, {
		method: 'POST',
		headers: {
			'X-CSRF-TOKEN': csrfToken,
			'Content-Type': 'application/json',
		},
		keepalive: true // Critical: keeps request alive even if page navigates away
	}).catch(err => {
		console.log('Click tracking failed', err);
	});
	
	// If opening in new tab, don't prevent default - let browser handle it
	// If opening in same tab, prevent default and navigate manually after a short delay
	if (!openInNewTab && event && linkUrl) {
		event.preventDefault();
		// Small delay to ensure tracking request is sent
		setTimeout(() => {
			window.location.href = linkUrl;
		}, 100);
	}
}
</script>
