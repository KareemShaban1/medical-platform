<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateSetting;
use Illuminate\Http\Request;

class AffiliateSettingsController extends Controller
{
    public function index()
    {
        abort_if(!hasPermission('view affiliate settings'), 403, __('You are not authorized to view affiliate settings'));
        $settings = AffiliateSetting::first();
        return view('backend.dashboards.admin.pages.affiliates.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        abort_if(!hasPermission('update affiliate settings'), 403, __('You are not authorized to update affiliate settings'));

        $validated = $request->validate([
            'default_discount_percent' => 'required|numeric|min:0|max:100',
            'default_commission_percent' => 'required|numeric|min:0|max:100',
        ]);

        $settings = AffiliateSetting::firstOrCreate([]);
        $settings->update($validated);

        return redirect()->to(url('/admin/affiliates/settings'))
            ->with('success', __('Affiliate settings updated successfully.'));
    }
}
