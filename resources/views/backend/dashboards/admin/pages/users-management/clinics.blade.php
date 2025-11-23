@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Clinic Users Management'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ __('Clinic Users Management') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users-management.index') }}">{{ __('Users Management') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Clinic Users') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-hospital"></i> {{ __('All Clinic Users') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="clinics-table" class="table table-bordered table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Clinic Name') }}</th>
                                    <th>{{ __('Admin Name') }}</th>
                                    <th>{{ __('Admin Email') }}</th>
                                    <th>{{ __('Total Users') }}</th>
                                    <th>{{ __('Doctor Profiles') }}</th>
                                    <th>{{ __('Status') }}</th>
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
        $('#clinics-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.users-management.clinics.data') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'admin_name', name: 'admin_name', orderable: false, searchable: false },
                { data: 'admin_email', name: 'admin_email', orderable: false, searchable: false },
                { data: 'users_count', name: 'users_count', orderable: false, searchable: false },
                { data: 'doctor_profiles_count', name: 'doctor_profiles_count', orderable: false, searchable: false },
                { data: 'status', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{ __("Loading...") }}</span>',
                search: '{{ __("Search") }}:',
                lengthMenu: '{{ __("Show") }} _MENU_ {{ __("entries") }}',
                info: '{{ __("Showing") }} _START_ {{ __("to") }} _END_ {{ __("of") }} _TOTAL_ {{ __("entries") }}',
                infoEmpty: '{{ __("Showing 0 to 0 of 0 entries") }}',
                infoFiltered: '({{ __("filtered from") }} _MAX_ {{ __("total entries") }})',
                paginate: {
                    first: '{{ __("First") }}',
                    last: '{{ __("Last") }}',
                    next: '{{ __("Next") }}',
                    previous: '{{ __("Previous") }}'
                },
                emptyTable: '{{ __("No data available in table") }}'
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> {{ __("Copy") }}',
                    className: 'btn btn-sm btn-secondary'
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> {{ __("Excel") }}',
                    className: 'btn btn-sm btn-success'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> {{ __("PDF") }}',
                    className: 'btn btn-sm btn-danger'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> {{ __("Print") }}',
                    className: 'btn btn-sm btn-info'
                }
            ]
        });
    });
</script>
@endpush
