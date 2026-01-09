@extends('backend.dashboards.clinic.layouts.app')

@section('title', __('My Tickets'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('clinic.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Tickets') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('My Tickets') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-bordered mb-3">
                            <li class="nav-item">
                                <a href="#submitted" data-bs-toggle="tab" class="nav-link active">
                                    <i class="mdi mdi-ticket-outline me-1"></i> {{ __('Submitted Tickets') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#new" data-bs-toggle="tab" class="nav-link">
                                    <i class="mdi mdi-plus-circle me-1"></i> {{ __('Submit New Ticket') }}
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Submitted Tickets Tab -->
                            <div class="tab-pane show active" id="submitted">
                                <div class="table-responsive">
                                    <table id="tickets-table" class="table table-hover dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Ticket #') }}</th>
                                                <th>{{ __('Type') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Created') }}</th>
                                                <th>{{ __('Last Reply') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- New Ticket Tab -->
                            <div class="tab-pane" id="new">
                                <form id="new-ticket-form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="type" class="form-label">{{ __('Ticket Type') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="type" id="type" class="form-select" required>
                                                <option value="">{{ __('Select Type') }}</option>
                                                @foreach($ticketTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($ticketTypes->isEmpty())
                                                <small
                                                    class="text-danger">{{ __('No ticket types available for your account.') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="details" class="form-label">{{ __('Details') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea name="details" id="details" class="form-control" rows="5" required
                                            minlength="10" maxlength="5000"
                                            placeholder="{{ __('Please describe your issue or request in detail...') }}"></textarea>
                                        <small
                                            class="text-muted">{{ __('Minimum 10 characters, maximum 5000 characters') }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="attachments" class="form-label">{{ __('Attachments') }}</label>
                                        <input type="file" name="attachments[]" id="attachments" class="form-control"
                                            multiple accept=".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx">
                                        <small
                                            class="text-muted">{{ __('Max 5 files. Allowed: JPEG, PNG, GIF, PDF, DOC, DOCX. Max 2MB each.') }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary" @if($ticketTypes->isEmpty()) disabled
                                        @endif>
                                            <i class="mdi mdi-send me-1"></i> {{ __('Submit Ticket') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            $('#tickets-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('clinic.tickets.data') }}',
                columns: [
                    { data: 'ticket_number', name: 'ticket_number' },
                    { data: 'type', name: 'type' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'last_reply', name: 'last_reply', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[3, 'desc']]
            });

            $('#new-ticket-form').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route('clinic.tickets.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#new-ticket-form')[0].reset();
                            $('a[href="#submitted"]').tab('show');
                            $('#tickets-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(key => toastr.error(errors[key][0]));
                        } else {
                            toastr.error(xhr.responseJSON?.message || '{{ __('Something went wrong') }}');
                        }
                    }
                });
            });
        });
    </script>
@endpush