@extends('backend.dashboards.admin.layouts.app')
@section('title', __('Affiliate Payout Requests'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Affiliate Payout Requests') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Owner') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Payment Method') }}</th>
                                    <th>{{ __('Payment Details') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Requested At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    @php
                                        $code = $request->affiliateCode;
                                        $owner = $code?->affiliateable;
                                        $ownerName = $owner?->name ?? $owner?->email ?? __('Unknown');
                                        $ownerType = $code?->affiliateable_type === 'App\\Models\\ClinicUser' ? __('Clinic User') : __('Affiliate User');
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold text-primary">{{ $code?->code ?? '-' }}</td>
                                        <td>
                                            @if($code)
                                                <a href="{{ route('admin.affiliates.users.show', $code->id) }}" class="text-primary fw-semibold">
                                                    {{ $ownerName }}
                                                </a>
                                            @else
                                                {{ $ownerName }}
                                            @endif
                                        </td>
                                        <td>{{ $ownerType }}</td>
                                        <td>{{ number_format($request->amount, 2) }}</td>
                                        <td>{{ $request->payout_method }}</td>
                                        <td class="text-wrap" style="min-width: 220px;">{{ $request->payout_details }}</td>
                                        <td class="text-wrap" style="min-width: 200px;">{{ $request->notes ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $request->status === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at?->format('M d, Y') }}</td>
                                        <td>
                                            @if($request->status === 'pending')
                                                @hasPermission('update affiliate payouts')
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#markPaidModal-{{ $request->id }}">
                                                    {{ __('Mark Paid') }}
                                                </button>
                                                @else
                                                <span class="text-muted">{{ __('No access') }}</span>
                                                @endhasPermission
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#payoutProofModal-{{ $request->id }}">
                                                    {{ __('View Proof') }}
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($request->status === 'pending')
                                    <div class="modal fade" id="markPaidModal-{{ $request->id }}" tabindex="-1" aria-labelledby="markPaidModalLabel-{{ $request->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.affiliates.payouts.mark-paid', $request->id) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="markPaidModalLabel-{{ $request->id }}">{{ __('Mark Payout as Paid') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Upload Proof Images') }}</label>
                                                            <input type="file" class="form-control" name="proof_images[]" multiple accept="image/*">
                                                            <small class="text-muted">{{ __('Upload screenshots or receipts (optional).') }}</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Admin Note') }}</label>
                                                            <textarea class="form-control" name="admin_note" rows="3" placeholder="{{ __('Add a note for the affiliate (optional).') }}"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                        <button type="submit" class="btn btn-success">{{ __('Mark Paid') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="modal fade" id="payoutProofModal-{{ $request->id }}" tabindex="-1" aria-labelledby="payoutProofModalLabel-{{ $request->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="payoutProofModalLabel-{{ $request->id }}">{{ __('Payout Proof') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if($request->admin_note)
                                                        <div class="mb-3">
                                                            <div class="text-muted">{{ __('Admin Note') }}</div>
                                                            <div class="fw-bold" style="white-space: pre-wrap;">{{ $request->admin_note }}</div>
                                                        </div>
                                                    @endif
                                                    <div class="row g-3">
                                                        @forelse($request->getMedia('affiliate_payout_proofs') as $media)
                                                            <div class="col-md-4">
                                                                <a href="{{ $media->getUrl() }}" target="_blank">
                                                                    <img src="{{ $media->getUrl() }}" class="img-fluid rounded border" alt="{{ $media->name }}">
                                                                </a>
                                                            </div>
                                                        @empty
                                                            <p class="text-muted mb-0">{{ __('No proof images uploaded.') }}</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            {{ __('No payout requests found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $requests->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
