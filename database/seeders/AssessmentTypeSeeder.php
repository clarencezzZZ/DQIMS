<?php

namespace Database\Seeders;

use App\Models\AssessmentType;
use Illuminate\Database\Seeder;

class AssessmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Cadastral Cost', 'default_amount' => 0.00],
            ['name' => 'Certification: A&D Status', 'default_amount' => 50.00],
            ['name' => 'Certification: Cadastral Map', 'default_amount' => 50.00],
            ['name' => 'Certification Cancellation Of Approved Plan', 'default_amount' => 50.00],
            ['name' => 'Certification GPPC', 'default_amount' => 50.00],
            ['name' => 'Certification Lot Data Computation', 'default_amount' => 50.00],
            ['name' => 'Certification Lot Status', 'default_amount' => 50.00],
            ['name' => 'Certification Rejection Order', 'default_amount' => 50.00],
            ['name' => 'Certification Survey Plan', 'default_amount' => 50.00],
            ['name' => 'Certification: Technical Description', 'default_amount' => 50.00],
            ['name' => 'GE Credit', 'default_amount' => 0.00],
            ['name' => 'Verification Fee', 'default_amount' => 0.00],
            ['name' => 'Inspection Fee', 'default_amount' => 0.00],
        ];

        foreach ($types as $type) {
            AssessmentType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
