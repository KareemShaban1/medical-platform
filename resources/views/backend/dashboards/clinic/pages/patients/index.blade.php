@extends('backend.dashboards.clinic.layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
					<button type="button" class="btn btn-primary" data-bs-toggle="modal"
						data-bs-target="#addPatientModal">
                            <i class="mdi mdi-plus"></i> {{ __('Add Patient') }}
                        </button>
                    </div>
                    <h4 class="page-title">{{ __('Patients Management') }}</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
					<table id="patients-table"
						class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Assigned Doctors') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Patient Modal -->
    <div class="modal fade" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPatientModalLabel">{{ __('Add New Patient') }}
                    </h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
                </div>
                <form id="addPatientForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Full Name') }}
                                <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="name"
							name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">{{ __('Phone') }}
                                <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="phone"
							name="phone" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
						<label for="email"
							class="form-label">{{ __('Email') }}</label>
						<input type="email" class="form-control" id="email"
							name="email">
                            <small
                                class="form-text text-muted">{{ __('Optional - If provided and matches registered user, account will be linked') }}</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
						<label for="password"
							class="form-label">{{ __('Password') }}</label>
						<input type="password" class="form-control" id="password"
							name="password">
                            <small
                                class="form-text text-muted">{{ __('Optional - Only required if email is provided') }}</small>
                            <div class="invalid-feedback"></div>
                        </div>

					@if(!$isDoctor)
                            <div class="mb-3">
						<label for="doctor_profile_id"
							class="form-label">{{ __('Assign to Doctor') }}</label>
						<select class="form-select select2" id="doctor_profile_id"
							required multiple name="doctor_profile_id">
                                    <option value="">
                                        {{ __('Select Doctor (Optional)') }}
                                    </option>
							@foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">
                                            {{ $doctor->name }}</option>
                                    @endforeach
                                </select>
                                <small
                                    class="form-text text-muted">{{ __('Optional - Assign this patient to a specific doctor') }}</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
					<button type="submit"
						class="btn btn-primary">{{ __('Add Patient') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPatientModalLabel">{{ __('Edit Patient') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
                </div>
                <form id="editPatientForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_patient_id" name="patient_id">
                    <div class="modal-body">
                        <div class="mb-3">
						<label for="edit_name"
							class="form-label">{{ __('Full Name') }} <span
                                    class="text-danger">*</span></label>
						<input type="text" class="form-control" id="edit_name"
							name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">{{ __('Phone') }}
                                <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="edit_phone"
							name="phone" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
						<label for="edit_email"
							class="form-label">{{ __('Email') }}</label>
						<input type="email" class="form-control" id="edit_email"
							name="email">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
						<label for="edit_password"
							class="form-label">{{ __('Password') }}</label>
						<input type="password" class="form-control"
							id="edit_password" name="password">
						<small
							class="form-text text-muted">{{ __('Leave empty to keep current password') }}</small>
                            <div class="invalid-feedback"></div>
                        </div>

					@if(!$isDoctor)
                            <div class="mb-3">
                                <label for="edit_doctor_profile_id"
                                    class="form-label">{{ __('Assign to Doctor') }}</label>
						<select class="form-select select2"
							id="edit_doctor_profile_id" multiple
                                    name="doctor_profile_id" required>
                                    <option value="">
                                        {{ __('Select Doctor (Optional)') }}
                                    </option>
							@foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">
                                            {{ $doctor->name }}</option>
                                    @endforeach
                                </select>
                                <small
                                    class="form-text text-muted">{{ __('Optional - Assign this patient to a specific doctor') }}</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
					<button type="submit"
						class="btn btn-primary">{{ __('Update Patient') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('backend.components.subscription-limit-handler')
    <script>
        $('.select2').select2();
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#patients-table').DataTable({
                processing: true,
                serverSide: true,
                searchable: true,
                ajax: "{{ route('clinic.patients.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'assigned_doctors',
                        name: 'assigned_doctors',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
                pageLength: 10,
                responsive: true,
                language: languages[language],
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination')
                        .addClass(
                            'pagination-rounded'
                        );
                }
            });

            // Add Patient Form
            $('#addPatientForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('clinic.patients.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
				if (response
					.status ===
					'success'
				) {
					$('#addPatientModal')
						.modal(
							'hide'
						);
					table.ajax
						.reload();
					toastr.success(response
						.message
					);
					$('#addPatientForm')[
							0
						]
						.reset();
					clearFormErrors
						();
                        }
                    },
                    error: function(xhr) {
                        if (handleSubscriptionError(xhr, {
                                actionUrl: '{{ route('home') }}#subscriptions-plans'
                            })) {
                            return;
                        }

                        if (xhr.status === 422) {
                            showFormErrors(xhr.responseJSON.errors, 'add');
                        } else {
                            toastr.error(
						'{{ __("An error occurred while adding the patient") }}'
					);
                        }
                    }
                });
            });




            // Edit Patient Form
            $('#editPatientForm').on('submit', function(e) {
                e.preventDefault();

                var patientId = $('#edit_patient_id').val();

                $.ajax({
			url: "{{ route('clinic.patients.update', ':id') }}"
				.replace(':id',
					patientId
				),
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
				if (response
					.status ===
					'success'
				) {
					$('#editPatientModal')
						.modal(
							'hide'
						);
					table.ajax
						.reload();
					toastr.success(response
						.message
					);
					clearFormErrors
						();
                        }
                    },
                    error: function(xhr) {
				if (xhr.status ===
					422
				) {
					showFormErrors
						(xhr.responseJSON
							.errors,
							'edit'
						);
                        } else {
                            toastr.error(
						'{{ __("An error occurred while updating the patient") }}'
					);
                        }
                    }
                });
            });

            // Clear form errors when modals are hidden
            $('#addPatientModal, #editPatientModal').on('hidden.bs.modal', function() {
                clearFormErrors();
            });
        });

        function editPatient(id) {
            clearFormErrors();

            $('#edit_patient_id').val(id);
            $('#editPatientModal').modal('show');

            // Clear previous doctor options before appending new ones
            $('#edit_doctor_profile_id').empty().append('<option value="">Select Doctor (Optional)</option>');

            $.ajax({
                url: "{{ route('clinic.patients.edit', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(data) {
                    $('#edit_name').val(data.name || '');
                    $('#edit_phone').val(data.phone || '');
                    $('#edit_email').val(data.email || '');

                    const assignedDoctorIds = data.assigned_doctor_ids || [];

                    // Populate doctor options and select assigned ones
                    data.doctors.forEach(function(doctor) {
				const isSelected =
					assignedDoctorIds
					.includes(doctor
						.id);
				$('#edit_doctor_profile_id')
					.append(`
					<option value="${doctor.id}" ${isSelected ? 'selected' : ''}>
						${doctor.name}
					</option>
				`);
                    });
                },
                error: function(xhr) {
                    console.error('Error fetching patient:', xhr);
                }
            });
        }

        function deletePatient(id) {
            Swal.fire({
		title: '{{ __("Are you sure?") }}',
		text: '{{ __("You will not be able to recover this patient record!") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f56565',
                cancelButtonColor: '#6b7280',
		confirmButtonText: '{{ __("Yes, delete it!") }}',
		cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
				url: "{{ route('clinic.patients.destroy', ':id') }}"
					.replace(':id',
						id
					),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
				success: function(
					response
				) {
					if (response
						.status ===
						'success'
					) {
						$('#patients-table')
							.DataTable()
							.ajax
							.reload();
						toastr.success(response
							.message
						);
                            }
                        },
                        error: function() {
					toastr.error(
						'{{ __("An error occurred while deleting the patient") }}'
					);
                        }
                    });
                }
            });
        }

        function showFormErrors(errors, formType) {
            clearFormErrors();

            $.each(errors, function(field, messages) {
                var prefix = formType === 'edit' ? 'edit_' : '';
                var input = $('#' + prefix + field);
                input.addClass('is-invalid');
                input.next('.invalid-feedback').text(messages[0]);
            });
        }

        function clearFormErrors() {
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }
    </script>
@endpush
