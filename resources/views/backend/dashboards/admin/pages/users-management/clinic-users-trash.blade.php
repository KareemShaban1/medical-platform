@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Trash Clinic Users'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Trash Clinic Users') }}</h4>
                    </div>
                    <div class="card-body">
                        <table id="clinic-users-trash-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Clinic') }}</th>
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
        $(document).ready(function () {
            $('#clinic-users-trash-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users-management.clinic-users.trash.data') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'clinic_name', name: 'clinic_name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function restore(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('Do you want to restore this clinic user?') }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, restore it!') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route('admin.users-management.clinic-users.restore', ':id') }}'.replace(':id', id), {
                        _token: '{{ csrf_token() }}'
                    }).done(function (response) {
                        Swal.fire('{{ __('Restored!') }}', response.message, 'success');
                        $('#clinic-users-trash-table').DataTable().ajax.reload();
                    });
                }
            });
        }

        function forceDelete(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('This will permanently delete the clinic user!') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, delete permanently!') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.users-management.clinic-users.force-delete', ':id') }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).done(function (response) {
                        Swal.fire('{{ __('Deleted!') }}', response.message, 'success');
                        $('#clinic-users-trash-table').DataTable().ajax.reload();
                    });
                }
            });
        }
    </script>
@endpush