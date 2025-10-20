@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="card mt-3">
	<div class="card-header">
		<h4 class="card-title">{{ __('Edit Expense') }}</h4>
	</div>
	<!-- error messages -->
	@if ($errors->any())
	<div class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif
	<div class="card-body p-4">
		<form action="{{ route('clinic.expenses.update', $expense->id) }}" method="POST"
			enctype="multipart/form-data">
			@csrf
			@method('PUT')

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
									:value="$expense->category_id"
									required />
							</div>

							<div class="col-md-6 mb-3">
								<x-input type="select"
									name="supplier_id"
									id="supplier_id"
									label="{{ __('Supplier') }}"
									:options="$suppliers->pluck('name', 'id')->toArray()"
									placeholder="{{ __('Select a supplier') }}"
									:value="$expense->supplier_id" />
							</div>

							<!-- Base Amount -->
							<div class="col-md-6 mb-3" id="amount_wrapper">
								<x-input type="number" name="amount"
									id="amount"
									label="{{ __('Amount') }}"
									placeholder="{{ __('Enter amount') }}"
									min="0" step="0.01"
									:value="$expense->amount" />
							</div>


							<!-- Expense Date -->
							<div class="col-md-6 mb-3">
								<x-input type="date" name="expense_date"
									id="expense_date"
									label="{{ __('Expense Date') }}"
									required
									:value="$expense->expense_date" />
							</div>

							<!-- Notes -->
							<div class="col-md-12 mb-3">
								<x-input type="textarea" name="notes"
									id="notes"
									label="{{ __('Notes') }}"
									placeholder="{{ __('Enter any additional notes') }}"
									rows="4"
									:value="$expense->notes" />
							</div>




						</div>


					</div>
				</div>
				<div class="col-md-4 mb-4 border p-3 rounded">


					<!-- Images -->
					<div class="col-md-12 mb-3">
						<x-input type="file" name="images" id="images"
							label="{{ __('Additional Images') }}"
							accept="image/*" multiple preview
							:value="$expense->images" />

						@foreach($expense->images as $image)
						<div class="col-6 mb-3">
							<div class="position-relative">
								<img src="{{ $image }}"
									alt="Expense Image"
									class="img-fluid rounded"
									style="width: 100%; height: 150px; object-fit: cover;"
									data-bs-toggle="modal"
									data-bs-target="#imageModal{{ $loop->index }}"
									style="cursor: pointer;">
							</div>
						</div>
						@endforeach
					</div>
				</div>



			</div>

			<!-- Submit -->
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

});
</script>

@endpush