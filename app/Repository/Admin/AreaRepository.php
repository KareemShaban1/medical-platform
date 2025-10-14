<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\AreaRepositoryInterface;
use App\Models\Area;

class AreaRepository implements AreaRepositoryInterface
{
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $areas = Area::query();

        return datatables()->of($areas)
            ->addColumn('governorate', fn($item) => $item->city->governorate->name)
            ->addColumn('city', fn($item) => $item->city->name)
            ->addColumn('action', fn($item) => $this->areaActions($item))
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveArea(new Area(), $request, 'created');
    }

    public function show($id)
    {
        return Area::findOrFail($id);
    }

    public function update($request, $id)
    {
        $area = Area::findOrFail($id);
        return $this->saveArea($area, $request, 'updated');
    }


    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Area deleted successfully'),
        ]);
    }

    
    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $areas = Area::onlyTrashed()->get();

        return datatables()->of($areas)
            ->addColumn('governorate', fn($item) => $item->city->governorate->name)
            ->addColumn('city', fn($item) => $item->city->name)
            ->addColumn('trash_action', fn($item) => $this->areaTrashActions($item))
            ->rawColumns(['trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $area = Area::onlyTrashed()->findOrFail($id);
        $area->restore();

        return $this->jsonResponse('success', __('Area restored successfully'));
    }

    public function forceDelete($id)
    {
        $area = Area::onlyTrashed()->findOrFail($id);
        $area->forceDelete();

        return $this->jsonResponse('success', __('Area deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveArea($area, $request, string $action)
    {
        try {
            $area->fill($request->validated())->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Area '.$action.' successfully'),
                ]);
            }

            return redirect()->route('admin.categories.index')->with('success', __('Area '.$action.' successfully'));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function areaActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editArea({$item->id})" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></button>
            <button onclick="deleteArea({$item->id})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

     private function areaTrashActions($item): string
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
