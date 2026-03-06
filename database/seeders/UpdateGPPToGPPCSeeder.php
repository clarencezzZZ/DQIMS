<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateGPPToGPPCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Update 'Certification GPP' to 'Certification GPPC' in categories table
        $updated = DB::table('categories')
            ->where('name', 'Certification GPP')
            ->update(['name' => 'Certification GPPC']);
        
        if ($updated > 0) {
            $this->command->info("Updated {$updated} record(s) from 'Certification GPP' to 'Certification GPPC'");
        } else {
            $this->command->info("No records found with 'Certification GPP'. No updates made.");
        }
    }
}
