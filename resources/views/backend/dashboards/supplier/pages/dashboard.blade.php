@extends('backend.dashboards.supplier.layouts.app')

@section('title', __('Dashboard'))

@push('styles')
    <style>
        /* Dashboard Styles */
        .dashboard-welcome {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        .dashboard-welcome h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .dashboard-welcome p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card .stat-icon.primary {
            background: rgba(17, 153, 142, 0.15);
            color: #11998e;
        }

        .stat-card .stat-icon.success {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .stat-card .stat-icon.warning {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .stat-card .stat-icon.danger {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .stat-card .stat-icon.info {
            background: rgba(23, 162, 184, 0.15);
            color: #17a2b8;
        }

        .stat-card .stat-icon.purple {
            background: rgba(111, 66, 193, 0.15);
            color: #6f42c1;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
        }

        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .stat-card .stat-change {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .stat-card .stat-change.positive {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .stat-card .stat-change.negative {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .quick-action-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-decoration: none;
            display: block;
            height: 100%;
        }

        .quick-action-card:hover {
            border-color: #11998e;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(17, 153, 142, 0.15);
        }

        .quick-action-card .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin: 0 auto 0.75rem;
            color: white;
        }

        .quick-action-card .action-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .quick-action-card .action-desc {
            color: #6c757d;
            font-size: 0.75rem;
            margin: 0;
        }

        .dashboard-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .dashboard-card .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dashboard-card .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: #2c3e50;
        }

        .dashboard-card .card-body {
            padding: 1.5rem;
        }

        .subscription-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .subscription-badge.active {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .order-item:hover {
            background: #f8f9fa;
        }

        .order-item .order-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: linear-gradient(135deg, #11998e, #38ef7d);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            margin-right: 1rem;
        }

        .order-item .details {
            flex: 1;
        }

        .order-item .name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.125rem;
        }

        .order-item .meta {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .order-item .amount {
            font-weight: 700;
            color: #11998e;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .status-confirmed {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-processing {
            background: rgba(23, 162, 184, 0.15);
            color: #17a2b8;
        }

        .status-shipped {
            background: rgba(111, 66, 193, 0.15);
            color: #6f42c1;
        }

        .status-delivered {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-cancelled {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .status-accepted {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-rejected {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .chart-container {
            position: relative;
            height: 250px;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .product-item:hover {
            background: #f8f9fa;
        }

        .product-item .product-img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 1rem;
            background: #f0f0f0;
        }

        .product-item .product-info {
            flex: 1;
        }

        .product-item .product-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.125rem;
        }

        .product-item .product-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .product-item .sold-count {
            font-weight: 700;
            color: #11998e;
        }

        .request-item {
            padding: 1rem;
            border-radius: 10px;
            border: 1px solid #f0f0f0;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }

        .request-item:hover {
            border-color: #11998e;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.1);
        }

        .request-item:last-child {
            margin-bottom: 0;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .revenue-highlight {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 16px;
            padding: 1.5rem;
            color: white;
            text-align: center;
        }

        .revenue-highlight .amount {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .revenue-highlight .label {
            opacity: 0.9;
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Announcement -->
        @if (isset($announcement) && $announcement)
            <div class="alert alert-info d-flex justify-content-between align-items-start mb-4" role="alert"
                id="dashboard-announcement" data-id="{{ $announcement->id }}">
                <div>
                    <div class="fw-bold"><i class="fas fa-bullhorn me-2"></i>{{ $announcement->title }}</div>
                    @if ($announcement->body)
                        <div class="mt-1">{!! nl2br(e($announcement->body)) !!}</div>
                    @endif
                    @if ($announcement->link_url)
                        <div class="mt-2"><a href="{{ $announcement->link_url }}" target="_blank"
                                class="btn btn-sm btn-primary">{{ __('Open Link') }}</a></div>
                    @endif
                </div>
                <button type="button" class="btn-close" aria-label="Close" id="dismiss-announcement"></button>
            </div>
        @endif

        <!-- Welcome Section -->
        <div class="dashboard-welcome">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>{{ __('Welcome back') }}, {{ auth('supplier')->user()->name }}! 👋</h2>
                    <p>{{ __('Here\'s your business overview for') }} {{ $supplier->name }}.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    @if ($subscription)
                        <span class="subscription-badge active">
                            <i class="fas fa-crown me-1"></i>{{ $subscription->plan->name ?? __('Active Plan') }}
                        </span>
                    @else
                        <a href="{{ route('supplier.subscriptions.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-rocket me-1"></i>{{ __('Upgrade Plan') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Stats Cards Row -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <div class="stat-value">{{ $totalProducts }}</div>
                            <div class="stat-label">{{ __('Total Products') }}</div>
                        </div>
                        <div class="text-end">
                            <small class="text-success d-block"><i class="fas fa-check-circle"></i> {{ $activeProducts }}
                                {{ __('active') }}</small>
                            @if ($outOfStockProducts > 0)
                                <small class="text-danger"><i class="fas fa-exclamation-circle"></i>
                                    {{ $outOfStockProducts }} {{ __('out of stock') }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <div class="stat-value">{{ $totalOrders }}</div>
                            <div class="stat-label">{{ __('Total Orders') }}</div>
                        </div>
                        <div class="text-end">
                            <small class="text-info d-block">{{ $ordersThisMonth }} {{ __('this month') }}</small>
                            @if ($pendingOrders > 0)
                                <small class="text-warning"><i class="fas fa-clock"></i> {{ $pendingOrders }}
                                    {{ __('pending') }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <div class="stat-value">{{ $totalOffers }}</div>
                            <div class="stat-label">{{ __('Total Offers') }}</div>
                        </div>
                        <div class="text-end">
                            <small class="text-success d-block"><i class="fas fa-check"></i> {{ $acceptedOffers }}
                                {{ __('accepted') }}</small>
                            <small class="text-warning"><i class="fas fa-clock"></i> {{ $pendingOffers }}
                                {{ __('pending') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <div class="stat-value">{{ $availableRequests }}</div>
                            <div class="stat-label">{{ __('Available Requests') }}</div>
                        </div>
                        <a href="{{ route('supplier.available-requests.index') }}"
                            class="btn btn-sm btn-outline-info">{{ __('View') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="revenue-highlight">
                    <div class="amount">{{ number_format($revenueThisMonth, 0) }} <small>{{ __('EGP') }}</small></div>
                    <div class="label">{{ __('Revenue This Month') }}</div>
                    @if ($revenueGrowth != 0)
                        <div class="mt-2">
                            <span class="badge bg-{{ $revenueGrowth > 0 ? 'light text-success' : 'light text-danger' }}">
                                <i class="fas fa-{{ $revenueGrowth > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ abs($revenueGrowth) }}% {{ __('vs last month') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-value">{{ number_format($totalPayouts, 0) }} <small class="text-muted"
                            style="font-size: 0.5em;">{{ __('EGP') }}</small></div>
                    <div class="stat-label">{{ __('Total Payouts Received') }}</div>
                    @if ($pendingPayouts > 0)
                        <small class="text-warning mt-2 d-block"><i class="fas fa-clock"></i>
                            {{ number_format($pendingPayouts, 0) }} {{ __('EGP pending') }}</small>
                    @endif
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value">{{ $totalStaff }}</div>
                    <div class="stat-label">{{ __('Team Members') }}</div>
                    <small class="text-success"><i class="fas fa-check-circle"></i> {{ $activeStaff }}
                        {{ __('active') }}</small>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-bolt me-2 text-warning"></i>{{ __('Quick Actions') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($quickActions as $action)
                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <a href="{{ route($action['route']) }}" class="quick-action-card">
                                <div class="action-icon bg-{{ $action['color'] }}">
                                    <i class="{{ $action['icon'] }}"></i>
                                </div>
                                <div class="action-title">{{ $action['title'] }}</div>
                                <p class="action-desc">{{ $action['description'] }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Weekly Orders Chart -->
            <div class="col-xl-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar me-2 text-primary"></i>{{ __('Weekly Orders') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="weeklyOrdersChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Revenue Chart -->
            <div class="col-xl-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line me-2 text-success"></i>{{ __('Weekly Revenue') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="weeklyRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-xl-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-shopping-bag me-2 text-success"></i>{{ __('Recent Orders') }}</h5>
                        <a href="{{ route('supplier.orders.index') }}"
                            class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                    <div class="card-body p-0">
                        @if ($recentOrders->count() > 0)
                            @foreach ($recentOrders as $orderItem)
                                <div class="order-item">
                                    <div class="order-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="details">
                                        <div class="name">{{ $orderItem->product->name ?? __('Product') }}</div>
                                        <div class="meta">
                                            <i class="far fa-clock me-1"></i>{{ $orderItem->created_at->diffForHumans() }}
                                            · {{ __('Qty') }}: {{ $orderItem->quantity }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="amount">
                                            {{ number_format($orderItem->price * $orderItem->quantity, 0) }}
                                            {{ __('EGP') }}</div>
                                        <span
                                            class="status-badge status-{{ $orderItem->status }}">{{ __(ucfirst($orderItem->status)) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-shopping-bag"></i>
                                <p>{{ __('No orders yet') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-xl-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-star me-2 text-warning"></i>{{ __('Top Products') }}</h5>
                        <a href="{{ route('supplier.products.index') }}"
                            class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                    <div class="card-body p-0">
                        @if ($topProducts->count() > 0)
                            @foreach ($topProducts as $product)
                                <div class="product-item">
                                    @if ($product->getFirstMediaUrl('product_images'))
                                        <img src="{{ $product->getFirstMediaUrl('product_images') }}" class="product-img"
                                            alt="{{ $product->name }}">
                                    @else
                                        <div class="product-img d-flex align-items-center justify-content-center">
                                            <i class="fas fa-box text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="product-info">
                                        <div class="product-name">{{ $product->name }}</div>
                                        <div class="product-meta">{{ number_format($product->price, 0) }}
                                            {{ __('EGP') }}</div>
                                    </div>
                                    <div class="sold-count">
                                        {{ $product->sold_count ?? 0 }} {{ __('sold') }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p>{{ __('No products yet') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Available Requests -->
            <div class="col-xl-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-clipboard-list me-2 text-info"></i>{{ __('New Requests for You') }}</h5>
                        <a href="{{ route('supplier.available-requests.index') }}"
                            class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                    <div class="card-body">
                        @if ($availableRequestsList->count() > 0)
                            @foreach ($availableRequestsList as $request)
                                <div class="request-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $request->subject ?? __('Purchase Request') }}</h6>
                                            <small class="text-muted">
                                                <i
                                                    class="fas fa-hospital me-1"></i>{{ $request->clinic->name ?? __('Clinic') }}
                                                · <i
                                                    class="fas fa-tag me-1"></i>{{ $request->categories->first()->name ?? __('Category') }}
                                            </small>
                                        </div>
                                        <a href="{{ route('supplier.available-requests.show', $request->id) }}"
                                            class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-clipboard-check"></i>
                                <p>{{ __('No new requests in your categories') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Offers -->
            <div class="col-xl-6">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5><i class="fas fa-handshake me-2 text-warning"></i>{{ __('Recent Offers') }}</h5>
                        <a href="{{ route('supplier.offers.index') }}"
                            class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                    <div class="card-body p-0">
                        @if ($recentOffers->count() > 0)
                            @foreach ($recentOffers as $offer)
                                <div class="order-item">
                                    <div class="order-icon"
                                        style="background: linear-gradient(135deg, #ffc107, #ff9800);">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="details">
                                        <div class="name">{{ $offer->request->subject ?? __('Offer') }}
                                            #{{ $offer->id }}</div>
                                        <div class="meta">
                                            <i
                                                class="fas fa-hospital me-1"></i>{{ $offer->request->clinic->name ?? __('Clinic') }}
                                            · {{ $offer->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="amount">{{ number_format($offer->total_price, 0) }}
                                            {{ __('EGP') }}</div>
                                        <span
                                            class="status-badge status-{{ $offer->status }}">{{ __(ucfirst($offer->status)) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-handshake-slash"></i>
                                <p>{{ __('No offers yet') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            // Dismiss Announcement
            $('#dismiss-announcement').on('click', function() {
                var id = $('#dashboard-announcement').data('id');
                $.ajax({
                    url: "{{ route('supplier.announcements.dismiss', ':id') }}".replace(':id',
                        id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    complete: function() {
                        $('#dashboard-announcement').remove();
                    }
                });
            });

            // Weekly Orders Chart
            const weeklyOrdersData = @json($weeklyOrders);
            new Chart(document.getElementById('weeklyOrdersChart'), {
                type: 'bar',
                data: {
                    labels: weeklyOrdersData.map(d => d.date),
                    datasets: [{
                        label: '{{ __('Orders') }}',
                        data: weeklyOrdersData.map(d => d.count),
                        backgroundColor: 'rgba(17, 153, 142, 0.8)',
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Weekly Revenue Chart
            const weeklyRevenueData = @json($weeklyRevenue);
            new Chart(document.getElementById('weeklyRevenueChart'), {
                type: 'line',
                data: {
                    labels: weeklyRevenueData.map(d => d.date),
                    datasets: [{
                        label: '{{ __('Revenue') }}',
                        data: weeklyRevenueData.map(d => d.amount),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#28a745',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' {{ __('EGP') }}';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
