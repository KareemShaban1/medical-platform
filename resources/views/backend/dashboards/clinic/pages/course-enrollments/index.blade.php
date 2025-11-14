@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('My Course Enrollments'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Course Enrollments') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('My Course Enrollments') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($enrollments->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Course') }}</th>
                                        <th>{{ __('Duration') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Enrolled on') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td>{{ $enrollment->id }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $enrollment->course?->title ?? ('#'.$enrollment->course_id) }}</div>
                                                <div class="text-muted small text-truncate" style="max-width: 420px;">
                                                    {{ Str::limit(app()->getLocale() === 'ar' ? ($enrollment->course?->description_ar ?? '') : ($enrollment->course?->description_en ?? ''), 120) }}
                                                </div>
                                            </td>
                                            <td>{{ $enrollment->course?->duration ?? '—' }} {{ __('weeks') }}</td>
                                            <td>
                                                @php
                                                    $badge = match($enrollment->status) {
                                                        'approved' => 'bg-success',
                                                        'rejected' => 'bg-secondary',
                                                        default => 'bg-warning',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badge }}">{{ ucfirst($enrollment->status) }}</span>
                                            </td>
                                            <td>{{ $enrollment->created_at?->format('M d, Y') ?? '—' }}</td>
                                            <td>
                                                @if($enrollment->status === 'approved' && $enrollment->course?->url)
                                                    <a href="{{ url('/courses/'.$enrollment->course->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-open-in-new"></i> {{ __('Open') }}
                                                    </a>
                                                @else
                                                    <span class="text-muted small">{{ __('No actions') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div>
                            {{ $enrollments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-school-outline text-muted" style="font-size: 48px;"></i>
                            <h5 class="mt-3">{{ __('No enrollments yet') }}</h5>
                            <p class="text-muted">{{ __('Browse available courses and enroll to see them here.') }}</p>
                            <a href="{{ route('courses') }}" class="btn btn-primary">
                                {{ __('Browse Courses') }} <i class="mdi mdi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

