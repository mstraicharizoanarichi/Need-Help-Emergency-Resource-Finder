<?php

namespace Database\Seeders;

use App\Models\ResourceCategory;
use Illuminate\Database\Seeder;

class ResourceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Hospital',
                'slug' => 'hospital',
                'icon' => 'hospital',
                'color' => 'red',
                'description' => 'General hospitals, specialized clinics, emergency rooms, trauma centers, and ICU facilities.',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Ambulance',
                'slug' => 'ambulance',
                'icon' => 'ambulance',
                'color' => 'orange',
                'description' => 'Emergency 24/7 ambulance services, ICU ambulances, air ambulance, and patient transport.',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Pharmacy',
                'slug' => 'pharmacy',
                'icon' => 'pill',
                'color' => 'emerald',
                'description' => '24/7 medicine stores, local chemists, surgical suppliers, and life-saving drug stockists.',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Blood Donor',
                'slug' => 'blood-donor',
                'icon' => 'droplet',
                'color' => 'rose',
                'description' => 'Voluntary blood donors, donor clubs, and emergency blood group match finder.',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Police Station',
                'slug' => 'police-station',
                'icon' => 'shield',
                'color' => 'blue',
                'description' => 'Thana / police stations, patrol outposts, highway police, and emergency law enforcement.',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Fire Service',
                'slug' => 'fire-service',
                'icon' => 'flame',
                'color' => 'amber',
                'description' => 'Fire stations, civil defence rescue teams, flood & natural disaster response units.',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Diagnostic Center',
                'slug' => 'diagnostic-center',
                'icon' => 'microscope',
                'color' => 'purple',
                'description' => 'Emergency pathology labs, X-ray, MRI, CT scan, blood tests, and ultrasound diagnostics.',
                'display_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            ResourceCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
