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
        return view('backend.dashboards.admin.pages.courses.index');
    }

    public function data()
    {
        return $this->courseRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dashboards.admin.pages.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        return $this->courseRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
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
        $course = $this->courseRepo->show($id);

        return view('backend.dashboards.admin.pages.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, $id)
    {
        return $this->courseRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->courseRepo->updateStatus($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->courseRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.admin.pages.courses.trash');
    }

    public function trashData()
    {
        return $this->courseRepo->trashData();
    }

    public function restore($id)
    {
        return $this->courseRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->courseRepo->forceDelete($id);
    }
}
