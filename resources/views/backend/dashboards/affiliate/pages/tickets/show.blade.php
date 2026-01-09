@extends('backend.dashboards.affiliate.layouts.app')

@section('title', __('Ticket Details') . ' - ' . $ticket->ticket_number)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('affiliate.dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('affiliate.tickets.index') }}">{{ __('Tickets') }}</a></li>
                            <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('Ticket Details') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Ticket Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>{{ __('Ticket #') }}</th>
                                <td>{{ $ticket->ticket_number }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Type') }}</th>
                                <td>{!! $ticket->type_badge !!}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Status') }}</th>
                                <td>{!! $ticket->status_badge !!}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Created') }}</th>
                                <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if($ticket->getMedia('ticket_attachments')->count())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Attachments') }}</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @foreach($ticket->getMedia('ticket_attachments') as $media)
                                    <li class="mb-2"><a href="{{ $media->getUrl() }}" target="_blank"><i
                                                class="mdi mdi-file-outline me-1"></i>{{ $media->file_name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Conversation') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-4">
                            <div
                                class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-account"></i></div>
                            <div class="ms-3">
                                <h6 class="mb-1">{{ __('You') }} <small
                                        class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small></h6>
                                <p class="text-muted mb-0">{{ $ticket->details }}</p>
                            </div>
                        </div>
                        @foreach($ticket->replies as $reply)
                            <div class="d-flex mb-4 {{ $reply->is_admin_reply ? 'justify-content-end' : '' }}">
                                @if(!$reply->is_admin_reply)
                                    <div
                                        class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-account"></i></div>@endif
                                <div class="{{ $reply->is_admin_reply ? 'me-3 text-end' : 'ms-3' }}">
                                    <h6 class="mb-1">{{ $reply->is_admin_reply ? __('Admin') : __('You') }} <small
                                            class="text-muted">{{ $reply->created_at->diffForHumans() }}</small></h6>
                                    <p
                                        class="text-muted mb-0 {{ $reply->is_admin_reply ? 'bg-light p-2 rounded d-inline-block' : '' }}">
                                        {{ $reply->message }}</p>
                                </div>
                                @if($reply->is_admin_reply)
                                    <div
                                        class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-shield-account"></i></div>@endif
                            </div>
                        @endforeach
                        @if(!$ticket->isClosed())
                            <hr>
                            <form id="reply-form">@csrf
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Your Reply') }}</label>
                                    <textarea name="message" class="form-control" rows="3" required minlength="5"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i
                                        class="mdi mdi-send me-1"></i>{{ __('Send Reply') }}</button>
                            </form>
                        @else
                            <div class="alert alert-secondary text-center">{{ __('This ticket is closed.') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#reply-form').on('submit', function (e) {
            e.preventDefault();
            $.post('{{ route('affiliate.tickets.reply', $ticket->id) }}', $(this).serialize(), function (r) {
                if (r.status === 'success') { toastr.success(r.message); location.reload(); }
            }).fail(function (xhr) { toastr.error(xhr.responseJSON?.message || '{{ __('Error') }}'); });
        });
    </script>
@endpush