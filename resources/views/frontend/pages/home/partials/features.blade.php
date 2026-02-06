<section id="our-services" class="relative py-24 overflow-hidden bg-gradient-to-b from-white via-indigo-50/30 to-white">
	<!-- 🌸 Animated background shapes -->
	<div class="absolute inset-0 overflow-hidden pointer-events-none">
		<!-- Circle 1 -->
		<div
			class="absolute top-10 left-10 w-40 h-40 bg-indigo-200 rounded-full opacity-30 animate-float-slow">
		</div>
		<!-- Circle 2 -->
		<div
			class="absolute bottom-20 right-20 w-60 h-60 bg-pink-200 rounded-full opacity-30 animate-float">
		</div>
		<!-- Circle 3 -->
		<div
			class="absolute top-1/2 left-1/3 w-32 h-32 bg-blue-200 rounded-full opacity-20 animate-float-slow">
		</div>
		<!-- Blob shape -->
		<div
			class="absolute bottom-10 left-1/4 w-72 h-72 bg-yellow-200 rounded-[60%] opacity-20 animate-blob">
		</div>
	</div>

	<!-- 💎 Content -->
	<div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<div class="mb-14 text-center">
			<span
				class="py-1 px-4 bg-primary-gradient rounded-full text-xs font-medium text-white text-center">
				{{ __('services title') }}
			</span>
			<h2 class="text-4xl text-center font-bold text-gray-900 py-5 font-tajawal">
				{{ __('service sub title') }}
			</h2>
			<!-- <p class="text-lg font-normal text-gray-500 max-w-md md:max-w-2xl mx-auto font-tajawal">
				{{ __('provides core features that are essential for the platform.') }}
			</p> -->
		</div>

		<!-- 🌟 Feature grid -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 place-items-center">
			<x-frontend.feature-card title="{{ __('clinics service') }}"
				description="{{ __('clinics service description') }}"
				:features=" __('clinics service features')" icon="fas fa-hospital"
				href="{{ route('clinics') }}"
				svgIcon="{{ asset('frontend/images/clinic.svg') }}" iconColor="text-blue-600"
				bgColor="bg-indigo-50" titleColor="group-hover:text-blue-600"
				hoverBgColor="group-hover:bg-blue-600"
				hoverShadowColor="group-hover:shadow-indigo-400/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('patients service') }}"
				description="{{ __('patients service description') }}"
				:features="[__('patients can book appointments'), __('patients can view their appointments')]"
				href="{{ route('doctors.index') }}"
				icon="fas fa-user" svgIcon="{{ asset('frontend/images/patient.svg') }}"
				iconColor="text-[#0f7986]" bgColor="bg-[#0f7986]"
				titleColor="group-hover:text-[#0f7986]" hoverBgColor="group-hover:bg-[#0f7986]"
				hoverShadowColor="group-hover:shadow-[#0f7986]/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('jobs service') }}"
				description="{{ __('jobs service description') }}"
				:features=" __('jobs service features')" icon="fas fa-briefcase"
				href="{{ route('jobs') }}"
				svgIcon="{{ asset('frontend/images/job.svg') }}" iconColor="text-[#ffc10d]"
				bgColor="bg-[#ffc10d]" titleColor="group-hover:text-[#ffc10d]"
				hoverBgColor="group-hover:bg-[#ffc10d]"
				hoverShadowColor="group-hover:shadow-[#ffc10d]/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('rental spaces service') }}"
				description="{{ __('rental spaces service description') }}"
				:features=" __('rental spaces service features')" icon="fas fa-building"
				href="{{ route('rental-spaces') }}"
				svgIcon="{{ asset('frontend/images/rental-space.svg') }}"
				iconColor="text-[#e04f5f]" bgColor="bg-[#e04f5f]"
				titleColor="group-hover:text-[#e04f5f]" hoverBgColor="group-hover:bg-[#e04f5f]"
				hoverShadowColor="group-hover:shadow-[#e04f5f]/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('suppliers service') }}"
				description="{{ __('suppliers service description') }}"
				:features=" __('suppliers service features')" icon="fas fa-truck"
				href="{{ route('suppliers') }}"
				svgIcon="{{ asset('frontend/images/supplier.svg') }}" iconColor="text-[#90dfaa]"
				bgColor="bg-[#90dfaa]" titleColor="group-hover:text-[#90dfaa]"
				hoverBgColor="group-hover:bg-[#90dfaa]"
				hoverShadowColor="group-hover:shadow-[#90dfaa]/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('blogs service') }}"
				description="{{ __('blogs service description') }}"
				:features=" __('blogs service features')" icon="fas fa-blog"
				href="{{ route('blogs') }}"
				iconColor="text-[#ffe6b8]" svgIcon="{{ asset('frontend/images/blog.svg') }}"
				bgColor="bg-[#ffe6b8]" titleColor="group-hover:text-[#ffe6b8]"
				hoverBgColor="group-hover:bg-[#ffe6b8]"
				hoverShadowColor="group-hover:shadow-[#ffe6b8]/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('courses service') }}"
				description="{{ __('courses service description') }}"
				:features=" __('courses service features')" icon="fas fa-book"
				href="{{ route('courses') }}"
				iconColor="text-[#0455bf]" svgIcon="{{ asset('frontend/images/course.svg') }}"
				bgColor="bg-[#0455bf]" titleColor="group-hover:text-[#0455bf]"
				hoverBgColor="group-hover:bg-[#0455bf]"
				hoverShadowColor="group-hover:shadow-[#0455bf]/50"
				hoverShadow="group-hover:shadow-2xl" />
		</div>
	</div>
</section>
