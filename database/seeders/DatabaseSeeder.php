<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{

    /**
     * List of applications to add.
     */
    private $permissions = [
        'role-list',
        'role-create',
        'role-edit',
        'role-delete',
        'permission-list', 
        'user-list',
        'permission-create',
        'assign-permission',
        'assign-role',
        'package-list',
        'package-create',
        'package-edit',
        'package-delete',
        'order-list',
        'wallet-list',
        'wallet-add',
        'user-settings'

    ];
   private $userPermissions = [
        'package-list',
        'wallet-list',
        'wallet-add',
        'user-settings',
        'buy-package'
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $userRole = Role::create(['name' => 'user']);
        foreach ($this->userPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        $userPermissions = Permission::pluck('id', 'id')->all();
        $userRole->syncPermissions($userPermissions);

        //admin
        foreach ($this->permissions as $permission) {
           $old = Permission::where('name',$permission)->first();
           if(!$old){
             Permission::create(['name' => $permission]);
           }
        }

        // Create admin User and assign the role to him.
        $admin = User::create([
            'name' => 'Ahon khan',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678')
        ]);

        $role = Role::create(['name' => 'admin']);

        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);

        $admin->assignRole([$role->id]);

        
    }
}
