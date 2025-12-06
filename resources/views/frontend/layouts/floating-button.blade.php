@php
	$contactPhoneDisplay = '+00201035067680';
	$contactPhoneDial = '201035067680';
	$contactEmail = 'info@tebbplus.com';
@endphp

<div class="fixed bottom-6 right-6 flex flex-col items-center space-y-3 group">
	<!-- whatsapp button -->
	<a href="https://wa.me/{{ $contactPhoneDial }}" target="_blank" rel="noopener"
		class="bg-green-500 opacity-0 translate-y-3 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-green-600 text-white rounded-full w-12 h-12 shadow-lg flex items-center justify-center"
		aria-label="WhatsApp">
		<i class="fab fa-whatsapp"></i>
	</a>

	<!-- phone button -->
	<a href="tel:+{{ $contactPhoneDial }}"
		class="bg-red-500 opacity-0 translate-y-3 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-red-600 text-white rounded-full w-12 h-12 shadow-lg flex items-center justify-center"
		aria-label="Call">
		<i class="fas fa-phone"></i>
	</a>

	<!-- email button -->
	<a href="mailto:{{ $contactEmail }}"
		class="bg-gray-500 text-white opacity-0 translate-y-3 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-gray-600 rounded-full w-12 h-12 shadow-lg flex items-center justify-center"
		aria-label="Email">
		<i class="fas fa-envelope"></i>
	</a>

	<!-- Main Floating Button -->
	<a href="tel:+{{ $contactPhoneDial }}"
		class="animate-bounce focus:animate-none hover:animate-none btn-primary hover:bg-green-700 text-white rounded-full w-14 h-14 shadow-2xl flex items-center justify-center"
		aria-label="Quick call {{ $contactPhoneDisplay }}">
		<i class="fas fa-phone"></i>
	</a>
</div>
