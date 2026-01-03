@extends('backend.dashboards.admin.layouts.app')

@section('title', __('Supplier Payouts'))

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ __('Supplier Payout Requests') }}</h4>
                </div>
            </div>
        </div>

        <!-- Status Filter Tabs -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-2">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.supplier-payouts.index') }}"
                                class="btn {{ !request('status') || request('status') === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ __('All') }} <span
                                    class="badge bg-light text-dark ms-1">{{ $statusCounts['all'] }}</span>
                            </a>
                            <a href="{{ route('admin.supplier-payouts.index', ['status' => 'pending']) }}"
                                class="btn {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                                {{ __('Pending') }} <span
                                    class="badge bg-light text-dark ms-1">{{ $statusCounts['pending'] }}</span>
                            </a>
                            <a href="{{ route('admin.supplier-payouts.index', ['status' => 'approved']) }}"
                                class="btn {{ request('status') === 'approved' ? 'btn-info' : 'btn-outline-info' }}">
                                {{ __('Approved') }} <span
                                    class="badge bg-light text-dark ms-1">{{ $statusCounts['approved'] }}</span>
                            </a>
                            <a href="{{ route('admin.supplier-payouts.index', ['status' => 'paid']) }}"
                                class="btn {{ request('status') === 'paid' ? 'btn-success' : 'btn-outline-success' }}">
                                {{ __('Paid') }} <span
                                    class="badge bg-light text-dark ms-1">{{ $statusCounts['paid'] }}</span>
                            </a>
                            <a href="{{ route('admin.supplier-payouts.index', ['status' => 'rejected']) }}"
                                class="btn {{ request('status') === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                                {{ __('Rejected') }} <span
                                    class="badge bg-light text-dark ms-1">{{ $statusCounts['rejected'] }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payout Requests Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered table-striped table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Supplier') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Method') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Requested At') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>
                                                <strong>{{ $request->supplier->name ?? 'N/A' }}</strong>
                                                <br><small class="text-muted">{{ $request->supplier->phone ?? '' }}</small>
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ number_format($request->amount, 2) }}
                                                    {{ __('EGP') }}</strong>
                                            </td>
                                            <td>{{ $request->payout_method }}</td>
                                            <td>
                                                <span class="badge {{ $request->status_badge_class }}">
                                                    {{ $request->status_label }}
                                                </span>
                                            </td>
                                            <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.supplier-payouts.show', $request->id) }}"
                                                    class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('No payout requests found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $requests->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
