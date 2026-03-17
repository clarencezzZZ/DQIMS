<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ModuleSetting;

class ModuleSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = [
            ['module_key' => 'restricted_access', 'module_name' => 'Restricted Modules (Inquiries & Categories)', 'is_enabled' => false],
            ['module_key' => 'users', 'module_name' => 'User Management', 'is_enabled' => true],
            ['module_key' => 'assessments', 'module_name' => 'Assessments', 'is_enabled' => true],
            ['module_key' => 'reports', 'module_name' => 'Reports & Analytics', 'is_enabled' => true],
        ];

        foreach ($modules as $module) {
            $existing = ModuleSetting::where('module_key', $module['module_key'])->first();
            if ($existing) {
                // Force update existing records to match seeder defaults if they exist
                $existing->update($module);
            } else {
                ModuleSetting::create($module);
            }
        }
    }
}
