@extends('backend.dashboards.clinic.layouts.app')
@section('title' , __('Jobs'))

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<a href="{{ route('clinic.jobs.create') }}" class="btn btn-primary">
						<i class="mdi mdi-plus"></i> {{ __('Add Job') }}
					</a>
				</div>
				<h4 class="page-title">{{ __('Jobs') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="job-table" class="table dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('Main Image') }}</th>
								<th>{{ __('Title') }}</th>
								<th>{{ __('Status') }}</th>
								<th>{{  __('Job Applications') }}
								</th>
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

@include('backend.dashboards.clinic.pages.jobs.scripts.index-scripts')
