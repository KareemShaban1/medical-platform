@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">{{ __('Add Rental Space') }}</h4>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('clinic.rental-spaces.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4 border p-3 rounded">
                <div class="row" style="display: flex; align-items: center;">
                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" class="form-check-input" id="statusToggle" name="status" value="1" {{ old('status') ? 'checked' : '' }}>
                        <label class="form-check-label" for="statusToggle">{{ __('Status') }}</label>
                    </div>
                </div>

                <div class="row">
                    <!-- Location -->
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">{{ __('Location') }}</label>
                        <textarea name="location" id="location" class="form-control" required>{{ old('location') }}</textarea>
                        @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" id="description" class="form-control" required>{{ old('description') }}</textarea>
                        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Main Image -->
                    <div class="col-md-6 mb-3">
                        <label for="main_image" class="form-label">{{ __('Main Image') }}</label>
                        <input type="file" name="main_image" id="main_image" class="form-control" accept="image/*" required>
                        <img id="main_image_preview" class="mt-2 img-thumbnail" style="max-height: 200px; display:none;">
                        @error('main_image') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Images -->
                    <div class="col-md-6 mb-3">
                        <label for="images" class="form-label">{{ __('Additional Images') }}</label>
                        <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
                        <div id="images_preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                        @error('images') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            <div class="mb-4 border p-3 rounded">
                <h5>{{ __('Availability') }}</h5>
                <div class="row">
                    <!-- Type -->
                    <div class="col-md-4 mb-2">
                        <label>{{ __('Type') }}</label>
                        <select name="availability[type]" id="availability_type" class="form-control">
                            <option value="daily">{{ __('Daily') }}</option>
                            <option value="weekly">{{ __('Weekly') }}</option>
                            <option value="monthly">{{ __('Monthly') }}</option>
                        </select>
                    </div>

                    <!-- Weekly (Days) -->
                    <div class="col-md-4 mb-2 availability-field weekly" style="display:none;">
                        <label>{{ __('From Day') }}</label>
                        <select name="availability[from_day]" class="form-control">
                            <option value="">{{ __('Select Day') }}</option>
                            <option value="monday">{{ __('Monday') }}</option>
                            <option value="tuesday">{{ __('Tuesday') }}</option>
                            <option value="wednesday">{{ __('Wednesday') }}</option>
                            <option value="thursday">{{ __('Thursday') }}</option>
                            <option value="friday">{{ __('Friday') }}</option>
                            <option value="saturday">{{ __('Saturday') }}</option>
                            <option value="sunday">{{ __('Sunday') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 availability-field weekly" style="display:none;">
                        <label>{{ __('To Day') }}</label>
                        <select name="availability[to_day]" class="form-control">
                            <option value="">{{ __('Select Day') }}</option>
                            <option value="monday">{{ __('Monday') }}</option>
                            <option value="tuesday">{{ __('Tuesday') }}</option>
                            <option value="wednesday">{{ __('Wednesday') }}</option>
                            <option value="thursday">{{ __('Thursday') }}</option>
                            <option value="friday">{{ __('Friday') }}</option>
                            <option value="saturday">{{ __('Saturday') }}</option>
                            <option value="sunday">{{ __('Sunday') }}</option>
                        </select>
                    </div>

                    <!-- Monthly (Dates) -->
                    <div class="col-md-4 mb-2 availability-field monthly" style="display:none;">
                        <label>{{ __('From Date') }}</label>
                        <input type="date" name="availability[from_date]" class="form-control">
                    </div>
                    <div class="col-md-4 mb-2 availability-field monthly" style="display:none;">
                        <label>{{ __('To Date') }}</label>
                        <input type="date" name="availability[to_date]" class="form-control">
                    </div>

                    <!-- Shared Time (used by all types) -->
                    <div class="col-md-4 mb-2 availability-time">
                        <label>{{ __('From Time') }}</label>
                        <input type="time" name="availability[from_time]" class="form-control">
                    </div>
                    <div class="col-md-4 mb-2 availability-time">
                        <label>{{ __('To Time') }}</label>
                        <input type="time" name="availability[to_time]" class="form-control">
                    </div>
                </div>
            </div>



            <!-- Pricing -->
            <div class="mb-4 border p-3 rounded">
                <h5>{{ __('Pricing') }}</h5>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label>{{ __('Price') }}</label>
                        <input type="number" step="0.01" name="pricing[price]" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>{{ __('Notes') }}</label>
                        <input type="text" name="pricing[notes]" class="form-control">
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
<script>
    // Main image preview
    document.getElementById('main_image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('main_image_preview');
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
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('img-thumbnail');
                img.style.maxHeight = '150px';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const typeSelect = document.getElementById("availability_type");

        function toggleAvailabilityFields() {
            const selectedType = typeSelect.value;

            // Hide all specific fields
            document.querySelectorAll(".availability-field").forEach(field => {
                field.style.display = "none";
            });

            // Show only relevant fields for selected type
            document.querySelectorAll(`.availability-field.${selectedType}`).forEach(field => {
                field.style.display = "block";
            });

            // Time fields are always shown (shared)
            document.querySelectorAll(".availability-time").forEach(field => {
                field.style.display = "block";
            });
        }

        toggleAvailabilityFields(); // on page load
        typeSelect.addEventListener("change", toggleAvailabilityFields);
    });
</script>
@endpush