@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Tickets Management'))

@push('styles')
<style>
    /* Fix dropdown styling for index page modal */
    .modal-body .form-select {
        display: block !important;
        width: 100% !important;
        padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
        font-weight: 400 !important;
        line-height: 1.5 !important;
        color: #212529 !important;
        background-color: #fff !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 16px 12px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        appearance: none !important;
    }

    .modal-body .form-select:focus {
        border-color: #86b7fe !important;
        outline: 0 !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }

    .modal-body .form-select option {
        color: #212529 !important;
        background-color: #fff !important;
    }

    .modal-body .mb-3 {
        display: block !important;
        margin-bottom: 1rem !important;
    }

    .modal-body .form-label {
        display: inline-block !important;
        margin-bottom: 0.5rem !important;
        font-weight: 500 !important;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Tickets') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Tickets Management') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="header-title">{{ __('All Tickets') }}</h4>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <a href="{{ route('admin.tickets.trash') }}" class="btn btn-warning mb-2">
                                    <i class="mdi mdi-delete-restore"></i> {{ __('View Trash') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tickets-table" class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Ticket Number') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Last Reply') }}</th>
                                    <th style="width: 85px;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">{{ __('Update Ticket Status') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status-update" class="form-label">{{ __('Status') }}</label>
                            <select class="form-select" id="status-update" name="status" required>
                                <option value="">{{ __('Select Status') }}</option>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="in_progress">{{ __('In Progress') }}</option>
                                <option value="closed">{{ __('Closed') }}</option>
                                <option value="accepted">{{ __('Accepted') }}</option>
                                <option value="rejected">{{ __('Rejected') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Status') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Document ready');

            // Initialize DataTable
            $('#tickets-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.tickets.data') }}',
                columns: [
                    { data: 'ticket_number', name: 'ticket_number' },
                    { data: 'user_name', name: 'user.name' },
                    { data: 'type', name: 'type' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'last_reply', name: 'last_reply', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Update Status Modal
            window.updateTicketStatus = function(id, currentStatus) {
                console.log('Opening modal for ticket:', id, 'with status:', currentStatus);

                $('#updateStatusForm').attr('action', `/admin/tickets/${id}/update-status`);

                // Set the current status as selected in the dropdown
                $('#status').val(currentStatus);

                console.log('Status set to:', $('#status').val());

                $('#updateStatusModal').modal('show');
            };

            // Debug modal events
            $('#updateStatusModal').on('show.bs.modal', function (e) {
                console.log('Modal is opening');
                console.log('Current status dropdown value:', $('#status').val());
            });

            $('#updateStatusModal').on('shown.bs.modal', function (e) {
                console.log('Modal is now visible');
                console.log('Status select element:', $('#status'));
                console.log('Is status select visible?', $('#status').is(':visible'));
            });

            // Clear modal when closed
            $('#updateStatusModal').on('hidden.bs.modal', function () {
                $('#status').val('');
                console.log('Modal closed and status cleared');
            });

            console.log('Update Status Modal');

            // Handle Status Update
            $('#updateStatusForm').on('submit', function(e) {
                e.preventDefault();
                console.log('Status form submitted');

                let form = $(this);
                let url = form.attr('action');
                let data = form.serialize();

                console.log('Form URL:', url);
                console.log('Form data:', data);

                $.ajax({
                    url: url,
                    method: 'PUT',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Status update success:', response);

                        // Hide modal
                        let modalElement = document.getElementById('updateStatusModal');
                        if (modalElement) {
                            let modal = bootstrap.Modal.getInstance(modalElement);
                            if (modal) {
                                modal.hide();
                            }
                        }

                        $('#tickets-table').DataTable().ajax.reload();
                        toastr.success(response.message || 'Status updated successfully');
                    },
                    error: function(xhr) {
                        console.error('Status update error:', xhr);

                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || '{{ __('Something went wrong') }}');
                        }
                    }
                });
            });

            // Delete Ticket
            window.deleteTicket = function(id) {
                if (confirm('{{ __('Are you sure you want to delete this ticket?') }}')) {
                    $.ajax({
                        url: `/admin/tickets/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#tickets-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        },
                        error: function() {
                            toastr.error('{{ __('Something went wrong') }}');
                        }
                    });
                }
            };
        });
    </script>
@endpush
