<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StaffProfile;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@nidsandtaray.com'],
            [
                'name' => 'Admin Nids & Taray',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '09171234567',
                'is_active' => true,
            ]
        );

        // 2. Staff Profiles (Manicuristas and Massage Therapists)
        $staffData = [
            [
                'name' => 'Nida Santos',
                'nickname' => 'Ate Nids',
                'email' => 'nids@nidsandtaray.com',
                'phone' => '09181112222',
                'specialization' => 'all_around',
                'status' => 'available',
                'queue_position' => 1,
                'commission_rate' => 40.00,
            ],
            [
                'name' => 'Taray Velasco',
                'nickname' => 'Ate Taray',
                'email' => 'taray@nidsandtaray.com',
                'phone' => '09183334444',
                'specialization' => 'all_around',
                'status' => 'serving_in_shop',
                'queue_position' => 2,
                'commission_rate' => 40.00,
            ],
            [
                'name' => 'Maria Kristina Cruz',
                'nickname' => 'Tin',
                'email' => 'tin@nidsandtaray.com',
                'phone' => '09185556666',
                'specialization' => 'manicurist',
                'status' => 'available',
                'queue_position' => 3,
                'commission_rate' => 40.00,
            ],
            [
                'name' => 'Jessa Mae Ramos',
                'nickname' => 'Jessa',
                'email' => 'jessa@nidsandtaray.com',
                'phone' => '09187778888',
                'specialization' => 'manicurist',
                'status' => 'on_home_service',
                'queue_position' => 4,
                'commission_rate' => 40.00,
            ],
            [
                'name' => 'Grace Dela Torre',
                'nickname' => 'Grace',
                'email' => 'grace@nidsandtaray.com',
                'phone' => '09189990000',
                'specialization' => 'massage_therapist',
                'status' => 'available',
                'queue_position' => 5,
                'commission_rate' => 45.00,
            ],
            [
                'name' => 'Analyn Flores',
                'nickname' => 'Ana',
                'email' => 'ana@nidsandtaray.com',
                'phone' => '09191234567',
                'specialization' => 'massage_therapist',
                'status' => 'on_break',
                'queue_position' => 6,
                'commission_rate' => 45.00,
            ],
        ];

        $createdStaff = [];
        foreach ($staffData as $s) {
            $user = User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'password' => Hash::make('staff123'),
                    'role' => 'staff',
                    'phone' => $s['phone'],
                    'is_active' => true,
                ]
            );

            $profile = StaffProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $s['name'],
                    'nickname' => $s['nickname'],
                    'phone' => $s['phone'],
                    'specialization' => $s['specialization'],
                    'status' => $s['status'],
                    'queue_position' => $s['queue_position'],
                    'commission_rate' => $s['commission_rate'],
                    'is_active' => true,
                    'last_status_change' => now(),
                ]
            );
            $createdStaff[] = $profile;
        }

        // 3. Service Categories
        $nailsCat = ServiceCategory::updateOrCreate(
            ['slug' => 'nails-and-toes'],
            [
                'name' => 'Nails, Toes & Cutics',
                'description' => 'Manicure, Pedicure, Cuticle Care, and Nail Art',
                'icon' => 'sparkles',
                'display_order' => 1,
            ]
        );

        $massageCat = ServiceCategory::updateOrCreate(
            ['slug' => 'massage-and-wellness'],
            [
                'name' => 'Massage & Wellness',
                'description' => 'Relaxing Body Treatments and Therapy',
                'icon' => 'hand-raised',
                'display_order' => 2,
            ]
        );

        $comboCat = ServiceCategory::updateOrCreate(
            ['slug' => 'combo-packages'],
            [
                'name' => 'Special Packages & Combos',
                'description' => 'Best value bundles for complete relaxation',
                'icon' => 'gift',
                'display_order' => 3,
            ]
        );

        // 4. Services
        $services = [
            // Nails
            ['category_id' => $nailsCat->id, 'name' => 'Regular Manicure with Cutics Cleaning', 'price' => 150.00, 'estimated_duration_minutes' => 35],
            ['category_id' => $nailsCat->id, 'name' => 'Regular Pedicure with Cutics Cleaning', 'price' => 180.00, 'estimated_duration_minutes' => 45],
            ['category_id' => $nailsCat->id, 'name' => 'Gel Manicure (Long-lasting)', 'price' => 350.00, 'estimated_duration_minutes' => 50],
            ['category_id' => $nailsCat->id, 'name' => 'Gel Pedicure (Long-lasting)', 'price' => 400.00, 'estimated_duration_minutes' => 60],
            ['category_id' => $nailsCat->id, 'name' => 'Deluxe Foot Spa with Pedicure & Massage', 'price' => 450.00, 'estimated_duration_minutes' => 60],
            ['category_id' => $nailsCat->id, 'name' => 'Nail Art / Design per Nail', 'price' => 50.00, 'estimated_duration_minutes' => 15],

            // Massage
            ['category_id' => $massageCat->id, 'name' => 'Full Body Swedish Massage (60 mins)', 'price' => 450.00, 'estimated_duration_minutes' => 60],
            ['category_id' => $massageCat->id, 'name' => 'Full Body Shiatsu / Dry Massage (60 mins)', 'price' => 450.00, 'estimated_duration_minutes' => 60],
            ['category_id' => $massageCat->id, 'name' => 'Combination Deep Tissue Massage (90 mins)', 'price' => 650.00, 'estimated_duration_minutes' => 90],
            ['category_id' => $massageCat->id, 'name' => 'Head, Back & Shoulder Stress Relief (30 mins)', 'price' => 250.00, 'estimated_duration_minutes' => 30],
            ['category_id' => $massageCat->id, 'name' => 'Foot Reflexology Therapy (45 mins)', 'price' => 350.00, 'estimated_duration_minutes' => 45],
            ['category_id' => $massageCat->id, 'name' => 'Ventosa Cupping with Massage (75 mins)', 'price' => 600.00, 'estimated_duration_minutes' => 75],

            // Combos
            ['category_id' => $comboCat->id, 'name' => 'Nids & Taray Queen Glow (Gel Mani-Pedi + Foot Spa)', 'price' => 1050.00, 'estimated_duration_minutes' => 110],
            ['category_id' => $comboCat->id, 'name' => 'Ultimate Relaxation (60m Swedish + Foot Spa + Pedicure)', 'price' => 950.00, 'estimated_duration_minutes' => 120],
        ];

        $createdServices = [];
        foreach ($services as $srv) {
            $createdServices[] = Service::updateOrCreate(
                ['category_id' => $srv['category_id'], 'name' => $srv['name']],
                [
                    'description' => 'Quality treatment by certified attendants',
                    'price' => $srv['price'],
                    'estimated_duration_minutes' => $srv['estimated_duration_minutes'],
                    'allow_home_service' => true,
                    'is_active' => true,
                ]
            );
        }

        // 5. Sample Transactions for Tracker Demo
        // Transaction 1: In-Shop Cash Paid
        $tx1 = Transaction::create([
            'tracking_number' => 'NT-' . now()->format('Ymd') . '-001',
            'customer_name' => 'Ma. Theresa Gomez',
            'customer_phone' => '09223344555',
            'service_type' => 'in_shop',
            'home_service_fee' => 0.00,
            'subtotal' => 630.00,
            'discount' => 0.00,
            'total_amount' => 630.00,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'payment_reference' => 'CASH-REC-01',
            'status' => 'completed',
            'created_by' => $admin->id,
            'completed_at' => now()->subHours(2),
            'created_at' => now()->subHours(3),
        ]);

        TransactionItem::create([
            'transaction_id' => $tx1->id,
            'service_id' => $createdServices[0]->id, // Manicure
            'staff_id' => $createdStaff[0]->id, // Ate Nids
            'price' => 150.00,
            'commission_rate' => 40.00,
            'staff_commission_amount' => 60.00,
            'status' => 'completed',
        ]);

        TransactionItem::create([
            'transaction_id' => $tx1->id,
            'service_id' => $createdServices[4]->id, // Foot Spa + Pedi
            'staff_id' => $createdStaff[2]->id, // Tin
            'price' => 450.00,
            'commission_rate' => 40.00,
            'staff_commission_amount' => 180.00,
            'status' => 'completed',
        ]);

        // Transaction 2: Home Service with PayMongo QR (Paid)
        $tx2 = Transaction::create([
            'tracking_number' => 'NT-' . now()->format('Ymd') . '-002',
            'customer_name' => 'Mrs. Carmela Reyes',
            'customer_phone' => '09178889999',
            'service_type' => 'home_service',
            'home_service_address' => 'Phase 3 Block 12 Lot 5, Villa Solita Subd.',
            'home_service_fee' => 150.00,
            'subtotal' => 650.00,
            'discount' => 0.00,
            'total_amount' => 800.00,
            'payment_method' => 'paymongo_qr',
            'payment_status' => 'paid',
            'payment_reference' => 'PM-PAY-984218',
            'status' => 'in_progress',
            'created_by' => $admin->id,
            'created_at' => now()->subHour(),
        ]);

        TransactionItem::create([
            'transaction_id' => $tx2->id,
            'service_id' => $createdServices[8]->id, // Combination Deep Tissue 90m
            'staff_id' => $createdStaff[3]->id, // Jessa
            'price' => 650.00,
            'commission_rate' => 40.00,
            'staff_commission_amount' => 260.00,
            'status' => 'in_progress',
        ]);

        // Transaction 3: Active in salon right now
        $tx3 = Transaction::create([
            'tracking_number' => 'NT-' . now()->format('Ymd') . '-003',
            'customer_name' => 'Bea Soriano',
            'customer_phone' => '09351239874',
            'service_type' => 'in_shop',
            'home_service_fee' => 0.00,
            'subtotal' => 450.00,
            'discount' => 0.00,
            'total_amount' => 450.00,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'in_progress',
            'created_by' => $admin->id,
            'created_at' => now()->subMinutes(20),
        ]);

        TransactionItem::create([
            'transaction_id' => $tx3->id,
            'service_id' => $createdServices[6]->id, // Swedish 60m
            'staff_id' => $createdStaff[1]->id, // Ate Taray
            'price' => 450.00,
            'commission_rate' => 40.00,
            'staff_commission_amount' => 180.00,
            'status' => 'in_progress',
        ]);
    }
}
