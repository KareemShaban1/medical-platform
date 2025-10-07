<?php

namespace App\Interfaces\Admin;

interface OrderRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function getOrderSuppliers($orderId);
    public function getOrderItems($orderId);
}
