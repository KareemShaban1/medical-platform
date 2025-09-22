@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.doctor-profiles.pending') }}" class="btn btn-warning me-2">
                        <i class="mdi mdi-clock-outline"></i> {{ __('Pending Reviews') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Doctor Profiles') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="profiles-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Photo') }}</th>
                                <th>{{ __('Doctor Name') }}</th>
                                <th>{{ __('Clinic User') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Featured') }}</th>
                                <th>{{ __('Reviewed By') }}</th>
                                <th>{{ __('Created At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">{{ __('Reject Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" id="rejectProfileId">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required maxlength="1000"></textarea>
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">{{ __('Please provide a clear reason for rejection so the doctor can make necessary improvements.') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Reject Profile') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let table = $('#profiles-table').DataTable({
    ajax: '{{ route("admin.doctor-profiles.data") }}',
    columns: [
        { data: 'id', name: 'id' },
        { data: 'profile_photo', name: 'profile_photo', orderable: false, searchable: false },
        { data: 'doctor_name', name: 'name' },
        { data: 'clinic_user', name: 'clinicUser.name' },
        { data: 'email', name: 'email' },
        { data: 'status', name: 'status' },
        { data: 'is_featured', name: 'is_featured', orderable: false, searchable: false },
        { data: 'reviewed_by', name: 'reviewer.name' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
    pageLength: 10,
    responsive: true,
    language: languages[language],
    buttons: [
        {
            extend: 'print',
            exportOptions: {
                columns: [0, 2, 3, 4, 5, 6, 8]
            }
        },
        {
            extend: 'excel',
            text: 'Excel',
            title: 'Doctor Profiles Data',
            exportOptions: {
                columns: [0, 2, 3, 4, 5, 6, 8]
            }
        },
        {
            extend: 'copy',
            exportOptions: {
                columns: [0, 2, 3, 4, 5, 6, 8]
            }
        }
    ],
    drawCallback: function() {
        $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
    }
});

// Approve profile
function approveProfile(id) {
    Swal.fire({
        title: 'Approve Profile?',
        text: "This will approve the doctor's profile and make it publicly visible.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.doctor-profiles.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    table.ajax.reload();
                    Swal.fire('Approved!', response.message, 'success');
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
                }
            });
        }
    });
}

// Reject profile - show modal
function rejectProfile(id) {
    $('#rejectProfileId').val(id);
    $('#rejection_reason').val('');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#rejectModal').modal('show');
}

// Handle reject form submission
$('#rejectForm').on('submit', function(e) {
    e.preventDefault();

    let profileId = $('#rejectProfileId').val();
    let reason = $('#rejection_reason').val();

    $.ajax({
        url: '{{ route("admin.doctor-profiles.reject", ":id") }}'.replace(':id', profileId),
        method: 'POST',
        data: {
            rejection_reason: reason,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#rejectModal').modal('hide');
            table.ajax.reload();
            Swal.fire('Rejected!', response.message, 'success');
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors || {};
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                Object.keys(errors).forEach(function(key) {
                    let $input = $('#' + key);
                    if ($input.length) {
                        $input.addClass('is-invalid');
                        $input.next('.invalid-feedback').text(errors[key][0]);
                    }
                });
            } else {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
            }
        }
    });
});

// Toggle featured status
function toggleFeatured(id) {
    Swal.fire({
        title: 'Toggle Featured Status?',
        text: "This will change the featured status of the doctor's profile.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, toggle it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.doctor-profiles.toggle-featured", ":id") }}'.replace(':id', id),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    table.ajax.reload();
                    Swal.fire('Success!', response.message, 'success');
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
                }
            });
        }
    });
}
</script>
@endpush
