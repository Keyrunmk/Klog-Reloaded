<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = new Permission();
        $permission->name = "Page Owner";
        $permission->slug = "page-owner";
        $permission->save();

        $role = new Role();
        $role->name = "Owner";
        $role->slug = "owner";
        $role->save();

        $role->permissions()->save($permission);

        $admin = new Admin();
        $admin->first_name = "Kiran";
        $admin->last_name = "Moktan";
        $admin->username = "kiranmk";
        $admin->email = "admin@admin.com";
        $admin->role_id = $role->id;
        $admin->password = Hash::make("password");
        $admin->save();
    }
}
