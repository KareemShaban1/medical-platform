<?php

namespace App\Interfaces\User;

interface TicketRepositoryInterface
{
    public function index();
    public function data();
    public function store($request);
    public function show($id);
    public function reply($id, $request);
}
