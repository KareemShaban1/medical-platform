<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Requests\Admin\Course\StoreCourseRequest;
use App\Http\Requests\Admin\Course\UpdateCourseRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\Admin\CourseRepositoryInterface;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseRepo;

    public function __construct(CourseRepositoryInterface $courseRepo)
    {
        $this->courseRepo = $courseRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view courses'), 403, __('You are not authorized to view courses'));
        return view('backend.dashboards.admin.pages.courses.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view courses'), 403, __('You are not authorized to view courses'));
        return $this->courseRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create course'), 403, __('You are not authorized to create course'));
        return view('backend.dashboards.admin.pages.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create course'), 403, __('You are not authorized to create course'));
        return $this->courseRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view courses'), 403, __('You are not authorized to view course'));
        $course = $this->courseRepo->show($id);

        return request()->ajax()
            ? response()->json($course)
            : view('backend.dashboards.admin.pages.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update course'), 403, __('You are not authorized to update course'));
        $course = $this->courseRepo->show($id);

        return view('backend.dashboards.admin.pages.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update course'), 403, __('You are not authorized to update course'));
        return $this->courseRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('toggle course status'), 403, __('You are not authorized to update course status'));
        return $this->courseRepo->updateStatus($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete course'), 403, __('You are not authorized to delete course'));
        return $this->courseRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash courses'), 403, __('You are not authorized to view trash courses'));
        return view('backend.dashboards.admin.pages.courses.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash courses'), 403, __('You are not authorized to view trash courses'));
        return $this->courseRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore course'), 403, __('You are not authorized to restore course'));
        return $this->courseRepo->restore($id);
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete course'), 403, __('You are not authorized to force delete course'));
        return $this->courseRepo->forceDelete($id);
    }
}