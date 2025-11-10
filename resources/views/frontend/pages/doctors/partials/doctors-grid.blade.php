@forelse($doctors as $doctor)
<div class="doctor-card card overflow-hidden bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300"
	data-speciality="{{ $doctor->speciality_id ?? '' }}"
	data-featured="{{ $doctor->is_featured ? '1' : '0' }}"
	data-has-clinic="{{ $doctor->clinicUser && $doctor->clinicUser->clinic_id ? '1' : '0' }}"
	data-name="{{ strtolower($doctor->name) }}">

	<!-- Featured Badge -->
	@if($doctor->is_featured)
	<div class="featured-badge">
		<i class="fas fa-check-circle"></i>
		<span>{{ __('Featured') }}</span>
	</div>
	@endif

	<!-- Doctor Photo Section -->
	<div class="relative h-48 bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center overflow-hidden">
		@if($doctor->profile_photo_url)
			<img src="{{ $doctor->profile_photo_url }}" alt="{{ $doctor->name }}"
				class="w-full h-full object-cover">
		@else
			<div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-5xl font-bold">
				{{ strtoupper(substr($doctor->name, 0, 1)) }}
			</div>
		@endif
	</div>

	<div class="p-5">
		<!-- Doctor Name with Verified Badge -->
		<div class="mb-3">
			<a href="{{ route('doctors.show', $doctor->id) }}" class="font-bold text-xl text-gray-900 hover:text-blue-600 transition-colors inline-flex items-center">
				{{ $doctor->name }}
				@if($doctor->is_featured)
					<span class="verified-badge">
						<i class="fas fa-check-circle"></i>
						<span>{{ __('Verified') }}</span>
					</span>
				@endif
			</a>
		</div>

		<!-- Speciality -->
		@if($doctor->speciality)
		<div class="mb-3">
			<span class="specialization-tag">
				<i class="fas fa-stethoscope mr-1"></i>
				{{ $doctor->speciality->name_en }}
			</span>
		</div>
		@endif

		<!-- Clinic Information -->
		@if($doctor->clinicUser && $doctor->clinicUser->clinic)
		<div class="mb-3 flex items-center text-sm text-gray-600">
			<i class="fas fa-hospital text-green-600 mr-2"></i>
			<span class="clinic-badge">
				{{ $doctor->clinicUser->clinic->name }}
			</span>
		</div>
		@else
		<div class="mb-3 flex items-center text-sm text-gray-500">
			<i class="fas fa-user-md text-blue-500 mr-2"></i>
			<span>{{ __('Independent Doctor') }}</span>
		</div>
		@endif

		<!-- Experience -->
		@if($doctor->years_experience)
		<div class="mb-3 flex items-center text-sm text-gray-600">
			<i class="fas fa-briefcase text-indigo-500 mr-2"></i>
			<span>{{ $doctor->years_experience }} {{ __('years experience') }}</span>
		</div>
		@endif

		<!-- Bio Preview -->
		@if($doctor->bio)
		<p class="text-gray-600 text-sm mb-4 line-clamp-2">
			{{ Str::limit($doctor->bio, 100) }}
		</p>
		@endif

		<!-- Specialties Tags -->
		@if($doctor->specialties && count($doctor->specialties) > 0)
		<div class="mb-4 flex flex-wrap gap-2">
			@foreach(array_slice($doctor->specialties, 0, 3) as $specialty)
				<span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
					{{ $specialty }}
				</span>
			@endforeach
			@if(count($doctor->specialties) > 3)
				<span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
					+{{ count($doctor->specialties) - 3 }}
				</span>
			@endif
		</div>
		@endif

		<!-- View Profile Button -->
		<a href="{{ route('doctors.show', $doctor->id) }}" class="btn-primary w-full text-center block">
			<i class="fas fa-user-md mr-2"></i>
			{{ __('view profile') }}
		</a>
	</div>
</div>
@empty
<div class="col-span-full text-center py-12">
	<div class="text-gray-500">
		<i class="fas fa-user-md text-6xl mb-4"></i>
		<h3 class="text-lg font-semibold mb-2">{{ __('no doctors found') }}</h3>
		<p>{{ __('try adjusting your search criteria or filters') }}</p>
	</div>
</div>
@endforelse

