@extends('backend.dashboards.affiliate.layouts.app')

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
                            <li class="breadcrumb-item"><a
                                    href="{{ route('affiliate.dashboard') }}">{{ __('Dashboard') }}</a></li>
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
                        <ul class="nav nav-tabs nav-bordered mb-3">
                            <li class="nav-item">
                                <a href="#submitted" data-bs-toggle="tab"
                                    class="nav-link active">{{ __('Submitted Tickets') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="#new" data-bs-toggle="tab" class="nav-link">{{ __('Submit New Ticket') }}</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane show active" id="submitted">
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
                                </table>
                            </div>

                            <div class="tab-pane" id="new">
                                <form id="new-ticket-form" enctype="multipart/form-data">@csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Ticket Type') }} *</label>
                                            <select name="type" class="form-select" required>
                                                <option value="">{{ __('Select Type') }}</option>
                                                @foreach($ticketTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Details') }} *</label>
                                        <textarea name="details" class="form-control" rows="5" required minlength="10"
                                            maxlength="5000"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Attachments') }}</label>
                                        <input type="file" name="attachments[]" class="form-control" multiple
                                            accept=".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx">
                                    </div>
                                    <button type="submit" class="btn btn-primary" @if($ticketTypes->isEmpty()) disabled
                                    @endif>{{ __('Submit Ticket') }}</button>
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

        $(function () {
            $('#tickets-table').DataTable({
                processing: true, serverSide: true,
                ajax: '{{ route('affiliate.tickets.data') }}',
                columns: [
                    { data: 'ticket_number' }, { data: 'type' }, { data: 'status' },
                    { data: 'created_at' }, { data: 'last_reply', orderable: false },
                    { data: 'action', orderable: false, searchable: false }
                ], order: [[3, 'desc']]
            });

            $('#new-ticket-form').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: '{{ route('affiliate.tickets.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (r) {
                        if (r.status === 'success') {
                            $('#new-ticket-form')[0].reset();
                            $('a[href="#submitted"]').tab('show');
                            $('#tickets-table').DataTable().ajax.reload();
                            toastr.success(r.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(function (msgs) {
                                msgs.forEach(function (msg) { toastr.error(msg); });
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || '{{ __('Error') }}');
                        }
                    }
                });
            });
        });
    </script>
@endpush