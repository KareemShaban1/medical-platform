@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Contact Messages'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ __('Contact Messages') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Contact Messages') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">{{ __('Total Messages') }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-2">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-0"><span class="counter-value" data-target="{{ $stats['total'] }}">{{ $stats['total'] }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-envelope text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">{{ __('New') }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-2">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-0"><span class="counter-value" data-target="{{ $stats['new'] }}">{{ $stats['new'] }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-bell text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">{{ __('Replied') }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-2">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-0"><span class="counter-value" data-target="{{ $stats['replied'] }}">{{ $stats['replied'] }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-check-circle text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">{{ __('Archived') }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-2">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-0"><span class="counter-value" data-target="{{ $stats['archived'] }}">{{ $stats['archived'] }}</span></h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-secondary-subtle rounded fs-3">
                                <i class="bx bx-archive text-secondary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('All Contact Messages') }}</h5>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select class="form-select" id="filter-status">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="new">{{ __('New') }}</option>
                                <option value="read">{{ __('Read') }}</option>
                                <option value="replied">{{ __('Replied') }}</option>
                                <option value="archived">{{ __('Archived') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Date From') }}</label>
                            <input type="date" class="form-control" id="filter-date-from">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Date To') }}</label>
                            <input type="date" class="form-control" id="filter-date-to">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="apply-filters">
                                <i class="fa fa-filter"></i> {{ __('Apply Filters') }}
                            </button>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="contact-messages-table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Message') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
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

@push('scripts')
<script>
    $(document).ready(function() {
        const table = $('#contact-messages-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.contact-messages.data") }}',
                data: function(d) {
                    d.status = $('#filter-status').val();
                    d.date_from = $('#filter-date-from').val();
                    d.date_to = $('#filter-date-to').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'full_name', name: 'full_name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'company', name: 'company', defaultContent: '-' },
                { data: 'short_message', name: 'message' },
                { data: 'status_badge', name: 'status', orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 25
        });

        // Apply filters
        $('#apply-filters').on('click', function() {
            table.ajax.reload();
        });

        // Reset filters on select/input change
        $('#filter-status, #filter-date-from, #filter-date-to').on('change', function() {
            table.ajax.reload();
        });

        // Delete message
        $(document).on('click', '.delete-btn', function() {
            const url = $(this).data('url');
            const id = $(this).data('id');

            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("You won't be able to revert this!") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __("Yes, delete it!") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || '{{ __("Error deleting message") }}');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
