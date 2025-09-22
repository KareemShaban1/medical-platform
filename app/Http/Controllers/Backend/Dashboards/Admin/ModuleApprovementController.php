<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Admin\ModuleApprovementRepositoryInterface;

class ModuleApprovementController extends Controller
{
    protected $moduleApprovementRepo;

    public function __construct(ModuleApprovementRepositoryInterface $moduleApprovementRepo)
    {
        $this->moduleApprovementRepo = $moduleApprovementRepo;
    }

    public function getApprovement($id)
    {
        return $this->moduleApprovementRepo->getApprovement($id);
    }

    public function storeApprovement(Request $request)
    {
        return $this->moduleApprovementRepo->storeApprovement($request);
    }

    public function updateApprovement(Request $request, $approvementId)
    {
        return $this->moduleApprovementRepo->updateApprovement($request, $approvementId);
    }

    
}