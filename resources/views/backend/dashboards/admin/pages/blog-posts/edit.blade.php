@extends('backend.dashboards.admin.layouts.app')
@section('title' , __('Edit Blog Post'))

@section('content')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">{{ __('Edit Blog Post') }}</h4>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.blog-posts.update', $blogPost->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4 border p-3 rounded">
                <div class="row" style="display: flex; align-items: center;">
                    <!-- blog categories -->
                    <div class="col-md-6 mb-3">
                        <label for="blog_category_id" class="form-label">{{ __('Blog Category') }}</label>
                        <select name="blog_category_id" id="blog_category_id" class="form-control" required>
                            <option value="">{{ __('Select Blog Category') }}</option>
                            @foreach ($blogCategories as $blogCategory)
                                <option value="{{ $blogCategory->id }}" {{ $blogCategory->id == $blogPost->blog_category_id ? 'selected' : '' }}>{{ $blogCategory->name_en }}</option>
                            @endforeach
                        </select>
                        @error('blog_category_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Title English -->
                    <div class="col-md-6 mb-3">
                        <label for="title_en" class="form-label">{{ __('Title English') }}</label>
                        <input type="text" name="title_en" id="title_en" class="form-control" value="{{ $blogPost->title_en }}" required>
                        @error('title_en') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Title Arabic -->
                    <div class="col-md-6 mb-3">
                        <label for="title_ar" class="form-label">{{ __('Title Arabic') }}</label>
                        <input type="text" name="title_ar" id="title_ar" class="form-control" value="{{ $blogPost->title_ar }}" required>
                        @error('title_ar') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content English -->
                     <div class="col-md-6 mb-3">
                        <label for="content_en" class="form-label">{{ __('Content English') }}</label>
                        <textarea name="content_en" id="content_en" class="form-control" required>{{ $blogPost->content_en }}</textarea>
                        @error('content_en') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content Arabic -->
                    <div class="col-md-6 mb-3">
                        <label for="content_ar" class="form-label">{{ __('Content Arabic') }}</label>
                        <textarea name="content_ar" id="content_ar" class="form-control" required>{{ $blogPost->content_ar }}</textarea>
                        @error('content_ar') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>


                    <!-- Status -->
                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" class="form-check-input" id="statusToggle" name="status" value="1" {{ $blogPost->status ? 'checked' : '' }}>
                        <label class="form-check-label" for="statusToggle">{{ __('Status') }}</label>
                    </div>

                    <!-- Main Image -->
                    <div class="col-md-6 mb-3">
                        <label for="main_image" class="form-label">{{ __('Main Image') }}</label>
                        @if($blogPost->main_image)
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ $blogPost->main_image }}" class="img-thumbnail" style="max-height:150px;">
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
