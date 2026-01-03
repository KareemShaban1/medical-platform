@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Trash Doctor Profiles'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Trash Doctor Profiles') }}</h4>
                    </div>
                    <div class="card-body">
                        <table id="doctor-profiles-trash-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('Photo') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Clinic User') }}</th>
                                    <th>{{ __('Speciality') }}</th>
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
            $('#doctor-profiles-trash-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.doctor-profiles.trash.data') }}',
                columns: [
                    { data: 'profile_photo', name: 'profile_photo', orderable: false, searchable: false },
                    { data: 'doctor_name', name: 'name' },
                    { data: 'clinic_user', name: 'clinic_user' },
                    { data: 'speciality', name: 'speciality' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function restore(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('Do you want to restore this doctor profile?') }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, restore it!') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route('admin.doctor-profiles.restore', ':id') }}'.replace(':id', id), {
                        _token: '{{ csrf_token() }}'
                    }).done(function (response) {
                        Swal.fire('{{ __('Restored!') }}', response.message, 'success');
                        $('#doctor-profiles-trash-table').DataTable().ajax.reload();
                    });
                }
            });
        }

        function forceDelete(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('This will permanently delete the doctor profile!') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, delete permanently!') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.doctor-profiles.force-delete', ':id') }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).done(function (response) {
                        Swal.fire('{{ __('Deleted!') }}', response.message, 'success');
                        $('#doctor-profiles-trash-table').DataTable().ajax.reload();
                    });
                }
            });
        }
    </script>
@endpush