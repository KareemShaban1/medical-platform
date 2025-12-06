<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\UserRepositoryInterface;
use App\Models\ClinicUser;
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
        setPermissionsTeamId(auth('clinic')->user()->clinic_id);
        $users = ClinicUser::with(['roles'])->where('clinic_id', auth('clinic')->user()->clinic_id);

        return datatables()->of($users)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('position_title', fn($item) => $item->position_title)
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
            $data['clinic_id'] = auth('clinic')->user()->clinic_id;
            $data['password'] = Hash::make($data['password']);

            $user = ClinicUser::create($data);

            if (isset($data['role'])) {
                setPermissionsTeamId($data['clinic_id']);
                // Find role by name and team_id to ensure correct role assignment
                $role = Role::where('name', $data['role'])
                    ->where('guard_name', 'clinic')
                    ->where('team_id', $data['clinic_id'])
                    ->first();
                
                if ($role) {
                    $user->assignRole($role);
                } else {
                    // Fallback: try assigning by name (Spatie will use team context)
                    $user->assignRole($data['role']);
                }
            }

            return $user;
        });
    }

    public function show($id)
    {
        setPermissionsTeamId(auth('clinic')->user()->clinic_id);
        return ClinicUser::with(['roles', 'clinic'])->where('clinic_id', auth('clinic')->user()->clinic_id)->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $user = ClinicUser::where('clinic_id', auth('clinic')->user()->clinic_id)->findOrFail($id);
            $data = $request;

            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            if (isset($data['role'])) {
                setPermissionsTeamId($user->clinic_id);
                // Find role by name and team_id to ensure correct role assignment
                $role = Role::where('name', $data['role'])
                    ->where('guard_name', 'clinic')
                    ->where('team_id', $user->clinic_id)
                    ->first();
                
                if ($role) {
                    $user->syncRoles([$role]);
                } else {
                    // Fallback: try assigning by name (Spatie will use team context)
                    $user->syncRoles([$data['role']]);
                }
            }

            return $user;
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            if (auth('clinic')->user()->id == $id) {
                throw new \Exception(__('You cannot delete yourself'));
            };

            $user = ClinicUser::where('clinic_id', auth('clinic')->user()->clinic_id)->findOrFail($id);
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
        setPermissionsTeamId(auth('clinic')->user()->clinic_id);
        $users = ClinicUser::onlyTrashed()->with(['roles'])->where('clinic_id', auth('clinic')->user()->clinic_id);

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
        $user = ClinicUser::onlyTrashed()->where('clinic_id', auth('clinic')->user()->clinic_id)->findOrFail($id);
        return $user->restore();
    }

    public function forceDelete($id)
    {
        if (auth('clinic')->user()->id == $id) {
            throw new \Exception(__('You cannot delete yourself'));
        };

        $user = ClinicUser::onlyTrashed()->where('clinic_id', auth('clinic')->user()->clinic_id)->findOrFail($id);
        return $user->forceDelete();
    }

    public function toggleStatus($id)
    {
        $user = ClinicUser::where('clinic_id', auth('clinic')->user()->clinic_id)->findOrFail($id);
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
        $html = '<div class="d-flex gap-2">';
        
        if (hasPermission('view users')) {
            $showUrl = route('clinic.users.show', $item->id);
            $html .= '<a href="' . $showUrl . '" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>';
        }
        
        if (hasPermission('update user')) {
            $html .= '<button onclick="editUser(' . $item->id . ')" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>';
        }
        
        if (hasPermission('delete user')) {
            $html .= '<button onclick="deleteUser(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    private function trashActions($item): string
    {
        $html = '<div class="d-flex gap-2">';
        
        if (hasPermission('restore user')) {
            $html .= '<button class="btn btn-sm btn-success" onclick="restoreUser(' . $item->id . ')">';
            $html .= '<i class="mdi mdi-restore"></i> Restore';
            $html .= '</button>';
        }
        
        if (hasPermission('force delete user')) {
            $html .= '<button class="btn btn-sm btn-danger" onclick="forceDeleteUser(' . $item->id . ')">';
            $html .= '<i class="mdi mdi-delete-forever"></i> Delete';
            $html .= '</button>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}