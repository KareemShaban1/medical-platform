<?php

namespace App\Interfaces\Clinic;

interface InvoiceRepositoryInterface
{
    public function data(array $filters = []);
    public function show(int $id);
    public function updateHeader(int $id, array $data);
    public function addItem(int $invoiceId, array $data);
    public function updateItem(int $invoiceId, int $itemId, array $data);
    public function deleteItem(int $invoiceId, int $itemId);
    public function markPaid(int $id, ?string $method = null);
}

