<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Super Administrator - Full system access, can manage all users including admins
        User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'email' => 'superadmin@denr.gov.ph',
            'password' => bcrypt('superadmin123'),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        // Create Admin user - Full access including assessments and user management
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@denr.gov.ph',
            'password' => bcrypt('admin123'),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        // Create Admin Front Desk user - Can access assessment form and manage inquiries
        User::create([
            'name' => 'Admin Front Desk',
            'username' => 'adminfront',
            'email' => 'adminfront@denr.gov.ph',
            'password' => bcrypt('adminfront123'),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        // Create Ground Floor Front Desk user - Can create inquiries only
        User::create([
            'name' => 'Ground Floor Front Desk',
            'username' => 'groundfloor',
            'email' => 'groundfloor@denr.gov.ph',
            'password' => bcrypt('ground123'),
            'role' => User::ROLE_FRONT_DESK,
            'is_active' => true,
        ]);

        // Create additional Front Desk users
        User::create([
            'name' => 'Front Desk 1',
            'username' => 'frontdesk1',
            'email' => 'frontdesk1@denr.gov.ph',
            'password' => bcrypt('password'),
            'role' => User::ROLE_FRONT_DESK,
            'is_active' => true,
        ]);

        // Create Section Staff users for each category
        $categories = Category::all();

        foreach ($categories as $category) {
            User::create([
                'name' => $category->name . ' Staff',
                'username' => strtolower($category->code),
                'email' => strtolower($category->code) . '@denr.gov.ph',
                'password' => bcrypt('password'),
                'role' => User::ROLE_SECTION_STAFF,
                'assigned_category_id' => $category->id,
                'is_active' => true,
            ]);
        }
    }
}
