<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementDismissal;

class AnnouncementController extends Controller
{
    public function dismiss($id)
    {
        $clinic = auth('clinic')->user()->clinic;
        $announcement = Announcement::findOrFail($id);

        AnnouncementDismissal::updateOrCreate([
            'announcement_id' => $announcement->id,
            'dismissable_type' => \App\Models\Clinic::class,
            'dismissable_id' => $clinic->id,
        ], [
            'dismissed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => __('Announcement dismissed')]);
    }
}

