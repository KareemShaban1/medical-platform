<?php

namespace App\Interfaces\Admin;

interface TicketRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function updateStatus($id, $status);
    public function reply($id, $request);
    public function trash();
    public function trashData();
    public function restore($id);
    public function forceDelete($id);
}
