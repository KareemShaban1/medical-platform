<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\SpecialityRepositoryInterface;
use App\Models\Speciality;

class SpecialityRepository implements SpecialityRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $items = Speciality::query();

        return datatables()->of($items)
            ->addColumn('action', fn($item) => $this->actions($item))
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->save(new Speciality(), $request, __('Speciality created successfully'));
    }

    public function show($id)
    {
        return Speciality::findOrFail($id);
    }

    public function update($request, $id)
    {
        $item = Speciality::findOrFail($id);
        return $this->save($item, $request, __('Speciality updated successfully'));
    }

    public function destroy($id)
    {
        $item = Speciality::findOrFail($id);
        try {
            $item->delete();
            return response()->json([
                'status' => 'success',
                'message' => __('Speciality deleted successfully'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Cannot delete speciality in use'),
            ], 422);
        }
    }

    private function save(Speciality $item, $request, string $message)
    {
        $item->fill($request->validated())->save();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.specialities.index')->with('success', $message);
    }

    private function actions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editItem({$item->id})" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></button>
            <button onclick="deleteItem({$item->id})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }
}
