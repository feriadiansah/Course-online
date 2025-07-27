<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create([
            'name' => 'admin'
        ]);
        $studentRole = Role::create([
            'name' => 'student'
        ]);
        $mentorRole = Role::create([
            'name' => 'mentor'
        ]);

        $user = User::create([
            'name' => 'Team miracle',
            'email' => 'miracle@admin.com',
            'password' => bcrypt('miracle123#')
        ]);
        $user->assignRole($adminRole);
    }
}
