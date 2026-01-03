@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Edit Banner'))

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<ol class="breadcrumb m-0">
						<li class="breadcrumb-item"><a
								href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
						</li>
						<li class="breadcrumb-item"><a
								href="{{ route('admin.banners.index') }}">{{ __('Banners') }}</a>
						</li>
						<li class="breadcrumb-item active">{{ __('Edit') }}</li>
					</ol>
				</div>
				<h4 class="page-title">{{ __('Edit Banner') }}</h4>
			</div>
		</div>
	</div>

	<form id="banner-form" method="POST" action="{{ route('admin.banners.update', $banner->id) }}"
		enctype="multipart/form-data">
		@csrf
		@method('PUT')
		<div class="row">
			<div class="col-lg-8">
				<div class="card">
					<div class="card-body">
						<h5 class="mb-3">{{ __('Basic Information') }}</h5>
						<div class="mb-3">
							<label
								class="form-label">{{ __('Title') }}</label>
							<input type="text" name="title"
								class="form-control"
								value="{{ old('title', $banner->title) }}">
						</div>
						<div class="mb-3">
							<label
								class="form-label">{{ __('Description') }}</label>
							<textarea name="description" class="form-control"
								rows="3">{{ old('description', $banner->description) }}</textarea>
						</div>
						<div class="mb-3">
							<label class="form-label">{{ __('Content') }}
								(HTML)</label>
							<div id="content" style="min-height: 300px;">{!!
								old('content', $banner->content) !!}
							</div>
							<textarea name="content" id="content-textarea"
								style="display: none;">{{ old('content', $banner->content) }}</textarea>
							<small
								class="text-muted">{{ __('You can use the editor to format your content') }}</small>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-body">
						<h5 class="mb-3">{{ __('Media') }}</h5>
						@if($banner->image)
						<div class="mb-2">
							<label
								class="form-label">{{ __('Current Image') }}</label>
							<div>
								<img src="{{ $banner->image }}"
									class="img-thumbnail"
									style="max-width: 200px;">
							</div>
						</div>
						@endif
						<div class="mb-3">
							<label
								class="form-label">{{ __('Banner Image') }}</label>
							<input type="file" name="image"
								class="form-control" accept="image/*">
							<small
								class="text-muted">{{ __('Leave empty to keep current image. Recommended size: 1920x600px. Max size: 5MB') }}</small>
							<div id="image-preview" class="mt-2"></div>
						</div>
						@if($banner->mobile_image)
						<div class="mb-2">
							<label
								class="form-label">{{ __('Current Mobile Image') }}</label>
							<div>
								<img src="{{ $banner->mobile_image }}"
									class="img-thumbnail"
									style="max-width: 200px;">
							</div>
						</div>
						@endif
						<div class="mb-3">
							<label class="form-label">{{ __('Mobile Image') }}
								({{ __('Optional') }})</label>
							<input type="file" name="mobile_image"
								class="form-control" accept="image/*">
							<small
								class="text-muted">{{ __('Leave empty to keep current image. Recommended size: 768x400px. Max size: 5MB') }}</small>
							<div id="mobile-image-preview" class="mt-2"></div>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-body">
						<h5 class="mb-3">{{ __('Link Settings') }}</h5>
						<div class="mb-3">
							<label
								class="form-label">{{ __('Link URL') }}</label>
							<input type="url" name="link_url"
								class="form-control"
								placeholder="https://example.com"
								value="{{ old('link_url', $banner->link_url) }}">
						</div>
						<div class="mb-3">
							<label
								class="form-label">{{ __('Link Text') }}</label>
							<input type="text" name="link_text"
								class="form-control"
								placeholder="{{ __('Click Here') }}"
								value="{{ old('link_text', $banner->link_text) }}">
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox"
								id="open_in_new_tab"
								name="open_in_new_tab" value="1"
								{{ old('open_in_new_tab', $banner->open_in_new_tab) ? 'checked' : '' }}>
							<label class="form-check-label"
								for="open_in_new_tab">
								{{ __('Open link in new tab') }}
							</label>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-body">
						<h5 class="mb-3">{{ __('Text & Button Positioning') }}</h5>

						<!-- Text Position -->
						<div class="mb-3">
							<label
								class="form-label">{{ __('Text Position') }}</label>
							<select name="text_position" id="text_position"
								class="form-control">
								<option value="top-left"
									{{ old('text_position', $banner->text_position ?? 'center') == 'top-left' ? 'selected' : '' }}>
									{{ __('Top Left') }}</option>
								<option value="top-center"
									{{ old('text_position', $banner->text_position ?? 'center') == 'top-center' ? 'selected' : '' }}>
									{{ __('Top Center') }}
								</option>
								<option value="top-right"
									{{ old('text_position', $banner->text_position ?? 'center') == 'top-right' ? 'selected' : '' }}>
									{{ __('Top Right') }}</option>
								<option value="center-left"
									{{ old('text_position', $banner->text_position ?? 'center') == 'center-left' ? 'selected' : '' }}>
									{{ __('Center Left') }}
								</option>
								<option value="center"
									{{ old('text_position', $banner->text_position ?? 'center') == 'center' ? 'selected' : '' }}>
									{{ __('Center') }}</option>
								<option value="center-right"
									{{ old('text_position', $banner->text_position ?? 'center') == 'center-right' ? 'selected' : '' }}>
									{{ __('Center Right') }}
								</option>
								<option value="bottom-left"
									{{ old('text_position', $banner->text_position ?? 'center') == 'bottom-left' ? 'selected' : '' }}>
									{{ __('Bottom Left') }}
								</option>
								<option value="bottom-center"
									{{ old('text_position', $banner->text_position ?? 'center') == 'bottom-center' ? 'selected' : '' }}>
									{{ __('Bottom Center') }}
								</option>
								<option value="bottom-right"
									{{ old('text_position', $banner->text_position ?? 'center') == 'bottom-right' ? 'selected' : '' }}>
									{{ __('Bottom Right') }}
								</option>
								<option value="custom"
									{{ old('text_position', $banner->text_position ?? 'center') == 'custom' ? 'selected' : '' }}>
									{{ __('Custom') }}</option>
							</select>
						</div>

						<!-- Custom Text Position -->
						<div id="custom-text-position"
							style="display: {{ old('text_position', $banner->text_position ?? 'center') == 'custom' ? 'block' : 'none' }};">
							<div class="row mb-2">
								<div class="col-6">
									<label
										class="form-label">{{ __('Top') }}</label>
									<input type="text"
										name="text_position_custom[top]"
										class="form-control"
										value="{{ old('text_position_custom.top', $banner->text_position_custom['top'] ?? '') }}"
										placeholder="10%">
								</div>
								<div class="col-6">
									<label
										class="form-label">{{ __('Left') }}</label>
									<input type="text"
										name="text_position_custom[left]"
										class="form-control"
										value="{{ old('text_position_custom.left', $banner->text_position_custom['left'] ?? '') }}"
										placeholder="20%">
								</div>
							</div>
						</div>

						<!-- Button Position -->
						<div class="mb-3">
							<label
								class="form-label">{{ __('Button Position') }}</label>
							<select name="button_position"
								id="button_position"
								class="form-control">
								<option value="below-text"
									{{ old('button_position', $banner->button_position ?? 'below-text') == 'below-text' ? 'selected' : '' }}>
									{{ __('Below Text') }}
								</option>
								<option value="above-text"
									{{ old('button_position', $banner->button_position ?? 'below-text') == 'above-text' ? 'selected' : '' }}>
									{{ __('Above Text') }}
								</option>
								<option value="left-of-text"
									{{ old('button_position', $banner->button_position ?? 'below-text') == 'left-of-text' ? 'selected' : '' }}>
									{{ __('Left of Text') }}
								</option>
								<option value="right-of-text"
									{{ old('button_position', $banner->button_position ?? 'below-text') == 'right-of-text' ? 'selected' : '' }}>
									{{ __('Right of Text') }}
								</option>
								<option value="custom"
									{{ old('button_position', $banner->button_position ?? 'below-text') == 'custom' ? 'selected' : '' }}>
									{{ __('Custom') }}</option>
							</select>
						</div>

						<!-- Custom Button Position -->
						<div id="custom-button-position"
							style="display: {{ old('button_position', $banner->button_position ?? 'below-text') == 'custom' ? 'block' : 'none' }};">
							<div class="row mb-2">
								<div class="col-6">
									<label
										class="form-label">{{ __('Top') }}</label>
									<input type="text"
										name="button_position_custom[top]"
										class="form-control"
										value="{{ old('button_position_custom.top', $banner->button_position_custom['top'] ?? '') }}"
										placeholder="80%">
								</div>
								<div class="col-6">
									<label
										class="form-label">{{ __('Left') }}</label>
									<input type="text"
										name="button_position_custom[left]"
										class="form-control"
										value="{{ old('button_position_custom.left', $banner->button_position_custom['left'] ?? '') }}"
										placeholder="50%">
								</div>
							</div>
						</div>

						<!-- Text Styling -->
						<div class="mb-3">
							<label
								class="form-label">{{ __('Text Color') }}</label>
							<div class="input-group">
								<input type="color" name="text_color"
									class="form-control form-control-color"
									value="{{ old('text_color', $banner->text_color ?? '#ffffff') }}">
								<input type="text" name="text_color"
									class="form-control"
									value="{{ old('text_color', $banner->text_color ?? '#ffffff') }}"
									pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label">{{ __('Text Background Color') }}
								({{ __('Optional') }})</label>
							<div class="input-group">
								<input type="color"
									name="text_background_color"
									class="form-control form-control-color"
									value="{{ old('text_background_color', $banner->text_background_color ?? '') }}">
								<input type="text"
									name="text_background_color"
									class="form-control"
									value="{{ old('text_background_color', $banner->text_background_color ?? '') }}"
									pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label">{{ __('Text Background Opacity') }}
								(0-100)</label>
							<input type="range" name="text_background_opacity"
								class="form-range" min="0" max="100"
								value="{{ old('text_background_opacity', $banner->text_background_opacity ?? 0) }}"
								oninput="this.nextElementSibling.value = this.value">
							<output>{{ old('text_background_opacity', $banner->text_background_opacity ?? 0) }}</output>%
						</div>

						<div class="mb-3">
							<label
								class="form-label">{{ __('Text Alignment') }}</label>
							<select name="text_alignment"
								class="form-control">
								<option value="left"
									{{ old('text_alignment', $banner->text_alignment ?? 'left') == 'left' ? 'selected' : '' }}>
									{{ __('Left') }}</option>
								<option value="center"
									{{ old('text_alignment', $banner->text_alignment ?? 'left') == 'center' ? 'selected' : '' }}>
									{{ __('Center') }}</option>
								<option value="right"
									{{ old('text_alignment', $banner->text_alignment ?? 'left') == 'right' ? 'selected' : '' }}>
									{{ __('Right') }}</option>
							</select>
						</div>

						<!-- Button Styling -->
						<div class="mb-3">
							<label
								class="form-label">{{ __('Button Background Color') }}</label>
							<div class="input-group">
								<input type="color" name="button_color"
									class="form-control form-control-color"
									value="{{ old('button_color', $banner->button_color ?? '#007bff') }}">
								<input type="text" name="button_color"
									class="form-control"
									value="{{ old('button_color', $banner->button_color ?? '#007bff') }}"
									pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
							</div>
						</div>

						<div class="mb-3">
							<label
								class="form-label">{{ __('Button Text Color') }}</label>
							<div class="input-group">
								<input type="color"
									name="button_text_color"
									class="form-control form-control-color"
									value="{{ old('button_text_color', $banner->button_text_color ?? '#ffffff') }}">
								<input type="text"
									name="button_text_color"
									class="form-control"
									value="{{ old('button_text_color', $banner->button_text_color ?? '#ffffff') }}"
									pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-4">
				<div class="card">
					<div class="card-body">
						<h5 class="mb-3">{{ __('Display Settings') }}</h5>
						<div class="mb-3">
							<label class="form-label">{{ __('Banner Position') }}
								<span
									class="text-danger">*</span></label>
							@php
							$allPageSections =
							\App\Constants\BannerConstants::getPageSections();
							$homePagePositions =
							\App\Constants\BannerConstants::getHomePagePositions();
							$selectedPosition = old('position',
							$banner->position);
							$selectedPages = old('target_pages',
							$banner->target_pages ?? []);
							$isCustomPosition =
							!array_key_exists($selectedPosition,
							$homePagePositions) &&
							!collect($allPageSections)->flatten(1)->has($selectedPosition);
							@endphp
							<select name="position" id="position"
								class="form-control"
								{{ !$isCustomPosition ? 'required' : '' }}>
								@if(empty($selectedPages) ||
								in_array('home', $selectedPages))
								@foreach($homePagePositions as $value => $label)
								<option value="{{ $value }}"
									{{ $selectedPosition == $value ? 'selected' : '' }}
									data-pages="home">
									{{ $label }}
								</option>
								@endforeach
								@endif
								@foreach($allPageSections as $page => $sections)
								@if($page !== 'home' &&
								(empty($selectedPages) ||
								in_array($page, $selectedPages)))
								@foreach($sections as $value => $label)
								<option value="{{ $value }}"
									{{ $selectedPosition == $value ? 'selected' : '' }}
									data-pages="{{ $page }}"
									{{ !in_array($page, $selectedPages) && !empty($selectedPages) ? 'style="display:none;"' : '' }}>
									{{ $label }}
								</option>
								@endforeach
								@endif
								@endforeach
								@if($isCustomPosition)
								<option value="{{ $selectedPosition }}"
									selected>
									{{ $selectedPosition }}
									({{ __('Custom') }})</option>
								@endif
							</select>
							<small
								class="text-muted">{{ __('Select where this banner/ad should appear. Positions will update based on selected target pages.') }}</small>
							<div class="mt-2">
								<label class="form-check-label">
									<input type="checkbox"
										id="custom_position_toggle"
										class="form-check-input"
										{{ $isCustomPosition ? 'checked' : '' }}>
									{{ __('Use custom position (enter manually)') }}
								</label>
								<input type="text"
									name="position_custom"
									id="position_custom"
									class="form-control mt-2"
									value="{{ $isCustomPosition ? $selectedPosition : '' }}"
									style="display: {{ $isCustomPosition ? 'block' : 'none' }};"
									placeholder="e.g., header, sidebar, footer">
							</div>
						</div>
						<div class="mb-3">
							<label
								class="form-label">{{ __('Priority') }}</label>
							<input type="number" name="priority"
								class="form-control"
								value="{{ old('priority', $banner->priority) }}"
								min="0" max="999">
							<small
								class="text-muted">{{ __('Higher number = displayed first') }}</small>
						</div>
						<div class="form-check mb-3">
							<input class="form-check-input" type="checkbox"
								id="status" name="status" value="1"
								{{ old('status', $banner->status) ? 'checked' : '' }}>
							<label class="form-check-label" for="status">
								{{ __('Active') }}
							</label>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-body">
						<h5 class="mb-3">{{ __('Scheduling') }}</h5>
						<div class="mb-3">
							<label
								class="form-label">{{ __('Start Date/Time') }}</label>
							<input type="datetime-local" name="start_at"
								class="form-control"
								value="{{ old('start_at', optional($banner->start_at)->format('Y-m-d\TH:i')) }}">
						</div>
						<div class="mb-3">
							<label
								class="form-label">{{ __('End Date/Time') }}</label>
							<input type="datetime-local" name="end_at"
								class="form-control"
								value="{{ old('end_at', optional($banner->end_at)->format('Y-m-d\TH:i')) }}">
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-body">
						<h5 class="mb-3">{{ __('Targeting') }}</h5>
						<div class="form-check mb-3">
							<input class="form-check-input" type="checkbox"
								id="show_on_all_pages"
								name="show_on_all_pages" value="1"
								{{ old('show_on_all_pages', $banner->show_on_all_pages) ? 'checked' : '' }}>
							<label class="form-check-label"
								for="show_on_all_pages">
								{{ __('Show on all pages') }}
							</label>
						</div>

						<hr>

						<div class="mb-3">
							<label
								class="form-label">{{ __('Target Pages') }}</label>
							<small
								class="text-muted d-block mb-2">{{ __('Select specific pages where this banner should appear. Leave empty if "Show on all pages" is checked.') }}</small>

							@php
							$targetPages =
							\App\Constants\BannerConstants::getTargetPagesGrouped();
							$selectedPages = old('target_pages',
							$banner->target_pages ?? []);
							@endphp

							<div class="border rounded p-3"
								style="max-height: 300px; overflow-y: auto;">
								@foreach($targetPages as $categoryKey => $category)
								<div class="mb-3">
									<strong
										class="d-block mb-2">{{ $category['label'] }}</strong>
									@foreach($category['pages'] as $pageValue => $pageLabel)
									<div class="form-check">
										<input class="form-check-input target-page"
											type="checkbox"
											name="target_pages[]"
											value="{{ $pageValue }}"
											id="target_{{ str_replace(['.', '-'], '_', $pageValue) }}"
											{{ in_array($pageValue, $selectedPages) ? 'checked' : '' }}>
										<label class="form-check-label"
											for="target_{{ str_replace(['.', '-'], '_', $pageValue) }}">
											{{ $pageLabel }}
										</label>
									</div>
									@endforeach
								</div>
								@endforeach
							</div>

							<div class="mt-2">
								<button type="button"
									class="btn btn-sm btn-outline-primary"
									id="select-all-pages">{{ __('Select All') }}</button>
								<button type="button"
									class="btn btn-sm btn-outline-secondary"
									id="deselect-all-pages">{{ __('Deselect All') }}</button>
							</div>
						</div>
					</div>
				</div>

				<div class="text-end">
					<button type="submit"
						class="btn btn-primary">{{ __('Update Banner') }}</button>
					<a href="{{ route('admin.banners.index') }}"
						class="btn btn-secondary">{{ __('Cancel') }}</a>
				</div>
			</div>
		</div>

		<!-- Banner Preview - Full Width -->
		<div class="row mt-4">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<h5 class="mb-3">{{ __('Live Preview') }}</h5>
						<div id="banner-preview" class="banner-preview-container"
							style="border: 2px dashed #ddd; padding: 0; background: #f9f9f9; min-height: 400px; position: relative; overflow: hidden;">
							<div id="preview-image-container"
								style="position: relative; width: 100%; min-height: 400px; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
								<img id="preview-image"
									src="{{ $banner->image ?? '' }}"
									alt=""
									style="width: 100%; height: auto; max-height: 500px; object-fit: cover; {{ $banner->image ? '' : 'display: none;' }}">
								<div id="preview-image-placeholder"
									style="padding: 40px; color: #999; text-align: center; {{ $banner->image ? 'display: none;' : '' }}">
									<i class="mdi mdi-image"
										style="font-size: 48px;"></i>
									<p class="mt-2">
										{{ __('Banner Image Preview') }}
									</p>
								</div>

								<!-- Overlay Content -->
								<div id="preview-overlay-content"
									style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; pointer-events: none;">
									<div id="preview-text-container"
										style="position: absolute; padding: 15px; max-width: 80%;">
										<h4 id="preview-title"
											style="{{ $banner->title ? '' : 'display: none;' }} margin-bottom: 10px; font-weight: bold; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); color: {{ $banner->text_color ?? '#ffffff' }};">
											{{ $banner->title }}
										</h4>
										<p id="preview-description"
											style="{{ $banner->description ? '' : 'display: none;' }} margin-bottom: 10px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); color: {{ $banner->text_color ?? '#ffffff' }};">
											{{ $banner->description }}
										</p>
										<div id="preview-html-content"
											style="{{ $banner->content ? '' : 'display: none;' }} margin-bottom: 10px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); color: {{ $banner->text_color ?? '#ffffff' }};">
											{!!
											$banner->content
											!!}
										</div>
									</div>
									<div id="preview-button-container"
										style="position: absolute; padding: 15px;">
										<a id="preview-link-button"
											href="{{ $banner->link_url ?? '#' }}"
											{{ $banner->open_in_new_tab ? 'target="_blank"' : '' }}
											style="{{ $banner->link_url ? 'display: inline-block;' : 'display: none;' }} padding: 8px 20px; background-color: {{ $banner->button_color ?? '#007bff' }}; color: {{ $banner->button_text_color ?? '#ffffff' }}; text-decoration: none; border-radius: 4px; pointer-events: auto;">
											{{ $banner->link_text ?: __('Click Here') }}
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
.ql-container {
	min-height: 300px;
	font-size: 14px;
}

.ql-editor {
	min-height: 300px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
<script>
$(document).ready(function() {
	// Initialize Quill editor
	window.quill = new Quill('#content', {
		theme: 'snow',
		modules: {
			toolbar: {
				container: [
					[{
						'header': [1, 2, 3, 4,
							5,
							6,
							false
						]
					}],
					['bold', 'italic',
						'underline',
						'strike'
					],
					[{
						'color': []
					}, {
						'background': []
					}],
					[{
						'script': 'sub'
					}, {
						'script': 'super'
					}],
					[{
						'list': 'ordered'
					}, {
						'list': 'bullet'
					}],
					[{
						'indent': '-1'
					}, {
						'indent': '+1'
					}],
					[{
						'align': []
					}],
					['blockquote',
						'code-block'
					],
					['link', 'image',
						'video'
					],
					['clean']
				],
				handlers: {
					'image': function() {
						var url = prompt(
							'Enter image URL:'
							);
						if (url) {
							var range =
								window
								.quill
								.getSelection();
							window.quill
								.insertEmbed(
									range
									.index,
									'image',
									url
								);
						}
					}
				}
			}
		},
		placeholder: 'Enter content here...'
	});

	// Handle image paste
	window.quill.root.addEventListener('paste', function(e) {
		var clipboardData = e.clipboardData;
		if (clipboardData && clipboardData.items && clipboardData
			.items.length) {
			var item = clipboardData.items[0];
			if (item.type.indexOf('image') !== -1) {
				e.preventDefault();
				var file = item.getAsFile();
				var reader = new FileReader();
				reader.onload = function(e) {
					var range = window
						.quill
						.getSelection();
					window.quill
						.insertEmbed(
							range
							.index,
							'image',
							e
							.target
							.result
						);
				};
				reader.readAsDataURL(file);
			}
		}
	});

	// Sync Quill content with hidden textarea for form submission
	window.quill.on('text-change', function() {
		var content = window.quill.root.innerHTML;
		$('#content-textarea').val(content);
		updatePreview();
	});

	// Initialize with existing content if any
	var existingContent = $('#content-textarea').val();
	if (existingContent) {
		window.quill.root.innerHTML = existingContent;
	}

	// Update textarea before form submission
	$('#banner-form').on('submit', function() {
		$('#content-textarea').val(window.quill.root.innerHTML);
	});

	// Position mapping function
	function getPositionStyles(position) {
		const positions = {
			'top-left': {
				top: '10%',
				left: '5%',
				transform: 'translate(0, 0)'
			},
			'top-center': {
				top: '10%',
				left: '50%',
				transform: 'translate(-50%, 0)'
			},
			'top-right': {
				top: '10%',
				right: '5%',
				left: 'auto',
				transform: 'translate(0, 0)'
			},
			'center-left': {
				top: '50%',
				left: '5%',
				transform: 'translate(0, -50%)'
			},
			'center': {
				top: '50%',
				left: '50%',
				transform: 'translate(-50%, -50%)'
			},
			'center-right': {
				top: '50%',
				right: '5%',
				left: 'auto',
				transform: 'translate(0, -50%)'
			},
			'bottom-left': {
				bottom: '10%',
				left: '5%',
				top: 'auto',
				transform: 'translate(0, 0)'
			},
			'bottom-center': {
				bottom: '10%',
				left: '50%',
				top: 'auto',
				transform: 'translate(-50%, 0)'
			},
			'bottom-right': {
				bottom: '10%',
				right: '5%',
				left: 'auto',
				top: 'auto',
				transform: 'translate(0, 0)'
			}
		};
		return positions[position] || positions['center'];
	}

	// Helper function to convert hex to rgba
	function hexToRgba(hex, alpha) {
		let r = parseInt(hex.slice(1, 3), 16);
		let g = parseInt(hex.slice(3, 5), 16);
		let b = parseInt(hex.slice(5, 7), 16);
		return `rgba(${r}, ${g}, ${b}, ${alpha})`;
	}

	// Update preview function
	function updatePreview() {
		// Get styling values
		let textColor = $('input[name="text_color"]').val() || '#ffffff';
		let textBgColor = $('input[name="text_background_color"]').val() || '';
		let textBgOpacity = $('input[name="text_background_opacity"]').val() || 0;
		let textAlignment = $('select[name="text_alignment"]').val() || 'left';
		let buttonColor = $('input[name="button_color"]').val() || '#007bff';
		let buttonTextColor = $('input[name="button_text_color"]').val() || '#ffffff';

		// Update title
		let title = $('input[name="title"]').val();
		if (title) {
			$('#preview-title').text(title).show().css('color', textColor);
		} else {
			$('#preview-title').hide();
		}

		// Update description
		let description = $('textarea[name="description"]').val();
		if (description) {
			$('#preview-description').text(description).show().css('color',
				textColor);
		} else {
			$('#preview-description').hide();
		}

		// Update content
		let content = '';
		if (typeof window.quill !== 'undefined' && window.quill) {
			content = window.quill.root.innerHTML;
		} else {
			content = $('#content-textarea').val();
		}
		if (content && content.trim() !== '' && content.trim() !== '<p><br></p>') {
			$('#preview-html-content').html(content).show().css('color',
				textColor);
		} else {
			$('#preview-html-content').hide();
		}

		// Apply text container background
		let textContainer = $('#preview-text-container');
		if (textBgColor && textBgOpacity > 0) {
			let rgba = hexToRgba(textBgColor, textBgOpacity / 100);
			textContainer.css({
				'background-color': rgba,
				'padding': '15px',
				'border-radius': '4px'
			});
		} else {
			textContainer.css({
				'background-color': 'transparent',
				'padding': '15px'
			});
		}

		// Apply text alignment
		textContainer.css('text-align', textAlignment);

		// Update text position
		let textPosition = $('#text_position').val() || 'center';
		let textPosStyles = {};
		if (textPosition === 'custom') {
			let customTop = $('input[name="text_position_custom[top]"]').val();
			let customLeft = $('input[name="text_position_custom[left]"]').val();
			textPosStyles = {
				top: customTop || '50%',
				left: customLeft || '50%',
				transform: 'translate(-50%, -50%)',
				right: 'auto',
				bottom: 'auto'
			};
		} else {
			textPosStyles = getPositionStyles(textPosition);
		}

		// Clear conflicting styles first
		textContainer.css({
			top: '',
			left: '',
			right: '',
			bottom: '',
			transform: '',
			marginTop: '',
			marginBottom: ''
		});

		// Apply text position styles
		textContainer.css(textPosStyles);

		// Update button position
		let buttonPosition = $('#button_position').val() || 'below-text';
		let buttonContainer = $('#preview-button-container');
		let buttonPosStyles = {};

		if (buttonPosition === 'custom') {
			let customTop = $('input[name="button_position_custom[top]"]').val();
			let customLeft = $('input[name="button_position_custom[left]"]')
				.val();
			buttonPosStyles = {
				top: customTop || '80%',
				left: customLeft || '50%',
				transform: 'translate(-50%, -50%)',
				bottom: 'auto',
				right: 'auto'
			};
		} else {
			// Position relative to text container
			// Get text container's computed position
			let textTop = textPosStyles.top || '50%';
			let textLeft = textPosStyles.left || '50%';
			let textTransform = textPosStyles.transform ||
				'translate(-50%, -50%)';

			// Extract numeric value from percentage
			let extractPercent = (val) => {
				if (!val || val === 'auto') return 50;
				if (typeof val === 'string' && val.includes(
						'%')) {
					return parseFloat(val.replace('%', ''));
				}
				return parseFloat(val) || 50;
			};

			let textTopPercent = extractPercent(textTop);
			let textLeftPercent = extractPercent(textLeft);

			switch (buttonPosition) {
				case 'below-text':
					// Position button below text, maintaining horizontal alignment
					buttonPosStyles = {
						top: (textTopPercent + 15) +
							'%',
						left: textPosStyles.left || (
							textLeftPercent +
							'%'),
						right: textPosStyles.right ||
							'auto',
						transform: textTransform,
						bottom: 'auto'
					};
					break;
				case 'above-text':
					// Position button above text
					buttonPosStyles = {
						bottom: (100 - textTopPercent +
							15) + '%',
						top: 'auto',
						left: textPosStyles.left || (
							textLeftPercent +
							'%'),
						right: textPosStyles.right ||
							'auto',
						transform: textTransform,
					};
					break;
				case 'left-of-text':
					// Position button to the left of text
					buttonPosStyles = {
						top: textTopPercent + '%',
						left: '5%',
						right: 'auto',
						transform: 'translate(0, -50%)',
						bottom: 'auto'
					};
					break;
				case 'right-of-text':
					// Position button to the right of text
					buttonPosStyles = {
						top: textTopPercent + '%',
						right: '5%',
						left: 'auto',
						transform: 'translate(0, -50%)',
						bottom: 'auto'
					};
					break;
			}
		}

		// Clear any conflicting styles first
		buttonContainer.css({
			top: '',
			left: '',
			right: '',
			bottom: '',
			transform: '',
			marginTop: '',
			marginBottom: ''
		});

		// Apply new styles
		buttonContainer.css(buttonPosStyles);

		// Update link button
		let linkUrl = $('input[name="link_url"]').val();
		let linkText = $('input[name="link_text"]').val() || '{{ __("Click Here") }}';
		if (linkUrl) {
			$('#preview-link-button').attr('href', linkUrl).text(linkText).css({
				'display': 'inline-block',
				'background-color': buttonColor,
				'color': buttonTextColor
			});
			if ($('#open_in_new_tab').is(':checked')) {
				$('#preview-link-button').attr('target', '_blank');
			} else {
				$('#preview-link-button').removeAttr('target');
			}
		} else {
			$('#preview-link-button').css('display', 'none');
		}
	}

	// Debounce function for better performance
	function debounce(func, wait) {
		let timeout;
		return function executedFunction(...args) {
			const later = () => {
				clearTimeout(timeout);
				func(...args);
			};
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
		};
	}

	// Create debounced version of updatePreview for text inputs
	const debouncedUpdatePreview = debounce(updatePreview, 300);

	// Show/hide custom position fields
	$('#text_position').on('change', function() {
		if ($(this).val() === 'custom') {
			$('#custom-text-position').show();
		} else {
			$('#custom-text-position').hide();
		}
		updatePreview();
	});

	$('#button_position').on('change', function() {
		if ($(this).val() === 'custom') {
			$('#custom-button-position').show();
		} else {
			$('#custom-button-position').hide();
		}
		updatePreview();
	});

	// Update preview on input changes (with debounce for text inputs)
	$('input[name="title"]').on('input', debouncedUpdatePreview);
	$('textarea[name="description"]').on('input', debouncedUpdatePreview);
	$('input[name="link_url"]').on('input', debouncedUpdatePreview);
	$('input[name="link_text"]').on('input', debouncedUpdatePreview);
	$('#open_in_new_tab').on('change', updatePreview);

	// Update preview on positioning and styling changes
	$('#text_position, #button_position').on('change', updatePreview);
	$('input[name="text_position_custom[top]"], input[name="text_position_custom[left]"]').on(
		'input', debouncedUpdatePreview);
	$('input[name="button_position_custom[top]"], input[name="button_position_custom[left]"]')
		.on('input', debouncedUpdatePreview);
	$('input[name="text_color"], input[name="text_background_color"], input[name="text_background_opacity"]')
		.on('input change', updatePreview);
	$('select[name="text_alignment"]').on('change', updatePreview);
	$('input[name="button_color"], input[name="button_text_color"]').on('input change',
		updatePreview);
	// Image preview
	$('input[name="image"]').on('change', function(e) {
		let file = e.target.files[0];
		if (file) {
			let reader = new FileReader();
			reader.onload = function(e) {
				$('#image-preview').html(
					'<img src="' +
					e.target
					.result +
					'" class="img-thumbnail" style="max-width: 300px;">'
				);
				// Update preview image
				$('#preview-image').attr(
						'src', e
						.target
						.result)
					.show();
				$('#preview-image-placeholder')
					.hide();
			};
			reader.readAsDataURL(file);
		}
	});

	// Mobile image preview
	$('input[name="mobile_image"]').on('change', function(e) {
		let file = e.target.files[0];
		if (file) {
			let reader = new FileReader();
			reader.onload = function(e) {
				$('#mobile-image-preview')
					.html('<img src="' +
						e.target
						.result +
						'" class="img-thumbnail" style="max-width: 300px;">'
					);
			};
			reader.readAsDataURL(file);
		}
	});

	// Form submission
	$('#banner-form').on('submit', function(e) {
		e.preventDefault();

		let formData = new FormData(this);

		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				if (response
					.success
				) {
					Swal.fire("{{ __('Success!') }}",
							response
							.message,
							'success'
						)
						.then(() => {
							if (response
								.redirect
							) {
								window.location
									.href =
									response
									.redirect;
							}
						});
				}
			},
			error: function(xhr) {
				if (xhr.status ===
					422
				) {
					let errors =
						xhr
						.responseJSON
						.errors;
					let errorMsg =
						Object
						.values(
							errors
						)
						.flat()
						.join(
							'<br>'
						);
					Swal.fire("{{ __('Validation Error!') }}",
						errorMsg,
						'error'
					);
				} else {
					Swal.fire("{{ __('Error!') }}",
						xhr
						.responseJSON
						?.message ||
						'{{ __("An error occurred") }}',
						'error'
					);
				}
			}
		});
	});

	// Ensure preview container is visible
	if ($('#banner-preview').length) {
		$('#banner-preview').show();
	}

	// Initialize preview immediately to apply saved positions
	if (typeof updatePreview === 'function') {
		updatePreview();
	}

	// Also update after a short delay to ensure editor is ready
	setTimeout(function() {
		if (typeof updatePreview === 'function') {
			updatePreview();
		}
	}, 500);

	// Handle "Show on all pages" checkbox
	$('#show_on_all_pages').on('change', function() {
		if ($(this).is(':checked')) {
			$('.target-page').prop('disabled', true).prop(
				'checked', false);
		} else {
			$('.target-page').prop('disabled', false);
		}
	});

	// Initialize state on page load
	if ($('#show_on_all_pages').is(':checked')) {
		$('.target-page').prop('disabled', true);
	}

	// Select/Deselect All buttons
	$('#select-all-pages').on('click', function() {
		if (!$('#show_on_all_pages').is(':checked')) {
			$('.target-page').prop('checked', true);
		}
	});

	$('#deselect-all-pages').on('click', function() {
		$('.target-page').prop('checked', false);
	});

	// Handle custom position toggle
	$('#custom_position_toggle').on('change', function() {
		if ($(this).is(':checked')) {
			$('#position').hide().prop('required', false);
			$('#position_custom').show().prop('required',
				true);
		} else {
			$('#position').show().prop('required', true);
			$('#position_custom').hide().prop('required',
				false);
		}
	});

	// Page sections data for dynamic position updates
	const pageSections = @json(\App\Constants\BannerConstants::getPageSections());
	const homePagePositions = @json(\App\Constants\BannerConstants::getHomePagePositions());
	let currentSelectedPosition = $('#position').val();

	// Function to update position dropdown based on selected target pages
	function updatePositionDropdown() {
		const selectedPages = [];
		$('.target-page:checked').each(function() {
			selectedPages.push($(this).val());
		});

		// If "Show on all pages" is checked, show all positions
		const showOnAllPages = $('#show_on_all_pages').is(':checked');

		// Get current selected position value
		const currentPosition = $('#position').val();

		// Clear and rebuild position dropdown
		$('#position').empty();

		// Add homepage positions if home is selected or show on all pages
		if (showOnAllPages || selectedPages.length === 0 || selectedPages.includes(
				'home')) {
			$.each(homePagePositions, function(value, label) {
				$('#position').append(
					$(
						'<option></option>'
						)
					.attr('value',
						value)
					.attr('data-pages',
						'home')
					.text(label)
					.prop('selected',
						value ===
						currentPosition
					)
				);
			});
		}

		// Add sections for selected pages
		if (showOnAllPages) {
			// Show all sections for all pages
			$.each(pageSections, function(page, sections) {
				if (page !== 'home') {
					$.each(sections, function(value,
						label
					) {
						$('#position')
							.append(
								$(
									'<option></option>'
									)
								.attr('value',
									value
								)
								.attr('data-pages',
									page
								)
								.text(
									label
									)
								.prop('selected',
									value ===
									currentPosition
								)
							);
					});
				}
			});
		} else if (selectedPages.length > 0) {
			// Show sections only for selected pages
			selectedPages.forEach(function(page) {
				if (pageSections[page]) {
					$.each(pageSections[page],
						function(value,
							label
						) {
							$('#position')
								.append(
									$(
										'<option></option>'
										)
									.attr('value',
										value
									)
									.attr('data-pages',
										page
									)
									.text(
										label
										)
									.prop('selected',
										value ===
										currentPosition
									)
								);
						});
				}
			});
		}

		// If current position is not in the list, add it as custom
		if (currentPosition && !$('#position option[value="' + currentPosition + '"]')
			.length) {
			$('#position').append(
				$('<option></option>')
				.attr('value', currentPosition)
				.text(currentPosition + ' ({{ __("Custom") }})')
				.prop('selected', true)
			);
		}

		// Update current selected position
		currentSelectedPosition = $('#position').val();
	}

	// Update position dropdown when target pages change
	$('.target-page, #show_on_all_pages').on('change', function() {
		updatePositionDropdown();
	});

	// Initialize position dropdown on page load
	updatePositionDropdown();

	// Update form submission to use custom position if enabled
	$('#banner-form').on('submit', function(e) {
		if ($('#custom_position_toggle').is(':checked') && $(
				'#position_custom').val()) {
			$('#position').val($('#position_custom').val());
		}
	});
});
</script>
@endpush
