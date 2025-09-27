<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ModuleApprovement;
use App\Models\Supplier;

class CheckSupplierApproval
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('supplier')->user();

        if (!$user || !$user->supplier) {
            return redirect()->to('/supplier/login');
        }

        $supplier = $user->supplier;
        $approval = $supplier->approvement;

        // If no approval record exists, create one as pending
        if (!$approval) {
            $systemAdmin = \App\Models\Admin::first();
            ModuleApprovement::create([
                'module_type' => Supplier::class,
                'module_id' => $supplier->id,
                'action' => 'pending',
                'action_by' => $systemAdmin ? $systemAdmin->id : 1,
                'notes' => 'Initial approval required'
            ]);
            $approval = $supplier->fresh()->approvement;
        }

        // Skip approval check for specific routes
        $skipRoutes = [
            'supplier.approval.show',
            'supplier.approval.upload',
            'supplier.logout'
        ];

        if (in_array($request->route()->getName(), $skipRoutes)) {
            return $next($request);
        }

        // Check approval status
        switch ($approval->action) {
            case 'pending':
            case 'under_review':
            case 'rejected':
                return redirect()->route('supplier.approval.show');

            case 'approved':
                return $next($request);

            default:
                return redirect()->route('supplier.approval.show');
        }
    }
}
