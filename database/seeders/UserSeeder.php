<?php

namespace Database\Seeders;

use App\Models\Permission;
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
        $permission_admin = Permission::where('name', 'admin')->first();
        $admin = User::create([
            'full_name'     => 'Admin',
            'phone_number'  => '07713982401',
            'password'      => bcrypt('11111111'),
            'address'       => 'بغداد -شارع فلسطين',
        ]);
        $permission = Permission::first();
        $user = User::create([
            'full_name'     => 'employee 1',
            'phone_number'  => '0771111111',
            'password'      => bcrypt('11111111'),
            'address'       => 'بغداد -شارع فلسطين',
            'salary'        => 500
        ]);
        $user->permissions()->sync($permission);
        $admin->permissions()->sync($permission_admin);
    }
}