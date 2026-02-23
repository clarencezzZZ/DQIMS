<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'code' => 'SECSIME NO.R4A-L_SMD-01',
                'section' => 'ACS',
                'name' => 'CANCELATION OF PREVIOUSLY APPROVED SURVEY PLANS(DAR)',
                'description' => 'SECSIME NO.R4A-L_SMD-01. CANCELATION OF PREVIOUSLY APPROVED SURVEY PLANS(DAR)',
                'color' => '#e74c3c',
                'is_active' => true,
            ],
            [
                'code' => 'SECSIME NO.R4A-L_SMD-02',
                'section' => 'ACS',
                'name' => 'AMENDENT OF PREVIOUSLY APPROVED SURVEY PLANS,CADASTRAL MAPS, LOT DATA COMPUTATIONS AND LOT DESCRIPTIONS',
                'description' => 'ACS - SECSIME NO.R4A-L_SMD-02. AMENDENT OF PREVIOUSLY APPROVED SURVEY PLANS,CADASTRAL MAPS, LOT DATA COMPUTATIONS AND LOT DESCRIPTIONS',
                'color' => '#e74c3c',
                'is_active' => true,
            ],
            [
                'code' => 'SECSIME NO.R4A-L_SMD-03',
                'section' => 'OOSS',
                'name' => 'CANCELATION OF PREVIOUSLY APPROVED SURVEY PLANS',
                'description' => 'OOSS - SECSIME NO.R4A-L_SMD-03. CANCELATION OF PREVIOUSLY APPROVED SURVEY PLANS',
                'color' => '#3498db',
                'is_active' => true,
            ],
            [
                'code' => 'SECSIME NO.R4A-L_SMD-04',
                'section' => 'ACS',
                'name' => 'ISSUANCE OF CERTIFIED CORRECTED TECHNICAL DESCRIPTION OF PREVIUOSLY APPROVED SURVEY PLAN',
                'description' => 'ACS - SECSIME NO.R4A-L_SMD-04. ISSUANCE OF CERTIFIED CORRECTED TECHNICAL DESCRIPTION OF PREVIUOSLY APPROVED SURVEY PLAN',
                'color' => '#e74c3c',
                'is_active' => true,
            ],
            [
                'code' => 'SECIME NO.R4A-L_SMD-05',
                'section' => 'RECORDS',
                'name' => 'ISSUANCE OF CERTIFIED TRUE COPY or PLAN COPY OF LAND RECORDS',
                'description' => 'RECORDS - SECIME NO.R4A-L_SMD-05. ISSUANCE OF CERTIFIED TRUE COPY or PLAN COPY OF LAND RECORDS',
                'color' => '#f39c12',
                'is_active' => true,
            ],
            [
                'code' => 'SECIME NO.R4A-L_SMD-06',
                'section' => 'RECORDS',
                'name' => 'ISSUANCE OF CERTIFICATE OF NO SURVEY RECORDS',
                'description' => 'RECORDS - SECIME NO.R4A-L_SMD-06. ISSUANCE OF CERTIFICATE OF NO SURVEY RECORDS',
                'color' => '#f39c12',
                'is_active' => true,
            ],
            [
                'code' => 'SECIME NO.R4A-L_SMD-07',
                'section' => 'OOSS',
                'name' => 'ISSUANCE OF APPROVED SURVEY PLAN(LAMS-IVAS)',
                'description' => 'OOSS - SECIME NO.R4A-L_SMD-07. ISSUANCE OF APPROVED SURVEY PLAN(LAMS-IVAS)',
                'color' => '#3498db',
                'is_active' => true,
            ],
            [
                'code' => 'SECIME NO.R4A-L_SMD-08',
                'section' => 'OOSS',
                'name' => 'ISSUANCE OF CERTIFICATION OF REJECTION OF UNAPPROVED SURVEY PLANS',
                'description' => 'OOSS - SECIME NO.R4A-L_SMD-08. ISSUANCE OF CERTIFICATION OF REJECTION OF UNAPPROVED SURVEY PLANS',
                'color' => '#3498db',
                'is_active' => true,
            ],
            [
                'code' => 'SECIME NO.R4A-L_SMD-09',
                'section' => 'OOSS',
                'name' => 'ISSUANCE OF CERTIFICATION OF STATUS OF LOT OR PLAN(if with previously approved)',
                'description' => 'OOSS - SECIME NO.R4A-L_SMD-09. ISSUANCE OF CERTIFICATION OF STATUS OF LOT OR PLAN(if with previously approved)',
                'color' => '#3498db',
                'is_active' => true,
            ],
            [
                'code' => 'SECIME NO.R4A-L_SMD-10',
                'section' => 'SCS',
                'name' => 'ISSUANCE OF CERTIFICATION OF GEOGRAPHIC POSITION PLANE or GRID COORDINATES',
                'description' => 'SCS - SECIME NO.R4A-L_SMD-10. ISSUANCE OF CERTIFICATION OF GEOGRAPHIC POSITION PLANE or GRID COORDINATES',
                'color' => '#2ecc71',
                'is_active' => true,
            ],
            [
                'code' => 'SECSIME NO.R4A-L_SMD-011',
                'section' => 'LES',
                'name' => 'ISSUANCE OF ALIENABLE AND DISPOSABLE',
                'description' => 'LES - SECSIME NO.R4A-L_SMD-011. ISSUANCE OF ALIENABLE AND DISPOSABLE',
                'color' => '#9b59b6',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
