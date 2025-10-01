@extends('backend.dashboards.clinic.layouts.app')
@section('title' , __('Edit Job'))

@section('content')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">{{ __('Edit Job') }}</h4>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('clinic.jobs.update', $job->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-9 mb-4 border p-3 rounded">
                        <div class="row" style="display: flex; align-items: center;">


                            <!-- Title -->
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">{{ __('Title') }}</label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ $job->title }}" required>
                                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- type -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">{{ __('Type') }}</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="full-time" {{ $job->type == 'full-time' ? 'selected' : '' }}>{{ __('Full-time') }}</option>
                                    <option value="part-time" {{ $job->type == 'part-time' ? 'selected' : '' }}>{{ __('Part-time') }}</option>
                                    <option value="contract" {{ $job->type == 'contract' ? 'selected' : '' }}>{{ __('Contract') }}</option>
                                    <option value="temporary" {{ $job->type == 'temporary' ? 'selected' : '' }}>{{ __('Temporary') }}</option>
                                    <option value="internship" {{ $job->type == 'internship' ? 'selected' : '' }}>{{ __('Internship') }}</option>
                                </select>
                                @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">{{ __('Description') }}</label>
                                <textarea name="description" id="description" class="form-control" required>{{ $job->description }}</textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label">{{ __('Location') }}</label>
                                <input type="text" name="location" id="location" class="form-control" value="{{ $job->location }}" required>
                                @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Salary -->
                            <div class="col-md-4 mb-3">
                                <label for="salary" class="form-label">{{ __('Salary') }}</label>
                                <input type="text" name="salary" id="salary" class="form-control" value="{{ $job->salary }}" required>
                                @error('salary') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>



                            <!-- Status -->
                            <div class="col-md-4 form-check form-switch">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox" class="form-check-input" id="statusToggle" name="status" value="1" {{ $job->status ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusToggle">{{ __('Status') }}</label>
                            </div>




                        </div>
                </div>
                <div class="col-md-3">
                     <!-- Main Image -->
                     <div class="col-md-12 mb-3 border p-3 rounded">
                        <label for="main_image" class="form-label">{{ __('Main Image') }}</label>
                        @if($job->main_image)
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ $job->main_image }}" class="img-thumbnail" style="max-height:150px;">
                        </div>
                        @endif
                        <input type="file" name="main_image" id="main_image" class="form-control" accept="image/*">
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