@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
	<div class="card-header">
		<h4 class="card-title">{{ __('Edit Clinic Inventory') }}</h4>
	</div>
	<!-- error messages -->
	@if ($errors->any())
	<div class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif
	<div class="card-body p-4">
		<form action="{{ route('clinic.clinic-inventory-movements.update', $clinicInventoryMovement->id) }}"
			method="POST" enctype="multipart/form-data">
			@csrf
			@method('PUT')

			<input type="hidden" name="clinic_inventory_id"
				value="{{ $clinicInventoryMovement->clinic_inventory_id }}">

			<div class="row d-flex justify-content-between gap-4">
				<div class="col-md-12 mb-4 border p-3 rounded">
					<div class="row" style="display: flex; align-items: center;">


						<div class="row">
							<!-- Quantity -->
							<div class="col-md-4 mb-3">
								<label for="quantity"
									class="form-label">{{ __('Quantity') }}</label>
								<input type="number" name="quantity"
									id="quantity"
									class="form-control"
									value="{{ old('quantity') ?? $clinicInventoryMovement->quantity }}"
									required>
								@error('quantity') <span
									class="text-danger">{{ $message }}</span>
								@enderror
							</div>
							<!-- Type -->
							<div class="col-md-4 mb-3">
								<label for="quantity"
									class="form-label">{{ __('Type') }}</label>
								<select name="type" id="type"
									class="form-control">
									<option value="in"
										{{ $clinicInventoryMovement->type == 'in' ? 'selected' : '' }}>
										{{ __('In') }}
									</option>
									<option value="out"
										{{ $clinicInventoryMovement->type == 'out' ? 'selected' : '' }}>
										{{ __('Out') }}
									</option>
								</select>
								@error('type') <span
									class="text-danger">{{ $message }}</span>
								@enderror
							</div>

							<!-- Unit -->
							<div class="col-md-4 mb-3">
								<label for="movement_date"
									class="form-label">{{ __('Movement Date') }}</label>
								<input type="datetime-local"
									name="movement_date"
									id="movement_date"
									class="form-control"
									value="{{ old('movement_date') ?? $clinicInventoryMovement->movement_date }}"
									required>
								@error('movement_date') <span
									class="text-danger">{{ $message }}</span>
								@enderror
							</div>

						</div>


						<!-- notes -->
						<div class="col-md-12 mb-3">
							<label for="notes"
								class="form-label">{{ __('Notes') }}</label>
							<textarea name="notes" id="notes"
								class="form-control"
								required>{{ old('notes') ?? $clinicInventoryMovement->notes }}</textarea>
							@error('notes') <span
								class="text-danger">{{ $message }}</span>
							@enderror

						</div>

					</div>
				</div>



			</div>

			<!-- Submit -->
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script>
// Main image preview
document.getElementById('main_image').addEventListener('change', function() {
	const file = this.files[0];
	if (file) {
		const reader = new FileReader();
		reader.onload = (e) => {
			const preview = document.getElementById(
				'main_image_preview');
			preview.src = e.target.result;
			preview.style.display = 'block';
		};
		reader.readAsDataURL(file);
	}
});

// Multiple images preview
document.getElementById('images').addEventListener('change', function() {
	const previewContainer = document.getElementById('images_preview');
	previewContainer.innerHTML = '';
	Array.from(this.files).forEach(file => {
		const reader = new FileReader();
		reader.onload = (e) => {
			const img = document.createElement(
				'img');
			img.src = e.target.result;
			img.classList.add('img-thumbnail');
			img.style.maxHeight = '120px';
			previewContainer.appendChild(img);
		};
		reader.readAsDataURL(file);
	});
});
</script>
@endpush
