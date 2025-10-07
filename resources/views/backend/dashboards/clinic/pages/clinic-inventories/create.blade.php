@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">{{ __('Add Clinic Inventory') }}</h4>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('clinic.clinic-inventories.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="row d-flex justify-content-between gap-4">
                <div class="col-md-7 mb-4 border p-3 rounded">
                    <div class="row" style="display: flex; align-items: center;">
                        <!-- Name -->
                        <div class="col-md-6 mb-3">
                            <x-input
                                type="text"
                                name="item_name"
                                label="{{ __('Item Name') }}"
                                placeholder="{{ __('Enter item name') }}"
                                required />
                        </div>

                        <div class="row">
                            <!-- Quantity -->
                            <div class="col-md-6 mb-3">
                                <x-input
                                    type="number"
                                    name="quantity"
                                    label="{{ __('Quantity') }}"
                                    placeholder="{{ __('Enter quantity') }}"
                                    min="0"
                                    step="1"
                                    required />
                            </div>

                            <!-- Unit -->
                            <div class="col-md-6 mb-3">
                                <x-input
                                    type="text"
                                    name="unit"
                                    label="{{ __('Unit') }}"
                                    placeholder="{{ __('Enter unit (e.g., pieces, kg, ml)') }}"
                                    required />
                            </div>

                        </div>


                        <!-- description -->
                        <div class="col-md-12 mb-3">
                            <x-input
                                type="textarea"
                                name="description"
                                label="{{ __('Description') }}"
                                placeholder="{{ __('Enter item description') }}"
                                rows="4"
                                required />
                        </div>

                    </div>
                </div>
                <div class="col-md-4 mb-4 border p-3 rounded">
                    <!-- Main Image -->
                    <div class="col-md-12 mb-3">
                        <x-input
                            type="file"
                            name="main_image"
                            label="{{ __('Main Image') }}"
                            accept="image/*"
                            preview
                            required />
                    </div>

                    <!-- Images -->
                    <div class="col-md-12 mb-3">
                        <x-input
                            type="file"
                            name="images"
                            label="{{ __('Additional Images') }}"
                            accept="image/*"
                            multiple
                            preview />
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
    document.addEventListener("DOMContentLoaded", function() {
        const typeSelect = document.getElementById("availability_type");

        function toggleAvailabilityFields() {
            const selectedType = typeSelect.value;

            // Hide all specific fields
            document.querySelectorAll(".availability-field").forEach(field => {
                field.style.display = "none";
            });

            // Show only relevant fields for selected type
            document.querySelectorAll(`.availability-field.${selectedType}`).forEach(
                field => {
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
