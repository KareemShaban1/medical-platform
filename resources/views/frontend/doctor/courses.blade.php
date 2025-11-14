@extends('frontend.layouts.app')

@section('title', __('My Course Enrollments'))

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
	<div class="max-w-6xl mx-auto space-y-8">
		<div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
			<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-teal-500 to-emerald-500 text-white">
				<div>
					<p class="text-sm uppercase tracking-wider text-white/80">{{ __('Doctor portal') }}</p>
					<h2 class="text-2xl font-semibold">{{ __('My Course Enrollments') }}</h2>
				</div>
				<div class="flex items-center gap-3">
					<a href="{{ route('doctor.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-full text-sm font-medium transition">
						<i class="fas fa-long-arrow-alt-left"></i>
						{{ __('Back to dashboard') }}
					</a>
				</div>
			</div>

			<div class="p-6">
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
					<div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
						<p class="text-sm text-gray-500">{{ __('Total enrollments') }}</p>
						<p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
					</div>
					<div class="rounded-xl border border-green-100 bg-green-50 px-4 py-3">
						<p class="text-sm text-gray-500">{{ __('Approved') }}</p>
						<p class="text-2xl font-semibold text-green-700">{{ $stats['approved'] }}</p>
					</div>
					<div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3">
						<p class="text-sm text-gray-500">{{ __('Pending') }}</p>
						<p class="text-2xl font-semibold text-amber-600">{{ $stats['pending'] }}</p>
					</div>
					<div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
						<p class="text-sm text-gray-500">{{ __('Rejected') }}</p>
						<p class="text-2xl font-semibold text-gray-700">{{ $stats['rejected'] }}</p>
					</div>
				</div>

				<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
					<div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
						<div>
							<h3 class="text-lg font-semibold text-gray-900">{{ __('Enrollment timeline') }}</h3>
							<p class="text-sm text-gray-500">{{ __('Track your courses and access materials when approved') }}</p>
						</div>
					</div>

					@if($enrollments->count())
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-100">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Course') }}</th>
										<th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Duration') }}</th>
										<th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
										<th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Enrolled on') }}</th>
										<th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-100">
									@foreach($enrollments as $enrollment)
										<tr class="hover:bg-gray-50 transition">
											<td class="px-6 py-4">
												<div class="font-semibold text-gray-900">{{ $enrollment->course?->title ?? __('Course') . ' #' . $enrollment->course_id }}</div>
												<p class="text-sm text-gray-500">
													{{ Str::limit(app()->getLocale() === 'ar' ? $enrollment->course?->description_ar : $enrollment->course?->description_en, 80) }}
												</p>
											</td>
											<td class="px-6 py-4 text-sm text-gray-700">
												{{ $enrollment->course?->duration ?? '—' }} {{ __('weeks') }}
											</td>
											<td class="px-6 py-4">
												@php
													$badgeClasses = match($enrollment->status) {
														'approved' => 'bg-green-100 text-green-700',
														'rejected' => 'bg-gray-200 text-gray-700',
														default => 'bg-amber-100 text-amber-700',
													};
												@endphp
												<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClasses }}">
													{{ ucfirst($enrollment->status) }}
												</span>
											</td>
											<td class="px-6 py-4 text-sm text-gray-700">
												{{ $enrollment->created_at?->format('M d, Y') ?? '—' }}
											</td>
											<td class="px-6 py-4">
												@if($enrollment->status === 'approved' && $enrollment->course?->url)
													<a href="{{ $enrollment->course->url }}" target="_blank"
														class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white rounded-lg text-sm font-semibold shadow hover:bg-emerald-600 transition">
														<i class="fas fa-external-link-alt"></i>
														{{ __('Open course') }}
													</a>
												@elseif($enrollment->status === 'pending')
													<span class="text-sm text-gray-500">{{ __('Awaiting approval') }}</span>
												@else
													<span class="text-sm text-gray-400">{{ __('No actions available') }}</span>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="px-6 py-4 border-t border-gray-100">
							{{ $enrollments->links() }}
						</div>
					@else
						<div class="p-8 text-center">
							<i class="fas fa-graduation-cap text-4xl text-gray-300 mb-4"></i>
							<p class="text-lg font-semibold text-gray-800 mb-2">{{ __('No enrollments yet') }}</p>
							<p class="text-gray-500 mb-6">{{ __('Explore our available courses and enroll to see them listed here.') }}</p>
							<a href="{{ route('courses') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-teal-500 text-white rounded-xl shadow hover:bg-teal-600 transition">
								{{ __('Browse courses') }}
								<i class="fas fa-arrow-right"></i>
							</a>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


