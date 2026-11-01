<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use App\Models\User;
use Illuminate\Database\Seeder;

class BloodDonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donorUser = User::where('email', 'donor.user@needhelp.bd')->first();

        $donors = [
            // O+ Donors (Most Common & Universal Red Blood Provider)
            [
                'user_id' => $donorUser?->id,
                'full_name' => 'Tanvir Hasan',
                'blood_group' => 'O+',
                'phone' => '+8801911445566',
                'alternative_phone' => '+8801711223399',
                'email' => 'tanvir.donor@gmail.com',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Dhanmondi',
                'latitude' => 23.7465000,
                'longitude' => 90.3735000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(4)->format('Y-m-d'),
                'total_donations' => 12,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
                'notes' => 'Available for emergency blood donation within Dhaka city anytime.',
            ],
            [
                'full_name' => 'Md. Shakil Hossain',
                'blood_group' => 'O+',
                'phone' => '+8801722334455',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Shahbagh',
                'latitude' => 23.7380000,
                'longitude' => 90.3950000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(5)->format('Y-m-d'),
                'total_donations' => 8,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],
            [
                'full_name' => 'Imtiaz Ahmed',
                'blood_group' => 'O+',
                'phone' => '+8801833445566',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Gulshan',
                'latitude' => 23.7925000,
                'longitude' => 90.4140000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(2)->format('Y-m-d'), // not eligible yet (<90 days)
                'total_donations' => 5,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],
            [
                'full_name' => 'Arifur Rahman',
                'blood_group' => 'O+',
                'phone' => '+8801715566778',
                'district' => 'Chattogram',
                'city' => 'Chattogram',
                'area' => 'Agrabad',
                'latitude' => 22.3260000,
                'longitude' => 91.8130000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(6)->format('Y-m-d'),
                'total_donations' => 14,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],

            // O- Donors (Universal Donor - Rare)
            [
                'full_name' => 'Nusrat Jahan',
                'blood_group' => 'O-',
                'phone' => '+8801912889900',
                'email' => 'nusrat.j@example.com',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Panthapath',
                'latitude' => 23.7515000,
                'longitude' => 90.3840000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(7)->format('Y-m-d'),
                'total_donations' => 9,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
                'notes' => 'Rare O Negative donor. Ready for critical neonate/trauma emergency calls.',
            ],
            [
                'full_name' => 'Fahim Morshed',
                'blood_group' => 'O-',
                'phone' => '+8801677889900',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Uttara',
                'latitude' => 23.8720000,
                'longitude' => 90.3990000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(4)->format('Y-m-d'),
                'total_donations' => 6,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],

            // A+ Donors
            [
                'full_name' => 'Sabbir Chowdhury',
                'blood_group' => 'A+',
                'phone' => '+8801718882233',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Mirpur',
                'latitude' => 23.8060000,
                'longitude' => 90.3680000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(5)->format('Y-m-d'),
                'total_donations' => 11,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],
            [
                'full_name' => 'Sadia Akter',
                'blood_group' => 'A+',
                'phone' => '+8801817773344',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Kalabagan',
                'latitude' => 23.7485000,
                'longitude' => 90.3800000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(8)->format('Y-m-d'),
                'total_donations' => 4,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],

            // A- Donors (Rare)
            [
                'full_name' => 'Mahmudul Karim',
                'blood_group' => 'A-',
                'phone' => '+8801915554433',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Mohammadpur',
                'latitude' => 23.7580000,
                'longitude' => 90.3620000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(6)->format('Y-m-d'),
                'total_donations' => 7,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],

            // B+ Donors
            [
                'full_name' => 'Rakibul Islam',
                'blood_group' => 'B+',
                'phone' => '+8801719998877',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Banani',
                'latitude' => 23.7930000,
                'longitude' => 90.4045000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(4)->format('Y-m-d'),
                'total_donations' => 15,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],
            [
                'full_name' => 'Nayeem Siddiqui',
                'blood_group' => 'B+',
                'phone' => '+8801816665544',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Tejgaon',
                'latitude' => 23.7620000,
                'longitude' => 90.3950000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(3)->format('Y-m-d'),
                'total_donations' => 3,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],

            // B- Donors (Rare)
            [
                'full_name' => 'Tahmidur Rahman',
                'blood_group' => 'B-',
                'phone' => '+8801671112233',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Dhanmondi',
                'latitude' => 23.7420000,
                'longitude' => 90.3780000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(5)->format('Y-m-d'),
                'total_donations' => 8,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],

            // AB+ Donors (Universal Plasma Donor)
            [
                'full_name' => 'Rezaul Haque',
                'blood_group' => 'AB+',
                'phone' => '+8801712229988',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Farmgate',
                'latitude' => 23.7570000,
                'longitude' => 90.3890000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(4)->format('Y-m-d'),
                'total_donations' => 10,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],

            // AB- Donors (Rarest Blood Group)
            [
                'full_name' => 'Anwar Hossain',
                'blood_group' => 'AB-',
                'phone' => '+8801819990011',
                'district' => 'Dhaka',
                'city' => 'Dhaka',
                'area' => 'Shahbagh',
                'latitude' => 23.7370000,
                'longitude' => 90.3985000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(7)->format('Y-m-d'),
                'total_donations' => 18,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
                'notes' => 'Extremely rare AB- donor. Registered volunteer with Sandhani and Red Crescent.',
            ],
            [
                'full_name' => 'Farhan Labib',
                'blood_group' => 'AB-',
                'phone' => '+8801716664422',
                'district' => 'Sylhet',
                'city' => 'Sylhet',
                'area' => 'Zindabazar',
                'latitude' => 24.8970000,
                'longitude' => 91.8710000,
                'is_available' => true,
                'last_donation_date' => now()->subMonths(4)->format('Y-m-d'),
                'total_donations' => 5,
                'verification_status' => 'verified',
                'privacy_setting' => 'contact_request',
            ],
        ];

        foreach ($donors as $donorData) {
            BloodDonor::updateOrCreate(
                ['phone' => $donorData['phone']],
                $donorData
            );
        }
    }
}
