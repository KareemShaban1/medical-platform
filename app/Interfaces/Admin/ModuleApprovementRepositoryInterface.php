<?php

namespace App\Interfaces\Admin;

interface ModuleApprovementRepositoryInterface
{
   public function getApprovement($id);
   public function storeApprovement($request);
   public function updateApprovement($request, $approvementId);

}
