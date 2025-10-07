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
		<form action="{{ route('clinic.clinic-inventories.update', $clinicInventory->id) }}" method="POST"
			enctype="multipart/form-data">
			@csrf
			@method('PUT')

			<div class="row d-flex justify-content-between gap-4">
				<div class="col-md-7 mb-4 border p-3 rounded">
					<div class="row" style="display: flex; align-items: center;">
						<!-- Name -->
						<div class="col-md-6 mb-3">
							<label for="item_name"
								class="form-label">{{ __('Item Name') }}</label>
							<input type="text" name="item_name" id="item_name"
								class="form-control"
								value="{{ old('item_name', $clinicInventory->item_name) }}"
								required>
							@error('item_name') <span
								class="text-danger">{{ $message }}</span>
							@enderror
						</div>

						<div class="row">
							<!-- Quantity -->
							<div class="col-md-6 mb-3">
								<label for="quantity"
									class="form-label">{{ __('Quantity') }}</label>
								<input type="number" name="quantity"
									id="quantity"
									class="form-control"
									value="{{ old('quantity', $clinicInventory->quantity) }}"
									required>
								@error('quantity') <span
									class="text-danger">{{ $message }}</span>
								@enderror
							</div>

							<!-- Unit -->
							<div class="col-md-6 mb-3">
								<label for="unit"
									class="form-label">{{ __('Unit') }}</label>
								<input type="text" name="unit" id="unit"
									class="form-control"
									value="{{ old('unit', $clinicInventory->unit) }}"
									required>
								@error('unit') <span
									class="text-danger">{{ $message }}</span>
								@enderror
							</div>

						</div>


						<!-- description -->
						<div class="col-md-12 mb-3">
							<label for="description"
								class="form-label">{{ __('Description') }}</label>
							<textarea name="description" id="description"
								class="form-control"
								required>{{ old('description', $clinicInventory->description) }}</textarea>
							@error('description') <span
								class="text-danger">{{ $message }}</span>
							@enderror

						</div>

					</div>
				</div>
				<div class="col-md-4 mb-4 border p-3 rounded">
					<!-- Main Image -->
					<div class="col-md-12 mb-3">
						<label for="main_image"
							class="form-label">{{ __('Main Image') }}</label><br>

						@if($clinicInventory->main_image)
						<div class="d-flex align-items-center gap-3 mb-2">
							<img src="{{ $clinicInventory->main_image }}"
								class="img-thumbnail"
								style="max-height:150px;">
						</div>
						@endif

						<input type="file" name="main_image" id="main_image"
							class="form-control" accept="image/*">
						<img id="main_image_preview" class="mt-2 img-thumbnail"
							style="max-height: 200px; display:none;">
						@error('main_image') <span
							class="text-danger">{{ $message }}</span>
						@enderror
					</div>

					<!-- Additional Images -->
					<div class="col-md-12 mb-3">
						<label
							class="form-label">{{ __('Additional Images') }}</label>
						<div class="d-flex flex-wrap gap-2 mb-2">
							@foreach($clinicInventory->images as $image)
							<div class="position-relative">
								<img src="{{ $image }}"
									class="img-thumbnail"
									style="max-height: 120px;">

							</div>
							@endforeach
						</div>
						<input type="file" name="images[]" id="images"
							class="form-control" accept="image/*" multiple>
						<div id="images_preview"
							class="d-flex flex-wrap gap-2 mt-2"></div>
						@error('images') <span
							class="text-danger">{{ $message }}</span>
						@enderror
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