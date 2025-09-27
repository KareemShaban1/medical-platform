<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierUser;
use App\Models\UserOtp;
use App\Models\ModuleApprovement;
use App\Notifications\Supplier\SupplierRegisteredNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SupplierController extends Controller
{
    public function registerSupplier(Request $request)
    {
        // Check if email is already verified first
        $verifiedUser = SupplierUser::where('email', $request->user_email)
            ->whereHas('supplier', function($query) {
                $query->where(['status' => 1, 'is_allowed' => 1]);
            })->first();

        if ($verifiedUser) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already registered and verified. Please use login instead.',
                'errors' => [
                    'user_email' => ['This email is already registered and verified. Please use login instead.']
                ]
            ], 422);
        }

        // Check if email exists but is unverified (allow re-registration)
        $unverifiedUser = SupplierUser::where('email', $request->user_email)
            ->whereHas('supplier', function($query) {
                $query->where(['status' => 0, 'is_allowed' => 0]);
            })->first();

        // Custom validation with field-specific error handling
        $validationRules = [
            'supplier_name' => 'required|string|min:2',
            'phone' => 'required|string|max:255|unique:suppliers,phone',
            'address' => 'required|string|min:10',
            'user_name' => 'required|string|min:2',
            'user_email' => 'required|email',
            'password' => 'required|string|min:8',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ];

        // Only add unique validation for email if user is not unverified
        if (!$unverifiedUser) {
            $validationRules['user_email'] .= '|unique:supplier_users,email';
            $validationRules['phone'] .= '|unique:suppliers,phone';
        }

        $validator = \Validator::make($request->all(), $validationRules, [
            'supplier_name.required' => 'Supplier name is required.',
            'supplier_name.min' => 'Supplier name must be at least 2 characters.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'address.required' => 'Address is required.',
            'address.min' => 'Address must be at least 10 characters.',
            'user_name.required' => 'User name is required.',
            'user_name.min' => 'User name must be at least 2 characters.',
            'user_email.required' => 'Email is required.',
            'user_email.email' => 'Please enter a valid email address.',
            'user_email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'images.*.image' => 'Only image files are allowed.',
            'images.*.mimes' => 'Images must be jpeg, png, jpg, or gif format.',
            'images.*.max' => 'Image size must be less than 10MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the form and try again.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Check if supplier user exists but not verified
            $existingUser = SupplierUser::where('email', $request->user_email)
                ->whereHas('supplier', function($query) {
                    $query->where(['status' => 0, 'is_allowed' => 0]);
                })->first();

            if ($existingUser) {
                // Continue with existing user - send OTP
                $supplier = $existingUser->supplier;
                $user = $existingUser;
            } else {
                // Create new supplier and user
                $supplier = Supplier::create([
                    'name' => $request->supplier_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'is_allowed' => false,
                    'status' => false
                ]);

                // Handle images
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $supplier->addMedia($image)->toMediaCollection('supplier_images');
                    }
                }

                // Create user
                $user = SupplierUser::create([
                    'name' => $request->user_name,
                    'email' => $request->user_email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'supplier_id' => $supplier->id,
                    'status' => false
                ]);

                // Create role
                $role = Role::firstOrCreate([
                    'name' => 'supplier-admin-' . $supplier->id,
                    'guard_name' => 'supplier',
                ], ['team_id' => $supplier->id]);

                setPermissionsTeamId($supplier->id);
                $user->assignRole($role);
            }

            // Generate and send OTP
            $otp = $this->generateOtp($supplier);
            $user->notify(new SupplierRegisteredNotification($otp));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration data saved! Please check your email for verification code.',
                'supplier_id' => $supplier->id
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

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'otp' => 'required|string|size:6'
        ]);

        try {
            $supplier = Supplier::findOrFail($request->supplier_id);

            // Get latest OTP
            $otpRecord = $supplier->otps()->latest()->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'No OTP found. Please request a new one.'
                ], 400);
            }

            if (!$otpRecord->verify($request->otp)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.'
                ], 400);
            }

            // Verify successfully - activate supplier and create approval
            DB::beginTransaction();

            $supplier->update(['status' => true, 'is_allowed' => true]);
            $supplier->supplierUsers()->update(['status' => true]);

            // Mark OTP as used and clean up
            $otpRecord->update(['is_used' => true]);

            // Create module approval - need to create a system admin first or find existing admin
            $systemAdmin = \App\Models\Admin::first(); // Get first admin or create system admin
            ModuleApprovement::create([
                'module_type' => Supplier::class,
                'module_id' => $supplier->id,
                'action' => 'pending',
                'action_by' => $systemAdmin ? $systemAdmin->id : 1, // Use system admin
                'notes' => 'Please upload required documents for approval'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully! Your supplier account is now active.',
                'redirect_url' => url('/supplier/login')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Verification failed. Please try again.'
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id'
        ]);

        try {
            $supplier = Supplier::findOrFail($request->supplier_id);
            $user = $supplier->supplierUsers()->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 400);
            }

            // Get latest OTP
            $otpRecord = $supplier->otps()->latest()->first();

            if (!$otpRecord || !$otpRecord->canResend()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot resend OTP at this time. Please wait.'
                ], 400);
            }

            // Resend OTP - extend expiry and send notification
            $otpRecord->resend();
            $user->notify(new SupplierRegisteredNotification($otpRecord));

            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully! Check your email.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.'
            ], 500);
        }
    }

    private function generateOtp($supplier)
    {
        // Invalidate existing OTPs
        $supplier->otps()->update(['is_used' => true]);

        // Create new OTP
        return $supplier->otps()->create([]);
    }
}
