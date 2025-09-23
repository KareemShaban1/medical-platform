@extends('backend.dashboards.admin.layouts.app')
@section('title' , __('Courses'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> {{ __('Add Course') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Courses') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="course-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Main Image') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Duration') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@include('backend.dashboards.admin.pages.courses.scripts.index-scripts')
