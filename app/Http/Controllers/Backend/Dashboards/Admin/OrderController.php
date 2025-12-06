<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view orders'), 403, __('You are not authorized to view orders'));
        return view('backend.dashboards.admin.pages.orders.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view orders'), 403, __('You are not authorized to view orders'));
        return $this->orderRepo->data();
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view order'), 403, __('You are not authorized to view order'));
        $order = $this->orderRepo->show($id);
        return request()->ajax()
            ? response()->json($order)
            : view('backend.dashboards.admin.pages.orders.show', compact('order'));
    }

    public function getOrderSuppliers($orderId)
    {
        // apply permissions
        abort_if(!hasPermission('view order'), 403, __('You are not authorized to view order'));
        $suppliers = $this->orderRepo->getOrderSuppliers($orderId);
        return response()->json($suppliers);
    }

    public function getOrderItems($orderId)
    {
        // apply permissions
        abort_if(!hasPermission('view order'), 403, __('You are not authorized to view order'));
        $items = $this->orderRepo->getOrderItems($orderId);
        return response()->json($items);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update order payment status'), 403, __('You are not authorized to update order payment status'));
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        try {
            $this->orderRepo->updatePaymentStatus($id, $request->only('payment_status'));
            return response()->json([
                'status' => 'success',
                'message' => __('Payment status updated successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function analytics(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view orders'), 403, __('You are not authorized to view orders'));
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $supplierId = $request->get('supplier_id');

        $ordersQuery = Order::with(['clinic', 'clinicUser', 'items.supplier'])
            ->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59',
            ]);

        if ($supplierId) {
            $ordersQuery->whereHas('items', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            });
        }

        $orders = $ordersQuery->get();

        $items = $orders->flatMap(function ($order) {
            return $order->items;
        });

        $totalOrders = $orders->count();
        $totalItems = (int) $items->sum('quantity');
        $totalRevenue = (float) $orders->sum('total');

        $paidOrders = $orders->where('payment_status', 'paid');
        $paidRevenue = (float) $paidOrders->sum('total');

        $pendingRevenue = $totalRevenue - $paidRevenue;

        $averageOrderValue = $totalOrders > 0
            ? round($totalRevenue / $totalOrders, 2)
            : 0;

        $statusCounts = $orders->groupBy('status')->map->count();
        $paymentStatusCounts = $orders->groupBy('payment_status')->map->count();

        $ordersByDate = $orders->groupBy(function ($order) {
            return optional($order->created_at)->format('Y-m-d');
        })->filter()->map->count();

        $revenueByDate = $orders->groupBy(function ($order) {
            return optional($order->created_at)->format('Y-m-d');
        })->filter()->map(function ($group) {
            return (float) $group->sum('total');
        });

        $itemsBySupplier = $items->groupBy('supplier_id');

        $suppliersSummary = $itemsBySupplier->map(function ($group, $id) {
            $supplier = $group->first()->supplier;
            $ordersCount = $group->pluck('order_id')->unique()->count();
            $itemsCount = (int) $group->sum('quantity');
            $revenue = (float) $group->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            return [
                'supplier_id' => $id,
                'supplier_name' => $supplier?->name ?? __('Unknown Supplier'),
                'orders_count' => $ordersCount,
                'items_count' => $itemsCount,
                'revenue' => $revenue,
            ];
        })->sortByDesc('revenue')->values();

        $clinicsSummary = $orders->groupBy('clinic_id')->map(function ($group) {
            $clinic = $group->first()->clinic;
            $items = $group->flatMap(function ($order) {
                return $order->items;
            });

            return [
                'clinic_name' => $clinic?->name ?? __('Unknown Clinic'),
                'orders_count' => $group->count(),
                'items_count' => (int) $items->sum('quantity'),
                'revenue' => (float) $group->sum('total'),
            ];
        })->sortByDesc('revenue')->values();

        $recentOrders = $orders->sortByDesc('created_at')->take(5);

        $selectedSupplierAnalytics = null;
        if ($supplierId && $itemsBySupplier->has($supplierId)) {
            $group = $itemsBySupplier->get($supplierId);
            $ordersCount = $group->pluck('order_id')->unique()->count();
            $itemsCount = (int) $group->sum('quantity');
            $revenue = (float) $group->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            $selectedSupplierAnalytics = [
                'supplier_id' => $supplierId,
                'orders_count' => $ordersCount,
                'items_count' => $itemsCount,
                'revenue' => $revenue,
            ];
        }

        $benefitedSuppliersIds = $itemsBySupplier->keys()->filter()->all();
        $benefitedSuppliers = !empty($benefitedSuppliersIds)
            ? Supplier::whereIn('id', $benefitedSuppliersIds)->orderBy('name')->get()
            : collect([]);

        $analytics = [
            'total_orders' => $totalOrders,
            'total_items' => $totalItems,
            'total_revenue' => $totalRevenue,
            'paid_revenue' => $paidRevenue,
            'pending_revenue' => $pendingRevenue,
            'average_order_value' => $averageOrderValue,
            'status_counts' => $statusCounts,
            'payment_status_counts' => $paymentStatusCounts,
            'orders_by_date' => $ordersByDate,
            'revenue_by_date' => $revenueByDate,
            'suppliers_summary' => $suppliersSummary,
            'clinics_summary' => $clinicsSummary,
            'recent_orders' => $recentOrders,
            'selected_supplier' => $supplierId ? $benefitedSuppliers->firstWhere('id', $supplierId) : null,
            'selected_supplier_analytics' => $selectedSupplierAnalytics,
        ];

        return view('backend.dashboards.admin.pages.orders.analytics', compact(
            'analytics',
            'benefitedSuppliers',
            'startDate',
            'endDate',
            'supplierId'
        ));
    }
}