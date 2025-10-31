<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementDismissal;

class AnnouncementController extends Controller
{
    public function dismiss($id)
    {
        $supplier = auth('supplier')->user()->supplier;
        $announcement = Announcement::findOrFail($id);

        AnnouncementDismissal::updateOrCreate([
            'announcement_id' => $announcement->id,
            'dismissable_type' => \App\Models\Supplier::class,
            'dismissable_id' => $supplier->id,
        ], [
            'dismissed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => __('Announcement dismissed')]);
    }
}

