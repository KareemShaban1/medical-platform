<?php

namespace App\Repository\Supplier;

use App\Interfaces\Supplier\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        $orders = Order::whereHas('items', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })->with(['clinic', 'clinicUser', 'items' => function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }]);

        return datatables()->of($orders)
            ->addColumn('clinic_name', fn($item) => $item->clinic->name ?? 'N/A')
            ->addColumn('clinic_user', fn($item) => $item->clinicUser->name ?? 'N/A')
            ->addColumn('items_count', fn($item) => $item->items->count())
            ->addColumn('supplier_total', fn($item) => $this->getSupplierTotal($item))
            ->addColumn('items_status', fn($item) => $this->itemsStatus($item))
            ->editColumn('status', fn($item) => $this->orderStatus($item))
            ->editColumn('payment_status', fn($item) => $this->paymentStatus($item))
            ->editColumn('created_at', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('action', fn($item) => $this->orderActions($item))
            ->rawColumns(['status', 'payment_status', 'items_status', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        return Order::whereHas('items', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })->with([
            'clinic',
            'clinicUser',
            'items' => function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId)->with('product');
            },
            'refunds' => function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }
        ])->findOrFail($id);
    }

    public function updateStatus($request, $id)
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        return DB::transaction(function () use ($request, $id, $supplierId) {
            $order = Order::whereHas('items', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })->findOrFail($id);

            // Update order supplier status
            $orderSupplier = $order->suppliers()->where('supplier_id', $supplierId)->first();
            if ($orderSupplier) {
                $orderSupplier->update(['status' => $request['status']]);
            }

            // Update individual items status if provided
            if (isset($request['item_statuses'])) {
                $itemIds = array_keys($request['item_statuses']);

                $items = OrderItem::where('order_id', $order->id)
                    ->where('supplier_id', $supplierId)
                    ->whereIn('id', $itemIds)
                    ->get();

                foreach ($items as $item) {
                    $newStatus = $request['item_statuses'][$item->id] ?? null;
                    if ($newStatus && $item->status !== $newStatus) {
                        $item->status = $newStatus;
                        $item->save();
                    }
                }
            }

            return $order;
        });
    }

    public function createRefund($request, $id)
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        return DB::transaction(function () use ($request, $id, $supplierId) {
            $order = Order::whereHas('items', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })->findOrFail($id);

            $refundData = [
                'order_id' => $order->id,
                'supplier_id' => $supplierId,
                'amount' => $request['amount'],
                'refund_type' => $request['refund_type'],
                'reason' => $request['reason'] ?? null,
                'status' => 'pending'
            ];

            if (isset($request['order_item_id'])) {
                $refundData['order_item_id'] = $request['order_item_id'];
            }

            return Refund::create($refundData);
        });
    }

    public function getOrderItems($orderId)
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        return OrderItem::where('order_id', $orderId)
            ->where('supplier_id', $supplierId)
            ->with('product')
            ->get();
    }

    public function updatePaymentStatus($request, $id)
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        return DB::transaction(function () use ($request, $id, $supplierId) {
            $order = Order::whereHas('items', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })->findOrFail($id);

            // Only allow payment status update for COD orders
            if ($order->payment_method != 0) {
                throw new \Exception('Payment status can only be updated for COD orders');
            }

            $order->update(['payment_status' => $request['payment_status']]);
            return $order;
        });
    }

    public function updateRefundStatus($request, $refundId)
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        return DB::transaction(function () use ($request, $refundId, $supplierId) {
            $refund = Refund::where('supplier_id', $supplierId)->findOrFail($refundId);

            $updateData = ['status' => $request['status']];
            if (isset($request['notes'])) {
                $updateData['notes'] = $request['notes'];
            }

            $refund->update($updateData);
            return $refund;
        });
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function getSupplierTotal($order): string
    {
        $total = $order->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
        return '$' . number_format($total, 2);
    }

    private function orderStatus($order): string
    {
        $status = $order->status ?? 'pending';

        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'delivering' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
        ];

        $class = $badges[$status] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($status) . '</span>';
    }

    private function paymentStatus($item): string
    {
        $badges = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger'
        ];

        $class = $badges[$item->payment_status] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($item->payment_status) . '</span>';
    }

    private function itemsStatus($order): string
    {
        $items = $order->items;
        if ($items->isEmpty()) {
            return '<span class="badge bg-secondary">-</span>';
        }

        $groups = $items->groupBy('status');
        $badgesMap = [
            'pending' => 'warning',
            'processing' => 'info',
            'delivering' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];

        $parts = [];
        foreach ($groups as $status => $group) {
            $class = $badgesMap[$status] ?? 'secondary';
            $count = $group->count();
            $label = ucfirst($status);
            $parts[] = '<span class="badge bg-' . $class . ' me-1">' . $label . ' (' . $count . ')</span>';
        }

        return implode(' ', $parts);
    }

    private function orderActions($item): string
    {
        $html = '<div class="d-flex gap-2">';

        if (hasPermission('view orders')) {
            $showUrl = route('supplier.orders.show', $item->id);
            $html .= '<a href="' . $showUrl . '" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>';
        }

        if (hasPermission('update order status')) {
            $html .= '<button onclick="updateOrderStatus(' . $item->id . ')" class="btn btn-sm btn-info" title="Update Status"><i class="fa fa-edit"></i></button>';
        }

        if (hasPermission('create refund')) {
            $html .= '<button onclick="createRefund(' . $item->id . ')" class="btn btn-sm btn-warning" title="Create Refund"><i class="fa fa-undo"></i></button>';
        }

        // Add payment status button for COD orders
        if (hasPermission('update order payment status') && $item->payment_method == 0) {
            $html .= '<button onclick="updatePaymentStatus(' . $item->id . ')" class="btn btn-sm btn-primary" title="Update Payment"><i class="fa fa-credit-card"></i></button>';
        }

        $html .= '</div>';
        return $html;
    }
}