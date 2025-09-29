<?php

namespace App\Interfaces\Supplier;

interface OfferRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function store($request);
    public function update($request, $id);
    public function destroy($id);
    public function getAvailableRequests();
    public function availableRequestsData();
}
