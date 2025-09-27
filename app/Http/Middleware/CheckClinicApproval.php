<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ModuleApprovement;
use App\Models\Clinic;

class CheckClinicApproval
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('clinic')->user();

        if (!$user || !$user->clinic) {
            return  redirect()->to('/clinic/login');
        }

        $clinic = $user->clinic;
        $approval = $clinic->approvement;

        // If no approval record exists, create one as pending
        if (!$approval) {
            $systemAdmin = \App\Models\Admin::first();
            ModuleApprovement::create([
                'module_type' => Clinic::class,
                'module_id' => $clinic->id,
                'action' => 'pending',
                'action_by' => $systemAdmin ? $systemAdmin->id : 1,
                'notes' => 'Initial approval required'
            ]);
            $approval = $clinic->fresh()->approvement;
        }

        // Skip approval check for specific routes
        $skipRoutes = [
            'clinic.approval.show',
            'clinic.approval.upload',
            'clinic.logout'
        ];

        if (in_array($request->route()->getName(), $skipRoutes)) {
            return $next($request);
        }

        // Check approval status
        switch ($approval->action) {
            case 'pending':
            case 'under_review':
            case 'rejected':
                return redirect()->route('clinic.approval.show');

            case 'approved':
                return $next($request);

            default:
                return redirect()->route('clinic.approval.show');
        }
    }
}
