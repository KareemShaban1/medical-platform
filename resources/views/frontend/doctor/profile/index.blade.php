@extends('frontend.layouts.app')

@section('title', __('Doctor Profile'))

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
	<div class="max-w-6xl mx-auto">
		<!-- Header -->
		<div class="mb-6 flex items-center justify-between">
			<div>
				<h1 class="text-3xl font-bold text-gray-900">{{ __('Doctor Profile') }}</h1>
				<p class="text-gray-600 mt-2">{{ __('Manage your professional profile') }}</p>
			</div>
			<a href="{{ route('doctor.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
				<i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
			</a>
		</div>

		@if($profile)
		<!-- Profile exists -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Main Content -->
			<div class="lg:col-span-2 space-y-6">
				<!-- Profile Overview -->
				<div class="bg-white rounded-lg shadow-md p-6">
					<div class="flex items-start gap-6">
						<div class="flex-shrink-0">
							@if($profile->profile_photo_url)
								<img src="{{ $profile->profile_photo_url }}" alt="Profile Photo"
									class="w-32 h-32 object-cover rounded-full">
							@else
								<div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center">
									<i class="fas fa-user text-5xl text-gray-400"></i>
								</div>
							@endif
						</div>
						<div class="flex-1">
							<h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $profile->name }}</h2>
							<p class="text-gray-600 mb-2">{{ $profile->email }}</p>
							@if($profile->speciality)
								<p class="text-blue-600 font-medium">{{ $profile->speciality->name_en }}</p>
							@endif
							@if($profile->years_experience)
								<p class="text-gray-500 text-sm mt-1">{{ $profile->years_experience }} {{ __('years of experience') }}</p>
							@endif
						</div>
					</div>

					@if($profile->bio)
					<div class="mt-6 pt-6 border-t border-gray-200">
						<h3 class="text-lg font-semibold text-gray-800 mb-2">{{ __('Bio') }}</h3>
						<p class="text-gray-600">{{ $profile->bio }}</p>
					</div>
					@endif

					@if($profile->specialties && count($profile->specialties) > 0)
					<div class="mt-6 pt-6 border-t border-gray-200">
						<h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('Specialties') }}</h3>
						<div class="flex flex-wrap gap-2">
							@foreach($profile->specialties as $specialty)
								<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">{{ $specialty }}</span>
							@endforeach
						</div>
					</div>
					@endif

					@if($profile->services_offered && count($profile->services_offered) > 0)
					<div class="mt-6 pt-6 border-t border-gray-200">
						<h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('Services Offered') }}</h3>
						<div class="flex flex-wrap gap-2">
							@foreach($profile->services_offered as $service)
								<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">{{ $service }}</span>
							@endforeach
						</div>
					</div>
					@endif

					<div class="mt-6 pt-6 border-t border-gray-200 flex gap-3">
						@if($profile->canBeEdited())
							<a href="{{ route('doctor.profile.edit') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
								<i class="fas fa-edit"></i> {{ __('Edit Profile') }}
							</a>
						@endif
						@if(in_array($profile->status, ['draft', 'rejected']))
							<button onclick="submitProfile()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
								<i class="fas fa-paper-plane"></i> {{ __('Submit for Review') }}
							</button>
						@endif
					</div>
				</div>
			</div>

			<!-- Sidebar - Approval Status Tracker -->
			<div class="space-y-6">
				<!-- Approval Status Timeline -->
				<div class="bg-white rounded-lg shadow-md p-6">
					<h3 class="text-xl font-semibold text-gray-800 mb-6">{{ __('Approval Status') }}</h3>

					<!-- Status Timeline -->
					<div class="relative">
						<!-- Timeline Line -->
						<div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>

						<!-- Draft Step -->
						<div class="relative flex items-start gap-4 mb-6">
							<div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
								{{ $profile->status === 'draft' ? 'bg-blue-500 ring-4 ring-blue-200' :
								(in_array($profile->status, ['pending', 'approved', 'rejected']) ? 'bg-green-500' : 'bg-gray-300') }}">
								@if(in_array($profile->status, ['pending', 'approved', 'rejected']))
									<i class="fas fa-check text-white text-xs"></i>
								@else
									<i class="fas fa-file text-white text-xs"></i>
								@endif
							</div>
							<div class="flex-1">
								<h4 class="font-semibold text-gray-900">{{ __('Profile Created') }}</h4>
								<p class="text-sm text-gray-500">{{ __('Your profile has been created') }}</p>
								@if($profile->status === 'draft')
									<span class="inline-block mt-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ __('Current Status') }}</span>
								@endif
							</div>
						</div>

						<!-- Pending Step -->
						<div class="relative flex items-start gap-4 mb-6">
							<div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
								{{ $profile->status === 'pending' ? 'bg-yellow-500 ring-4 ring-yellow-200 animate-pulse' :
								($profile->status === 'approved' ? 'bg-green-500' :
								($profile->status === 'rejected' ? 'bg-red-500' : 'bg-gray-300')) }}">
								@if($profile->status === 'pending')
									<i class="fas fa-clock text-white text-xs"></i>
								@elseif(in_array($profile->status, ['approved', 'rejected']))
									<i class="fas fa-check text-white text-xs"></i>
								@else
									<i class="fas fa-circle text-white text-xs"></i>
								@endif
							</div>
							<div class="flex-1">
								<h4 class="font-semibold text-gray-900">{{ __('Under Review') }}</h4>
								<p class="text-sm text-gray-500">{{ __('Admin is reviewing your profile') }}</p>
								@if($profile->status === 'pending')
									<span class="inline-block mt-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded animate-pulse">{{ __('In Progress') }}</span>
									@if($profile->reviewed_at)
										<p class="text-xs text-gray-400 mt-1">{{ __('Submitted') }}: {{ $profile->reviewed_at->format('M d, Y') }}</p>
									@endif
								@endif
							</div>
						</div>

						<!-- Approved/Rejected Step -->
						<div class="relative flex items-start gap-4">
							<div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
								{{ $profile->status === 'approved' ? 'bg-green-500 ring-4 ring-green-200' :
								($profile->status === 'rejected' ? 'bg-red-500 ring-4 ring-red-200' : 'bg-gray-300') }}">
								@if($profile->status === 'approved')
									<i class="fas fa-check-circle text-white"></i>
								@elseif($profile->status === 'rejected')
									<i class="fas fa-times-circle text-white"></i>
								@else
									<i class="fas fa-circle text-white text-xs"></i>
								@endif
							</div>
							<div class="flex-1">
								@if($profile->status === 'approved')
									<h4 class="font-semibold text-green-700">{{ __('Approved') }}</h4>
									<p class="text-sm text-gray-500">{{ __('Your profile has been approved') }}</p>
									@if($profile->reviewed_at)
										<p class="text-xs text-gray-400 mt-1">{{ __('Approved on') }}: {{ $profile->reviewed_at->format('M d, Y') }}</p>
									@endif
									@if($profile->reviewer)
										<p class="text-xs text-gray-400">{{ __('By') }}: {{ $profile->reviewer->name }}</p>
									@endif
									<span class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded">{{ __('Active') }}</span>
								@elseif($profile->status === 'rejected')
									<h4 class="font-semibold text-red-700">{{ __('Rejected') }}</h4>
									<p class="text-sm text-gray-500">{{ __('Your profile needs revision') }}</p>
									@if($profile->rejection_reason)
										<div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
											<p class="text-sm font-medium text-red-800 mb-1">{{ __('Rejection Reason') }}:</p>
											<p class="text-sm text-red-700">{{ $profile->rejection_reason }}</p>
										</div>
									@endif
									@if($profile->reviewed_at)
										<p class="text-xs text-gray-400 mt-2">{{ __('Reviewed on') }}: {{ $profile->reviewed_at->format('M d, Y') }}</p>
									@endif
									<span class="inline-block mt-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded">{{ __('Needs Revision') }}</span>
								@else
									<h4 class="font-semibold text-gray-700">{{ __('Final Decision') }}</h4>
									<p class="text-sm text-gray-500">{{ __('Waiting for admin review') }}</p>
								@endif
							</div>
						</div>
					</div>

					<!-- Progress Bar -->
					<div class="mt-6 pt-6 border-t border-gray-200">
						<div class="flex items-center justify-between mb-2">
							<span class="text-sm font-medium text-gray-700">{{ __('Progress') }}</span>
							<span class="text-sm text-gray-500">
								@php
									$progress = 0;
									if($profile->status === 'draft') $progress = 33;
									elseif($profile->status === 'pending') $progress = 66;
									elseif(in_array($profile->status, ['approved', 'rejected'])) $progress = 100;
								@endphp
								{{ $progress }}%
							</span>
						</div>
						<div class="w-full bg-gray-200 rounded-full h-3">
							<div class="h-3 rounded-full transition-all duration-500
								{{ $profile->status === 'approved' ? 'bg-green-500' :
								($profile->status === 'pending' ? 'bg-yellow-500' :
								($profile->status === 'rejected' ? 'bg-red-500' : 'bg-blue-500')) }}"
								style="width: {{ $progress }}%"></div>
						</div>
					</div>
				</div>

				<!-- Quick Info -->
				<div class="bg-white rounded-lg shadow-md p-6">
					<h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Profile Details') }}</h3>
					<div class="space-y-3 text-sm">
						<div>
							<span class="font-medium text-gray-700">{{ __('Phone') }}:</span>
							<span class="text-gray-600">{{ $profile->phone ?: __('Not provided') }}</span>
						</div>
						@if($profile->reviewed_at)
						<div>
							<span class="font-medium text-gray-700">{{ __('Last Reviewed') }}:</span>
							<span class="text-gray-600">{{ $profile->reviewed_at->format('M d, Y H:i') }}</span>
						</div>
						@endif
						<div>
							<span class="font-medium text-gray-700">{{ __('Created') }}:</span>
							<span class="text-gray-600">{{ $profile->created_at->format('M d, Y') }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		@else
		<!-- No profile yet -->
		<div class="bg-white rounded-lg shadow-md p-12 text-center">
			<div class="mb-6">
				<i class="fas fa-user-md text-6xl text-gray-400"></i>
			</div>
			<h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('No Profile Created Yet') }}</h2>
			<p class="text-gray-600 mb-6 max-w-md mx-auto">
				{{ __('Create your doctor profile to be visible to patients and showcase your expertise.') }}
			</p>
			<a href="{{ route('doctor.profile.create') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
				<i class="fas fa-plus"></i> {{ __('Create Your Profile') }}
			</a>
		</div>
		@endif
	</div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function submitProfile() {
    Swal.fire({
        title: '{{ __("Submit Profile for Review?") }}',
        text: "{{ __('Your profile will be submitted to admins for review and approval.') }}",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("Yes, submit it!") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("doctor.profile.submit") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire('{{ __("Submitted!") }}', response.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong") }}', 'error');
                }
            });
        }
    });
}
</script>
@endpush
@endsection


