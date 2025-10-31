<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierInfoController extends Controller
{
    public function index()
    {
        $supplier = auth('supplier')->user()->supplier;
        return view('backend.dashboards.supplier.pages.supplier-info.index', compact('supplier'));
    }

    public function update(Request $request)
    {
        $supplier = auth('supplier')->user()->supplier;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $supplier->update($request->only(['name', 'phone', 'address']));

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Supplier info updated successfully')]);
        }

        return redirect()->back()->with('success', __('Supplier info updated successfully'));
    }
}

