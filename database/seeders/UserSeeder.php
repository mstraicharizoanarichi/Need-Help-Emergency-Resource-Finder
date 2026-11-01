<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password123');

        // 1. Admin
        User::updateOrCreate(
            ['email' => 'admin@needhelp.bd'],
            [
                'name' => 'System Administrator',
                'phone' => '+8801700000001',
                'role' => 'admin',
                'status' => 'active',
                'organization_name' => 'Need Help Emergency Network Bangladesh',
                'organization_type' => 'Emergency Response Authority',
                'address' => 'Secretariat, Dhaka-1000, Bangladesh',
                'email_verified_at' => now(),
                'password' => $password,
            ]
        );

        // 2. Providers
        $providers = [
            [
                'email' => 'square.hospital@needhelp.bd',
                'name' => 'Dr. Kazi M. Rahman',
                'phone' => '+8801713377775',
                'role' => 'provider',
                'status' => 'active',
                'organization_name' => 'Square Hospitals Ltd.',
                'organization_type' => 'Hospital & Healthcare',
                'address' => '18/F Bir Uttam Qazi Nuruzzaman Sarak, West Panthapath, Dhaka 1205',
            ],
            [
                'email' => 'alif.ambulance@needhelp.bd',
                'name' => 'Md. Al-Amin',
                'phone' => '+8801713205555',
                'role' => 'provider',
                'status' => 'active',
                'organization_name' => 'Alif 24/7 Ambulance Service',
                'organization_type' => 'Emergency Medical Transport',
                'address' => 'Block C, Dhanmondi, Dhaka',
            ],
            [
                'email' => 'tamanna.pharmacy@needhelp.bd',
                'name' => 'Sajjad Hossain',
                'phone' => '+8801819223344',
                'role' => 'provider',
                'status' => 'active',
                'organization_name' => 'Tamanna Pharmacy & Drug Store',
                'organization_type' => 'Retail Pharmacy',
                'address' => 'Road 27, Dhanmondi, Dhaka 1209',
            ],
            [
                'email' => 'labaid.diagnostic@needhelp.bd',
                'name' => 'Dr. Farhana Yasmin',
                'phone' => '+8801766663300',
                'role' => 'provider',
                'status' => 'active',
                'organization_name' => 'Labaid Diagnostic Center',
                'organization_type' => 'Diagnostic Center',
                'address' => 'House 1, Road 4, Dhanmondi, Dhaka 1205',
            ],
            [
                'email' => 'dhanmondi.police@needhelp.bd',
                'name' => 'Duty Officer - Dhanmondi PS',
                'phone' => '+8801713373125',
                'role' => 'provider',
                'status' => 'active',
                'organization_name' => 'Dhanmondi Police Station (DMP)',
                'organization_type' => 'Law Enforcement',
                'address' => 'House 51, Road 9/A, Dhanmondi, Dhaka',
            ],
            [
                'email' => 'pending.provider@needhelp.bd',
                'name' => 'Kamal Uddin (New Clinic)',
                'phone' => '+8801911998877',
                'role' => 'provider',
                'status' => 'pending', // Pending verification demo
                'organization_name' => 'City Care Emergency Clinic',
                'organization_type' => 'Clinic',
                'address' => 'Sector 7, Uttara, Dhaka',
            ],
        ];

        foreach ($providers as $prov) {
            User::updateOrCreate(
                ['email' => $prov['email']],
                array_merge($prov, [
                    'email_verified_at' => now(),
                    'password' => $password,
                ])
            );
        }

        // 3. Normal Users
        $users = [
            [
                'email' => 'citizen@needhelp.bd',
                'name' => 'Rahim Ahmed',
                'phone' => '+8801711223344',
                'role' => 'user',
                'status' => 'active',
                'address' => 'Mirpur-10, Dhaka',
            ],
            [
                'email' => 'user2@needhelp.bd',
                'name' => 'Fatima Begum',
                'phone' => '+8801811556677',
                'role' => 'user',
                'status' => 'active',
                'address' => 'Agrabad, Chattogram',
            ],
            [
                'email' => 'donor.user@needhelp.bd',
                'name' => 'Tanvir Hasan (O+ Donor)',
                'phone' => '+8801911445566',
                'role' => 'user',
                'status' => 'active',
                'address' => 'Dhanmondi, Dhaka',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                array_merge($u, [
                    'email_verified_at' => now(),
                    'password' => $password,
                ])
            );
        }
    }
}
