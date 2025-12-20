<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\ClinicUser;
use App\Models\UserOtp;
use App\Models\ModuleApprovement;
use App\Notifications\Clinic\ClinicRegisteredNotification;
use App\Notifications\Admin\EntityRegisteredNotification;
use App\Models\Admin;
use App\Http\Requests\StoreClinicRequest;
use App\Http\Requests\UpdateClinicRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Area;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Services\Affiliate\AffiliateService;

class ClinicController extends Controller
{

    public function registerClinic(Request $request, AffiliateService $affiliateService)
    {
        // Check if email is already verified first
        $verifiedUser = ClinicUser::where('email', $request->user_email)
            ->whereHas('clinic', function($query) {
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
        $unverifiedUser = ClinicUser::where('email', $request->user_email)
            ->whereHas('clinic', function($query) {
                $query->where(['status' => 0, 'is_allowed' => 0]);
            })->first();

        // Custom validation with field-specific error handling
        $validationRules = [
            'clinic_name' => 'required|string|min:2',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|min:10',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'user_name' => 'required|string|min:2',
            'user_email' => 'required|email',
            'password' => 'required|string|min:8',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ];

        // Only add unique validation for email if user is not unverified
        if (!$unverifiedUser) {
            $validationRules['user_email'] .= '|unique:clinic_users,email';
            $validationRules['phone'] .= '|unique:clinics,phone';
        }

        $validator = \Validator::make($request->all(), $validationRules, [
            'clinic_name.required' => 'Clinic name is required.',
            'clinic_name.min' => 'Clinic name must be at least 2 characters.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'address.required' => 'Address is required.',
            'address.min' => 'Address must be at least 10 characters.',
            'governorate_id.required' => 'Governorate is required.',
            'governorate_id.exists' => 'Invalid governorate.',
            'city_id.required' => 'City is required.',
            'city_id.exists' => 'Invalid city.',
            'area_id.required' => 'Area is required.',
            'area_id.exists' => 'Invalid area.',
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

            // Check if clinic user exists but not verified
            $existingUser = ClinicUser::where('email', $request->user_email)
                ->whereHas('clinic', function($query) {
                    $query->where(['status' => 0, 'is_allowed' => 0]);
                })->first();

            if ($existingUser) {
                // Continue with existing user - send OTP
                $clinic = $existingUser->clinic;
                $user = $existingUser;
                $affiliateService->ensureCode($user);
            } else {
                // Create new clinic and user
                $clinic = Clinic::create([
                    'name' => $request->clinic_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'is_allowed' => false,
                    'status' => false,
                    'governorate_id' => $request->governorate_id,
                    'city_id' => $request->city_id,
                    'area_id' => $request->area_id,
                ]);

                // Handle images
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $clinic->addMedia($image)->toMediaCollection('clinic_images');
                    }
                }

                // Create user
                $user = ClinicUser::create([
                    'name' => $request->user_name,
                    'email' => $request->user_email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'clinic_id' => $clinic->id,
                    'status' => false
                ]);

                $affiliateService->ensureCode($user);

                // Create role
                $role = Role::firstOrCreate([
                    'name' => 'clinic-admin-' . $clinic->id,
                    'guard_name' => 'clinic',
                ], ['team_id' => $clinic->id]);

                setPermissionsTeamId($clinic->id);
                $user->assignRole($role);
            }

            // Generate and send OTP
            $otp = $this->generateOtp($clinic);
            $user->notify(new ClinicRegisteredNotification($otp));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration data saved! Please check your email for verification code.',
                'clinic_id' => $clinic->id
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
            'clinic_id' => 'required|exists:clinics,id',
            'otp' => 'required|string|size:6'
        ]);

        try {
            $clinic = Clinic::findOrFail($request->clinic_id);

            // Get latest OTP
            $otpRecord = $clinic->otps()->latest()->first();

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

            // Verify successfully - activate clinic and create approval
            DB::beginTransaction();

            $clinic->update(['status' => true, 'is_allowed' => true]);
            $clinic->clinicUsers()->update(['status' => true]);

            // Mark OTP as used and clean up
            $otpRecord->update(['is_used' => true]);

            // Create module approval - need to create a system admin first or find existing admin
            $systemAdmin = \App\Models\Admin::first(); // Get first admin or create system admin
            ModuleApprovement::create([
                'module_type' => Clinic::class,
                'module_id' => $clinic->id,
                'action' => 'pending',
                'action_by' => $systemAdmin ? $systemAdmin->id : 1, // Use system admin
                'notes' => 'Please upload required documents for approval'
            ]);

            DB::commit();

            // Notify all active admins in DB with redirect to clinic management
            $admins = Admin::where('status', true)->get();
            if ($admins->count()) {
                Notification::send($admins, new EntityRegisteredNotification('Clinic', $clinic->id, $clinic->name));

                Log::info('entity.db_notification.admin', [
                    'context' => 'clinic_registration_verified',
                    'clinic_id' => $clinic->id,
                    'admin_ids' => $admins->pluck('id')->all(),
                    'status' => 'stored_in_database',
                ]);
            } else {
                Log::warning('entity.db_notification.admin_skipped', [
                    'context' => 'clinic_registration_verified',
                    'clinic_id' => $clinic->id,
                    'reason' => 'no_active_admins',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully! Your clinic is now active.',
                'redirect_url' => url('/clinic/login')
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                //'message' => 'Verification failed. Please try again.'
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'clinic_id' => 'required|exists:clinics,id'
        ]);

        try {
            $clinic = Clinic::findOrFail($request->clinic_id);
            $user = $clinic->clinicUsers()->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 400);
            }

            // Get latest OTP
            $otpRecord = $clinic->otps()->latest()->first();

            if (!$otpRecord || !$otpRecord->canResend()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot resend OTP at this time. Please wait.'
                ], 400);
            }

            // Resend OTP - extend expiry and send notification
            $otpRecord->resend();
            $user->notify(new ClinicRegisteredNotification($otpRecord));

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

    private function generateOtp($clinic)
    {
        // Invalidate existing OTPs
        $clinic->otps()->update(['is_used' => true]);

        // Create new OTP
        return $clinic->otps()->create([]);
    }
    public function getGovernorates()
    {
        $governorates = Governorate::orderBy('name')->get();
        return response()->json($governorates);
    }

    public function getCities(Request $request)
    {
        $request->validate([
            'governorate_id' => 'required|exists:governorates,id'
        ]);

        $cities = City::where('governorate_id', $request->governorate_id)
            ->orderBy('name')
            ->get();

        return response()->json($cities);
    }

    public function getAreas(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id'
        ]);

        $areas = Area::where('city_id', $request->city_id)
            ->orderBy('name')
            ->get();

        return response()->json($areas);
    }
}
