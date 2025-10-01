@extends('frontend.layouts.app')

@section('title', 'Apply for ' . $job->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
	<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
		<!-- Job Information Card -->
		<div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden">
			<div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
				<h1 class="text-2xl font-bold text-white">{{ $job->title }}</h1>
				<p class="text-blue-100 mt-1">{{ $job->clinic->name ?? 'Medical Clinic' }}</p>
			</div>
			<div class="p-6">
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div class="space-y-3">
						<div class="flex items-center">
							<svg class="w-5 h-5 text-gray-500 mr-3"
								fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
								</path>
								<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
								</path>
							</svg>
							<span class="text-gray-700"><strong>Location:</strong>
								{{ $job->location }}</span>
						</div>
						<div class="flex items-center">
							<svg class="w-5 h-5 text-gray-500 mr-3"
								fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6">
								</path>
							</svg>
							<span class="text-gray-700"><strong>Type:</strong>
								{{ ucfirst($job->type) }}</span>
						</div>
					</div>
					<div class="space-y-3">
						<div class="flex items-center">
							<svg class="w-5 h-5 text-gray-500 mr-3"
								fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
								</path>
							</svg>
							<span class="text-gray-700"><strong>Salary:</strong>
								{{ $job->salary ? '$' . number_format($job->salary) : 'Not specified' }}</span>
						</div>

					</div>
				</div>
				@if($job->description)
				<div class="mt-6 pt-6 border-t border-gray-200">
					<h3 class="text-lg font-semibold text-gray-900 mb-3">Job Description
					</h3>
					<p class="text-gray-700 leading-relaxed">{{ $job->description }}</p>
				</div>
				@endif
			</div>
		</div>

		<!-- Success Message -->
		@if(session('success'))
		<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
			<div class="flex">
				<svg class="w-5 h-5 text-green-400 mr-3 mt-0.5" fill="currentColor"
					viewBox="0 0 20 20">
					<path fill-rule="evenodd"
						d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
						clip-rule="evenodd"></path>
				</svg>
				<div>
					<h3 class="text-sm font-medium text-green-800">Success!</h3>
					<div class="mt-1 text-sm text-green-700">{{ session('success') }}
					</div>
				</div>
			</div>
		</div>
		@endif

		<!-- Error Message -->
		@if(session('error'))
		<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
			<div class="flex">
				<svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="currentColor"
					viewBox="0 0 20 20">
					<path fill-rule="evenodd"
						d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
						clip-rule="evenodd"></path>
				</svg>
				<div>
					<h3 class="text-sm font-medium text-red-800">Error!</h3>
					<div class="mt-1 text-sm text-red-700">{{ session('error') }}</div>
				</div>
			</div>
		</div>
		@endif

		<!-- Application Form -->
		<div class="bg-white rounded-lg shadow-lg overflow-hidden">
			<div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
				<h2 class="text-xl font-semibold text-gray-900">Job Application Form</h2>
				<p class="text-gray-600 mt-1">Please fill out the form below to apply for this
					position.</p>
			</div>
			<div class="p-6">
				<form action="{{ route('jobs.submit-application', $job->id) }}" method="POST"
					enctype="multipart/form-data">
					@csrf

					<!-- Dynamic Application Fields -->
					@if($applicationFields && count($applicationFields) > 0)
					<div class="space-y-6">
						<div class="border-b border-gray-200 pb-4">
							<h3 class="text-lg font-medium text-gray-900">
								Application Information</h3>
							<p class="text-gray-600 mt-1">Please provide the
								following information to complete your
								application.</p>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							@foreach($applicationFields as $field)
							<div class="space-y-2">
								<label for="{{ $field->field_name }}"
									class="block text-sm font-medium text-gray-700">
									{{ $field->field_label }}
									@if($field->required)
									<span
										class="text-red-500 ml-1">*</span>
									@endif
								</label>

								@if($field->field_type == 'text' ||
								$field->field_type == 'email' ||
								$field->field_type == 'phone')
								<input type="{{ $field->field_type }}"
									name="{{ $field->field_name }}"
									id="{{ $field->field_name }}"
									class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error($field->field_name) border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
									value="{{ old($field->field_name) }}"
									{{ $field->required ? 'required' : '' }}
									placeholder="Enter {{ strtolower($field->field_label) }}">
								@elseif($field->field_type ==
								'textarea')
								<textarea name="{{ $field->field_name }}"
									id="{{ $field->field_name }}"
									rows="4"
									class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error($field->field_name) border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
									{{ $field->required ? 'required' : '' }}
									placeholder="Enter {{ strtolower($field->field_label) }}">{{ old($field->field_name) }}</textarea>
								@elseif($field->field_type == 'file')
								<div
									class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
									<div
										class="space-y-1 text-center">
										<svg class="mx-auto h-12 w-12 text-gray-400"
											stroke="currentColor"
											fill="none"
											viewBox="0 0 48 48">
											<path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
												stroke-width="2"
												stroke-linecap="round"
												stroke-linejoin="round" />
										</svg>
										<div
											class="flex text-sm text-gray-600">
											<label for="{{ $field->field_name }}"
												class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
												<span>Upload
													a
													file</span>
												<input type="file"
													name="{{ $field->field_name }}"
													id="{{ $field->field_name }}"
													class="sr-only @error($field->field_name) border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
													{{ $field->required ? 'required' : '' }}>
											</label>
											<p
												class="pl-1">
												or
												drag
												and
												drop
											</p>
										</div>
										<p
											class="text-xs text-gray-500">
											PDF, DOC,
											DOCX, JPG,
											PNG up to
											2MB</p>
									</div>
								</div>
								@elseif($field->field_type == 'select')
								<select name="{{ $field->field_name }}"
									id="{{ $field->field_name }}"
									class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error($field->field_name) border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
									{{ $field->required ? 'required' : '' }}>
									<option value="">Select an
										option</option>
									@if($field->options)
									@foreach($field->options as
									$option)
									<option value="{{ $option }}"
										{{ old($field->field_name) == $option ? 'selected' : '' }}>
										{{ $option }}
									</option>
									@endforeach
									@endif
								</select>
								@elseif($field->field_type ==
								'checkbox')
								<div class="space-y-3">
									@if($field->options)
									@foreach($field->options as
									$index => $option)
									<div
										class="flex items-center">
										<input type="checkbox"
											name="{{ $field->field_name }}[]"
											id="{{ $field->field_name }}_{{ $index }}"
											class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
											value="{{ $option }}"
											{{ in_array($option, old($field->field_name, [])) ? 'checked' : '' }}>
										<label for="{{ $field->field_name }}_{{ $index }}"
											class="ml-3 text-sm text-gray-700">
											{{ $option }}
										</label>
									</div>
									@endforeach
									@else
									<div
										class="flex items-center">
										<input type="checkbox"
											name="{{ $field->field_name }}"
											id="{{ $field->field_name }}"
											class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
											value="1"
											{{ old($field->field_name) ? 'checked' : '' }}>
										<label for="{{ $field->field_name }}"
											class="ml-3 text-sm text-gray-700">
											{{ $field->field_label }}
										</label>
									</div>
									@endif
								</div>
								@elseif($field->field_type == 'radio')
								<div class="space-y-3">
									@if($field->options)
									@foreach($field->options as
									$index => $option)
									<div
										class="flex items-center">
										<input type="radio"
											name="{{ $field->field_name }}"
											id="{{ $field->field_name }}_{{ $index }}"
											class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
											value="{{ $option }}"
											{{ old($field->field_name) == $option ? 'checked' : '' }}>
										<label for="{{ $field->field_name }}_{{ $index }}"
											class="ml-3 text-sm text-gray-700">
											{{ $option }}
										</label>
									</div>
									@endforeach
									@endif
								</div>
								@endif

								@error($field->field_name)
								<p class="mt-1 text-sm text-red-600">
									{{ $message }}
								</p>
								@enderror
							</div>
							@endforeach
						</div>
					</div>
					@else
					<div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
						<div class="flex">
							<svg class="w-5 h-5 text-blue-400 mr-3 mt-0.5"
								fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd"
									d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
									clip-rule="evenodd"></path>
							</svg>
							<div>
								<h3
									class="text-sm font-medium text-blue-800">
									No Application Fields
									Configured</h3>
								<div class="mt-1 text-sm text-blue-700">
									<p>No application fields have
										been configured for
										this job. Please
										contact the clinic
										for application
										instructions.</p>
								</div>
							</div>
						</div>
					</div>
					@endif

					<!-- Form Actions -->
					<div class="mt-8 flex justify-between items-center">
						<a href="{{ route('jobs.show', $job->id) }}"
							class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
							<svg class="w-4 h-4 mr-2" fill="none"
								stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M10 19l-7-7m0 0l7-7m-7 7h18">
								</path>
							</svg>
							Back to Job Details
						</a>
						<button type="submit"
							class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
							<svg class="w-4 h-4 mr-2" fill="none"
								stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
								</path>
							</svg>
							Submit Application
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
