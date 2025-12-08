@pushOnce('styles')
<style>
.halo {
	position: absolute;
	inset: 0;
	background: radial-gradient(circle at 30% 20%, rgba(245,158,11,0.16), transparent 45%), radial-gradient(circle at 80% 0%, rgba(59,130,246,0.16), transparent 40%);
	pointer-events: none;
}

.card-gradient {
	background: linear-gradient(145deg, #ffffff, #f9fafb);
}

.share-pill {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	padding: 10px 14px;
	border-radius: 9999px;
	background: linear-gradient(135deg, #111827, #f59e0b);
	color: #fff;
	font-weight: 700;
	text-decoration: none;
	transition: transform 0.2s ease, box-shadow 0.2s ease;
	box-shadow: 0 10px 24px rgba(245, 158, 11, 0.25);
	border: 1px solid rgba(255,255,255,0.1);
}

.share-pill:hover {
	transform: translateY(-2px);
	box-shadow: 0 12px 30px rgba(245, 158, 11, 0.35);
	color: #fff;
}
.doctor-card {
	position: relative;
}
.card-gradient {
	background: linear-gradient(145deg, #ffffff, #f9fafb);
}

.pro-badge-chip {
	display: inline-flex;
	align-items: center;
	gap: 4px;
	padding: 0;
	border-radius: 9999px;
	background: transparent;
	color: #1d9bf0;
	font-weight: 700;
	font-size: 14px;
}

.pro-badge-chip i {
	color: #1d9bf0;
}

.pro-card {
	position: relative;
	overflow: hidden;
	border: 1px solid rgba(245, 158, 11, 0.35);
	box-shadow: 0 16px 40px rgba(245, 158, 11, 0.18);
}

.pro-card::after {
	content: '';
	position: absolute;
	inset: 0;
	background: radial-gradient(circle at 10% 20%, rgba(245,158,11,0.15), transparent 35%), radial-gradient(circle at 90% 0%, rgba(17,24,39,0.25), transparent 45%);
	pointer-events: none;
}
</style>
@endPushOnce
@pushOnce('scripts')
<script>
document.addEventListener('click', function(e) {
	const btn = e.target.closest('.copy-share-link');
	if (!btn) return;
	const url = btn.dataset.url;
	if (!url) return;

	const handleSuccess = () => showToast();

	if (navigator.clipboard && navigator.clipboard.writeText) {
		navigator.clipboard.writeText(url).then(handleSuccess);
	} else {
		const el = document.createElement('textarea');
		el.value = url;
		document.body.appendChild(el);
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
		handleSuccess();
	}
});

function showToast() {
	const toast = document.createElement('div');
	toast.textContent = '{{ __("Link copied!") }}';
	toast.style.position = 'fixed';
	toast.style.bottom = '24px';
	toast.style.right = '24px';
	toast.style.padding = '12px 16px';
	toast.style.background = 'linear-gradient(135deg, #111827, #f59e0b)';
	toast.style.color = '#fff';
	toast.style.borderRadius = '9999px';
	toast.style.boxShadow = '0 12px 30px rgba(0,0,0,0.25)';
	toast.style.zIndex = '9999';
	toast.style.transition = 'opacity .3s ease';
	document.body.appendChild(toast);
	setTimeout(() => {
		toast.style.opacity = '0';
		setTimeout(() => toast.remove(), 300);
	}, 1500);
}
</script>
@endPushOnce

@forelse($doctors as $doctor)
@php
    $proService = app(\App\Services\ProfessionalBioService::class);
    $hasProBio = $proService->hasForDoctor($doctor);
    $profileSlug = $hasProBio ? $proService->ensureSlug($doctor, 'doctor') : null;
    $profileUrl = $hasProBio
        ? route('doctors.show', $profileSlug)
        : route('doctors.show', $doctor->id);
    $shareUrl = $hasProBio ? $proService->getShareUrl($doctor, 'doctor') : null;
@endphp
<div class="doctor-card card overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300 {{ $hasProBio ? 'pro-card card-gradient' : 'bg-white' }}"
	data-speciality="{{ $doctor->speciality_id ?? '' }}"
	data-featured="{{ $doctor->is_featured ? '1' : '0' }}"
	data-has-clinic="{{ $doctor->clinicUser && $doctor->clinicUser->clinic_id ? '1' : '0' }}"
	data-name="{{ strtolower($doctor->name) }}">
	@if($hasProBio)
		<div class="halo"></div>
	@endif

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
			<a href="{{ $profileUrl }}" class="font-bold text-xl text-gray-900 hover:text-blue-600 transition-colors inline-flex items-center gap-2">
				{{ $doctor->name }}
                @if($hasProBio)
                    <i class="fas fa-check-circle text-sky-500 text-sm"></i>
                @endif
			</a>
		</div>

		<!-- Speciality -->
		@if($doctor->speciality)
		<div class="mb-3">
			<span class="specialization-tag">
				<i class="fas fa-stethoscope mx-1"></i>
				{{ $doctor->speciality->name_en }}
			</span>
		</div>
		@endif

		<!-- Clinic Information -->
		@if($doctor->clinicUser && $doctor->clinicUser->clinic)
		<div class="mb-3 flex items-center text-sm text-gray-600">
			<i class="fas fa-hospital text-green-600 mx-2"></i>
			<span class="clinic-badge">
				{{ $doctor->clinicUser->clinic->name }}
			</span>
		</div>
		@else
		<div class="mb-3 flex items-center text-sm text-gray-500">
			<i class="fas fa-user-md text-blue-500 mx-2"></i>
			<span>{{ __('Independent Doctor') }}</span>
		</div>
		@endif

		<!-- Experience -->
		@if($doctor->years_experience)
		<div class="mb-3 flex items-center text-sm text-gray-600">
			<i class="fas fa-briefcase text-indigo-500 mx-2"></i>
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

        <a href="{{ $profileUrl }}" class="btn-primary w-full text-center block">
            <i class="fas fa-user-md mx-2"></i>
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
