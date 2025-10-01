@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.patients.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Patients') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Patient Details') }}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 text-center">
                            <!-- Patient Avatar -->
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                 style="width: 120px; height: 120px; color: white; font-size: 48px;">
                                <i class="mdi mdi-account"></i>
                            </div>

                            <h4>{{ $patient->name }}</h4>
                            <p class="text-muted">{{ $patient->phone }}</p>

                            <div class="mb-3">
                                {!! $patient->status_badge !!}
                            </div>

                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <button onclick="editPatient({{ $patient->id }})" class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit"></i> {{ __('Edit') }}
                                </button>
                                <button onclick="deletePatient({{ $patient->id }})" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> {{ __('Delete') }}
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-muted mb-3">{{ __('Contact Information') }}</h6>

                                    <div class="mb-2">
                                        <strong>{{ __('Name') }}:</strong><br>
                                        {{ $patient->name }}
                                    </div>

                                    <div class="mb-2">
                                        <strong>{{ __('Phone') }}:</strong><br>
                                        {{ $patient->phone }}
                                    </div>

                                    <div class="mb-2">
                                        <strong>{{ __('Email') }}:</strong><br>
                                        {{ $patient->email ?: __('Not provided') }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-muted mb-3">{{ __('Account Information') }}</h6>

                                    <div class="mb-2">
                                        <strong>{{ __('Patient Type') }}:</strong><br>
                                        @if($patient->isRegistered())
                                            <span class="badge bg-success">{{ __('Registered User') }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ __('Clinic Created') }}</span>
                                        @endif
                                    </div>

                                    @if($patient->user)
                                    <div class="mb-2">
                                        <strong>{{ __('Linked User Account') }}:</strong><br>
                                        {{ $patient->user->name }} ({{ $patient->user->email }})
                                    </div>
                                    @endif

                                    <div class="mb-2">
                                        <strong>{{ __('Created At') }}:</strong><br>
                                        {{ $patient->created_at->format('Y-m-d H:i') }}
                                    </div>

                                    <div class="mb-2">
                                        <strong>{{ __('Last Updated') }}:</strong><br>
                                        {{ $patient->updated_at->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                            </div>

                            @if($patient->clinic)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="text-uppercase text-muted mb-3">{{ __('Clinic Information') }}</h6>

                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $patient->clinic->name }}</h6>
                                            <p class="card-text">
                                                <strong>{{ __('Phone') }}:</strong> {{ $patient->clinic->phone }}<br>
                                                <strong>{{ __('Address') }}:</strong> {{ $patient->clinic->address }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Future: Medical Records, Appointments, etc. -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="text-uppercase text-muted mb-3">{{ __('Quick Actions') }}</h6>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <i class="mdi mdi-calendar fa-2x mb-2"></i>
                                                    <h6>{{ __('Appointments') }}</h6>
                                                    <small>{{ __('Coming Soon') }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <i class="mdi mdi-file-document fa-2x mb-2"></i>
                                                    <h6>{{ __('Medical Records') }}</h6>
                                                    <small>{{ __('Coming Soon') }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <i class="mdi mdi-pill fa-2x mb-2"></i>
                                                    <h6>{{ __('Prescriptions') }}</h6>
                                                    <small>{{ __('Coming Soon') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Patient Modal -->
<div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPatientModalLabel">{{ __('Edit Patient') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPatientForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_patient_id" name="patient_id" value="{{ $patient->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" value="{{ $patient->name }}" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">{{ __('Phone') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_phone" name="phone" value="{{ $patient->phone }}" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" class="form-control" id="edit_email" name="email" value="{{ $patient->email }}">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_password" class="form-label">{{ __('Password') }}</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                        <small class="form-text text-muted">{{ __('Leave empty to keep current password') }}</small>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Patient') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Edit Patient Form
    $('#editPatientForm').on('submit', function(e) {
        e.preventDefault();

        var patientId = $('#edit_patient_id').val();

        $.ajax({
            url: "{{ route('clinic.patients.update', ':id') }}".replace(':id', patientId),
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#editPatientModal').modal('hide');
                    toastr.success(response.message);
                    // Reload the page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showFormErrors(xhr.responseJSON.errors);
                } else {
                    toastr.error('{{ __("An error occurred while updating the patient") }}');
                }
            }
        });
    });

    // Clear form errors when modal is hidden
    $('#editPatientModal').on('hidden.bs.modal', function() {
        clearFormErrors();
    });
});

function editPatient(id) {
    clearFormErrors();
    $('#editPatientModal').modal('show');
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
                url: "{{ route('clinic.patients.destroy', ':id') }}".replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        // Redirect to patients index
                        setTimeout(() => {
                            window.location.href = "{{ route('clinic.patients.index') }}";
                        }, 1000);
                    }
                },
                error: function() {
                    toastr.error('{{ __("An error occurred while deleting the patient") }}');
                }
            });
        }
    });
}

function showFormErrors(errors) {
    clearFormErrors();

    $.each(errors, function(field, messages) {
        var input = $('#edit_' + field);
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
