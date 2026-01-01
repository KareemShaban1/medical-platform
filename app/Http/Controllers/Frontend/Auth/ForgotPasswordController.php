<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Auth\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password page
     */
    public function showForgotPassword()
    {
        return view('frontend.auth.forgot-password');
    }

    /**
     * Send password reset OTP
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => __('No account found with this email address.')
                ], 404);
            }

            // Check if user has a patient profile
            if (!$user->patient) {
                return response()->json([
                    'success' => false,
                    'message' => __('No patient account found with this email address.')
                ], 404);
            }

            // Invalidate existing OTPs
            $user->otps()->update(['is_used' => true]);

            // Create new OTP
            $otp = $user->otps()->create([]);

            // Send notification
            $user->notify(new PasswordResetNotification($otp, 'patient'));

            return response()->json([
                'success' => true,
                'message' => __('A verification code has been sent to your email.'),
                'user_id' => $user->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to send verification code. Please try again.')
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|string|size:6'
        ]);

        try {
            $user = User::findOrFail($request->user_id);

            // Get latest OTP
            $otpRecord = $user->otps()->latest()->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => __('No verification code found. Please request a new one.')
                ], 400);
            }

            if (!$otpRecord->verify($request->otp)) {
                return response()->json([
                    'success' => false,
                    'message' => __('Invalid or expired verification code.')
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => __('Verification successful. Please set your new password.'),
                'reset_token' => encrypt($user->id . '|' . now()->timestamp)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Verification failed. Please try again.')
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        try {
            // Decrypt and validate token
            $tokenData = decrypt($request->reset_token);
            [$userId, $timestamp] = explode('|', $tokenData);

            // Token expires after 10 minutes
            if (now()->timestamp - $timestamp > 600) {
                return response()->json([
                    'success' => false,
                    'message' => __('Reset token has expired. Please start over.')
                ], 400);
            }

            $user = User::findOrFail($userId);

            DB::beginTransaction();

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Invalidate all OTPs
            $user->otps()->update(['is_used' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Password reset successfully! You can now login with your new password.'),
                'redirect_url' => url('/patient/login')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('Failed to reset password. Please try again.')
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $user = User::findOrFail($request->user_id);

            // Invalidate existing OTPs
            $user->otps()->update(['is_used' => true]);

            // Create new OTP
            $otp = $user->otps()->create([]);

            // Send notification
            $user->notify(new PasswordResetNotification($otp, 'patient'));

            return response()->json([
                'success' => true,
                'message' => __('A new verification code has been sent to your email.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to resend verification code. Please try again.')
            ], 500);
        }
    }
}
