<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeatureMaster;
use App\Models\Plan;
use App\Models\PlanFeature;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Create features
        $features = [
            [
                'code' => 'max_patients',
                'name' => 'Max Patients',
                'description' => 'Maximum number of patients allowed',
                'unit' => 'patients',
                'value_type' => 'integer',
                'is_active' => true,
            ],
            [
                'code' => 'max_products',
                'name' => 'Max Products',
                'description' => 'Maximum number of products allowed',
                'unit' => 'products',
                'value_type' => 'integer',
                'is_active' => true,
            ],
            [
                'code' => 'lab_module',
                'name' => 'Lab Module',
                'description' => 'Access to laboratory management module',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'inventory_module',
                'name' => 'Inventory Module',
                'description' => 'Access to inventory management module',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'appointments_module',
                'name' => 'Appointments Module',
                'description' => 'Access to appointments management module',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'prescriptions_module',
                'name' => 'Prescriptions Module',
                'description' => 'Access to prescriptions management module',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'medical_records_module',
                'name' => 'Medical Records Module',
                'description' => 'Access to medical records management module',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'expenses_module',
                'name' => 'Expenses Module',
                'description' => 'Access to expenses management module',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'rental_spaces_module',
                'name' => 'Rental Spaces Module',
                'description' => 'Access to rental spaces management module',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
        ];

        $featureModels = [];
        foreach ($features as $feature) {
            $featureModels[$feature['code']] = FeatureMaster::create($feature);
        }

        // Create Doctor Plans
        $doctorPlans = [
            [
                'plan_type' => 'doctor',
                'level' => 'free',
                'name' => 'Free Doctor Plan',
                'price' => 0,
                'duration_in_days' => null,
                'is_active' => true,
                'description' => 'Basic plan for standalone doctors',
                'features' => [
                    ['code' => 'max_patients', 'is_enabled' => true, 'is_limited' => true, 'value' => '10'],
                    ['code' => 'lab_module', 'is_enabled' => false],
                    ['code' => 'appointments_module', 'is_enabled' => true],
                    ['code' => 'prescriptions_module', 'is_enabled' => true],
                ],
            ],
            [
                'plan_type' => 'doctor',
                'level' => 'basic',
                'name' => 'Basic Doctor Plan',
                'price' => 99.00,
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Standard plan for doctors',
                'features' => [
                    ['code' => 'max_patients', 'is_enabled' => true, 'is_limited' => true, 'value' => '100'],
                    ['code' => 'lab_module', 'is_enabled' => true],
                    ['code' => 'appointments_module', 'is_enabled' => true],
                    ['code' => 'prescriptions_module', 'is_enabled' => true],
                    ['code' => 'medical_records_module', 'is_enabled' => true],
                ],
            ],
            [
                'plan_type' => 'doctor',
                'level' => 'advanced',
                'name' => 'Advanced Doctor Plan',
                'price' => 199.00,
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Advanced plan for doctors',
                'features' => [
                    ['code' => 'max_patients', 'is_enabled' => true, 'is_limited' => true, 'value' => '500'],
                    ['code' => 'lab_module', 'is_enabled' => true],
                    ['code' => 'appointments_module', 'is_enabled' => true],
                    ['code' => 'prescriptions_module', 'is_enabled' => true],
                    ['code' => 'medical_records_module', 'is_enabled' => true],
                    ['code' => 'expenses_module', 'is_enabled' => true],
                ],
            ],
        ];

        foreach ($doctorPlans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = Plan::create($planData);

            foreach ($features as $featureData) {
                $feature = $featureModels[$featureData['code']];
                PlanFeature::create([
                    'plan_id' => $plan->id,
                    'feature_id' => $feature->id,
                    'is_enabled' => $featureData['is_enabled'],
                    'value' => $featureData['value'] ?? null,
                    'is_limited' => $featureData['is_limited'] ?? false,
                ]);
            }
        }

        // Create Clinic Plans
        $clinicPlans = [
            [
                'plan_type' => 'clinic',
                'level' => 'free',
                'name' => 'Free Clinic Plan',
                'price' => 0,
                'duration_in_days' => null,
                'is_active' => true,
                'description' => 'Basic plan for clinics',
                'features' => [
                    ['code' => 'max_patients', 'is_enabled' => true, 'is_limited' => true, 'value' => '50'],
                    ['code' => 'lab_module', 'is_enabled' => false],
                    ['code' => 'inventory_module', 'is_enabled' => false],
                    ['code' => 'appointments_module', 'is_enabled' => true],
                ],
            ],
            [
                'plan_type' => 'clinic',
                'level' => 'basic',
                'name' => 'Basic Clinic Plan',
                'price' => 299.00,
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Standard plan for clinics',
                'features' => [
                    ['code' => 'max_patients', 'is_enabled' => true, 'is_limited' => true, 'value' => '500'],
                    ['code' => 'lab_module', 'is_enabled' => true],
                    ['code' => 'inventory_module', 'is_enabled' => true],
                    ['code' => 'appointments_module', 'is_enabled' => true],
                    ['code' => 'prescriptions_module', 'is_enabled' => true],
                    ['code' => 'medical_records_module', 'is_enabled' => true],
                ],
            ],
            [
                'plan_type' => 'clinic',
                'level' => 'advanced',
                'name' => 'Advanced Clinic Plan',
                'price' => 599.00,
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Advanced plan for clinics',
                'features' => [
                    ['code' => 'max_patients', 'is_enabled' => true, 'is_limited' => true, 'value' => '2000'],
                    ['code' => 'lab_module', 'is_enabled' => true],
                    ['code' => 'inventory_module', 'is_enabled' => true],
                    ['code' => 'appointments_module', 'is_enabled' => true],
                    ['code' => 'prescriptions_module', 'is_enabled' => true],
                    ['code' => 'medical_records_module', 'is_enabled' => true],
                    ['code' => 'expenses_module', 'is_enabled' => true],
                    ['code' => 'rental_spaces_module', 'is_enabled' => true],
                ],
            ],
        ];

        foreach ($clinicPlans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = Plan::create($planData);

            foreach ($features as $featureData) {
                $feature = $featureModels[$featureData['code']];
                PlanFeature::create([
                    'plan_id' => $plan->id,
                    'feature_id' => $feature->id,
                    'is_enabled' => $featureData['is_enabled'],
                    'value' => $featureData['value'] ?? null,
                    'is_limited' => $featureData['is_limited'] ?? false,
                ]);
            }
        }

        // Create Supplier Plans
        $supplierPlans = [
            [
                'plan_type' => 'supplier',
                'level' => 'free',
                'name' => 'Free Supplier Plan',
                'price' => 0,
                'duration_in_days' => null,
                'is_active' => true,
                'description' => 'Basic plan for suppliers',
                'features' => [
                    ['code' => 'max_products', 'is_enabled' => true, 'is_limited' => true, 'value' => '50'],
                ],
            ],
            [
                'plan_type' => 'supplier',
                'level' => 'basic',
                'name' => 'Basic Supplier Plan',
                'price' => 149.00,
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Standard plan for suppliers',
                'features' => [
                    ['code' => 'max_products', 'is_enabled' => true, 'is_limited' => true, 'value' => '500'],
                ],
            ],
            [
                'plan_type' => 'supplier',
                'level' => 'advanced',
                'name' => 'Advanced Supplier Plan',
                'price' => 399.00,
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Advanced plan for suppliers',
                'features' => [
                    ['code' => 'max_products', 'is_enabled' => true, 'is_limited' => true, 'value' => '5000'],
                ],
            ],
        ];

        foreach ($supplierPlans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = Plan::create($planData);

            foreach ($features as $featureData) {
                $feature = $featureModels[$featureData['code']];
                PlanFeature::create([
                    'plan_id' => $plan->id,
                    'feature_id' => $feature->id,
                    'is_enabled' => $featureData['is_enabled'],
                    'value' => $featureData['value'] ?? null,
                    'is_limited' => $featureData['is_limited'] ?? false,
                ]);
            }
        }
    }
}

