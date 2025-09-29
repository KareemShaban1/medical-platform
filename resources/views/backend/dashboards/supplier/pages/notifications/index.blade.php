@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Notifications'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('supplier.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Notifications') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Notifications') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-success mb-2" onclick="markAllAsRead()">
                                <i class="mdi mdi-check-all me-2"></i> {{ __('Mark All as Read') }}
                            </button>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <button type="button" class="btn btn-light mb-2 me-1" onclick="refreshTable()">
                                    <i class="mdi mdi-refresh"></i> {{ __('Refresh') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="notifications-table" class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Message') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
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
    var table = $('#notifications-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier.notifications.data') }}",
        columns: [
            {data: 'title', name: 'title'},
            {data: 'message', name: 'message'},
            {data: 'type', name: 'type'},
            {data: 'status', name: 'status'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[4, 'desc']],
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
    $('#notifications-table').DataTable().ajax.reload();
    // Also update the notification badge
    loadNotifications();
}

function markAsRead(notificationId) {
    $.post('{{ route("supplier.notifications.mark-as-read", ":id") }}'.replace(':id', notificationId), {
        _token: '{{ csrf_token() }}'
    }).done(function(response) {
        if (response.status === 'success') {
            refreshTable();
            Swal.fire({
                icon: 'success',
                title: '{{ __("Success") }}',
                text: '{{ __("Notification marked as read") }}',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }).fail(function() {
        Swal.fire({
            icon: 'error',
            title: '{{ __("Error") }}',
            text: '{{ __("Failed to mark notification as read") }}'
        });
    });
}

function markAllAsRead() {
    Swal.fire({
        title: '{{ __("Mark All as Read?") }}',
        text: '{{ __("This will mark all notifications as read.") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("Yes, mark all!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ route("supplier.notifications.mark-all-as-read") }}', {
                _token: '{{ csrf_token() }}'
            }).done(function(response) {
                if (response.status === 'success') {
                    refreshTable();
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Success") }}',
                        text: '{{ __("All notifications marked as read") }}',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("Error") }}',
                    text: '{{ __("Failed to mark notifications as read") }}'
                });
            });
        }
    });
}

// Load notifications function for badge update (from top-bar)
function loadNotifications() {
    $.get('{{ route("supplier.notifications.latest") }}')
        .done(function(response) {
            updateNotificationBadge(response.unread_count);
        });
}

function updateNotificationBadge(count) {
    const badge = $('#notification-count');
    const sidebarBadge = $('#sidebar-notification-count');

    if (count > 0) {
        const displayCount = count > 99 ? '99+' : count;
        badge.text(displayCount).show();
        sidebarBadge.text(displayCount).show();
    } else {
        badge.hide();
        sidebarBadge.hide();
    }
}
</script>
@endpush
