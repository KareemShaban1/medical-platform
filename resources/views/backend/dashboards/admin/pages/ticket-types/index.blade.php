@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Ticket Types Management'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">{{ __('Tickets') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Ticket Types') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('Ticket Types Management') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="header-title">{{ __('All Ticket Types') }}</h4>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                    data-bs-target="#ticketTypeModal" onclick="resetForm()">
                                    <i class="mdi mdi-plus-circle me-1"></i> {{ __('Add New Type') }}
                                </button>
                                <a href="{{ route('admin.ticket-types.trash') }}" class="btn btn-warning mb-2">
                                    <i class="mdi mdi-delete-restore"></i> {{ __('View Trash') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="ticket-types-table" class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Slug') }}</th>
                                    <th>{{ __('Badge') }}</th>
                                    <th>{{ __('User Types') }}</th>
                                    <th>{{ __('Status') }}</th>
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

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="ticketTypeModal" tabindex="-1" aria-labelledby="ticketTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketTypeModalLabel">{{ __('Add Ticket Type') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="ticketTypeForm">
                    @csrf
                    <input type="hidden" id="ticket_type_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">{{ __('Name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="slug" class="form-label">{{ __('Slug') }}</label>
                                <input type="text" class="form-control" id="slug" name="slug"
                                    placeholder="{{ __('Auto-generated if empty') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="badge_color" class="form-label">{{ __('Badge Color') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="badge_color" name="badge_color" required>
                                    <option value="primary">{{ __('Primary (Blue)') }}</option>
                                    <option value="secondary">{{ __('Secondary (Gray)') }}</option>
                                    <option value="success">{{ __('Success (Green)') }}</option>
                                    <option value="danger">{{ __('Danger (Red)') }}</option>
                                    <option value="warning">{{ __('Warning (Yellow)') }}</option>
                                    <option value="info">{{ __('Info (Cyan)') }}</option>
                                    <option value="dark">{{ __('Dark') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" checked>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Available For User Types') }} <span
                                    class="text-danger">*</span></label>
                            <div class="row">
                                @foreach($userTypes as $key => $label)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="user_types[]"
                                                value="{{ $key }}" id="user_type_{{ $key }}">
                                            <label class="form-check-label" for="user_type_{{ $key }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#ticket-types-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.ticket-types.data') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'slug', name: 'slug' },
                    { data: 'badge', name: 'badge', orderable: false },
                    { data: 'user_types', name: 'user_types', orderable: false },
                    { data: 'status', name: 'status', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Form submit
            $('#ticketTypeForm').on('submit', function (e) {
                e.preventDefault();

                let id = $('#ticket_type_id').val();
                let url = id ? `/admin/ticket-types/${id}` : '{{ route('admin.ticket-types.store') }}';
                let method = id ? 'PUT' : 'POST';

                // Gather form data
                let formData = {
                    _token: '{{ csrf_token() }}',
                    name: $('#name').val(),
                    slug: $('#slug').val(),
                    description: $('#description').val(),
                    badge_color: $('#badge_color').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0,
                    user_types: []
                };

                $('input[name="user_types[]"]:checked').each(function () {
                    formData.user_types.push($(this).val());
                });

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function (response) {
                        $('#ticketTypeModal').modal('hide');
                        table.ajax.reload();
                        toastr.success(response.message);
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(function (key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || '{{ __('Something went wrong') }}');
                        }
                    }
                });
            });
        });

        function resetForm() {
            $('#ticketTypeModalLabel').text('{{ __('Add Ticket Type') }}');
            $('#ticketTypeForm')[0].reset();
            $('#ticket_type_id').val('');
            $('#is_active').prop('checked', true);
            $('input[name="user_types[]"]').prop('checked', false);
        }

        function editTicketType(id) {
            $.ajax({
                url: `/admin/ticket-types/${id}`,
                method: 'GET',
                success: function (data) {
                    $('#ticketTypeModalLabel').text('{{ __('Edit Ticket Type') }}');
                    $('#ticket_type_id').val(data.id);
                    $('#name').val(data.name);
                    $('#slug').val(data.slug);
                    $('#description').val(data.description);
                    $('#badge_color').val(data.badge_color);
                    $('#is_active').prop('checked', data.is_active);

                    // Set user types
                    $('input[name="user_types[]"]').prop('checked', false);
                    if (data.allowed_user_types) {
                        data.allowed_user_types.forEach(function (ut) {
                            $(`#user_type_${ut.user_type}`).prop('checked', true);
                        });
                    }

                    $('#ticketTypeModal').modal('show');
                },
                error: function () {
                    toastr.error('{{ __('Failed to load ticket type') }}');
                }
            });
        }

        function deleteTicketType(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: "{{ __('This will move the ticket type to trash.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __('Yes, delete it!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/ticket-types/${id}`,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            $('#ticket-types-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        },
                        error: function () {
                            toastr.error('{{ __('Something went wrong') }}');
                        }
                    });
                }
            });
        }
    </script>
@endpush