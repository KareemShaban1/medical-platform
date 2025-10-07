@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Ticket Details'))

@push('styles')
<style>
    /* Fix dropdown styling */
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

    /* Ensure modal content is properly displayed */
    .modal-body .mb-3 {
        display: block !important;
        margin-bottom: 1rem !important;
    }

    .modal-body .form-label {
        display: inline-block !important;
        margin-bottom: 0.5rem !important;
        font-weight: 500 !important;
    }

    /* Fix z-index issues */
    .modal {
        z-index: 1050 !important;
    }

    .modal-backdrop {
        z-index: 1040 !important;
    }

    /* Ensure modal dialog is properly centered */
    .modal-dialog-centered {
        display: flex !important;
        align-items: center !important;
        min-height: calc(100% - 1rem) !important;
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">{{ __('Tickets') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Ticket Details') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Ticket Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">{{ $ticket->ticket_number }}</h4>
                        <div>
                            {!! $ticket->status_badge !!}
                            {!! $ticket->type_badge !!}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('User:') }}</strong> {{ $ticket->user->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Created:') }}</strong> {{ $ticket->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>{{ __('Ticket Details') }}</h5>
                        <div class="bg-light p-3 rounded">
                            {{ $ticket->details }}
                        </div>
                    </div>

                    @if($ticket->attachments)
                        <div class="mb-4">
                            <h5>{{ __('Attachments') }}</h5>
                            <div class="row">
                                @foreach($ticket->attachments as $attachment)
                                    <div class="col-md-3 mb-2">
                                        <a href="{{ $attachment['url'] }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-download"></i> {{ $attachment['name'] }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Replies Section -->
                    <div class="mb-4">
                        <h5>{{ __('Conversation') }}</h5>
                        <div id="replies-container">
                            @foreach($ticket->replies as $reply)
                                <div class="d-flex mb-3 {{ $reply->is_admin_reply ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="card {{ $reply->is_admin_reply ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 70%;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="fw-bold">{{ $reply->author_name }}</small>
                                                <small class="{{ $reply->is_admin_reply ? 'text-white-50' : 'text-muted' }}">
                                                    {{ $reply->created_at->format('Y-m-d H:i') }}
                                                </small>
                                            </div>
                                            <p class="mb-0">{{ $reply->message }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Reply Form -->
                    @if($ticket->isOpen())
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>{{ __('Send Reply') }}</h5>
                                <form id="replyForm" method="POST" action="{{ route('admin.tickets.reply', $ticket->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control" name="message" rows="4" placeholder="{{ __('Type your reply here...') }}" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-paper-plane"></i> {{ __('Send Reply') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> {{ __('This ticket is closed and cannot receive new replies.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Ticket Actions') }}</h5>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                            <i class="fa fa-edit"></i> {{ __('Update Status') }}
                        </button>

                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('Back to Tickets') }}
                        </a>

                        <button class="btn btn-danger" onclick="deleteTicket({{ $ticket->id }})">
                            <i class="fa fa-trash"></i> {{ __('Delete Ticket') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Ticket Information') }}</h5>

                    <div class="mb-2">
                        <strong>{{ __('Ticket Number:') }}</strong><br>
                        {{ $ticket->ticket_number }}
                    </div>

                    <div class="mb-2">
                        <strong>{{ __('Type:') }}</strong><br>
                        {!! $ticket->type_badge !!}
                    </div>

                    <div class="mb-2">
                        <strong>{{ __('Status:') }}</strong><br>
                        {!! $ticket->status_badge !!}
                    </div>

                    <div class="mb-2">
                        <strong>{{ __('Created:') }}</strong><br>
                        {{ $ticket->created_at->format('M d, Y H:i') }}
                    </div>

                    @if($ticket->closed_at)
                        <div class="mb-2">
                            <strong>{{ __('Closed:') }}</strong><br>
                            {{ $ticket->closed_at->format('M d, Y H:i') }}
                        </div>
                    @endif
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
                <form id="updateStatusForm" method="POST" action="{{ route('admin.tickets.update-status', $ticket->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status-update" class="form-label">{{ __('Status') }}</label>
                            <select class="form-select" id="status-update" name="status" required>
                                <option value="">{{ __('Select Status') }}</option>
                                <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                <option value="accepted" {{ $ticket->status === 'accepted' ? 'selected' : '' }}>{{ __('Accepted') }}</option>
                                <option value="rejected" {{ $ticket->status === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
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
            console.log('Document ready - initializing ticket details page');

            // Debug modal opening
            $('#updateStatusModal').on('show.bs.modal', function (e) {
                console.log('Modal is opening');
                console.log('Current status dropdown value:', $('#status-update').val());
            });

            $('#updateStatusModal').on('shown.bs.modal', function (e) {
                console.log('Modal is now visible');
                console.log('Status select element:', $('#status-update'));
                console.log('Is status select visible?', $('#status-update').is(':visible'));
            });

            // Handle Status Update
            $('#updateStatusForm').on('submit', function(e) {
                e.preventDefault();
                console.log('Status form submitted');

                let form = $(this);
                let url = form.attr('action');
                let data = form.serialize();

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

                        toastr.success(response.message || 'Status updated successfully');

                        // Reload after delay
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
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

            // Handle Reply Form
            $('#replyForm').on('submit', function(e) {
                e.preventDefault();
                console.log('Reply form submitted');

                let form = $(this);
                let data = form.serialize();

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Reply success:', response);

                        form[0].reset();
                        toastr.success(response.message || 'Reply sent successfully');

                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        console.error('Reply error:', xhr);

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
                Swal.fire({
                    title: '{{ __('Are you sure?') }}',
                    text: "{{ __('You won\'t be able to revert this!') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __('Yes, delete it!') }}',
                    cancelButtonText: '{{ __('Cancel') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/tickets/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                toastr.success(response.message || 'Ticket deleted successfully');
                                setTimeout(function() {
                                    window.location.href = '{{ route('admin.tickets.index') }}';
                                }, 1500);
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message || '{{ __('Something went wrong') }}');
                            }
                        });
                    }
                });
            };
        });
    </script>
@endpush
