<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClinicInfoController extends Controller
{
    public function index()
    {
        $clinic = auth('clinic')->user()->clinic;
        return view('backend.dashboards.clinic.pages.clinic-info.index', compact('clinic'));
    }

    public function update(Request $request)
    {
        $clinic = auth('clinic')->user()->clinic;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $clinic->update($request->only(['name', 'phone', 'address']));

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Clinic info updated successfully')]);
        }

        return redirect()->back()->with('success', __('Clinic info updated successfully'));
    }
}

