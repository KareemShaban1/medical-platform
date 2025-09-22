<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Http\Requests\StoreClinicRequest;
use App\Http\Requests\UpdateClinicRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\ClinicUser;

class ClinicController extends Controller
{

    public function registerClinic(Request $request)
    {
        $request->validate([
            'clinic_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'user_name' => 'required',
            'user_email' => 'required|email',
            'password' => 'required',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        try {
            DB::beginTransaction();

            // Create the clinic with is_active = false
            $clinic = Clinic::create([
                'name' => $request->clinic_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_allowed' => false,
                'status' => false
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $clinic->addMedia($image)->toMediaCollection('clinic_images');
                }
            }

            // Create the user
            $user = ClinicUser::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'clinic_id' => $clinic->id

            ]);
            $role = Role::firstOrCreate([
                'name' => 'clinic-admin-' . $clinic->id,
                'guard_name' => 'clinic',
            ] , ['team_id' => $clinic->id]);

            setPermissionsTeamId($clinic->id);
            $user->assignRole( $role );


            DB::commit();



            return response()->json([
                'success' => true,
                'message' => 'Clinic registered successfully! Your Clinic is under review.'
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
