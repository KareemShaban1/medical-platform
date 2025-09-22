@extends('backend.dashboards.admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button onclick="markAllAsRead()" class="btn btn-primary">
                        <i class="mdi mdi-check-all"></i> {{ __('Mark All as Read') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Notifications') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="notifications-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Message') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created At') }}</th>
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
let table = $('#notifications-table').DataTable({
    ajax: '{{ route("admin.notifications.data") }}',
    columns: [
        { data: 'id', name: 'id' },
        { data: 'title', name: 'title' },
        { data: 'message', name: 'message' },
        { data: 'type', name: 'type', orderable: false },
        { data: 'status', name: 'status', orderable: false },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
    pageLength: 15,
    responsive: true,
    language: languages[language],
    buttons: [
        {
            extend: 'print',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5]
            }
        },
        {
            extend: 'excel',
            text: 'Excel',
            title: 'Notifications Data',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5]
            }
        },
        {
            extend: 'copy',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5]
            }
        }
    ],
    drawCallback: function() {
        $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
    }
});

// Mark individual notification as read
function markAsRead(notificationId) {
    $.post('{{ route("admin.notifications.mark-as-read", ":id") }}'.replace(':id', notificationId), {
        _token: '{{ csrf_token() }}'
    }).done(function(response) {
        if (response.status === 'success') {
            table.ajax.reload();
            Swal.fire({
                icon: 'success',
                title: '{{ __("Success") }}',
                text: '{{ __("Notification marked as read") }}',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }).fail(function() {
        Swal.fire('Error!', '{{ __("Something went wrong") }}', 'error');
    });
}

// Mark all notifications as read
function markAllAsRead() {
    Swal.fire({
        title: '{{ __("Mark All as Read?") }}',
        text: '{{ __("This will mark all your notifications as read.") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("Yes, mark all as read!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ route("admin.notifications.mark-all-as-read") }}', {
                _token: '{{ csrf_token() }}'
            }).done(function(response) {
                if (response.status === 'success') {
                    table.ajax.reload();

                    // Update the notification bell in the header
                    if (typeof loadNotifications === 'function') {
                        loadNotifications();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Success") }}',
                        text: '{{ __("All notifications marked as read") }}',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }).fail(function() {
                Swal.fire('Error!', '{{ __("Something went wrong") }}', 'error');
            });
        }
    });
}

// Notification click handler (for when clicking on notification title/message)
$(document).on('click', '.notification-link', function(e) {
    e.preventDefault();

    const notificationId = $(this).data('notification-id');
    const actionUrl = $(this).data('action-url');

    // Mark as read and redirect
    $.post('{{ route("admin.notifications.mark-as-read", ":id") }}'.replace(':id', notificationId), {
        _token: '{{ csrf_token() }}'
    }).done(function(response) {
        if (response.status === 'success') {
            if (actionUrl && actionUrl !== '#') {
                window.location.href = actionUrl;
            }
        }
    });
});
</script>
@endpush
