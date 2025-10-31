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
        return view('backend.dashboards.admin.pages.announcements.index');
    }

    public function data()
    {
        return $this->repo->data();
    }

    public function create()
    {
        $clinics = Clinic::select('id','name')->orderBy('name')->get();
        $suppliers = Supplier::select('id','name')->orderBy('name')->get();
        return view('backend.dashboards.admin.pages.announcements.create', compact('clinics','suppliers'));
    }

    public function store(StoreAnnouncementRequest $request)
    {
        return $this->repo->store($request);
    }

    public function edit($id)
    {
        $announcement = $this->repo->show($id);
        $clinics = Clinic::select('id','name')->orderBy('name')->get();
        $suppliers = Supplier::select('id','name')->orderBy('name')->get();
        return view('backend.dashboards.admin.pages.announcements.edit', compact('announcement','clinics','suppliers'));
    }

    public function update(UpdateAnnouncementRequest $request, $id)
    {
        return $this->repo->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->repo->destroy($id);
    }
}

