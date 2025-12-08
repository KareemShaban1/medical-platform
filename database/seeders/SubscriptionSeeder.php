<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\FeatureMaster;
use App\Models\Plan;
use App\Models\PlanFeature;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset tables to ensure clean seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PlanFeature::truncate();
        Plan::truncate();
        FeatureMaster::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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
            [
                'code' => 'purchase_request_offer',
                'name' => 'Purchase Request Offer Quota',
                'description' => 'Maximum purchase request offers that can be accepted',
                'unit' => 'offers',
                'value_type' => 'integer',
                'is_active' => true,
            ],
            [
                'code' => 'pay_in_advance',
                'name' => 'Electronic Collection (Pay in Advance)',
                'description' => 'Access to electronic collection / pre-payment feature',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'purchase_requests',
                'name' => 'Purchase Requests',
                'description' => 'Ability to create purchase/tender requests',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'post_jobs',
                'name' => 'Post Jobs',
                'description' => 'Ability to post job listings',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'marketplace_access',
                'name' => 'Marketplace & Rental Access',
                'description' => 'Ability to purchase from the store and rent medical spaces',
                'value_type' => 'boolean',
                'is_active' => true,
            ],
            [
                'code' => 'professional_bio',
                'name' => 'Professional Bio with Shareable Link',
                'description' => 'Premium bio, badge, and shareable link',
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
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Basic plan for standalone doctors',
                'features' => [
                    ['code' => 'marketplace_access', 'is_enabled' => true],
                ],
            ],
            [
                'plan_type' => 'doctor',
                'level' => 'basic',
                'name' => 'Paid Doctor Plan',
                'price' => 99.00,
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Paid plan for doctors',
                'features' => [
                    ['code' => 'marketplace_access', 'is_enabled' => true],
                    ['code' => 'professional_bio', 'is_enabled' => true],
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
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Basic plan for clinics',
                'features' => [
                    ['code' => 'max_patients', 'is_enabled' => true, 'is_limited' => true, 'value' => '2'],
                    ['code'=> 'marketplace_access' , 'is_enabled' => true , 'is_limited' => false , 'value' => null ],
                    ['code' => 'lab_module', 'is_enabled' => true , 'is_limited' => true , 'value' => '2' ],
                    ['code' => 'inventory_module', 'is_enabled' => true , 'is_limited' => true , 'value' => '2' ],
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
                    ['code' => 'max_patients', 'is_enabled' => true, 'is_limited' => false, 'value' => null],
                    ['code'=> 'marketplace_access' , 'is_enabled' => true , 'is_limited' => false , 'value' => null ],
                    ['code' => 'lab_module', 'is_enabled' => true],
                    ['code' => 'inventory_module', 'is_enabled' => true],
                    ['code' => 'appointments_module', 'is_enabled' => true],
                    ['code' => 'prescriptions_module', 'is_enabled' => true],
                    ['code' => 'medical_records_module', 'is_enabled' => true],
                    ['code' => 'purchase_requests', 'is_enabled' => true, 'is_limited' => false, 'value' => null],
                    ['code' => 'professional_bio', 'is_enabled' => true],
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
                    ['code' => 'max_patients', 'is_enabled' => true, 'is_limited' => false, 'value' => null],
                    ['code'=> 'marketplace_access' , 'is_enabled' => true , 'is_limited' => false , 'value' => null ],
                    ['code' => 'lab_module', 'is_enabled' => true],
                    ['code' => 'inventory_module', 'is_enabled' => true],
                    ['code' => 'appointments_module', 'is_enabled' => true],
                    ['code' => 'prescriptions_module', 'is_enabled' => true],
                    ['code' => 'medical_records_module', 'is_enabled' => true],
                    ['code' => 'expenses_module', 'is_enabled' => true],
                    ['code' => 'rental_spaces_module', 'is_enabled' => true],
                    ['code' => 'purchase_requests', 'is_enabled' => true, 'is_limited' => false, 'value' => null],
                    ['code' => 'post_jobs', 'is_enabled' => true, 'is_limited' => false, 'value' => null],
                    ['code' => 'professional_bio', 'is_enabled' => true],
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
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'Basic plan for suppliers',
                'features' => [
                    ['code' => 'max_products', 'is_enabled' => true, 'is_limited' => true, 'value' => '2'],
                    ['code' => 'purchase_request_offer', 'is_enabled' => false],
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
                    ['code' => 'max_products', 'is_enabled' => true, 'is_limited' => true, 'value' => '30'],
                    ['code' => 'purchase_request_offer', 'is_enabled' => false],
                    ['code' => 'professional_bio', 'is_enabled' => true],
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
                    ['code' => 'max_products', 'is_enabled' => true, 'is_limited' => true, 'value' => '100'],
                    ['code' => 'purchase_request_offer', 'is_enabled' => true, 'is_limited' => true, 'value' => '10'],
                    ['code' => 'professional_bio', 'is_enabled' => true],
                ],
            ],
            [
                'plan_type' => 'supplier',
                'level' => 'vip',
                'name' => 'VIP Supplier Plan',
                'price' => 799.00,
                'duration_in_days' => 30,
                'is_active' => true,
                'description' => 'VIP plan for suppliers',
                'features' => [
                    ['code' => 'max_products', 'is_enabled' => true, 'is_limited' => false, 'value' => null],
                    ['code' => 'purchase_request_offer', 'is_enabled' => true, 'is_limited' => false, 'value' => null],
                    ['code' => 'professional_bio', 'is_enabled' => true],
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
