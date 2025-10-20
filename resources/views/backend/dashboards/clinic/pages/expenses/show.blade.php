@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<a href="{{ route('clinic.expenses.index') }}"
						class="btn btn-secondary">
						<i class="mdi mdi-arrow-left"></i>
						{{ __('Back to Expenses') }}
					</a>
				</div>
				<h4 class="page-title">{{ __('Expense Details') }}</h4>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-6">
							<h5 class="mb-3">
								{{ __('Expense Information') }}
							</h5>

							<div class="table-responsive">
								<table
									class="table table-borderless table-nowrap mb-0">
									<tbody>
										<tr>
											<th scope="row"
												style="width: 50%;">
												{{ __('Category') }}:
											</th>
											<td>{{ $expense->category->name }}
											</td>
										</tr>




										<tr>
											<th
												scope="row">
												{{ __('Supplier') }}:
											</th>
											<td>{{ $expense->supplier->name ?? __('N/A') }}
											</td>
										</tr>



										<tr>
											<th
												scope="row">
												{{ __('Effective From') }}:
											</th>
											<td>{{ $expense->expense_date }}
											</td>
										</tr>

										<tr>
											<th
												scope="row">
												{{ __('Amount') }}:
											</th>
											<td>{{ $expense->amount }}
											</td>
										</tr>

									</tbody>
								</table>
							</div>
						</div>

						<div class="col-lg-6">
							<h5 class="mb-3">{{ __('Notes') }}</h5>

							<div class="mb-3">
								<h6>{{ __('Notes') }}:</h6>
								<p class="text-muted">
									{{ $expense->notes ?: __('No notes available') }}
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>


		</div>

		<div class="col-lg-4">


			<div class="card">
				<div class="card-body">
					<h5 class="card-title mb-3">{{ __('Expense Images') }}</h5>

					@if($expense->images)
					<div class="row">
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

						<!-- Image Modal -->
						<div class="modal fade" id="imageModal{{ $loop->index }}"
							tabindex="-1" aria-hidden="true">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<h5
											class="modal-title">
											{{ __('Expense Image') }}
										</h5>
										<button type="button"
											class="btn-close"
											data-bs-dismiss="modal"></button>
									</div>
									<div
										class="modal-body text-center">
										<img src="{{ $image }}"
											alt="Expense Image"
											class="img-fluid">
									</div>
								</div>
							</div>
						</div>
						@endforeach
					</div>
					@else
					<div class="text-center">
						<i class="mdi mdi-image-off display-4 text-muted"></i>
						<p class="text-muted mt-2">{{ __('No images available') }}
						</p>
					</div>
					@endif
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					<h5 class="card-title mb-3">{{ __('Timestamps') }}</h5>

					<div class="table-responsive">
						<table class="table table-borderless table-nowrap mb-0">
							<tbody>
								<tr>
									<th scope="row">
										{{ __('Created At') }}:
									</th>
									<td>{{ $expense->created_at->format('Y-m-d H:i:s') }}
									</td>
								</tr>
								<tr>
									<th scope="row">
										{{ __('Updated At') }}:
									</th>
									<td>{{ $expense->updated_at->format('Y-m-d H:i:s') }}
									</td>
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