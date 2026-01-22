@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Standalone Doctors Management'))

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ __('Standalone Doctors Management') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Standalone Doctors') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="row mb-3">
            <div class="col-12">
                {{-- <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <span>{{ __('Standalone doctors are clinic users who registered independently without associating with a clinic.') }}</span>
                </div> --}}
            </div>
        </div>

        <!-- Data Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-user-md"></i> {{ __('All Standalone Doctors') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="standalone-doctors-table" class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Speciality') }}</th>
                                        <th>{{ __('Doctor Status') }}</th>
                                        <th>{{ __('Account Status') }}</th>
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
        $(document).ready(function() {
            $('#standalone-doctors-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users-management.standalone-doctors.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'speciality',
                        name: 'speciality',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'doctor_status',
                        name: 'doctor_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status'
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
                pageLength: 25,
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{ __('Loading...') }}</span>',
                    search: '{{ __('Search') }}:',
                    lengthMenu: '{{ __('Show') }} _MENU_ {{ __('entries') }}',
                    info: '{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_ {{ __('entries') }}',
                    infoEmpty: '{{ __('Showing 0 to 0 of 0 entries') }}',
                    infoFiltered: '({{ __('filtered from') }} _MAX_ {{ __('total entries') }})',
                    paginate: {
                        first: '{{ __('First') }}',
                        last: '{{ __('Last') }}',
                        next: '{{ __('Next') }}',
                        previous: '{{ __('Previous') }}'
                    },
                    emptyTable: '{{ __('No data available in table') }}'
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> {{ __('Copy') }}',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> {{ __('Excel') }}',
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> {{ __('PDF') }}',
                        className: 'btn btn-sm btn-danger'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> {{ __('Print') }}',
                        className: 'btn btn-sm btn-info'
                    }
                ]
            });
        });

        function deleteStandaloneDoctor(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('You are about to move this doctor to trash!') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, delete it!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.users-management.standalone-doctors.destroy', ':id') }}'
                            .replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    '{{ __('Deleted!') }}',
                                    response.message,
                                    'success'
                                );
                                $('#standalone-doctors-table').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    '{{ __('Error!') }}',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                '{{ __('Error!') }}',
                                '{{ __('Something went wrong!') }}',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Status Toggle Handler
        $(document).on('change', '.toggle-status', function() {
            var status = $(this).prop('checked') ? 1 : 0;
            var user_id = $(this).data('id');
            var user_type = $(this).data('type');
            var $toggle = $(this);

            $.ajax({
                type: "POST",
                dataType: "json",
                url: '{{ route('admin.users-management.toggle-status') }}',
                data: {
                    'status': status,
                    'user_id': user_id,
                    'user_type': user_type,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    })
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    })
                },
                error: function(xhr) {
                    $toggle.prop('checked', !status);
                    Swal.fire('{{ __('Error!') }}', xhr.responseJSON.message ||
                        '{{ __('Something went wrong!') }}', 'error');
                }
            });
        });
    </script>
@endpush
