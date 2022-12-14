<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userPermission = new Permission();
        $userPermission->name = "User Access";
        $userPermission->slug = "user-access";
        $userPermission->save();

        $createCategory = new Permission();
        $createCategory->name = "Create category";
        $createCategory->slug = "create-category";
        $createCategory->save();

        $banUser = new Permission();
        $banUser->name = "Ban User";
        $banUser->slug = "ban-user";
        $banUser->save();

        $admin = new Role();
        $admin->name = "Admin";
        $admin->slug = "admin";
        $admin->save();
        $admin->permissions()->saveMany([$createCategory, $banUser]);

        $user = new Role();
        $user->name = "User";
        $user->slug = "user";
        $user->save();
        $user->permissions()->save($userPermission);

    }
}
