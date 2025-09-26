@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <!-- <a href="{{ route('admin.rental-spaces.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> {{ __('Add Rental Space') }}
                    </a> -->
                </div>
                <h4 class="page-title">{{ __('Rental Spaces') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="rental-space-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Approval') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@include('backend.dashboards.admin.pages.rental-spaces.approval-modal')


@endsection

@include('backend.dashboards.admin.pages.rental-spaces.scripts.index-scripts')