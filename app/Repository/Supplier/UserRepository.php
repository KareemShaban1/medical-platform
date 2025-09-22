<?php

namespace App\Repository\Supplier;

use App\Interfaces\Supplier\UserRepositoryInterface;
use App\Models\SupplierUser;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        setPermissionsTeamId(auth('supplier')->user()->supplier_id);
        $users = SupplierUser::with(['roles'])->where('supplier_id', auth('supplier')->user()->supplier_id);

        return datatables()->of($users)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('email', fn($item) => $item->email)
            ->addColumn('phone', fn($item) => $item->phone)
            ->addColumn('roles', fn($item) => $this->userRoles($item))
            ->editColumn('status', fn($item) => $this->userStatus($item))
            ->addColumn('action', fn($item) => $this->userActions($item))
            ->rawColumns(['roles', 'status', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request;
            $data['supplier_id'] = auth('supplier')->user()->supplier_id;
            $data['password'] = Hash::make($data['password']);

            $user = SupplierUser::create($data);

            if (isset($data['role'])) {
                setPermissionsTeamId($data['supplier_id']);
                $user->assignRole($data['role']);
            }

            return $user;
        });
    }

    public function show($id)
    {
        setPermissionsTeamId(auth('supplier')->user()->supplier_id);
        return SupplierUser::with(['roles', 'supplier'])->where('supplier_id', auth('supplier')->user()->supplier_id)->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $user = SupplierUser::where('supplier_id', auth('supplier')->user()->supplier_id)->findOrFail($id);
            $data = $request;

            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            if (isset($data['role'])) {
                setPermissionsTeamId($user->supplier_id);
                $user->syncRoles([$data['role']]);
            }

            return $user;
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            if (auth('supplier')->user()->id == $id) {
                throw new \Exception(__('You cannot delete yourself'));
            };

            $user = SupplierUser::where('supplier_id', auth('supplier')->user()->supplier_id)->findOrFail($id);
            $user->delete();
            return $user;
        });
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        setPermissionsTeamId(auth('supplier')->user()->supplier_id);
        $users = SupplierUser::onlyTrashed()->with(['roles'])->where('supplier_id', auth('supplier')->user()->supplier_id);

        return datatables()->of($users)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('email', fn($item) => $item->email)
            ->addColumn('phone', fn($item) => $item->phone)
            ->addColumn('roles', fn($item) => $this->userRoles($item))
            ->addColumn('status', fn() => '<span class="badge bg-secondary">Trashed</span>')
            ->editColumn('deleted_at', fn($item) => $item->deleted_at->format('Y-m-d H:i:s'))
            ->addColumn('action', fn($item) => $this->trashActions($item))
            ->rawColumns(['roles', 'status', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        $user = SupplierUser::onlyTrashed()->where('supplier_id', auth('supplier')->user()->supplier_id)->findOrFail($id);
        return $user->restore();
    }

    public function forceDelete($id)
    {
        if (auth('supplier')->user()->id == $id) {
            throw new \Exception(__('You cannot delete yourself'));
        };

        $user = SupplierUser::onlyTrashed()->where('supplier_id', auth('supplier')->user()->supplier_id)->findOrFail($id);
        return $user->forceDelete();
    }

    public function toggleStatus($id)
    {
        $user = SupplierUser::where('supplier_id', auth('supplier')->user()->supplier_id)->findOrFail($id);
        $user->update(['status' => !$user->status]);
        return $user;
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function userStatus($item): string
    {
        $checked = $item->status ? 'checked' : '';
        $statusText = $item->status ? 'Active' : 'Inactive';
        $statusClass = $item->status ? 'success' : 'danger';

        return <<<HTML
            <div class="form-check form-switch mt-2">
                <input type="checkbox" class="form-check-input toggle-status"
                       data-id="{$item->id}" id="status-{$item->id}"
                       {$checked}>
                <label class="form-check-label" for="status-{$item->id}">
                    <span class="badge bg-{$statusClass}">{$statusText}</span>
                </label>
            </div>
        HTML;
    }

    private function userRoles($item): string
    {
        if ($item->roles && $item->roles->count() > 0) {
            $role = $item->roles->first();
            return '<span class="badge bg-info">' . $role->name . '</span>';
        }
        return '<span class="badge bg-secondary">No Role</span>';
    }

    private function userActions($item): string
    {
        $showUrl = route('supplier.users.show', $item->id);
        return <<<HTML
        <div class="d-flex gap-2">
            <a href="{$showUrl}" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>
            <button onclick="editUser({$item->id})" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>
            <button onclick="deleteUser({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function trashActions($item): string
    {
        return <<<HTML
        <button class="btn btn-sm btn-success" onclick="restoreUser({$item->id})">
            <i class="mdi mdi-restore"></i> Restore
        </button>
        <button class="btn btn-sm btn-danger" onclick="forceDeleteUser({$item->id})">
            <i class="mdi mdi-delete-forever"></i> Delete
        </button>
        HTML;
    }
}
