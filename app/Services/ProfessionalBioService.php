<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\Supplier;
use App\Models\DoctorProfile;
use App\Services\Subscription\SubscriptionFeatureService;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class ProfessionalBioService
{
    protected SubscriptionFeatureService $featureService;

    public function __construct(SubscriptionFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    public function hasForClinic(Clinic $clinic): bool
    {
        return $this->planHasProfessionalBio($clinic);
    }

    public function hasForSupplier(Supplier $supplier): bool
    {
        return $this->planHasProfessionalBio($supplier);
    }

    public function hasForDoctor(DoctorProfile $doctor): bool
    {
        $clinicUser = $doctor->clinicUser;
        if (!$clinicUser) {
            return false;
        }

        return $this->planHasProfessionalBio($clinicUser);
    }

    /**
     * Generate and persist a unique slug for the given model if missing.
     */
    public function ensureSlug(Model $model, string $type): string
    {
        if (!empty($model->slug)) {
            return $model->slug;
        }

        $baseName = $model->name ?? 'profile';

        switch ($type) {
            case 'clinic':
                $slug = Str::slug($baseName);
                break;
            case 'supplier':
                $slug = Str::slug($baseName);
                break;
            case 'doctor':
                $slug = Str::slug($baseName);
                break;
            default:
                $slug = Str::slug($baseName);
        }

        $original = $slug;
        $i = 1;
        while ($model->newQuery()->where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i;
            $i++;
        }

        $model->slug = $slug;
        $model->saveQuietly();

        return $slug;
    }

    public function getShareUrl(Model $model, string $type): string
    {
        $slug = $this->ensureSlug($model, $type);

        return match ($type) {
            'clinic' => url('/clinics/' . $slug),
            'supplier' => url('/suppliers/' . $slug),
            'doctor' => url('/doctors/' . $slug),
            default => url('/'),
        };
    }

    /**
     * Helper: check if effective subscription's plan has professional_bio enabled.
     */
    protected function planHasProfessionalBio($entity): bool
    {
        $subscription = app(\App\Services\Subscription\SubscriptionService::class)->getEffectiveSubscription($entity);
        if (!$subscription || !$subscription->isActive() || !$subscription->plan) {
            return false;
        }

        return $subscription->plan->planFeatures()
            ->whereHas('feature', function ($q) {
                $q->where('code', 'professional_bio');
            })
            ->where('is_enabled', true)
            ->exists();
    }
}
