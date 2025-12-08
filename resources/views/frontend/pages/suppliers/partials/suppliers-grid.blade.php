@pushOnce('styles')
<style>
.pro-chip {
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

.share-pill {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	padding: 9px 12px;
	border-radius: 9999px;
	display: none;
}

.pro-card {
	position: relative;
	overflow: hidden;
	border: 1px solid rgba(245, 158, 11, 0.35);
	box-shadow: 0 16px 40px rgba(245, 158, 11, 0.18);
	background: linear-gradient(145deg, #ffffff, #f9fafb);
}

.pro-card::after {
	content: '';
	position: absolute;
	inset: 0;
	background: radial-gradient(circle at 15% 25%, rgba(245,158,11,0.15), transparent 40%), radial-gradient(circle at 90% 0%, rgba(17,24,39,0.22), transparent 45%);
	pointer-events: none;
}

.halo {
	position: absolute;
	inset: 0;
	background: radial-gradient(circle at 20% 20%, rgba(245,158,11,0.14), transparent 45%), radial-gradient(circle at 80% 0%, rgba(14,165,233,0.12), transparent 40%);
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

	const done = () => showToast();

	if (navigator.clipboard && navigator.clipboard.writeText) {
		navigator.clipboard.writeText(url).then(done);
	} else {
		const el = document.createElement('textarea');
		el.value = url;
		document.body.appendChild(el);
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
		done();
	}
});

function showToast() {
	const toast = document.createElement('div');
	toast.textContent = '{{ __("Link copied!") }}';
	Object.assign(toast.style, {
		position: 'fixed',
		bottom: '24px',
		right: '24px',
		padding: '12px 16px',
		background: 'linear-gradient(135deg, #111827, #f59e0b)',
		color: '#fff',
		borderRadius: '9999px',
		boxShadow: '0 12px 30px rgba(0,0,0,0.25)',
		zIndex: '9999',
		transition: 'opacity .3s ease'
	});
	document.body.appendChild(toast);
	setTimeout(() => {
		toast.style.opacity = '0';
		setTimeout(() => toast.remove(), 300);
	}, 1500);
}
</script>
@endPushOnce

@forelse($suppliers as $supplier)
@php
	$proService = app(\App\Services\ProfessionalBioService::class);
	$hasProBio = $proService->hasForSupplier($supplier);
	$profileSlug = $hasProBio ? $proService->ensureSlug($supplier, 'supplier') : null;
	$profileUrl = $hasProBio
		? route('suppliers.show', $profileSlug)
		: route('suppliers.show', $supplier->id);
	$shareUrl = $hasProBio ? $proService->getShareUrl($supplier, 'supplier') : null;
@endphp
<div class="supplier-card card overflow-hidden {{ $hasProBio ? 'pro-card' : 'bg-white' }}"
	data-name="{{ $supplier->name }}">
	@if($hasProBio)
		<div class="halo"></div>
	@endif
	<div class="h-48 bg-gray-200 flex items-center justify-center">
		@if($supplier->images && count($supplier->images) > 0)
		<img src="{{ $supplier->images[0] }}" alt="{{ $supplier->name }}" class="w-full h-full object-cover">
		@else
		<i class="fas fa-truck text-4xl text-gray-400"></i>
	@endif
	</div>
	<div class="p-4">
		<a href="{{ $profileUrl }}" class="font-semibold text-lg mb-2 inline-flex items-center gap-2">
			{{ $supplier->name }}
			@if($hasProBio)
				<i class="fas fa-check-circle text-sky-500 text-sm"></i>
			@endif
		</a>
		<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-map-marker-alt mx-2"></i>
			<span>{{ $supplier->address ?? 'Location not specified' }}</span>
		</div>
		<div class="flex items-center text-sm text-gray-500 mb-3">
			<i class="fas fa-phone mx-2"></i>
			<span>{{ $supplier->phone ?? 'Contact not available' }}</span>
		</div>
		<a href="{{ $profileUrl }}" class="btn-primary w-full">
			{{ __('view details') }}
		</a>
	</div>
</div>
@empty
<div class="col-span-full text-center py-12">
	<div class="text-gray-500">
		<i class="fas fa-search text-4xl mb-4"></i>
		<h3 class="text-lg font-semibold mb-2">{{ __('no suppliers found') }}</h3>
		<p>{{ __('try adjusting your search criteria or filters') }}</p>
	</div>
</div>
@endforelse
