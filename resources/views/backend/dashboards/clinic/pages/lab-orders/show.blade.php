@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="page-title">{{ __('Lab Order') }} #{{ $order->id }}</h4>
        <a href="{{ route('clinic.lab-orders.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">{{ __('Patient') }}</dt>
                        <dd class="col-sm-9">{{ $order->patient?->name }}</dd>

                        <dt class="col-sm-3">{{ __('Test Name') }}</dt>
                        <dd class="col-sm-9">{{ $order->test_name }}</dd>

                        <dt class="col-sm-3">{{ __('Lab Name') }}</dt>
                        <dd class="col-sm-9">{{ $order->lab_name ?: '—' }}</dd>

                        <dt class="col-sm-3">{{ __('Status') }}</dt>
                        <dd class="col-sm-9"><span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'received' ? 'info' : 'warning') }}">{{ ucfirst($order->status) }}</span></dd>

                        <dt class="col-sm-3">{{ __('Cost') }}</dt>
                        <dd class="col-sm-9">{{ $order->cost_amount ? number_format($order->cost_amount, 2) : '—' }}</dd>

                        <dt class="col-sm-3">{{ __('Notes') }}</dt>
                        <dd class="col-sm-9">{{ $order->notes ?: '—' }}</dd>

                        <dt class="col-sm-3">{{ __('Sent At') }}</dt>
                        <dd class="col-sm-9">{{ $order->sent_at?->format('Y-m-d H:i') ?: '—' }}</dd>

                        <dt class="col-sm-3">{{ __('Received At') }}</dt>
                        <dd class="col-sm-9">{{ $order->received_at?->format('Y-m-d H:i') ?: '—' }}</dd>

                        <dt class="col-sm-3">{{ __('Reviewed At') }}</dt>
                        <dd class="col-sm-9">{{ $order->reviewed_at?->format('Y-m-d H:i') ?: '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Result Files & Comment') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('clinic.lab-orders.upload', $order->id) }}" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Result Comment') }}</label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="{{ __('Add a note for these results (optional)') }}">{{ old('comment', $order->result_comment) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Upload Files') }}</label>
                            <input type="file" name="results[]" multiple class="form-control" />
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="replace" id="replaceFiles" value="1">
                                <label class="form-check-label" for="replaceFiles">{{ __('Replace existing files') }}</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">{{ __('Save Changes') }}</button>
                        </div>
                    </form>
                    @if($order->status !== 'completed')
                        <form method="POST" action="{{ route('clinic.lab-orders.complete', $order->id) }}" onsubmit="return confirm('{{ __('Mark as completed?') }}')" class="mt-2">
                            @csrf
                            <button class="btn btn-outline-primary">{{ __('Mark Completed') }}</button>
                        </form>
                    @endif
                    @if(count($order->attachments) > 0)
                        <ul class="list-group">
                            @foreach($order->attachments as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="uil-file"></i> {{ $file['name'] }}</span>
                                    <a href="{{ $file['url'] }}" target="_blank" class="btn btn-sm btn-outline-secondary">{{ __('Download') }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">{{ __('No files uploaded yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('Doctor') }}</h6></div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ __('Created By') }}:</strong> {{ $order->creator?->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
