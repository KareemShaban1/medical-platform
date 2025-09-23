<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\CourseRepositoryInterface;
use App\Models\Course;
use App\Traits\HandlesMediaUploads;

class CourseRepository implements CourseRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $courses = Course::query();

        return datatables()->of($courses)
            ->editColumn('main_image', function ($item) {
                return '<img src="' . $item->getFirstMediaUrl('main_image') . '" alt="" class="img-fluid" style="width: 50px; height: 50px;">';
            })
            ->editColumn('status', fn($item) => $this->courseStatus($item))
            ->addColumn('action', fn($item) => $this->courseActions($item))
            ->rawColumns(['status', 'action', 'main_image'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveCourse(new Course(), $request, 'created');
    }

    public function show($id)
    {
        return Course::findOrFail($id);
    }


    public function update($request, $id)
    {
        $course = Course::findOrFail($id);
        return $this->saveCourse($course, $request, 'updated');
    }

    public function updateStatus($request)
    {
        $course = Course::findOrFail($request->id);

        // fallback to "status" if field is not sent
        $field = $request->field ?? 'status';
        $value = (bool)$request->value;

        $course->{$field} = $value;
        $course->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Course status updated successfully'),
        ]);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Course deleted successfully'),
        ]);
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $courses = Course::onlyTrashed()->get();

        return datatables()->of($courses)
            ->editColumn('status', fn($item) => $this->courseStatus($item))
            ->addColumn('trash_action', fn($item) => $this->courseTrashActions($item))
            ->rawColumns(['status', 'trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->restore();

        return $this->jsonResponse('success', __('Course restored successfully'));
    }

    public function forceDelete($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->forceDelete();

        return $this->jsonResponse('success', __('Course deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveCourse($course, $request, string $action)
    {
        try {
            $course->fill($request->validated())->save();

            // Media
            if ($request->hasFile('main_image')) {
                $this->processMedia($course, $request, [
                    ['field' => 'main_image', 'collection' => 'main_image', 'multiple' => false],
                ], $action);
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Course '.$action.' successfully'),
                ]);
            }

            return redirect()->route('admin.courses.index')->with('success', __('Course '.$action.' successfully'));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function courseStatus($item): string
    {
        $checked = $item->status ? 'checked' : '';
        return <<<HTML
        <div class="form-check form-switch mt-2">
            <input type="checkbox" 
                   class="form-check-input toggle-boolean" 
                   data-id="{$item->id}" 
                   data-field="status" 
                   value="1" {$checked}>
        </div>
        HTML;
    }

    private function courseActions($item): string
    {
        $editUrl = route('admin.courses.edit', $item->id);
        $showUrl = route('admin.courses.show', $item->id);
        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
           <a href="{$editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
           <button onclick="deleteCourse({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function courseTrashActions($item): string
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
