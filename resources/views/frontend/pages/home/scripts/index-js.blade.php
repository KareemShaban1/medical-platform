@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.2/flowbite.min.js"></script>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
	// Initialize Banner Swiper
	const bannerSwiper = new Swiper('.bannerSwiper', {
		loop: true,
		autoplay: {
			delay: 3000,
			disableOnInteraction: false,
		},
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
			renderBullet: function(index, className) {
				return '<span class="' + className + '">' + (index + 1) +
					"</span>";
			},
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
	});

	// Initialize Clinics Swiper
	const clinicsSwiper = new Swiper('.clinicsSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 4,
				spaceBetween: 40,
			},
			navigation: {
				nextEl: '.clinics-swiper-button-next',
				prevEl: '.clinics-swiper-button-prev',
			},
		},
	});

	const productsSwiper = new Swiper('.productsSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 4,
				spaceBetween: 40,
			},
		},
		navigation: {
			nextEl: '.products-swiper-button-next',
			prevEl: '.products-swiper-button-prev',
		},
	});

	// Initialize Suppliers Swiper
	const suppliersSwiper = new Swiper('.suppliersSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 4,
				spaceBetween: 40,
			},
		},
		navigation: {
			nextEl: '.suppliers-swiper-button-next',
			prevEl: '.suppliers-swiper-button-prev',
		},
	});

	// Initialize Jobs Swiper
	const jobsSwiper = new Swiper('.jobsSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 4,
				spaceBetween: 40,
			},
		},
		navigation: {
			nextEl: '.jobs-swiper-button-next',
			prevEl: '.jobs-swiper-button-prev',
		},
	});

	// Initialize Rental Swiper
	const rentalSwiper = new Swiper('.rentalSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 4,
				spaceBetween: 40,
			},
		},
		navigation: {
			nextEl: '.rental-spaces-swiper-button-next',
			prevEl: '.rental-spaces-swiper-button-prev',
		},
	});

	// Initialize Courses Swiper
	const coursesSwiper = new Swiper('.coursesSwiper', {
		loop: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		breakpoints: {
			640: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1024: {
				slidesPerView: 4,
				spaceBetween: 40,
			},
		},
		navigation: {
			nextEl: '.courses-swiper-button-next',
			prevEl: '.courses-swiper-button-prev',
		},
	});
</script>

@endpush
