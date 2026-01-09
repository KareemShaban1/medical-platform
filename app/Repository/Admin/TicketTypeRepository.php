<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\TicketTypeRepositoryInterface;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketTypeRepository implements TicketTypeRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $ticketTypes = TicketType::with('allowedUserTypes')->latest();

        return datatables()->of($ticketTypes)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('slug', fn($item) => '<code>' . $item->slug . '</code>')
            ->addColumn('badge', fn($item) => $item->badge)
            ->addColumn('user_types', fn($item) => $this->getUserTypeBadges($item))
            ->addColumn('status', fn($item) => $this->getStatusBadge($item))
            ->addColumn('action', fn($item) => $this->ticketTypeActions($item))
            ->rawColumns(['slug', 'badge', 'user_types', 'status', 'action'])
            ->make(true);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $ticketType = TicketType::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'badge_color' => $data['badge_color'] ?? 'secondary',
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (!empty($data['user_types'])) {
                $ticketType->syncUserTypes($data['user_types']);
            }

            return $ticketType;
        });
    }

    public function show($id)
    {
        return TicketType::with('allowedUserTypes')->findOrFail($id);
    }

    public function update(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $ticketType = TicketType::findOrFail($id);

            $ticketType->update([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'badge_color' => $data['badge_color'] ?? 'secondary',
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['user_types'])) {
                $ticketType->syncUserTypes($data['user_types']);
            }

            return $ticketType;
        });
    }

    public function destroy($id)
    {
        $ticketType = TicketType::findOrFail($id);
        $ticketType->delete();
        return $ticketType;
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $ticketTypes = TicketType::onlyTrashed()->with('allowedUserTypes')->latest();

        return datatables()->of($ticketTypes)
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('slug', fn($item) => '<code>' . $item->slug . '</code>')
            ->addColumn('badge', fn($item) => $item->badge)
            ->addColumn('deleted_at', fn($item) => $item->deleted_at->format('Y-m-d H:i:s'))
            ->addColumn('action', fn($item) => $this->trashActions($item))
            ->rawColumns(['slug', 'badge', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        $ticketType = TicketType::onlyTrashed()->findOrFail($id);
        $ticketType->restore();
        return $ticketType;
    }

    public function forceDelete($id)
    {
        return DB::transaction(function () use ($id) {
            $ticketType = TicketType::onlyTrashed()->findOrFail($id);
            $ticketType->allowedUserTypes()->delete();
            return $ticketType->forceDelete();
        });
    }

    // ---------- Private Helpers ----------

    private function getUserTypeBadges($item): string
    {
        $userTypes = TicketType::availableUserTypes();
        $badges = [];

        foreach ($item->user_types_array as $type) {
            $label = $userTypes[$type] ?? $type;
            $badges[] = '<span class="badge bg-secondary me-1">' . e($label) . '</span>';
        }

        return count($badges) > 0 ? implode('', $badges) : '<span class="text-muted">None</span>';
    }

    private function getStatusBadge($item): string
    {
        return $item->is_active
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    private function ticketTypeActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editTicketType({$item->id})" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>
            <button onclick="deleteTicketType({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function trashActions($item): string
    {
        return <<<HTML
        <button class="btn btn-sm btn-success" onclick="restoreTicketType({$item->id})">
            <i class="mdi mdi-restore"></i> Restore
        </button>
        <button class="btn btn-sm btn-danger" onclick="forceDeleteTicketType({$item->id})">
            <i class="mdi mdi-delete-forever"></i> Delete
        </button>
        HTML;
    }
}
