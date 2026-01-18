@extends('backend.dashboards.clinic.layouts.app')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">{{ __('Edit Rental Space') }}</h4>
                <a href="{{ route('clinic.rental-spaces.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> {{ __('Back to List') }}
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('clinic.rental-spaces.update', $rentalSpace->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>{{ __('Basic Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">{{ __('Name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="{{ old('name', $rentalSpace->name) }}" required>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Listing Type -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">{{ __('Listing Type') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="listing_type" id="listing_type" class="form-control" required>
                                            <option value="rent"
                                                {{ old('listing_type', $rentalSpace->listing_type) == 'rent' ? 'selected' : '' }}>
                                                {{ __('For Rent') }}</option>
                                            <option value="sale"
                                                {{ old('listing_type', $rentalSpace->listing_type) == 'sale' ? 'selected' : '' }}>
                                                {{ __('For Sale') }}</option>
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">{{ __('Status') }}</label>
                                        <div class="form-check form-switch mt-2">
                                            <input type="hidden" name="status" value="0">
                                            <input type="checkbox" class="form-check-input" id="statusToggle" name="status"
                                                value="1" {{ old('status', $rentalSpace->status) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusToggle">{{ __('Active') }}</label>
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <div class="col-md-12 mb-3">
                                        <label for="location" class="form-label">{{ __('Location') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea name="location" id="location" class="form-control" rows="2" required>{{ old('location', $rentalSpace->location) }}</textarea>
                                        @error('location')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="col-md-12 mb-3">
                                        <label for="description" class="form-label">{{ __('Description') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description', $rentalSpace->description) }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Capacity & Area -->
                                    <div class="col-md-4 mb-3">
                                        <label for="capacity" class="form-label">{{ __('Capacity (persons)') }}</label>
                                        <input type="number" name="capacity" id="capacity" class="form-control"
                                            value="{{ old('capacity', $rentalSpace->capacity) }}" min="1">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="area_sqm" class="form-label">{{ __('Area (sqm)') }}</label>
                                        <input type="number" name="area_sqm" id="area_sqm" class="form-control"
                                            value="{{ old('area_sqm', $rentalSpace->area_sqm) }}" step="0.01"
                                            min="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div class="card border mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fa fa-list-check me-2"></i>{{ __('Amenities') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php $currentAmenities = old('amenities', $rentalSpace->amenities ?? []); @endphp
                                    @foreach (\App\Models\RentalSpace::AMENITIES as $key => $label)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="amenities[]"
                                                    value="{{ $key }}" id="amenity_{{ $key }}"
                                                    {{ in_array($key, $currentAmenities) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="amenity_{{ $key }}">{{ __($label) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Images Column -->
                    <div class="col-lg-4">
                        <div class="card border mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fa fa-image me-2"></i>{{ __('Images') }}</h5>
                            </div>
                            <div class="card-body">
                                <!-- Current Main Image -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Current Main Image') }}</label>
                                    @if ($rentalSpace->main_image)
                                        <div class="mb-2">
                                            <img src="{{ $rentalSpace->main_image }}" class="img-thumbnail"
                                                style="max-height:150px;">
                                        </div>
                                    @endif
                                    <label for="main_image" class="form-label">{{ __('Change Main Image') }}</label>
                                    <input type="file" name="main_image" id="main_image" class="form-control"
                                        accept="image/*">
                                    <img id="main_image_preview" class="mt-2 img-thumbnail"
                                        style="max-height: 150px; display:none;">
                                    @error('main_image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Current Additional Images -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Current Additional Images') }}</label>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        @foreach ($rentalSpace->images as $image)
                                            <img src="{{ $image }}" class="img-thumbnail"
                                                style="max-height: 80px;">
                                        @endforeach
                                    </div>
                                    <label for="images" class="form-label">{{ __('Add More Images') }}</label>
                                    <input type="file" name="images[]" id="images" class="form-control"
                                        accept="image/*" multiple>
                                    <div id="images_preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                    @error('images')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="card border mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fa fa-dollar-sign me-2"></i>{{ __('Pricing') }}</h5>
                    </div>
                    <div class="card-body">
                        <!-- Sale Price (shown when listing_type is sale) -->
                        <div class="row mb-3" id="sale_price_section"
                            style="display: {{ old('listing_type', $rentalSpace->listing_type) == 'sale' ? 'flex' : 'none' }};">
                            <div class="col-md-4">
                                <label for="sale_price" class="form-label">{{ __('Sale Price') }}
                                    ({{ __('EGP') }})</label>
                                <input type="number" step="0.01" name="sale_price" id="sale_price"
                                    class="form-control" value="{{ old('sale_price', $rentalSpace->sale_price) }}">
                            </div>
                        </div>

                        <!-- Rental Pricing (shown when listing_type is rent) -->
                        <div id="rental_pricing_section"
                            style="display: {{ old('listing_type', $rentalSpace->listing_type) == 'rent' ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ __('Pricing Type') }}</label>
                                    <select name="pricing[pricing_type]" class="form-control">
                                        <option value="hourly"
                                            {{ old('pricing.pricing_type', optional($rentalSpace->pricing)->pricing_type) == 'hourly' ? 'selected' : '' }}>
                                            {{ __('Hourly') }}</option>
                                        <option value="daily"
                                            {{ old('pricing.pricing_type', optional($rentalSpace->pricing)->pricing_type) == 'daily' ? 'selected' : '' }}>
                                            {{ __('Daily') }}</option>
                                        <option value="weekly"
                                            {{ old('pricing.pricing_type', optional($rentalSpace->pricing)->pricing_type) == 'weekly' ? 'selected' : '' }}>
                                            {{ __('Weekly') }}</option>
                                        <option value="monthly"
                                            {{ old('pricing.pricing_type', optional($rentalSpace->pricing)->pricing_type) == 'monthly' ? 'selected' : '' }}>
                                            {{ __('Monthly') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ __('Price') }} ({{ __('EGP') }})</label>
                                    <input type="number" step="0.01" name="pricing[price]" class="form-control"
                                        value="{{ old('pricing.price', optional($rentalSpace->pricing)->price) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Pricing Notes') }}</label>
                                    <input type="text" name="pricing[notes]" class="form-control"
                                        value="{{ old('pricing.notes', optional($rentalSpace->pricing)->notes) }}"
                                        placeholder="{{ __('e.g., Includes utilities') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Availability Section -->
                <div class="card border mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fa fa-calendar me-2"></i>{{ __('Availability') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Availability Type') }}</label>
                                <select name="availability[type]" id="availability_type" class="form-control">
                                    <option value="daily"
                                        {{ old('availability.type', optional($rentalSpace->availability)->type) == 'daily' ? 'selected' : '' }}>
                                        {{ __('Daily') }}</option>
                                    <option value="weekly"
                                        {{ old('availability.type', optional($rentalSpace->availability)->type) == 'weekly' ? 'selected' : '' }}>
                                        {{ __('Weekly') }}</option>
                                    <option value="monthly"
                                        {{ old('availability.type', optional($rentalSpace->availability)->type) == 'monthly' ? 'selected' : '' }}>
                                        {{ __('Monthly') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Weekly Schedule Builder -->
                        <div class="mb-4">
                            <h6 class="mb-3">{{ __('Weekly Schedule') }} <small
                                    class="text-muted">({{ __('Set available hours for each day') }})</small></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 150px;">{{ __('Day') }}</th>
                                            <th style="width: 100px;">{{ __('Available') }}</th>
                                            <th>{{ __('From Time') }}</th>
                                            <th>{{ __('To Time') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $existingSchedules = $rentalSpace->schedules->keyBy('day_of_week');
                                        @endphp
                                        @foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                            @php
                                                $schedule = $existingSchedules->get($day);
                                                $isAvailable = old(
                                                    "schedules.{$day}.is_available",
                                                    $schedule ? $schedule->is_available : false,
                                                );
                                                $startTime = old(
                                                    "schedules.{$day}.start_time",
                                                    $schedule ? substr($schedule->start_time, 0, 5) : '',
                                                );
                                                $endTime = old(
                                                    "schedules.{$day}.end_time",
                                                    $schedule ? substr($schedule->end_time, 0, 5) : '',
                                                );
                                                $hasError =
                                                    $errors->has("schedules.{$day}.start_time") ||
                                                    $errors->has("schedules.{$day}.end_time");
                                            @endphp
                                            <tr class="{{ $hasError ? 'table-danger' : '' }}">
                                                <td>
                                                    <strong>{{ __(ucfirst($day)) }}</strong>
                                                    <input type="hidden"
                                                        name="schedules[{{ $day }}][day_of_week]"
                                                        value="{{ $day }}">
                                                    @if ($hasError)
                                                        <div class="text-danger small mt-1">
                                                            @error("schedules.{$day}.start_time")
                                                                {{ $message }}
                                                            @enderror
                                                            @error("schedules.{$day}.end_time")
                                                                {{ $message }}
                                                            @enderror
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input type="hidden"
                                                            name="schedules[{{ $day }}][is_available]"
                                                            value="0">
                                                        <input type="checkbox" class="form-check-input schedule-toggle"
                                                            name="schedules[{{ $day }}][is_available]"
                                                            value="1" data-day="{{ $day }}"
                                                            {{ $isAvailable ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="time"
                                                        name="schedules[{{ $day }}][start_time]"
                                                        class="form-control schedule-time {{ $errors->has("schedules.{$day}.start_time") ? 'is-invalid' : '' }}"
                                                        data-day="{{ $day }}" value="{{ $startTime }}"
                                                        {{ !$isAvailable ? 'disabled' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="time" name="schedules[{{ $day }}][end_time]"
                                                        class="form-control schedule-time {{ $errors->has("schedules.{$day}.end_time") ? 'is-invalid' : '' }}"
                                                        data-day="{{ $day }}" value="{{ $endTime }}"
                                                        {{ !$isAvailable ? 'disabled' : '' }}>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setAllDays()">
                                    <i class="fa fa-check-double me-1"></i> {{ __('Select All Weekdays') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllDays()">
                                    <i class="fa fa-times me-1"></i> {{ __('Clear All') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> {{ __('Update Rental Space') }}
                    </button>
                    <a href="{{ route('clinic.rental-spaces.index') }}"
                        class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>
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
                    img.style.maxHeight = '80px';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });

        // Toggle listing type sections
        document.getElementById('listing_type').addEventListener('change', function() {
            const isRent = this.value === 'rent';
            document.getElementById('sale_price_section').style.display = isRent ? 'none' : 'flex';
            document.getElementById('rental_pricing_section').style.display = isRent ? 'block' : 'none';
        });

        // Schedule toggle - enable/disable time inputs
        document.querySelectorAll('.schedule-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const day = this.dataset.day;
                const timeInputs = document.querySelectorAll(`.schedule-time[data-day="${day}"]`);
                timeInputs.forEach(input => {
                    input.disabled = !this.checked;
                    if (!this.checked) input.value = '';
                });
            });
        });

        // Set all days (all 7 days)
        function setAllDays() {
            const allDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            allDays.forEach(day => {
                const toggle = document.querySelector(`.schedule-toggle[data-day="${day}"]`);
                if (toggle && !toggle.checked) {
                    toggle.checked = true;
                    toggle.dispatchEvent(new Event('change'));
                }
            });
        }

        // Clear all days
        function clearAllDays() {
            document.querySelectorAll('.schedule-toggle').forEach(toggle => {
                toggle.checked = false;
                toggle.dispatchEvent(new Event('change'));
            });
        }
    </script>
@endpush
