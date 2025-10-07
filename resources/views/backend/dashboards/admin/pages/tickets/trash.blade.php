@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Trash Tickets'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">{{ __('Tickets') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Trash') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Trash Tickets') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="header-title">{{ __('Deleted Tickets') }}</h4>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <a href="{{ route('admin.tickets.index') }}" class="btn btn-primary mb-2">
                                    <i class="mdi mdi-arrow-left"></i> {{ __('Back to Tickets') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="trash-tickets-table" class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Ticket Number') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Deleted At') }}</th>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#trash-tickets-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.tickets.trash.data') }}',
                columns: [
                    { data: 'ticket_number', name: 'ticket_number' },
                    { data: 'user_name', name: 'user.name' },
                    { data: 'type', name: 'type' },
                    { data: 'status', name: 'status' },
                    { data: 'deleted_at', name: 'deleted_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Restore Ticket
            window.restoreTicket = function(id) {
                if (confirm('{{ __('Are you sure you want to restore this ticket?') }}')) {
                    $.ajax({
                        url: `/admin/tickets/${id}/restore`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#trash-tickets-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        },
                        error: function() {
                            toastr.error('{{ __('Something went wrong') }}');
                        }
                    });
                }
            };

            // Force Delete Ticket
            window.forceDeleteTicket = function(id) {
                if (confirm('{{ __('Are you sure you want to permanently delete this ticket? This action cannot be undone.') }}')) {
                    $.ajax({
                        url: `/admin/tickets/${id}/force-delete`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#trash-tickets-table').DataTable().ajax.reload();
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
