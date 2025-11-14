@extends('backend.dashboards.admin.layouts.app')
@section('title' , __('Edit Course'))

@section('content')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">{{ __('Edit Course') }}</h4>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4 border p-3 rounded">
                <div class="row" style="display: flex; align-items: center;">


                    <!-- Title English -->
                    <div class="col-md-6 mb-3">
                        <label for="title_en" class="form-label">{{ __('Title English') }}</label>
                        <input type="text" name="title_en" id="title_en" class="form-control" value="{{ $course->title_en }}" required>
                        @error('title_en') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Title Arabic -->
                    <div class="col-md-6 mb-3">
                        <label for="title_ar" class="form-label">{{ __('Title Arabic') }}</label>
                        <input type="text" name="title_ar" id="title_ar" class="form-control" value="{{ $course->title_ar }}" required>
                        @error('title_ar') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description English -->
                     <div class="col-md-6 mb-3">
                        <label for="description_en" class="form-label">{{ __('Description English') }}</label>
                        <textarea name="description_en" id="description_en" class="form-control" required>{{ $course->description_en }}</textarea>
                        @error('description_en') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description Arabic -->
                    <div class="col-md-6 mb-3">
                        <label for="description_ar" class="form-label">{{ __('Description Arabic') }}</label>
                        <textarea name="description_ar" id="description_ar" class="form-control" required>{{ $course->description_ar }}</textarea>
                        @error('description_ar') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Level -->
                     <div class="col-md-6 mb-3">
                        <label for="level" class="form-label">{{ __('Level') }}</label>
                        <select name="level" id="level" class="form-control" required>
                            <option value="beginner" {{ $course->level == 'beginner' ? 'selected' : '' }}>{{ __('Beginner') }}</option>
                            <option value="intermediate" {{ $course->level == 'intermediate' ? 'selected' : '' }}>{{ __('Intermediate') }}</option>
                            <option value="advanced" {{ $course->level == 'advanced' ? 'selected' : '' }}>{{ __('Advanced') }}</option>
                            <option value="expert" {{ $course->level == 'expert' ? 'selected' : '' }}>{{ __('Expert') }}</option>
                        </select>
                        @error('level') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>


                    <!-- Duration -->
                    <div class="col-md-6 mb-3">
                        <label for="duration" class="form-label">{{ __('Duration') }}</label>
                        <input type="text" name="duration" id="duration" class="form-control" value="{{ $course->duration }}" required>
                        @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- url -->
                    <div class="col-md-6 mb-3">
                        <label for="url" class="form-label">{{ __('URL') }}</label>
                        <input type="text" name="url" id="url" class="form-control" value="{{ $course->url }}" required>
                        @error('url') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- start date -->
                     <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $course->start_date->format('Y-m-d') }}" required>
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- end date -->
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $course->end_date->format('Y-m-d') }}" required>
                        @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>


                    <!-- Status -->
                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" class="form-check-input" id="statusToggle" name="status" value="1" {{ $course->status ? 'checked' : '' }}>
                        <label class="form-check-label" for="statusToggle">{{ __('Status') }}</label>
                    </div>

                    <!-- Main Image -->
                    <div class="col-md-6 mb-3">
                        <label for="main_image" class="form-label">{{ __('Main Image') }}</label>
                        @if($course->main_image)
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ $course->main_image }}" class="img-thumbnail" style="max-height:150px;">
                        </div>
                        @endif
                        <input type="file" name="main_image" id="main_image" class="form-control" accept="image/*">
                        <img id="main_image_preview" class="mt-2 img-thumbnail" style="max-height: 200px; display:none;">
                        @error('main_image') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>


                </div>
            </div>

            <!-- Course Links Repeater -->
            <div class="mb-4 border p-3 rounded">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-0">{{ __('Course Content') }}</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-link-row"><i class="fa fa-plus"></i> {{ __('Add Link') }}</button>
                </div>
                <div id="links-container" class="row g-3">
                    <!-- rows injected by JS -->
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
<script>
    // Simple dynamic repeater for course links (preload existing)
    (function(){
        const container = document.getElementById('links-container');
        const addBtn = document.getElementById('add-link-row');
        let idx = 0;

        // Pre-define labels to avoid Blade parsing issues
        const labels = {
            title: @json(__('Title')),
            url: @json(__('URL')),
            order: @json(__('Order')),
            active: @json(__('Active')),
            description: @json(__('Description'))
        };

        function addRow(data = {}){
            const row = document.createElement('div');
            row.className = 'col-12 border rounded p-3 position-relative';
            row.innerHTML =
                '<button type="button" class="btn-close position-absolute" style="right:8px;top:8px" aria-label="Close"></button>' +
                '<div class="row g-3">' +
                    '<div class="col-md-4">' +
                        '<label class="form-label">' + labels.title + '</label>' +
                        '<input type="text" name="links[' + idx + '][title]" class="form-control" value="' + (data.title || '') + '">' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<label class="form-label">' + labels.url + '</label>' +
                        '<input type="text" name="links[' + idx + '][url]" class="form-control" value="' + (data.url || '') + '">' +
                    '</div>' +
                    '<div class="col-md-2">' +
                        '<label class="form-label">' + labels.order + '</label>' +
                        '<input type="number" name="links[' + idx + '][sort_order]" class="form-control" value="' + (data.sort_order ?? idx) + '">' +
                    '</div>' +
                    '<div class="col-md-2 d-flex align-items-center">' +
                        '<div class="form-check form-switch mt-4">' +
                            '<input class="form-check-input" type="checkbox" name="links[' + idx + '][is_active]" ' + (data.is_active === false ? '' : 'checked') + '>' +
                            '<label class="form-check-label">' + labels.active + '</label>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-12">' +
                        '<label class="form-label">' + labels.description + '</label>' +
                        '<textarea name="links[' + idx + '][description]" class="form-control" rows="2">' + (data.description || '') + '</textarea>' +
                    '</div>' +
                '</div>';
            row.querySelector('.btn-close').addEventListener('click', ()=> row.remove());
            container.appendChild(row);
            idx++;
        }
        addBtn.addEventListener('click', ()=> addRow());
        // preload existing
        @php
            $courseLinks = $course->links()->orderBy('sort_order')->get(['title','url','description','sort_order','is_active'])
        @endphp
        const existing = @json($courseLinks);
        if (existing.length) {
            existing.forEach(e => addRow(e));
        } else {
            addRow();
        }
    })();
</script>
@endpush
