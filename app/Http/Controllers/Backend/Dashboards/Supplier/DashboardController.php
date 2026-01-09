<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Product;
use App\Models\Offer;
use App\Models\OrderItem;
use App\Models\Request as PurchaseRequest;
use App\Models\Subscription;
use App\Models\SupplierUser;
use App\Models\SupplierPayoutRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $supplier = auth('supplier')->user()->supplier;
        $supplierId = $supplier->id;
        $user = auth('supplier')->user();

        // Announcement
        $announcement = Announcement::active()
            ->where(function ($q) use ($supplier) {
                $q->where('target_suppliers_all', true)
                    ->orWhereHas('suppliers', function ($q) use ($supplier) {
                        $q->where('suppliers.id', $supplier->id);
                    });
            })
            ->whereDoesntHave('dismissals', function ($q) use ($supplier) {
                $q->where('dismissable_type', \App\Models\Supplier::class)
                    ->where('dismissable_id', $supplier->id);
            })
            ->latest('created_at')
            ->first();

        // Date ranges
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Products Stats
        $totalProducts = Product::where('supplier_id', $supplierId)->count();
        $activeProducts = Product::where('supplier_id', $supplierId)->where('status', true)->count();
        $outOfStockProducts = Product::where('supplier_id', $supplierId)
            ->where('stock', '<=', 0)
            ->count();

        // Get supplier's product IDs for order queries
        $supplierProductIds = Product::where('supplier_id', $supplierId)->pluck('id')->toArray();

        // Orders Stats - using product IDs directly to avoid relationship issues
        $totalOrders = 0;
        $ordersThisMonth = 0;
        $pendingOrders = 0;
        $revenueThisMonth = 0;
        $revenueLastMonth = 0;

        if (!empty($supplierProductIds)) {
            $totalOrders = OrderItem::whereIn('product_id', $supplierProductIds)
                ->distinct('order_id')
                ->count('order_id');

            $ordersThisMonth = OrderItem::whereIn('product_id', $supplierProductIds)
                ->where('created_at', '>=', $thisMonth)
                ->distinct('order_id')
                ->count('order_id');

            $pendingOrders = OrderItem::whereIn('product_id', $supplierProductIds)
                ->where('status', 'pending')
                ->count();

            // Revenue Stats
            $revenueThisMonth = OrderItem::whereIn('product_id', $supplierProductIds)
                ->whereHas('order', function ($q) {
                    $q->where('payment_status', 'paid');
                })
                ->where('created_at', '>=', $thisMonth)
                ->sum(DB::raw('price * quantity')) ?? 0;

            $revenueLastMonth = OrderItem::whereIn('product_id', $supplierProductIds)
                ->whereHas('order', function ($q) {
                    $q->where('payment_status', 'paid');
                })
                ->whereBetween('created_at', [$lastMonth, $endOfLastMonth])
                ->sum(DB::raw('price * quantity')) ?? 0;
        }

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        // Offers Stats
        $totalOffers = Offer::where('supplier_id', $supplierId)->count();
        $pendingOffers = Offer::where('supplier_id', $supplierId)->where('status', 'pending')->count();
        $acceptedOffers = Offer::where('supplier_id', $supplierId)->where('status', 'accepted')->count();
        $offersThisMonth = Offer::where('supplier_id', $supplierId)
            ->where('created_at', '>=', $thisMonth)
            ->count();

        // Available Requests (that match supplier specialized categories)
        // Use the scopeForSupplierCategories from Request model
        $availableRequests = PurchaseRequest::where('status', 'open')
            ->forSupplierCategories($supplierId)
            ->count();

        // Payout Stats
        $pendingPayouts = SupplierPayoutRequest::where('supplier_id', $supplierId)
            ->where('status', 'pending')
            ->sum('amount') ?? 0;

        $totalPayouts = SupplierPayoutRequest::where('supplier_id', $supplierId)
            ->where('status', 'paid')
            ->sum('amount') ?? 0;

        // Staff Stats
        $totalStaff = SupplierUser::where('supplier_id', $supplierId)->count();
        $activeStaff = SupplierUser::where('supplier_id', $supplierId)->where('status', true)->count();

        // Subscription Info
        $subscription = Subscription::where('subscribable_type', \App\Models\Supplier::class)
            ->where('subscribable_id', $supplierId)
            ->where('status', 'active')
            ->with('plan')
            ->first();

        // Weekly Orders Chart Data
        $weeklyOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = 0;
            if (!empty($supplierProductIds)) {
                $count = OrderItem::whereIn('product_id', $supplierProductIds)
                    ->whereDate('created_at', $date)
                    ->distinct('order_id')
                    ->count('order_id');
            }
            $weeklyOrders[] = [
                'date' => $date->format('D'),
                'count' => $count
            ];
        }

        // Weekly Revenue Chart
        $weeklyRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = 0;
            if (!empty($supplierProductIds)) {
                $revenue = OrderItem::whereIn('product_id', $supplierProductIds)
                    ->whereHas('order', function ($q) {
                        $q->where('payment_status', 'paid');
                    })
                    ->whereDate('created_at', $date)
                    ->sum(DB::raw('price * quantity')) ?? 0;
            }
            $weeklyRevenue[] = [
                'date' => $date->format('D'),
                'amount' => $revenue
            ];
        }

        // Order Status Distribution
        $orderStatusData = [];
        if (!empty($supplierProductIds)) {
            $orderStatusData = OrderItem::whereIn('product_id', $supplierProductIds)
                ->select('status', DB::raw('count(DISTINCT order_id) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        }

        // Recent Orders - load order and product, skip user relationship
        $recentOrders = collect();
        if (!empty($supplierProductIds)) {
            $recentOrders = OrderItem::whereIn('product_id', $supplierProductIds)
                ->with(['order.clinic', 'product'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        // Top Products
        $topProducts = Product::where('supplier_id', $supplierId)
            ->withCount(['orderItems as sold_count' => function ($q) {
                $q->select(DB::raw('COALESCE(SUM(quantity), 0)'));
            }])
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        // Recent Offers
        $recentOffers = Offer::where('supplier_id', $supplierId)
            ->with(['request.clinic'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Available Requests List - use the scopeForSupplierCategories
        $availableRequestsList = PurchaseRequest::where('status', 'open')
            ->forSupplierCategories($supplierId)
            ->with(['clinic', 'categories'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Quick Actions
        $quickActions = $this->getSupplierQuickActions($user);

        return view('backend.dashboards.supplier.pages.dashboard', compact(
            'announcement',
            'supplier',
            'totalProducts',
            'activeProducts',
            'outOfStockProducts',
            'totalOrders',
            'ordersThisMonth',
            'pendingOrders',
            'revenueThisMonth',
            'revenueLastMonth',
            'revenueGrowth',
            'totalOffers',
            'pendingOffers',
            'acceptedOffers',
            'offersThisMonth',
            'availableRequests',
            'pendingPayouts',
            'totalPayouts',
            'totalStaff',
            'activeStaff',
            'subscription',
            'weeklyOrders',
            'weeklyRevenue',
            'orderStatusData',
            'recentOrders',
            'topProducts',
            'recentOffers',
            'availableRequestsList',
            'quickActions'
        ));
    }

    private function getSupplierQuickActions($user)
    {
        $actions = [];

        $actions[] = [
            'title' => __('Add Product'),
            'icon' => 'fas fa-plus-circle',
            'route' => 'supplier.products.index',
            'color' => 'primary',
            'description' => __('Add new product to catalog')
        ];

        $actions[] = [
            'title' => __('View Orders'),
            'icon' => 'fas fa-shopping-bag',
            'route' => 'supplier.orders.index',
            'color' => 'success',
            'description' => __('Manage customer orders')
        ];

        $actions[] = [
            'title' => __('Available Requests'),
            'icon' => 'fas fa-clipboard-list',
            'route' => 'supplier.available-requests.index',
            'color' => 'info',
            'description' => __('View and submit offers')
        ];

        $actions[] = [
            'title' => __('My Offers'),
            'icon' => 'fas fa-handshake',
            'route' => 'supplier.offers.index',
            'color' => 'warning',
            'description' => __('Track submitted offers')
        ];

        $actions[] = [
            'title' => __('Payouts'),
            'icon' => 'fas fa-money-bill-wave',
            'route' => 'supplier.payouts.index',
            'color' => 'danger',
            'description' => __('Manage earnings & payouts')
        ];

        $actions[] = [
            'title' => __('Team Members'),
            'icon' => 'fas fa-users',
            'route' => 'supplier.users.index',
            'color' => 'secondary',
            'description' => __('Manage your team')
        ];

        return $actions;
    }
}
