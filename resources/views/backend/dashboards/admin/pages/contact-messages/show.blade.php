@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Contact Message Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ __('Contact Message Details') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.contact-messages.index') }}">{{ __('Contact Messages') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Details') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">{{ __('Message Details') }}</h5>
                        <div>
                            @if($message->status === 'new')
                                <span class="badge bg-primary">{{ __('New') }}</span>
                            @elseif($message->status === 'read')
                                <span class="badge bg-info">{{ __('Read') }}</span>
                            @elseif($message->status === 'replied')
                                <span class="badge bg-success">{{ __('Replied') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Archived') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>{{ __('Name') }}:</strong></p>
                            <p>{{ $message->full_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>{{ __('Email') }}:</strong></p>
                            <p><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>{{ __('Phone') }}:</strong></p>
                            <p><a href="tel:{{ $message->phone }}">{{ $message->phone }}</a></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>{{ __('Company') }}:</strong></p>
                            <p>{{ $message->company ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <p class="mb-2"><strong>{{ __('Message') }}:</strong></p>
                            <div class="alert alert-secondary" role="alert">
                                {{ $message->message }}
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>{{ __('Submitted At') }}:</strong></p>
                            <p>{{ $message->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        @if($message->read_at)
                        <div class="col-md-6">
                            <p class="mb-2"><strong>{{ __('Read At') }}:</strong></p>
                            <p>{{ $message->read_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <p class="mb-2"><strong>{{ __('Privacy Policy Agreement') }}:</strong></p>
                            <p>
                                @if($message->agree_to_policies)
                                    <span class="badge bg-success"><i class="fa fa-check"></i> {{ __('Agreed') }}</span>
                                @else
                                    <span class="badge bg-danger"><i class="fa fa-times"></i> {{ __('Not Agreed') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Admin Notes') }}</h5>
                </div>
                <div class="card-body">
                    <form id="notes-form">
                        <textarea name="admin_notes" id="admin_notes" rows="5" class="form-control" placeholder="{{ __('Add your notes here...') }}">{{ $message->admin_notes }}</textarea>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fa fa-save"></i> {{ __('Save Notes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Change Status') }}</h5>
                </div>
                <div class="card-body">
                    <form id="status-form">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-select" id="status-select">
                                <option value="new" {{ $message->status === 'new' ? 'selected' : '' }}>{{ __('New') }}</option>
                                <option value="read" {{ $message->status === 'read' ? 'selected' : '' }}>{{ __('Read') }}</option>
                                <option value="replied" {{ $message->status === 'replied' ? 'selected' : '' }}>{{ __('Replied') }}</option>
                                <option value="archived" {{ $message->status === 'archived' ? 'selected' : '' }}>{{ __('Archived') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-check"></i> {{ __('Update Status') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <a href="mailto:{{ $message->email }}" class="btn btn-info w-100 mb-2">
                        <i class="fa fa-envelope"></i> {{ __('Send Email') }}
                    </a>
                    <a href="tel:{{ $message->phone }}" class="btn btn-success w-100 mb-2">
                        <i class="fa fa-phone"></i> {{ __('Call') }}
                    </a>
                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary w-100">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Update status
        $('#status-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route("admin.contact-messages.update-status", $message->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: $('#status-select').val()
                },
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || '{{ __("Error updating status") }}');
                }
            });
        });

        // Save notes
        $('#notes-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route("admin.contact-messages.add-notes", $message->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    admin_notes: $('#admin_notes').val()
                },
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || '{{ __("Error saving notes") }}');
                }
            });
        });
    });
</script>
@endpush
@endsection
