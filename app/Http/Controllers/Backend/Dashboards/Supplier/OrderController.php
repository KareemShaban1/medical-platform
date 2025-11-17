<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Interfaces\Supplier\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
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
        return view('backend.dashboards.supplier.pages.orders.index');
    }

    public function data()
    {
        return $this->orderRepo->data();
    }

    public function show($id)
    {
        $order = $this->orderRepo->show($id);
        return request()->ajax()
            ? response()->json($order)
            : view('backend.dashboards.supplier.pages.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivering,completed,cancelled',
            'item_statuses' => 'sometimes|array',
            'item_statuses.*' => 'in:pending,processing,delivering,completed,cancelled'
        ]);

        $this->orderRepo->updateStatus($request->all(), $id);
        return $this->jsonResponse('success', __('Order status updated successfully'));
    }

    public function createRefund(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'refund_type' => 'required|in:full,partial',
            'reason' => 'nullable|string|max:500',
            'order_item_id' => 'nullable|exists:order_items,id'
        ]);

        $this->orderRepo->createRefund($request->all(), $id);
        return $this->jsonResponse('success', __('Refund request created successfully'));
    }

    public function getOrderItems($orderId)
    {
        $items = $this->orderRepo->getOrderItems($orderId);
        return response()->json($items);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed'
        ]);

        $this->orderRepo->updatePaymentStatus($request->all(), $id);
        return $this->jsonResponse('success', __('Payment status updated successfully'));
    }

    public function updateRefundStatus(Request $request, $refundId)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,processed',
            'notes' => 'nullable|string|max:500'
        ]);

        $this->orderRepo->updateRefundStatus($request->all(), $refundId);
        return $this->jsonResponse('success', __('Refund status updated successfully'));
    }

    public function analytics(Request $request)
    {
        $supplierUser = auth('supplier')->user();
        $supplierId = $supplierUser->supplier_id ?? null;

        abort_if(!$supplierId, 403);

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $orders = Order::whereHas('items', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59',
            ])
            ->with([
                'clinic',
                'clinicUser',
                'items' => function ($query) use ($supplierId) {
                    $query->where('supplier_id', $supplierId);
                },
                'suppliers' => function ($query) use ($supplierId) {
                    $query->where('supplier_id', $supplierId);
                },
            ])
            ->get();

        $items = $orders->flatMap(function ($order) {
            return $order->items;
        });

        $totalOrders = $orders->count();
        $totalItems = (int) $items->sum('quantity');
        $totalRevenue = (float) $items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $paidOrders = $orders->where('payment_status', 'paid');
        $paidRevenue = (float) $paidOrders->flatMap(function ($order) {
            return $order->items;
        })->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $pendingRevenue = $totalRevenue - $paidRevenue;

        $averageOrderValue = $totalOrders > 0
            ? round($totalRevenue / $totalOrders, 2)
            : 0;

        $orderStatusCounts = $orders->groupBy('status')->map->count();

        $supplierStatusCounts = $orders->map(function ($order) {
            $supplierPivot = $order->suppliers->first();
            return $supplierPivot ? $supplierPivot->status : 'pending';
        })->groupBy(function ($status) {
            return $status;
        })->map->count();

        $paymentStatusCounts = $orders->groupBy('payment_status')->map->count();

        $revenueByDate = $items->groupBy(function ($item) {
            return optional($item->order->created_at)->format('Y-m-d');
        })->filter()->map(function ($group) {
            return (float) $group->sum(function ($item) {
                return $item->quantity * $item->price;
            });
        });

        $ordersByDate = $orders->groupBy(function ($order) {
            return optional($order->created_at)->format('Y-m-d');
        })->filter()->map->count();

        $topClinics = $orders->groupBy('clinic_id')->map(function ($group) {
            $clinic = $group->first()->clinic;
            $items = $group->flatMap(function ($order) {
                return $order->items;
            });

            return [
                'clinic_name' => $clinic?->name ?? __('Unknown Clinic'),
                'orders_count' => $group->count(),
                'items_count' => (int) $items->sum('quantity'),
                'revenue' => (float) $items->sum(function ($item) {
                    return $item->quantity * $item->price;
                }),
            ];
        })->sortByDesc('revenue')->values();

        $recentOrders = $orders->sortByDesc('created_at')->take(5);

        $analytics = [
            'total_orders' => $totalOrders,
            'total_items' => $totalItems,
            'total_revenue' => $totalRevenue,
            'paid_revenue' => $paidRevenue,
            'pending_revenue' => $pendingRevenue,
            'average_order_value' => $averageOrderValue,
            'order_status_counts' => $orderStatusCounts,
            'supplier_status_counts' => $supplierStatusCounts,
            'payment_status_counts' => $paymentStatusCounts,
            'revenue_by_date' => $revenueByDate,
            'orders_by_date' => $ordersByDate,
            'top_clinics' => $topClinics,
            'recent_orders' => $recentOrders,
        ];

        return view('backend.dashboards.supplier.pages.orders.analytics', compact(
            'analytics',
            'orders',
            'startDate',
            'endDate'
        ));
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
