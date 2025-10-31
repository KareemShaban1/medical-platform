<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\AnnouncementRepositoryInterface;
use App\Models\Announcement;
use App\Models\Clinic;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    public function data()
    {
        $announcements = Announcement::query();

        return datatables()->of($announcements)
            ->addColumn('audience', function($item){
                $parts = [];
                if ($item->target_clinics_all) { $parts[] = 'All Clinics'; }
                if ($item->target_suppliers_all) { $parts[] = 'All Suppliers'; }
                if (!$item->target_clinics_all && $item->clinics()->count()) { $parts[] = 'Some Clinics'; }
                if (!$item->target_suppliers_all && $item->suppliers()->count()) { $parts[] = 'Some Suppliers'; }
                return implode(' + ', $parts) ?: '-';
            })
            ->editColumn('status', fn($item) => $item->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('action', function($item){
                $editUrl = route('admin.announcements.edit', $item->id);
                $deleteJs = "deleteAnnouncement($item->id)";
                return '<div class="d-flex gap-2">'
                    .'<a href="'.$editUrl.'" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></a>'
                    .'<button onclick="'.$deleteJs.'" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>'
                    .'</div>';
            })
            ->rawColumns(['status','action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function() use ($request) {
            $announcement = Announcement::create([
                'title' => $request->title,
                'body' => $request->body,
                'link_url' => $request->link_url,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'target_clinics_all' => (bool)$request->target_clinics_all,
                'target_suppliers_all' => (bool)$request->target_suppliers_all,
                'status' => (bool)$request->status,
                'created_by' => auth('admin')->id() ?? null,
            ]);

            $announcement->clinics()->sync($request->clinic_ids ?? []);
            $announcement->suppliers()->sync($request->supplier_ids ?? []);

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => __('Announcement created successfully')]);
            }
            return redirect()->route('admin.announcements.index')->with('success', __('Announcement created successfully'));
        });
    }

    public function show($id)
    {
        return Announcement::with(['clinics:id,name', 'suppliers:id,name'])->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function() use ($request, $id) {
            $announcement = Announcement::findOrFail($id);
            $announcement->update([
                'title' => $request->title,
                'body' => $request->body,
                'link_url' => $request->link_url,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'target_clinics_all' => (bool)$request->target_clinics_all,
                'target_suppliers_all' => (bool)$request->target_suppliers_all,
                'status' => (bool)$request->status,
            ]);

            $announcement->clinics()->sync($request->clinic_ids ?? []);
            $announcement->suppliers()->sync($request->supplier_ids ?? []);

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => __('Announcement updated successfully')]);
            }
            return redirect()->route('admin.announcements.index')->with('success', __('Announcement updated successfully'));
        });
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->clinics()->detach();
        $announcement->suppliers()->detach();
        $announcement->delete();

        return $this->jsonResponse('success', __('Announcement deleted successfully'));
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['success' => $status === 'success', 'status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
