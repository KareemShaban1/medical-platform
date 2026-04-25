@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container mt-4 card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h4>{{ __('Clinic Users Payslips') }}</h4>

		<div class="d-flex justify-content-between align-items-center mb-3">
			<div>
				<label for="monthFilter" class="me-2">{{ __('Month') }}:</label>
				<input type="month" id="monthFilter" class="form-control d-inline-block"
					style="width: 200px;" value="{{ now()->format('Y-m') }}">
			</div>
		</div>
	</div>
	<div class="card-body">


		<table id="clinicUsersTable" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>{{ __('Name') }}</th>
					<th>{{ __('Has Payslip') }}</th>
					<th>{{ __('Action') }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
	const table = $('#clinicUsersTable').DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: '{{ route("clinic.payslips.data") }}',
			data: d => {
				d.month = $('#monthFilter')
					.val();
			}
		},
		columns: [{
				data: 'name',
				name: 'name'
			},
			{
				data: 'has_payslip',
				name: 'has_payslip'
			},
			{
				data: 'action',
				name: 'action',
				orderable: false,
				searchable: false
			},
		]
	});

	$('#monthFilter').on('change', function() {
		table.ajax.reload();
	});

	$(document).on('click', '.add-payslip', function() {
		const userId = $(this).data('user-id');
		const createUrl =
			'{{ route("clinic.payslips.create", ["userId" => "__USER_ID__"]) }}'
			.replace('__USER_ID__', userId);
		window.location.href = createUrl;
	});

	$(document).on('click', '.edit-payslip', function() {
		const payslipId = $(this).data('id');
		const editUrl = '{{ route("clinic.payslips.edit", ":id") }}'
			.replace(':id', payslipId);
		window.location.href = editUrl;
	});

});
</script>
@endpush
