<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add Mr. Stanly M. Lota as an officer
        User::updateOrCreate([
            'email' => 'stanly.lota@denr.gov.ph',
        ], [
            'name' => 'Mr. Stanly M. Lota',
            'username' => 's.lota',
            'email' => 'stanly.lota@denr.gov.ph',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SECTION_STAFF,
            'is_active' => true,
        ]);
    }
}