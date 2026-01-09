<?php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ticketTypes = [
            [
                'name' => 'Refund Request',
                'slug' => 'refund',
                'description' => 'Request for refund of payment',
                'badge_color' => 'primary',
                'is_active' => true,
                'user_types' => ['user'], // Only for patients
            ],
            [
                'name' => 'Complaint',
                'slug' => 'complaint',
                'description' => 'General complaint or issue report',
                'badge_color' => 'warning',
                'is_active' => true,
                'user_types' => ['user', 'clinic_user', 'supplier_user', 'affiliate_user'], // All user types
            ],
            [
                'name' => 'General Inquiry',
                'slug' => 'general',
                'description' => 'General questions or inquiries',
                'badge_color' => 'info',
                'is_active' => true,
                'user_types' => ['user', 'clinic_user', 'supplier_user', 'affiliate_user'], // All user types
            ],
            [
                'name' => 'Technical Support',
                'slug' => 'technical',
                'description' => 'Technical issues with the platform',
                'badge_color' => 'secondary',
                'is_active' => true,
                'user_types' => ['clinic_user', 'supplier_user'], // Clinic and Supplier users
            ],
            [
                'name' => 'Billing Issue',
                'slug' => 'billing',
                'description' => 'Issues related to billing and invoices',
                'badge_color' => 'danger',
                'is_active' => true,
                'user_types' => ['clinic_user', 'supplier_user', 'affiliate_user'], // Business users
            ],
        ];

        foreach ($ticketTypes as $typeData) {
            $userTypes = $typeData['user_types'];
            unset($typeData['user_types']);

            // Check if already exists
            $ticketType = TicketType::where('slug', $typeData['slug'])->first();

            if (!$ticketType) {
                $ticketType = TicketType::create($typeData);
            }

            // Sync user types
            $ticketType->syncUserTypes($userTypes);
        }
    }
}
