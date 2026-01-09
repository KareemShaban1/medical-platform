@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Trash Ticket Types'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.ticket-types.index') }}">{{ __('Ticket Types') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Trash') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Trash Ticket Types') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="header-title">{{ __('Deleted Ticket Types') }}</h4>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <a href="{{ route('admin.ticket-types.index') }}" class="btn btn-primary mb-2">
                                    <i class="mdi mdi-arrow-left"></i> {{ __('Back to Ticket Types') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="trash-table" class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Slug') }}</th>
                                    <th>{{ __('Badge') }}</th>
                                    <th>{{ __('Deleted At') }}</th>
                                    <th style="width: 150px;">{{ __('Action') }}</th>
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
        $(document).ready(function () {
            $('#trash-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.ticket-types.trash.data') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'slug', name: 'slug' },
                    { data: 'badge', name: 'badge', orderable: false },
                    { data: 'deleted_at', name: 'deleted_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function restoreTicketType(id) {
            $.ajax({
                url: `/admin/ticket-types/${id}/restore`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    $('#trash-table').DataTable().ajax.reload();
                    toastr.success(response.message);
                },
                error: function () {
                    toastr.error('{{ __('Something went wrong') }}');
                }
            });
        }

        function forceDeleteTicketType(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: "{{ __('This will permanently delete the ticket type!') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __('Yes, delete forever!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/ticket-types/${id}/force-delete`,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            $('#trash-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        },
                        error: function () {
                            toastr.error('{{ __('Something went wrong') }}');
                        }
                    });
                }
            });
        }
    </script>
@endpush