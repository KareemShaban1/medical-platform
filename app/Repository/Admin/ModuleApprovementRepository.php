<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\ModuleApprovementRepositoryInterface;
use App\Models\ModuleApprovement;
use App\Models\RentalSpace;
use App\Models\ClinicUser;
use App\Notifications\Clinic\RentalSpaceApprovalStatusNotification;

class ModuleApprovementRepository implements ModuleApprovementRepositoryInterface
{
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function getApprovement($id)
    {
        return ModuleApprovement::findOrFail($id);
    }

    public function storeApprovement($request)
    {
        $moduleApprovement = ModuleApprovement::create([
            'module_id' => $request->module_id,
            'module_type' => $request->module_type,
            'action' => $request->action,
            'notes' => $request->notes,
            'action_by' => auth()->user()->id,
        ]);

        // If this approvement is for a RentalSpace and action is approved/rejected, notify clinic users
        $this->notifyIfRentalSpace($moduleApprovement);
        return response()->json(['message' => 'Approval added', 'data' => $moduleApprovement]);
    }

    public function updateApprovement($request, $approvementId)
    {
        $approvement = ModuleApprovement::findOrFail($approvementId);
        if ($approvement) {
            $approvement->update($request->only(['action', 'notes']) + ['action_by' => auth()->user()->id]);
            // Notify clinic users if needed
            $this->notifyIfRentalSpace($approvement);
        } else {
            return response()->json(['message' => 'Approval not found']);
        }

        return response()->json(['message' => 'Approval updated', 'data' => $approvement]);
    }

    /**
     * Send notification to clinic users when a RentalSpace approvement changes.
     */
    private function notifyIfRentalSpace(ModuleApprovement $approvement): void
    {
        if ($approvement->module_type !== RentalSpace::class) {
            return;
        }

        if (!in_array($approvement->action, ['approved', 'rejected'])) {
            return;
        }

        $rentalSpace = RentalSpace::find($approvement->module_id);
        if (!$rentalSpace) {
            return;
        }

        $clinicUsers = ClinicUser::where('clinic_id', $rentalSpace->clinic_id)->get();
        foreach ($clinicUsers as $user) {
            $user->notify(new RentalSpaceApprovalStatusNotification($rentalSpace, $approvement->action, $approvement->notes));
        }
    }
}
