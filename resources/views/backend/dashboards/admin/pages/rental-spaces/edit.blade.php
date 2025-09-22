@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">{{ __('Edit Rental Space') }}</h4>
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
        <form action="{{ route('admin.rental-spaces.update', $rentalSpace->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4 border p-3 rounded">
                <div class="row mb-3" style="display: flex; align-items: center;">
                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $rentalSpace->name) }}" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-4 form-check form-switch mx-3">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" class="form-check-input" id="statusToggle" name="status" value="1" {{ old('status', $rentalSpace->status) ? 'checked' : '' }}>
                        <label class="form-check-label" for="statusToggle">{{ __('Status') }}</label>
                    </div>
                </div>

                <div class="row">
                    <!-- Location -->
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">{{ __('Location') }}</label>
                        <textarea name="location" id="location" class="form-control" required>{{ old('location', $rentalSpace->location) }}</textarea>
                        @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" id="description" class="form-control" required>{{ old('description', $rentalSpace->description) }}</textarea>
                        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>



                <div class="row">
                    <!-- Main Image -->
                    <div class="col-md-6 mb-3">
                        <label for="main_image" class="form-label">{{ __('Main Image') }}</label><br>

                        @if($rentalSpace->main_image)
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ $rentalSpace->main_image }}" class="img-thumbnail" style="max-height:150px;">
                        </div>
                        @endif

                        <input type="file" name="main_image" id="main_image" class="form-control" accept="image/*">
                        <img id="main_image_preview" class="mt-2 img-thumbnail" style="max-height: 200px; display:none;">
                        @error('main_image') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Additional Images -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Additional Images') }}</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($rentalSpace->images as $image)
                            <div class="position-relative">
                                <img src="{{ $image }}" class="img-thumbnail" style="max-height: 120px;">

                            </div>
                            @endforeach
                        </div>
                        <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
                        <div id="images_preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                        @error('images') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Availability -->
            <div class="mb-4 border p-3 rounded">
                <h5>{{ __('Availability') }}</h5>
                <div class="row">
                    <!-- Type -->
                    <div class="col-md-4 mb-2">
                        <label>{{ __('Type') }}</label>
                        <select name="availability[type]" id="availability_type" class="form-control">
                            <option value="daily" {{ old('availability.type', $rentalSpace->availability->type) == 'daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                            <option value="weekly" {{ old('availability.type', $rentalSpace->availability->type) == 'weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                            <option value="monthly" {{ old('availability.type', $rentalSpace->availability->type) == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                        </select>
                    </div>

                    <!-- Weekly (Days) -->
                    <div class="col-md-4 mb-2 availability-field weekly" style="display:none;">
                        <label>{{ __('From Day') }}</label>
                        <select name="availability[from_day]" class="form-control">
                            <option value="" {{ old('availability.from_day', $rentalSpace->availability->from_day) == '' ? 'selected' : '' }}>{{ __('Select Day') }}</option>
                            <option value="monday" {{ old('availability.from_day', $rentalSpace->availability->from_day) == 'monday' ? 'selected' : '' }}>{{ __('Monday') }}</option>
                            <option value="tuesday" {{ old('availability.from_day', $rentalSpace->availability->from_day) == 'tuesday' ? 'selected' : '' }}>{{ __('Tuesday') }}</option>
                            <option value="wednesday" {{ old('availability.from_day', $rentalSpace->availability->from_day) == 'wednesday' ? 'selected' : '' }}>{{ __('Wednesday') }}</option>
                            <option value="thursday" {{ old('availability.from_day', $rentalSpace->availability->from_day) == 'thursday' ? 'selected' : '' }}>{{ __('Thursday') }}</option>
                            <option value="friday" {{ old('availability.from_day', $rentalSpace->availability->from_day) == 'friday' ? 'selected' : '' }}>{{ __('Friday') }}</option>
                            <option value="saturday" {{ old('availability.from_day', $rentalSpace->availability->from_day) == 'saturday' ? 'selected' : '' }}>{{ __('Saturday') }}</option>
                            <option value="sunday" {{ old('availability.from_day', $rentalSpace->availability->from_day) == 'sunday' ? 'selected' : '' }}>{{ __('Sunday') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 availability-field weekly" style="display:none;">
                        <label>{{ __('To Day') }}</label>
                        <select name="availability[to_day]" class="form-control">
                            <option value="" {{ old('availability.to_day', $rentalSpace->availability->to_day) == '' ? 'selected' : '' }}>{{ __('Select Day') }}</option>
                            <option value="monday" {{ old('availability.to_day', $rentalSpace->availability->to_day) == 'monday' ? 'selected' : '' }}>{{ __('Monday') }}</option>
                            <option value="tuesday" {{ old('availability.to_day', $rentalSpace->availability->to_day) == 'tuesday' ? 'selected' : '' }}>{{ __('Tuesday') }}</option>
                            <option value="wednesday" {{ old('availability.to_day', $rentalSpace->availability->to_day) == 'wednesday' ? 'selected' : '' }}>{{ __('Wednesday') }}</option>
                            <option value="thursday" {{ old('availability.to_day', $rentalSpace->availability->to_day) == 'thursday' ? 'selected' : '' }}>{{ __('Thursday') }}</option>
                            <option value="friday" {{ old('availability.to_day', $rentalSpace->availability->to_day) == 'friday' ? 'selected' : '' }}>{{ __('Friday') }}</option>
                            <option value="saturday" {{ old('availability.to_day', $rentalSpace->availability->to_day) == 'saturday' ? 'selected' : '' }}>{{ __('Saturday') }}</option>
                            <option value="sunday" {{ old('availability.to_day', $rentalSpace->availability->to_day) == 'sunday' ? 'selected' : '' }}>{{ __('Sunday') }}</option>
                        </select>
                    </div>

                    <!-- Monthly (Dates) -->
                    <div class="col-md-4 mb-2 availability-field monthly" style="display:none;">
                        <label>{{ __('From Date') }}</label>
                        <input type="date" name="availability[from_date]" class="form-control" value="{{ old('availability.from_date', $rentalSpace->availability->from_date) }}">
                    </div>
                    <div class="col-md-4 mb-2 availability-field monthly" style="display:none;">
                        <label>{{ __('To Date') }}</label>
                        <input type="date" name="availability[to_date]" class="form-control" value="{{ old('availability.to_date', $rentalSpace->availability->to_date) }}">
                    </div>

                    <!-- Shared Time (used by all types) -->
                    <div class="col-md-4 mb-2 availability-time">
                        <label>{{ __('From Time') }}</label>
                        <input type="time" name="availability[from_time]" class="form-control" value="{{ old('availability.from_time', $rentalSpace->availability->from_time) }}">
                    </div>
                    <div class="col-md-4 mb-2 availability-time">
                        <label>{{ __('To Time') }}</label>
                        <input type="time" name="availability[to_time]" class="form-control" value="{{ old('availability.to_time', $rentalSpace->availability->to_time) }}">
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="mb-4 border p-3 rounded">
                <h5>{{ __('Pricing') }}</h5>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label>{{ __('Price') }}</label>
                        <input type="number" step="0.01" name="pricing[price]" class="form-control"
                            value="{{ old('pricing.price', $rentalSpace->pricing->price ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>{{ __('Notes') }}</label>
                        <input type="text" name="pricing[notes]" class="form-control"
                            value="{{ old('pricing.notes', $rentalSpace->pricing->notes ?? '') }}">
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
                img.style.maxHeight = '120px';
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