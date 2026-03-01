<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OfficerOfDay;
use App\Models\User;

class OfficerOfDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add default officer - Mr. Stanley M. Lota
        $lotaUser = User::where('name', 'Mr. Stanley M. Lota')->first();
        
        OfficerOfDay::createOrUpdateOfficer(
            'Mr. Stanley M. Lota', 
            'user', 
            $lotaUser ? $lotaUser->id : null
        );
        
        // Only keep Mr. Stanley M. Lota
        // Do not add any custom officers from assessments to keep the dropdown clean
        // The only options will be Mr. Stanley M. Lota and 'Other'
        
        // We intentionally do not add any custom officers from assessments
        // to maintain the restricted dropdown as requested
        
        // Also remove any existing custom officers to maintain restriction
        \App\Models\OfficerOfDay::where('name', '!=', 'Mr. Stanley M. Lota')->delete();
    }
}
