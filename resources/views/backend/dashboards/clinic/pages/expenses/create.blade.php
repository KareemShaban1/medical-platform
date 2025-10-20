@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
	<div class="card-header">
		<h4 class="card-title">{{ __('Add Expense') }}</h4>
	</div>
	<div class="card-body p-4">
		<form action="{{ route('clinic.expenses.store') }}" method="POST" enctype="multipart/form-data">
			@csrf


			<div class="row d-flex justify-content-between gap-4">
				<div class="col-md-7 mb-4 border p-3 rounded">
					<div class="row" style="display: flex; align-items: center;">


						<div class="row">

							<div class="col-md-6 mb-3">
								<x-input type="select"
									name="category_id"
									id="category_id"
									label="{{ __('Category') }}"
									:options="$expenseCategories->pluck('name', 'id')->toArray()"
									placeholder="{{ __('Select a category') }}"
									required />
							</div>

							<div class="col-md-6 mb-3">
								<x-input type="select"
									name="supplier_id"
									id="supplier_id"
									label="{{ __('Supplier') }}"
									:options="$suppliers->pluck('name', 'id')->toArray()"
									placeholder="{{ __('Select a supplier') }}" />
							</div>

							<!-- Base Amount -->
							<div class="col-md-6 mb-3" id="amount">
								<x-input type="number" name="amount"
									id="amount"
									label="{{ __('Amount') }}"
									placeholder="{{ __('Enter amount') }}"
									min="0" step="0.01"
									required />
							</div>



							<!-- Effective From -->
							<div class="col-md-6 mb-3">
								<x-input type="date" name="expense_date"
									id="expense_date"
									label="{{ __('Expense Date') }}"
									required />
							</div>

							<!-- Notes -->
							<div class="col-md-12 mb-3">
								<x-input type="textarea" name="notes"
									id="notes"
									label="{{ __('Notes') }}"
									placeholder="{{ __('Enter any additional notes') }}"
									rows="4" />
							</div>




						</div>


					</div>
				</div>
				<div class="col-md-4 mb-4 border p-3 rounded">


					<!-- Images -->
					<div class="col-md-12 mb-3">
						<x-input type="file" name="images" id="images"
							label="{{ __('Additional Images') }}"
							accept="image/*" multiple preview />
					</div>
				</div>



			</div>
			<!-- Submit -->
			<button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
		</form>
	</div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {

});
</script>
@endpush
