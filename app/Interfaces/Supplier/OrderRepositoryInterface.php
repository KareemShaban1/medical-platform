<?php

namespace App\Interfaces\Supplier;

interface OrderRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function updateStatus($request, $id);
    public function createRefund($request, $id);
    public function getOrderItems($orderId);
    public function updatePaymentStatus($request, $id);
    public function updateRefundStatus($request, $refundId);
}
