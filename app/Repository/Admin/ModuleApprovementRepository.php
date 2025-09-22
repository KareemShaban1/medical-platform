<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\ModuleApprovementRepositoryInterface;
use App\Models\ModuleApprovement;

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
        return response()->json(['message' => 'Approval added', 'data' => $moduleApprovement]);
    }

    public function updateApprovement($request, $approvementId)
    {
        $approvement = ModuleApprovement::findOrFail($approvementId);
       if($approvement){
        $approvement->update($request->only(['action', 'notes']) + ['action_by' => auth()->user()->id]);
       }else{
           return response()->json(['message' => 'Approval not found']);
       }
        
        return response()->json(['message' => 'Approval updated', 'data' => $approvement]);
    }
}
