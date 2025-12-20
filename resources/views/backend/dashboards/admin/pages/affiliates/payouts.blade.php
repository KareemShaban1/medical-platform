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
                                                <form method="POST" action="{{ route('admin.affiliates.payouts.mark-paid', $request->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        {{ __('Mark Paid') }}
                                                    </button>
                                                </form>
                                                @else
                                                <span class="text-muted">{{ __('No access') }}</span>
                                                @endhasPermission
                                            @else
                                                <span class="text-muted">
                                                    {{ __('Paid') }}
                                                    @if($request->paid_at)
                                                        ({{ $request->paid_at->format('M d, Y') }})
                                                    @endif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
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
