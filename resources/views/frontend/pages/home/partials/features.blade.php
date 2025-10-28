<section class="relative py-24 overflow-hidden bg-gradient-to-b from-white via-indigo-50/30 to-white">
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
				{{ __('features') }}
			</span>
			<h2 class="text-4xl text-center font-bold text-gray-900 py-5 font-tajawal">
				{{ __('our core features') }}
			</h2>
			<p class="text-lg font-normal text-gray-500 max-w-md md:max-w-2xl mx-auto font-tajawal">
				{{ __('provides core features that are essential for the platform.') }}
			</p>
		</div>

		<!-- 🌟 Feature grid -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 place-items-center">
			<x-frontend.feature-card title="{{ __('clinics') }}"
				description="{{ __('view all clinics and their services. You can also add your clinic to the platform.') }}"
				:features="[__('doctors can add their services'), __('clinics can add their services')]"
				icon="fas fa-hospital" svgIcon="{{ asset('frontend/images/clinic.svg') }}"
				iconColor="text-blue-600" bgColor="bg-indigo-50"
				titleColor="group-hover:text-blue-600" hoverBgColor="group-hover:bg-blue-600"
				hoverShadowColor="group-hover:shadow-indigo-400/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('patients') }}"
				description="{{ __('patients can book appointments with clinics and suppliers.') }}"
				:features="[__('patients can book appointments'), __('patients can view their appointments')]"
				icon="fas fa-user" svgIcon="{{ asset('frontend/images/patient.svg') }}"
				iconColor="text-[#0f7986]" bgColor="bg-[#0f7986]"
				titleColor="group-hover:text-[#0f7986]" hoverBgColor="group-hover:bg-[#0f7986]"
				hoverShadowColor="group-hover:shadow-[#0f7986]/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('jobs') }}"
				description="{{ __('find your next career opportunity.') }}"
				:features="[__('jobs can be posted'), __('jobs can be applied to')]"
				icon="fas fa-briefcase" svgIcon="{{ asset('frontend/images/job.svg') }}"
				iconColor="text-[#ffc10d]" bgColor="bg-[#ffc10d]"
				titleColor="group-hover:text-[#ffc10d]" hoverBgColor="group-hover:bg-[#ffc10d]"
				hoverShadowColor="group-hover:shadow-[#ffc10d]/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('rental spaces') }}"
				description="{{ __('find your next rental space.') }}"
				:features="[__('rental spaces can be posted'), __('rental spaces can be booked')]"
				icon="fas fa-building" svgIcon="{{ asset('frontend/images/rental-space.svg') }}"
				iconColor="text-[#e04f5f]" bgColor="bg-[#e04f5f]"
				titleColor="group-hover:text-[#e04f5f]" hoverBgColor="group-hover:bg-[#e04f5f]"
				hoverShadowColor="group-hover:shadow-[#e04f5f]/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('suppliers') }}"
				description="{{ __('view all suppliers and their products. You can also add your supplier to the platform.') }}"
				:features="[__('suppliers can add their products'), __('suppliers can add their services')]"
				icon="fas fa-truck" svgIcon="{{ asset('frontend/images/supplier.svg') }}"
				iconColor="text-[#90dfaa]" bgColor="bg-[#90dfaa]"
				titleColor="group-hover:text-[#90dfaa]" hoverBgColor="group-hover:bg-[#90dfaa]"
				hoverShadowColor="group-hover:shadow-[#90dfaa]/50"
				hoverShadow="group-hover:shadow-2xl" />

			<x-frontend.feature-card title="{{ __('blogs') }}"
				description="{{ __('view all blogs and their posts.') }}"
				:features="[__('blogs can be posted'), __('blogs can be read')]"
				icon="fas fa-blog" iconColor="text-[#ffe6b8]"
				svgIcon="{{ asset('frontend/images/blog.svg') }}" bgColor="bg-[#ffe6b8]"
				titleColor="group-hover:text-[#ffe6b8]" hoverBgColor="group-hover:bg-[#ffe6b8]"
				hoverShadowColor="group-hover:shadow-[#ffe6b8]/50"
				hoverShadow="group-hover:shadow-2xl" />
		</div>
	</div>
</section>