@extends('backend.dashboards.admin.layouts.app')

@section('title', __('All Orders'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('All Orders') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('All Orders') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="orders-table" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>{{ __('Order Number') }}</th>
                                        <th>{{ __('Clinic') }}</th>
                                        <th>{{ __('Clinic User') }}</th>
                                        <th>{{ __('Suppliers') }}</th>
                                        <th>{{ __('Items') }}</th>
                                        <th>{{ __('Total') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Payment Status') }}</th>
                                        <th>{{ __('Date') }}</th>
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
            $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.orders.data') }}',
                columns: [{
                        data: 'number',
                        name: 'number'
                    },
                    {
                        data: 'clinic_name',
                        name: 'clinic_name'
                    },
                    {
                        data: 'clinic_user',
                        name: 'clinic_user'
                    },
                    {
                        data: 'suppliers_count',
                        name: 'suppliers_count'
                    },
                    {
                        data: 'items_count',
                        name: 'items_count'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
