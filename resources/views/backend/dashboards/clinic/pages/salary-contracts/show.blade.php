@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title-right">
					<a href="{{ route('clinic.salary-contracts.index') }}"
						class="btn btn-secondary">
						<i class="mdi mdi-arrow-left"></i>
						{{ __('Back to Salary Contracts') }}
					</a>
				</div>
				<h4 class="page-title">{{ __('Salary Contract Details') }}</h4>
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
								{{ __('Salary Contract Information') }}
							</h5>

							<div class="table-responsive">
								<table
									class="table table-borderless table-nowrap mb-0">
									<tbody>
										<tr>
											<th scope="row"
												style="width: 50%;">
												{{ __('User') }}:
											</th>
											<td>{{ $salaryContract->clinicUser->name }}
											</td>
										</tr>


										

										<tr>
											<th
												scope="row">
												{{ __('Salary Type') }}:
											</th>
											<td>{{ $salaryContract->salary_type }}
											</td>
										</tr>

										@if($salaryContract->salary_type === 'fixed' || $salaryContract->salary_type === 'hourly')
										<tr>
											<th
												scope="row">
												{{ __('Amount') }}:
											</th>
											<td>
												{{ $salaryContract->base_amount }}
											</td>
										</tr>
										@elseif($salaryContract->salary_type === 'percentage')
										<tr>
											<th
												scope="row">
												{{ __('Percentage Rate') }}:
											</th>
											<td>{{ $salaryContract->percentage_rate }}
											</td>
										</tr>
										@endif

										<tr>
											<th
												scope="row">
												{{ __('Effective From') }}:
											</th>
											<td>{{ $salaryContract->effective_from }}
											</td>
										</tr>

										<tr>
											<th
												scope="row">
												{{ __('Effective To') }}:
											</th>
											<td>{{ $salaryContract->effective_to }}
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
									{{ $salaryContract->notes ?: __('No notes available') }}
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
					<h5 class="card-title mb-3">{{ __('Salary Contract Images') }}</h5>

					@if($salaryContract->images)
					<div class="row">
						@foreach($salaryContract->images as $image)
						<div class="col-6 mb-3">
							<div class="position-relative">
								<img src="{{ $image }}"
									alt="Clinic User Salary Image"
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
											{{ __('Salary Contract Image') }}
										</h5>
										<button type="button"
											class="btn-close"
											data-bs-dismiss="modal"></button>
									</div>
									<div
										class="modal-body text-center">
										<img src="{{ $image }}"
											alt="Clinic User Salary Image"
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
									<td>{{ $salaryContract->created_at->format('Y-m-d H:i:s') }}
									</td>
								</tr>
								<tr>
									<th scope="row">
										{{ __('Updated At') }}:
									</th>
									<td>{{ $salaryContract->updated_at->format('Y-m-d H:i:s') }}
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
