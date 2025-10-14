<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\GovernorateRepositoryInterface;
use App\Models\Governorate;

class GovernorateRepository implements GovernorateRepositoryInterface
{
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $governorates = Governorate::query();

        return datatables()->of($governorates)
            ->addColumn('action', fn($item) => $this->governorateActions($item))
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveGovernorate(new Governorate(), $request, 'created');
    }

    public function show($id)
    {
        return Governorate::findOrFail($id);
    }

    public function update($request, $id)
    {
        $governorate = Governorate::findOrFail($id);
        return $this->saveGovernorate($governorate, $request, 'updated');
    }


    public function destroy($id)
    {
        $governorate = Governorate::findOrFail($id);
        $governorate->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Governorate deleted successfully'),
        ]);
    }

    
    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $governorates = Governorate::onlyTrashed()->get();

        return datatables()->of($governorates)
            ->addColumn('trash_action', fn($item) => $this->governorateTrashActions($item))
            ->rawColumns(['trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $governorate = Governorate::onlyTrashed()->findOrFail($id);
        $governorate->restore();

        return $this->jsonResponse('success', __('Governorate restored successfully'));
    }

    public function forceDelete($id)
    {
        $governorate = Governorate::onlyTrashed()->findOrFail($id);
        $governorate->forceDelete();

        return $this->jsonResponse('success', __('Governorate deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveGovernorate($governorate, $request, string $action)
    {
        try {
            $governorate->fill($request->validated())->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Governorate '.$action.' successfully'),
                ]);
            }

            return redirect()->route('admin.categories.index')->with('success', __('Governorate '.$action.' successfully'));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function governorateActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editGovernorate({$item->id})" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></button>
            <button onclick="deleteGovernorate({$item->id})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

     private function governorateTrashActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="restore({$item->id})" class="btn btn-sm btn-info" title="Restore"><i class="fa fa-undo"></i></button>
            <button onclick="forceDelete({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }
 

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
