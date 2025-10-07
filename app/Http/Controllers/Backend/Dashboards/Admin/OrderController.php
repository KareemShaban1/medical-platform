<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\OrderRepositoryInterface;

class OrderController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        return view('backend.dashboards.admin.pages.orders.index');
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
            : view('backend.dashboards.admin.pages.orders.show', compact('order'));
    }

    public function getOrderSuppliers($orderId)
    {
        $suppliers = $this->orderRepo->getOrderSuppliers($orderId);
        return response()->json($suppliers);
    }

    public function getOrderItems($orderId)
    {
        $items = $this->orderRepo->getOrderItems($orderId);
        return response()->json($items);
    }
}
