@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Available Requests'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('supplier.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Available Requests') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Available Requests') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information me-2"></i>
                                {{ __('Browse requests in your specialized categories and submit competitive offers.') }}
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <button type="button" class="btn btn-success mb-2 me-1" onclick="refreshTable()">
                                    <i class="mdi mdi-refresh"></i> {{ __('Refresh') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="available-requests-table" class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Clinic') }}</th>
                                    <th>{{ __('Categories') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Timeline') }}</th>
                                    <th>{{ __('Attachments') }}</th>
                                    <th>{{ __('Created') }}</th>
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
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#available-requests-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier.available-requests.data') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'clinic_name', name: 'clinic_name'},
            {data: 'categories', name: 'categories'},
            {data: 'description', name: 'description'},
            {data: 'quantity', name: 'quantity'},
            {data: 'timeline', name: 'timeline'},
            {data: 'attachments_count', name: 'attachments_count'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']],
        responsive: true,
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });
});

function refreshTable() {
    $('#available-requests-table').DataTable().ajax.reload();
}

function submitOffer(requestId) {
    window.location.href = "{{ route('supplier.available-requests.create-offer', ':id') }}".replace(':id', requestId);
}
</script>
@endpush
