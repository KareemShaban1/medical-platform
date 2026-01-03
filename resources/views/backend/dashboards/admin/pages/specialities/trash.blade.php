@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Trash Specialities'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Trash Specialities') }}</h4>
                    </div>
                    <div class="card-body">
                        <table id="specialities-trash-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name (EN)') }}</th>
                                    <th>{{ __('Name (AR)') }}</th>
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
            $('#specialities-trash-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.specialities.trash.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name_en', name: 'name_en' },
                    { data: 'name_ar', name: 'name_ar' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function restore(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('Do you want to restore this speciality?') }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, restore it!') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route('admin.specialities.restore', ':id') }}'.replace(':id', id), {
                        _token: '{{ csrf_token() }}'
                    }).done(function (response) {
                        Swal.fire('{{ __('Restored!') }}', response.message, 'success');
                        $('#specialities-trash-table').DataTable().ajax.reload();
                    });
                }
            });
        }

        function forceDelete(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('This will permanently delete the speciality!') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, delete permanently!') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.specialities.force-delete', ':id') }}'.replace(':id', id),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).done(function (response) {
                        Swal.fire('{{ __('Deleted!') }}', response.message, 'success');
                        $('#specialities-trash-table').DataTable().ajax.reload();
                    });
                }
            });
        }
    </script>
@endpush