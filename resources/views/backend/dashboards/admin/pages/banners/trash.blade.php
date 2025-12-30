@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Banners Trash'))

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
						<li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">{{ __('Banners') }}</a></li>
						<li class="breadcrumb-item active">{{ __('Trash') }}
						</li>
					</ol>
				</div>
				<h4 class="page-title">{{ __('Banners Trash') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="row mb-2">
						<div class="col-sm-4">
							<a href="{{ route('admin.banners.index') }}"
								class="btn btn-primary mb-2 me-2">
								<i class="mdi mdi-arrow-left me-2"></i>
								{{ __('Back to Banners') }}
							</a>
						</div>
						<div class="col-sm-8 text-sm-end">
							<button type="button" class="btn btn-success mb-2"
								onclick="refreshTable()"><i
									class="mdi mdi-refresh"></i>
								{{ __('Refresh') }}</button>
						</div>
					</div>

					<div class="table-responsive">
						<table id="banners-trash-table"
							class="table table-centered w-100 dt-responsive nowrap">
							<thead class="table-light">
								<tr>
									<th>{{ __('Image') }}</th>
									<th>{{ __('Title') }}</th>
									<th>{{ __('Position') }}</th>
									<th>{{ __('Deleted At') }}</th>
									<th style="width: 120px;">
										{{ __('Action') }}
									</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
function refreshTable() {
	$('#banners-trash-table').DataTable().ajax.reload(null, false);
}

$(document).on('click', '.restore-btn', function() {
	let id = $(this).data('id');
	if (!confirm('{{ __('Are you sure you want to restore this banner?') }}')) return;
	$.ajax({
		url: "{{ route('admin.banners.restore', ':id') }}".replace(':id', id),
		type: 'POST',
		data: {
			_token: '{{ csrf_token() }}'
		},
		success: function(res) {
			if (res.success) {
				refreshTable();
				Swal.fire('{{ __('Success!') }}', res.message, 'success');
			} else {
				Swal.fire('{{ __('Error!') }}', res.message || 'Error', 'error');
			}
		},
		error: function(xhr) {
			Swal.fire('{{ __('Error!') }}', xhr.responseJSON?.message || 'Error', 'error');
		}
	});
});

$(document).on('click', '.force-delete-btn', function() {
	let id = $(this).data('id');
	if (!confirm('{{ __('Are you sure? This action cannot be undone!') }}')) return;
	$.ajax({
		url: "{{ route('admin.banners.force-delete', ':id') }}".replace(':id', id),
		type: 'DELETE',
		data: {
			_token: '{{ csrf_token() }}'
		},
		success: function(res) {
			if (res.success) {
				refreshTable();
				Swal.fire('{{ __('Success!') }}', res.message, 'success');
			} else {
				Swal.fire('{{ __('Error!') }}', res.message || 'Error', 'error');
			}
		},
		error: function(xhr) {
			Swal.fire('{{ __('Error!') }}', xhr.responseJSON?.message || 'Error', 'error');
		}
	});
});

$(function() {
	$('#banners-trash-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: "{{ route('admin.banners.trash.data') }}",
		columns: [{
				data: 'image_preview',
				name: 'image_preview',
				orderable: false,
				searchable: false
			},
			{
				data: 'title',
				name: 'title'
			},
			{
				data: 'position',
				name: 'position'
			},
			{
				data: 'deleted_at',
				name: 'deleted_at'
			},
			{
				data: 'action',
				name: 'action',
				orderable: false,
				searchable: false
			},
		],
		order: [
			[3, 'desc']
		],
		language: {
			paginate: {
				previous: "<i class='mdi mdi-chevron-left'>",
				next: "<i class='mdi mdi-chevron-right'>"
			}
		},
		drawCallback: function() {
			$('.dataTables_paginate > .pagination')
				.addClass('pagination-rounded');
		}
	});
});
</script>
@endpush






