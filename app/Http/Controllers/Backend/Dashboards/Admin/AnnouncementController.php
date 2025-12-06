<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Announcement\StoreAnnouncementRequest;
use App\Http\Requests\Admin\Announcement\UpdateAnnouncementRequest;
use App\Interfaces\Admin\AnnouncementRepositoryInterface;
use App\Models\Clinic;
use App\Models\Supplier;

class AnnouncementController extends Controller
{
    protected $repo;

    public function __construct(AnnouncementRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view announcements'), 403, __('You are not authorized to view announcements'));
        return view('backend.dashboards.admin.pages.announcements.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view announcements'), 403, __('You are not authorized to view announcements'));
        return $this->repo->data();
    }

    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create announcement'), 403, __('You are not authorized to create announcement'));
        $clinics = Clinic::select('id','name')->orderBy('name')->get();
        $suppliers = Supplier::select('id','name')->orderBy('name')->get();
        return view('backend.dashboards.admin.pages.announcements.create', compact('clinics','suppliers'));
    }

    public function store(StoreAnnouncementRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create announcement'), 403, __('You are not authorized to create announcement'));
        return $this->repo->store($request);
    }

    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update announcement'), 403, __('You are not authorized to view announcement'));
        $announcement = $this->repo->show($id);
        $clinics = Clinic::select('id','name')->orderBy('name')->get();
        $suppliers = Supplier::select('id','name')->orderBy('name')->get();
        return view('backend.dashboards.admin.pages.announcements.edit', compact('announcement','clinics','suppliers'));
    }

    public function update(UpdateAnnouncementRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update announcement'), 403, __('You are not authorized to update announcement'));
        return $this->repo->update($request, $id);
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete announcement'), 403, __('You are not authorized to delete announcement'));
        return $this->repo->destroy($id);
    }
}
