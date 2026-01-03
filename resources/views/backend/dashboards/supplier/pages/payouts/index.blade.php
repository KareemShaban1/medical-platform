@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Payouts'))

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ __('Payouts') }}</h4>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-currency-usd widget-icon bg-success-lighten text-success"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0">{{ __('Available Balance') }}</h5>
                        <h3 class="mt-3 mb-3">{{ number_format($eligibleAmount, 2) }} {{ __('EGP') }}</h3>
                        <p class="mb-0 text-muted">
                            <span class="text-success me-2">{{ $eligibleOrders->count() }}</span>
                            <span>{{ __('Eligible Orders') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-clock-outline widget-icon bg-warning-lighten text-warning"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0">{{ __('Minimum Payout') }}</h5>
                        <h3 class="mt-3 mb-3">{{ number_format($minimumAmount, 2) }} {{ __('EGP') }}</h3>
                        <p class="mb-0 text-muted">
                            {{ __('Minimum amount required') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-calendar-clock widget-icon bg-info-lighten text-info"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0">{{ __('Payout Frequency') }}</h5>
                        <h3 class="mt-3 mb-3">{{ $cooldownWeeks }} {{ __('Weeks') }}</h3>
                        <p class="mb-0 text-muted">
                            @if (!$cooldownInfo['can_request'] && $cooldownInfo['next_request_date'])
                                <span
                                    class="text-warning">{{ __('Next payout available: :date', ['date' => $cooldownInfo['next_request_date']->format('Y-m-d')]) }}</span>
                            @else
                                <span class="text-success">{{ __('You can request a payout now') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notice -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="mdi mdi-information-outline me-2 font-22"></i>
                    <div>
                        {{ __('Payout requests are processed within 2-5 business days. Make sure your payment details are correct before submitting.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Payout Profile Section -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Payment Profile') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('supplier.payouts.profile.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="payout_method" class="form-label">{{ __('Payment Method') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="payout_method" name="payout_method" required>
                                    <option value="">{{ __('Select Method') }}</option>
                                    @foreach ($payoutMethods as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ $payoutProfile && $payoutProfile->payout_method === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="payout_details" class="form-label">{{ __('Payment Details') }} <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="payout_details" name="payout_details" rows="4" required
                                    placeholder="{{ __('Enter your account number, phone number, or bank details...') }}">{{ $payoutProfile->payout_details ?? '' }}</textarea>
                                <div class="form-text">{{ __('Include all necessary details for receiving payment') }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">{{ __('Notes') }}</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"
                                    placeholder="{{ __('Any additional notes...') }}">{{ $payoutProfile->notes ?? '' }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="mdi mdi-content-save me-1"></i> {{ __('Save Profile') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Request Payout Section -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Request Payout') }}</h5>
                        @if ($hasPendingRequest)
                            <span class="badge bg-warning">{{ __('Pending Request Exists') }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if (!$payoutProfile)
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert-outline me-1"></i>
                                {{ __('Please set up your payment profile first before requesting a payout.') }}
                            </div>
                        @elseif($hasPendingRequest)
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-1"></i>
                                {{ __('You already have a pending payout request. Please wait for it to be processed.') }}
                            </div>
                        @elseif(!$cooldownInfo['can_request'])
                            <div class="alert alert-warning">
                                <i class="mdi mdi-clock-outline me-1"></i>
                                {{ __('You can request your next payout on :date (:days days and :hours hours remaining).', [
                                    'date' => $cooldownInfo['next_request_date']->format('Y-m-d'),
                                    'days' => $cooldownInfo['days_remaining'],
                                    'hours' => $cooldownInfo['hours_remaining'],
                                ]) }}
                            </div>
                        @elseif($eligibleAmount < $minimumAmount)
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert-outline me-1"></i>
                                {{ __('Your balance (:amount EGP) is below the minimum payout amount (:min EGP).', [
                                    'amount' => number_format($eligibleAmount, 2),
                                    'min' => number_format($minimumAmount, 2),
                                ]) }}
                            </div>
                        @else
                            <form action="{{ route('supplier.payouts.request') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Select Orders for Payout') }}</label>
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                                    </th>
                                                    <th>{{ __('Order #') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($eligibleOrders as $orderSupplier)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" class="form-check-input order-checkbox"
                                                                name="order_ids[]" value="{{ $orderSupplier->id }}"
                                                                data-amount="{{ $orderSupplier->subtotal }}">
                                                        </td>
                                                        <td>{{ $orderSupplier->order->number ?? 'N/A' }}</td>
                                                        <td>{{ $orderSupplier->created_at->format('Y-m-d') }}</td>
                                                        <td>{{ number_format($orderSupplier->subtotal, 2) }}
                                                            {{ __('EGP') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">
                                                            {{ __('No eligible orders available') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="mb-3 p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ __('Selected Total:') }}</span>
                                        <span class="fs-4 fw-bold text-success" id="selectedTotal">0.00
                                            {{ __('EGP') }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="supplier_note" class="form-label">{{ __('Note (Optional)') }}</label>
                                    <textarea class="form-control" id="supplier_note" name="supplier_note" rows="2"
                                        placeholder="{{ __('Any message for the admin...') }}"></textarea>
                                </div>

                                <button type="submit" class="btn btn-success w-100" id="submitBtn" disabled>
                                    <i class="mdi mdi-cash-fast me-1"></i> {{ __('Request Payout') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Payout History -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Payout History') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered table-striped table-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Method') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Requested At') }}</th>
                                        <th>{{ __('Paid At') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payoutRequests as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>{{ number_format($request->amount, 2) }} {{ __('EGP') }}</td>
                                            <td>{{ $payoutMethods[$request->payout_method] ?? $request->payout_method }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $request->status_badge_class }}">
                                                    {{ $request->status_label }}
                                                </span>
                                            </td>
                                            <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $request->paid_at ? $request->paid_at->format('Y-m-d H:i') : '-' }}</td>
                                            <td>
                                                <a href="{{ route('supplier.payouts.show', $request->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('No payout requests yet') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $payoutRequests->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const minAmount = {{ $minimumAmount }};

            function updateTotal() {
                let total = 0;
                $('.order-checkbox:checked').each(function() {
                    total += parseFloat($(this).data('amount'));
                });

                $('#selectedTotal').text(total.toFixed(2) + ' {{ __('EGP') }}');
                $('#submitBtn').prop('disabled', total < minAmount || total === 0);
            }

            // Select all checkbox
            $('#selectAll').on('change', function() {
                $('.order-checkbox').prop('checked', $(this).is(':checked'));
                updateTotal();
            });

            // Individual checkbox
            $('.order-checkbox').on('change', function() {
                updateTotal();

                // Update select all state
                const allChecked = $('.order-checkbox').length === $('.order-checkbox:checked').length;
                $('#selectAll').prop('checked', allChecked);
            });
        });
    </script>
@endpush
