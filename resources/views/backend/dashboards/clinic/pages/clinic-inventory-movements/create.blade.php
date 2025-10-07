@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
	<div class="card-header">
		<h4 class="card-title">{{ __('Add Clinic Inventory Movement') }}</h4>
	</div>
	<div class="card-body p-4">
		<form action="{{ route('clinic.clinic-inventory-movements.store') }}" method="POST"
			enctype="multipart/form-data">
			@csrf

			<input type="hidden" name="clinic_inventory_id" value="{{ $id }}">
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
									value="{{ old('quantity') }}"
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
									<option value="in">
										{{ __('In') }}
									</option>
									<option value="out">
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
									value="{{ old('movement_date') }}"
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
								required>{{ old('notes') }}</textarea>
							@error('notes') <span
								class="text-danger">{{ $message }}</span>
							@enderror

						</div>

					</div>
				</div>



			</div>
			<!-- Submit -->
			<button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
		</form>
	</div>
</div>
@endsection
@push('scripts')

@endpush
