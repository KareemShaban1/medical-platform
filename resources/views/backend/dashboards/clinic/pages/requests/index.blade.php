@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('Purchase Requests'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('clinic.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Purchase Requests') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Purchase Requests') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <a href="{{ route('clinic.requests.create') }}" class="btn btn-danger mb-2">
                                <i class="mdi mdi-plus-circle me-2"></i> {{ __('Create New Request') }}
                            </a>
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
                        <table id="requests-table" class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Categories') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Timeline') }}</th>
                                    <th>{{ __('Offers') }}</th>
                                    <th>{{ __('Status') }}</th>
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
    var table = $('#requests-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('clinic.requests.data') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'categories', name: 'categories'},
            {data: 'description', name: 'description', render: function(data) {
                return data.length > 50 ? data.substring(0, 50) + '...' : data;
            }},
            {data: 'quantity', name: 'quantity'},
            {data: 'timeline', name: 'timeline'},
            {data: 'offers_count', name: 'offers_count'},
            {data: 'status', name: 'status'},
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
    $('#requests-table').DataTable().ajax.reload();
}

function editRequest(id) {
    window.location.href = "{{ route('clinic.requests.edit', ':id') }}".replace(':id', id);
}

function deleteRequest(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("You won\'t be able to revert this!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("Yes, delete it!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('clinic.requests.destroy', ':id') }}".replace(':id', id),
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('{{ __("Deleted!") }}', response.message, 'success');
                        refreshTable();
                    } else {
                        Swal.fire('{{ __("Error!") }}', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
                }
            });
        }
    });
}

function cancelRequest(id) {
    Swal.fire({
        title: '{{ __("Cancel Request?") }}',
        text: '{{ __("This will decline all pending offers and close the request.") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f1556c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("Yes, cancel it!") }}',
        cancelButtonText: '{{ __("No, keep it") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('clinic.requests.cancel', ':id') }}".replace(':id', id),
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('{{ __("Canceled!") }}', response.message, 'success');
                        refreshTable();
                    } else {
                        Swal.fire('{{ __("Error!") }}', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong!") }}', 'error');
                }
            });
        }
    });
}
</script>
@endpush
