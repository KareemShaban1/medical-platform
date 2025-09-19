<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\SupplierUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SupplierController extends Controller
{
    public function registerSupplier(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'user_name' => 'required',
            'user_email' => 'required|email',
            'password' => 'required',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        try {
            DB::beginTransaction();

            // Create the clinic with is_active = false
            $supplier = Supplier::create([
                'name' => $request->supplier_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_allowed' => false,
                'status' => false
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $supplier->addMedia($image)->toMediaCollection('supplier_images');
                }
            }


            // Create the user
            $user = SupplierUser::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->password),
                'supplier_id' => $supplier->id

            ]);

            if (Role::where('name', 'supplier-admin')
                ->where('guard_name', 'supplier')->exists()
            ) {
                $user->assignRole('supplier-admin');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supplier registered successfully! Your Supplier is under review.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
