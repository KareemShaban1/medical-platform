<?php

namespace App\Http\Controllers\Backend\Dashboards\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateUser;
use App\Models\AffiliateCode;
use App\Services\Affiliate\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('backend.dashboards.affiliate.auth.register');
    }

    public function register(Request $request, AffiliateService $affiliateService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:affiliate_users,email',
            'phone' => 'nullable|string|max:20|unique:affiliate_users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = AffiliateUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => true,
        ]);

        $code = $affiliateService->generateCode($user->name);
        $user->affiliateCode()->create([
            'code' => $code,
            'is_active' => true,
        ]);

        $loginUrl = LaravelLocalization::getLocalizedURL(null, 'affiliate/login');
        return redirect()->to($loginUrl)->with('status', __('Affiliate account created. Please login.'));
    }
}
