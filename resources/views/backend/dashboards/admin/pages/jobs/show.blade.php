@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Jobs') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Job Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('Job Information') }}</h5>

                            <div class="table-responsive">
                                <table class="table table-borderless table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 50%;">{{ __('Title') }}:</th>
                                            <td>{{ $job->title }}</td>
                                        </tr>


                                        <tr>
                                            <th scope="row">{{ __('Status') }}:</th>
                                            <td>
                                                @if($job->status)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Approval Status') }}:</th>
                                            <td>
                                               @if($job->approvement->action == 'under_review')
                                                <span class="badge bg-warning">{{ __('Under Review') }}</span>
                                               @elseif($job->approvement->action == 'approved')
                                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                               @else
                                                <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                               @endif
                                            </td>
                                        </tr>
                                        @if($job->approvement->action && $job->approvement->notes)
                                        <tr>
                                            <th scope="row">{{ __('Approval Notes') }}:</th>
                                            <td class="text-danger">{{ $job->approvement->notes }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('Description') }}</h5>

                            <div class="mb-3">
                                <h6>{{ __('Description') }}:</h6>
                                <p class="text-muted">{{ $job->description ?: __('No description available') }}</p>
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
               <img src="{{ $job->main_image }}" alt="Job Image" 
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
                                    <td>{{ $job->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Updated At') }}:</th>
                                    <td>{{ $job->updated_at->format('Y-m-d H:i:s') }}</td>
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