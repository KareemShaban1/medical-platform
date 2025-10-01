@extends('backend.dashboards.admin.layouts.app')
@section('title' , __('Course Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Courses') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Course Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('Course Information') }}</h5>

                            <div class="table-responsive">
                                <table class="table table-borderless table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 50%;">{{ __('Title (English)') }}:</th>
                                            <td>{{ $course->title_en }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" style="width: 50%;">{{ __('Title (Arabic)') }}:</th>
                                            <td>{{ $course->title_ar }}</td>
                                        </tr>


                                        <tr>
                                            <th scope="row">{{ __('Status') }}:</th>
                                            <td>
                                                @if($course->status)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th scope="row">{{ __('Duration') }}:</th>
                                            <td>{{ $course->duration }}</td>
                                        </tr>

                                        <tr>
                                            <th scope="row">{{ __('Level') }}:</th>
                                            <td>{{ $course->level }}</td>
                                        </tr>

                                        <tr>
                                            <th scope="row">{{ __('Start Date') }}:</th>
                                            <td>{{ $course->start_date->format('Y-m-d') }}</td>
                                        </tr>

                                        <tr>
                                            <th scope="row">{{ __('End Date') }}:</th>
                                            <td>{{ $course->end_date->format('Y-m-d') }}</td>
                                        </tr>
                                       
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('Description') }}</h5>

                            <div class="mb-3">
                                <h6>{{ __('Description (English)') }}:</h6>
                                <p class="text-muted">{{ $course->description_en ?: __('No description available') }}</p>
                            </div>

                            <div class="mb-3">
                                <h6>{{ __('Description (Arabic)') }}:</h6>
                                <p class="text-muted">{{ $course->description_ar ?: __('No description available') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

      
        </div>

        <div class="col-lg-4">

        <!-- main image -->
        <div class="card">
            <div class="card-body">
               <div class="col-md-6">
               <img src="{{ $course->main_image }}" alt="Course Image" 
                class="img-fluid rounded"
                style="width: 100%; height: 150px; object-fit: contain;">
               </div>
            </div>
        </div>

        

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Timestamps') }}</h5>

                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">{{ __('Created At') }}:</th>
                                    <td>{{ $course->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Updated At') }}:</th>
                                    <td>{{ $course->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .img-fluid:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease-in-out;
    }
</style>
@endpush