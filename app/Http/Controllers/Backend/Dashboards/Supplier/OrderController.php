<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Interfaces\Supplier\OrderRepositoryInterface;
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

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
