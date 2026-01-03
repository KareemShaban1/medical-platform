@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Trash Patients'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ __('Trash Patients') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="patients-trash-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Location') }}</th>
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
            $('#patients-trash-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users-management.patients.trash.data') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'location', name: 'location' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function restore(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('Do you want to restore this patient?') }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, restore it!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route('admin.users-management.patients.restore', ':id') }}'.replace(':id', id), {
                        _token: '{{ csrf_token() }}'
                    }).done(function (response) {
                        Swal.fire('{{ __('Restored!') }}', response.message, 'success');
                        $('#patients-trash-table').DataTable().ajax.reload();
                    });
                }
            });
        }

        function forceDelete(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('This will permanently delete the patient!') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, delete permanently!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.users-management.patients.force-delete', ':id') }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).done(function (response) {
                        Swal.fire('{{ __('Deleted!') }}', response.message, 'success');
                        $('#patients-trash-table').DataTable().ajax.reload();
                    });
                }
            });
        }
    </script>
@endpush