@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('clinic.doctor-profiles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Profiles') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Trashed Doctor Profiles') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="trash-profiles-table" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('Photo') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Speciality') }}</th>
                                <th>{{ __('Years Exp.') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Deleted At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#trash-profiles-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("clinic.doctor-profiles.trash.data") }}',
        columns: [
            { data: 'profile_photo', name: 'profile_photo', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'speciality', name: 'speciality.name_en' },
            { data: 'years_experience', name: 'years_experience' },
            { data: 'status', name: 'status' },
            { data: 'deleted_at', name: 'deleted_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[7, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'excel',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            },
            'copy'
        ]
    });
});

// ============================================
// RESTORE PROFILE
// ============================================
function restoreProfile(id) {
    Swal.fire({
        title: '{{ __("Restore Profile?") }}',
        text: '{{ __("This will restore the profile and make it active again.") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("Yes, restore it!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.doctor-profiles.restore", ":id") }}'.replace(':id', id),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#trash-profiles-table').DataTable().ajax.reload();
                    Swal.fire('{{ __("Restored!") }}', response.message, 'success');
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message || '{{ __("Failed to restore profile") }}', 'error');
                }
            });
        }
    });
}

// ============================================
// FORCE DELETE PROFILE (PERMANENT)
// ============================================
function forceDeleteProfile(id) {
    Swal.fire({
        title: '{{ __("Are you absolutely sure?") }}',
        text: '{{ __("This will permanently delete the profile. This action cannot be undone!") }}',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("Yes, delete permanently!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("clinic.doctor-profiles.force-delete", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#trash-profiles-table').DataTable().ajax.reload();
                    Swal.fire('{{ __("Deleted!") }}', response.message, 'success');
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error") }}', xhr.responseJSON?.message || '{{ __("Failed to delete profile") }}', 'error');
                }
            });
        }
    });
}
</script>
@endpush
