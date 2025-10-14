<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\CityRepositoryInterface;
use App\Models\City;

class CityRepository implements CityRepositoryInterface
{
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $cities = City::query();

        return datatables()->of($cities)
            ->addColumn('governorate', fn($item) => $item->governorate->name)
            ->addColumn('action', fn($item) => $this->cityActions($item))
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveCity(new City(), $request, 'created');
    }

    public function show($id)
    {
        return City::findOrFail($id);
    }

    public function getCitiesByGovernorateId($request)
    {
        $governorateId = $request->id;
        return City::where('governorate_id', $governorateId)->get();
    }

    public function update($request, $id)
    {
        $city = City::findOrFail($id);
        return $this->saveCity($city, $request, 'updated');
    }


    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('City deleted successfully'),
        ]);
    }

    
    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $cities = City::onlyTrashed()->get();

        return datatables()->of($cities)
            ->addColumn('governorate', fn($item) => $item->governorate->name)
            ->addColumn('trash_action', fn($item) => $this->cityTrashActions($item))
            ->rawColumns(['trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $city = City::onlyTrashed()->findOrFail($id);
        $city->restore();

        return $this->jsonResponse('success', __('City restored successfully'));
    }

    public function forceDelete($id)
    {
        $city = City::onlyTrashed()->findOrFail($id);
        $city->forceDelete();

        return $this->jsonResponse('success', __('City deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveCity($city, $request, string $action)
    {
        try {
            $city->fill($request->validated())->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('City '.$action.' successfully'),
                ]);
            }

            return redirect()->route('admin.categories.index')->with('success', __('City '.$action.' successfully'));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function cityActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editCity({$item->id})" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></button>
            <button onclick="deleteCity({$item->id})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

     private function cityTrashActions($item): string
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
