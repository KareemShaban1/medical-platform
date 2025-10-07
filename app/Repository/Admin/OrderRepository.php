<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSupplier;

class OrderRepository implements OrderRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $orders = Order::with(['clinic', 'clinicUser', 'suppliers.supplier', 'items']);

        return datatables()->of($orders)
            ->addColumn('clinic_name', fn($item) => $item->clinic->name ?? 'N/A')
            ->addColumn('clinic_user', fn($item) => $item->clinicUser->name ?? 'N/A')
            ->addColumn('suppliers_count', fn($item) => $item->suppliers->count())
            ->addColumn('items_count', fn($item) => $item->items->count())
            ->editColumn('total', fn($item) => '$' . number_format($item->total, 2))
            ->editColumn('status', fn($item) => $this->orderStatus($item))
            ->editColumn('payment_status', fn($item) => $this->paymentStatus($item))
            ->editColumn('created_at', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('action', fn($item) => $this->orderActions($item))
            ->rawColumns(['status', 'payment_status', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        return Order::with([
            'clinic',
            'clinicUser',
            'items.product',
            'items.supplier',
            'suppliers.supplier',
            'refunds'
        ])->findOrFail($id);
    }

    public function getOrderSuppliers($orderId)
    {
        return OrderSupplier::where('order_id', $orderId)
            ->with('supplier')
            ->get();
    }

    public function getOrderItems($orderId)
    {
        return OrderItem::where('order_id', $orderId)
            ->with(['product', 'supplier'])
            ->get();
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function orderStatus($item): string
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'delivering' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary'
        ];

        $class = $badges[$item->status] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($item->status) . '</span>';
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

    private function orderActions($item): string
    {
        $showUrl = route('admin.orders.show', $item->id);
        return <<<HTML
        <div class="d-flex gap-2">
            <a href="{$showUrl}" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>
        </div>
        HTML;
    }
}
