<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\OfficerOfDay;

class FixStanleyLotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Mr. Stanley M. Lota user if not exists
        $user = User::where('name', 'Mr. Stanley M. Lota')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Mr. Stanley M. Lota',
                'email' => 'stanley.lota@denr.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'section_staff',
                'is_active' => true
            ]);
        }
        
        // Update the officer of day record to link to the user
        $officer = OfficerOfDay::where('name', 'Mr. Stanley M. Lota')->first();
        if ($officer) {
            $officer->update(['user_id' => $user->id]);
        }
        
        // Update existing assessments that have officer_of_day = 1 to use the correct officer
        \App\Models\Assessment::where('officer_of_day', 1)->update(['officer_of_day' => $user->id]);
    }
}
