<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\CreateRoleRequest;
use App\Http\Requests\Supplier\UpdateRoleRequest;
use App\Interfaces\Supplier\RoleRepositoryInterface;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->roleRepository->index();
    }

    /**
     * Get DataTable data for roles.
     */
    public function data()
    {
        return $this->roleRepository->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->roleRepository->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRoleRequest $request)
    {
        return $this->roleRepository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->roleRepository->show($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return $this->roleRepository->edit($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, string $id)
    {
        return $this->roleRepository->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->roleRepository->destroy($id);
    }

    /**
     * Display trashed roles.
     */
    public function trash()
    {
        return $this->roleRepository->trash();
    }

    /**
     * Get DataTable data for trashed roles.
     */
    public function trashData()
    {
        return $this->roleRepository->trashData();
    }

    /**
     * Restore a trashed role.
     */
    public function restore(string $id)
    {
        return $this->roleRepository->restore($id);
    }

    /**
     * Permanently delete a trashed role.
     */
    public function forceDelete(string $id)
    {
        return $this->roleRepository->forceDelete($id);
    }
}
