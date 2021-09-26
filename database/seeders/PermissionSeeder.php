<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create([
            'name' => 'add_employee',
            'name_ar' => 'اضافة موضفين',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'admin',
            'name_ar' => 'المدير',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'Accounter',
            'name_ar' => 'كشف حسابات',
            'active'  => true
        ]);
    }
}