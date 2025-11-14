@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Course Enrollments'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Course Enrollments') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('Course Enrollments') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3 mb-3 align-items-end">
                            <div class="col-md-3">
                                <label for="status-filter" class="form-label">{{ __('Filter by status') }}</label>
                                <select id="status-filter" class="form-select form-select-sm">
                                    <option value="all">{{ __('All statuses') }}</option>
                                    <option value="pending">{{ __('Pending') }}</option>
                                    <option value="approved">{{ __('Approved') }}</option>
                                    <option value="rejected">{{ __('Rejected') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="enrollments-table" class="table table-hover dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Course') }}</th>
                                        <th>{{ __('Clinic / User') }}</th>
                                        <th>{{ __('Contact') }}</th>
                                        <th>{{ __('Status') }}</th>
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
    </div>
@endsection

@push('scripts')
<script>
    function updateEnrollmentStatus(id, status) {
        $.ajax({
            url: '{{ route('admin.course-enrollments.update-status', ['id' => '__ID__']) }}'.replace('__ID__', id),
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(resp) {
                toastr.success(resp.message || 'Updated');
                $('#enrollments-table').DataTable().ajax.reload(null, false);
            },
            error: function() {
                toastr.error('Failed to update status');
            }
        });
    }

    function deleteEnrollment(id) {
        Swal.fire({
            title: '{{ __('Are you sure?') }}',
            text: '{{ __('This action cannot be undone') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('Yes, delete it!') }}',
            cancelButtonText: '{{ __('Cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('admin.course-enrollments.destroy', ['id' => '__ID__']) }}'.replace('__ID__', id),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(resp) {
                        toastr.success(resp.message || 'Deleted');
                        $('#enrollments-table').DataTable().ajax.reload(null, false);
                    },
                    error: function() {
                        toastr.error('Failed to delete');
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        const table = $('#enrollments-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.course-enrollments.data') }}',
                data: function(params) {
                    params.status = $('#status-filter').val() || 'all';
                }
            },
            order: [[0, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-md-6"l><"col-md-6 text-md-end"B>>rt<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
            buttons: [
                { extend: 'excel', className: 'btn btn-sm btn-light dt-btn-light', text: '<i class="mdi mdi-file-excel"></i> {{ __('Export Excel') }}' },
                { extend: 'print', className: 'btn btn-sm btn-light dt-btn-light', text: '<i class="mdi mdi-printer"></i> {{ __('Print') }}' }
            ],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'course', name: 'course' },
                { data: 'clinic', name: 'clinic' },
                { data: 'contact', name: 'contact', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $('#status-filter').on('change', function() {
            table.ajax.reload();
        });

        // Highlight specific enrollment if requested
        const highlight = new URLSearchParams(window.location.search).get('highlight');
        if (highlight) {
            setTimeout(() => {
                $("#enrollments-table tbody tr").each(function() {
                    const id = $(this).find('td').first().text().trim();
                    if (id === highlight) {
                        $(this).addClass('table-warning');
                    }
                })
            }, 1000);
        }
    });
</script>
@push('styles')
<style>
/* Use light (white) backgrounds for table rows and buttons on this page */
#enrollments-table thead th,
#enrollments-table tbody tr,
#enrollments-table tbody td {
    background-color: #ffffff !important;
}

.dt-buttons .btn.dt-btn-light {
    background-color: #ffffff !important;
    color: #111827 !important;
    border: 1px solid #e5e7eb !important;
    box-shadow: none !important;
}
.dt-buttons .btn.dt-btn-light:hover {
    background-color: #f9fafb !important;
    color: #111827 !important;
}
</style>
@endpush
@endpush
