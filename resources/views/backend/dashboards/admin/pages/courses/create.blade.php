@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">{{ __('Add Course') }}</h4>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4 border p-3 rounded">
                <div class="row" style="display: flex; align-items: center;">
                  

                    <!-- Title English -->
                    <div class="col-md-6 mb-3">
                        <label for="title_en" class="form-label">{{ __('Title English') }}</label>
                        <input type="text" name="title_en" id="title_en" class="form-control" value="{{ old('title_en') }}" required>
                        @error('title_en') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Title Arabic -->
                    <div class="col-md-6 mb-3">
                        <label for="title_ar" class="form-label">{{ __('Title Arabic') }}</label>
                        <input type="text" name="title_ar" id="title_ar" class="form-control" value="{{ old('title_ar') }}" required>
                        @error('title_ar') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description English -->
                     <div class="col-md-6 mb-3">
                        <label for="description_en" class="form-label">{{ __('Description English') }}</label>
                        <textarea name="description_en" id="description_en" class="form-control" required>{{ old('description_en') }}</textarea>
                        @error('description_en') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description Arabic -->
                    <div class="col-md-6 mb-3">
                        <label for="description_ar" class="form-label">{{ __('Description Arabic') }}</label>
                        <textarea name="description_ar" id="description_ar" class="form-control" required>{{ old('description_ar') }}</textarea>
                        @error('description_ar') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Level -->
                     <div class="col-md-6 mb-3">
                        <label for="level" class="form-label">{{ __('Level') }}</label>
                        <select name="level" id="level" class="form-control" required>
                            <option value="beginner">{{ __('Beginner') }}</option>
                            <option value="intermediate">{{ __('Intermediate') }}</option>
                            <option value="advanced">{{ __('Advanced') }}</option>
                            <option value="expert">{{ __('Expert') }}</option>
                        </select>
                        @error('level') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Duration -->
                    <div class="col-md-6 mb-3">
                        <label for="duration" class="form-label">{{ __('Duration') }}</label>
                        <input type="text" name="duration" id="duration" class="form-control" value="{{ old('duration') }}" required>
                        @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- url -->
                    <div class="col-md-6 mb-3">
                        <label for="url" class="form-label">{{ __('URL') }}</label>
                        <input type="text" name="url" id="url" class="form-control" value="{{ old('url') }}" required>
                        @error('url') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- start date -->
                     <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- end date -->
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required>
                        @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>


                    <!-- Status -->
                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" class="form-check-input" id="statusToggle" name="status" value="1" {{ old('status') ? 'checked' : '' }}>
                        <label class="form-check-label" for="statusToggle">{{ __('Status') }}</label>
                    </div>

                    <!-- Main Image -->
                    <div class="col-md-6 mb-3">
                        <label for="main_image" class="form-label">{{ __('Main Image') }}</label>
                        <input type="file" name="main_image" id="main_image" class="form-control" accept="image/*" required>
                        <img id="main_image_preview" class="mt-2 img-thumbnail" style="max-height: 200px; display:none;">
                        @error('main_image') <span class="text-danger">{{ $message }}</span> @enderror
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
</script>
@endpush
