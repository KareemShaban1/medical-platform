<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\CreateRoleRequest;
use App\Http\Requests\Clinic\UpdateRoleRequest;
use App\Interfaces\Clinic\RoleRepositoryInterface;
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
        // apply permissions
        abort_if(!hasPermission('view roles'), 403, __('You are not authorized to view roles'));

        return $this->roleRepository->index();
    }

    /**
     * Get DataTable data for roles.
     */
    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view roles'), 403, __('You are not authorized to view roles'));

        return $this->roleRepository->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create role'), 403, __('You are not authorized to create roles'));

        return $this->roleRepository->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRoleRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create role'), 403, __('You are not authorized to create roles'));

        return $this->roleRepository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // apply permissions
        abort_if(!hasPermission('view role'), 403, __('You are not authorized to view role'));

        return $this->roleRepository->show($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // apply permissions
        abort_if(!hasPermission('edit role'), 403, __('You are not authorized to edit role'));

        return $this->roleRepository->edit($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, string $id)
    {
        // apply permissions
        abort_if(!hasPermission('update role'), 403, __('You are not authorized to update role'));

        return $this->roleRepository->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // apply permissions
        abort_if(!hasPermission('delete role'), 403, __('You are not authorized to delete role'));

        return $this->roleRepository->destroy($id);
    }

    /**
     * Display trashed roles.
     */
    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash roles'), 403, __('You are not authorized to view trash roles'));

        return $this->roleRepository->trash();
    }

    /**
     * Get DataTable data for trashed roles.
     */
    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash roles'), 403, __('You are not authorized to view trash roles'));

        return $this->roleRepository->trashData();
    }

    /**
     * Restore a trashed role.
     */
    public function restore(string $id)
    {
        // apply permissions
        abort_if(!hasPermission('restore role'), 403, __('You are not authorized to restore role'));

        return $this->roleRepository->restore($id);
    }

    /**
     * Permanently delete a trashed role.
     */
    public function forceDelete(string $id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete role'), 403, __('You are not authorized to force delete role'));

        return $this->roleRepository->forceDelete($id);
    }
}
