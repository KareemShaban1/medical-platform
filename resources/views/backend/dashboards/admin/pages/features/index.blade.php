@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Features Management'))

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					@hasPermission('create feature')
					<button class="btn btn-primary" onclick="createFeature()">
						<i class="mdi mdi-plus"></i> {{ __('Add Feature') }}
					</button>
					@endhasPermission
				</div>
				<h4 class="page-title">{{ __('Features Management') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="features-table" class="table dt-responsive nowrap w-100">
						<thead>
							<tr>
								<th>{{ __('ID') }}</th>
								<th>{{ __('Code') }}</th>
								<th>{{ __('Name') }}</th>
								<th>{{ __('Type') }}</th>
								<th>{{ __('Unit') }}</th>
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

<div id="feature-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
const table = $('#features-table').DataTable({
	processing: true,
	serverSide: true,
	ajax: '{{ route('
	admin.features.data ') }}',
	order: [
		[0, 'desc']
	],
	columns: [{
			data: 'id',
			name: 'id'
		},
		{
			data: 'code',
			name: 'code'
		},
		{
			data: 'name',
			name: 'name'
		},
		{
			data: 'value_type',
			name: 'value_type'
		},
		{
			data: 'unit',
			name: 'unit'
		},
		{
			data: 'is_active',
			name: 'is_active'
		},
		{
			data: 'action',
			name: 'action',
			orderable: false,
			searchable: false
		},
	]
});

window.createFeature = function() {
	$.get('{{ route('
		admin.features.create ') }}',
		function(resp) {
			if (resp.success && resp.html) {
				$('#feature-modal').html(resp
						.html)
					.modal('show');
			}
		}).fail(function() {
		// Fallback
		$.get('{{ route('
			admin.features
			.index ') }}?modal=create',
			function(resp) {
				if (resp.success &&
					resp
					.html
				) {
					$('#feature-modal')
						.html(resp
							.html
						)
						.modal(
							'show'
						);
				}
			});
	});
};

window.editFeature = function(id) {
	$.get('{{ route('
		admin.features.show ', ['
		id ' => '
		__ID__ ']) }}'.replace('__ID__', id),
		function(resp) {
			if (resp.success && resp.html) {
				$('#feature-modal').html(resp
						.html)
					.modal('show');
			}
		});
};

window.deleteFeature = function(id) {
	Swal.fire({
			title: '{{ __('
			Are you sure ? ') }}',
			text : '{{ __('
			This will delete the feature
			.Make sure it\ 's not used in any plans.'
		)
	}
}
',
icon: 'warning',
	showCancelButton: true,
	confirmButtonText: '{{ __('
Yes, delete it!') }}',
	cancelButtonText: '{{ __('
Cancel ') }}',
	confirmButtonColor: '#d33',
	cancelButtonColor: '#3085d6',
}).then((result) => {
if (result.isConfirmed) {
	$.ajax({
		url: '{{ route('
		admin.features.destroy ', ['
		id ' => '
		__ID__ ']) }}'.replace('__ID__', id),
		type: 'DELETE',
		data: {
			_token: '{{ csrf_token() }}'
		},
		success: function(resp) {
			Swal.fire({
				title: '{{ __('
				Deleted!
				') }}',
				text: resp.message ||
					'{{ __('
				Feature deleted successfully ') }}',
				icon: 'success',
				confirmButtonColor: '#079184',
			});
			table.ajax.reload(null,
				false);
		},
		error: function(xhr) {
			const error = xhr.responseJSON
				?.message ||
				'{{ __('
			Failed to delete feature
				') }}';
			Swal.fire({
				title: '{{ __('
				Error ') }}',
				text: error,
				icon: 'error',
				confirmButtonColor: '#079184',
			});
		}
	});
}
});
};
});
</script>
@endpush
