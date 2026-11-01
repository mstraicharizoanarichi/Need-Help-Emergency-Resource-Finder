<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $citizen = User::where('email', 'citizen@needhelp.bd')->first();
        $admin = User::where('role', 'admin')->first();
        $tamanna = Resource::where('slug', 'like', '%tamanna-pharmacy%')->first();
        $dmch = Resource::where('slug', 'like', '%dhaka-medical%')->first();

        $reports = [
            [
                'resource_id' => $tamanna?->id,
                'reporter_user_id' => $citizen?->id,
                'reporter_name' => 'Rahim Ahmed',
                'reporter_email' => 'citizen@needhelp.bd',
                'reporter_phone' => '+8801711223344',
                'report_type' => 'incorrect_phone',
                'description' => 'The alternative telephone line 02-9123456 is currently out of order. Direct mobile hotline +8801819223344 works fine.',
                'status' => 'pending',
            ],
            [
                'resource_id' => $dmch?->id,
                'reporter_user_id' => null,
                'reporter_name' => 'Dr. Hasan (Visitor)',
                'reporter_email' => 'hasan.med@gmail.com',
                'reporter_phone' => '+8801714556677',
                'report_type' => 'outdated_info',
                'description' => 'New Sheikh Hasina National Institute of Burn and Plastic Surgery is located directly adjacent to DMCH with 500 burn beds.',
                'status' => 'resolved',
                'admin_notes' => 'Verified and updated details in hospital profile description.',
                'resolved_by' => $admin?->id,
                'resolved_at' => now()->subDay(),
            ],
        ];

        foreach ($reports as $r) {
            if (!empty($r['resource_id'])) {
                Report::updateOrCreate(
                    [
                        'resource_id' => $r['resource_id'],
                        'report_type' => $r['report_type'],
                        'description' => $r['description'],
                    ],
                    $r
                );
            }
        }
    }
}
