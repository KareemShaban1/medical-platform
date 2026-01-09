<?php

namespace App\Interfaces\Admin;

interface TicketTypeRepositoryInterface
{
    public function index();

    public function data();

    public function store(array $data);

    public function show($id);

    public function update(array $data, $id);

    public function destroy($id);

    public function trash();

    public function trashData();

    public function restore($id);

    public function forceDelete($id);
}
