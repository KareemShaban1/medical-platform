<?php

namespace App\Interfaces\Clinic;

interface WorkingHourRepositoryInterface
{
    public function index();
    public function forUser($clinicUserId);
    public function bulkSave($clinicUserId, array $slots, bool $isRecurring = true);
    public function destroy($id);
}

