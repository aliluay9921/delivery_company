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
            'name' => 'admin',
            'name_ar' => 'المدير',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'Company Balance',
            'name_ar' => 'صندوق الشركة',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'Accounter',
            'name_ar' => 'كشف حسابات',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'add employee',
            'name_ar' => 'اضافة موضفين',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'edit employee',
            'name_ar' => 'تعديل موضفين',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'view employee',
            'name_ar' => 'مشاهدة موضفين',
            'active'  => true
        ]);

        Permission::create([
            'name' => 'add driver',
            'name_ar' => 'اضافة مندوبين',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'edit driver',
            'name_ar' => 'تعديل مندوبين',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'view driver',
            'name_ar' => 'مشاهدة مندوبين',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'add customer',
            'name_ar' => 'اضافة عملاء',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'edit customer',
            'name_ar' => 'تعديل عملاء',
            'active'  => true
        ]);
        Permission::create([
            'name' => 'view customer',
            'name_ar' => 'مشاهدة عملاء',
            'active'  => true
        ]);

        Permission::create([
            'name' => 'goods Managment',
            'name_ar' => 'ادارة البضائع',
            'active'  => true
        ]);
    }
}