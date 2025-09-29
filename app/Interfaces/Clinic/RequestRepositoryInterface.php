<?php

namespace App\Interfaces\Clinic;

interface RequestRepositoryInterface
{
    public function index();
    public function data();
    public function show($id);
    public function store($request);
    public function update($request, $id);
    public function destroy($id);
    public function getCategories();
    public function acceptOffer($requestId, $offerId);
    public function cancelRequest($id);
}
