<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();
        $superAdmin = \App\Models\User::factory()->create([
                'username' => 'superadmin',
                'is_superadmin' => true,
        ]);

        $admin = \App\Models\User::factory()->create([
            'username' => 'admin',
        ]);
        
        
        Permission::firstOrCreate(['name' => 'list-user']);
        Permission::firstOrCreate(['name' => 'store-user']);
        Permission::firstOrCreate(['name' => 'view-user']);
        Permission::firstOrCreate(['name' => 'update-user']);
        Permission::firstOrCreate(['name' => 'delete-user']);
        
        Permission::firstOrCreate(['name' => 'list-device']);
        Permission::firstOrCreate(['name' => 'store-device']);
        Permission::firstOrCreate(['name' => 'view-device']);
        Permission::firstOrCreate(['name' => 'update-device']);
        Permission::firstOrCreate(['name' => 'delete-device']);
        
        Permission::firstOrCreate(['name' => 'list-job']);
        Permission::firstOrCreate(['name' => 'store-job']);
        Permission::firstOrCreate(['name' => 'view-job']);
        Permission::firstOrCreate(['name' => 'update-job']);
        Permission::firstOrCreate(['name' => 'delete-job']);
        
        Permission::firstOrCreate(['name' => 'list-dataset']);
        Permission::firstOrCreate(['name' => 'store-dataset']);
        Permission::firstOrCreate(['name' => 'view-dataset']);
        Permission::firstOrCreate(['name' => 'update-dataset']);
        Permission::firstOrCreate(['name' => 'delete-dataset']);
        
        $role = Role::firstOrCreate(['name' => 'user-management']);
        $role->syncPermissions([
            "list-user",
            "store-user",
            "view-user",
            "update-user",
            'delete-user'
        ]);
        $role = Role::firstOrCreate(['name' => 'device-management']);
        $role->syncPermissions([
            "list-device",
            "store-device",
            "view-device",
            "update-device",
            'delete-device'
        ]);
        $role = Role::firstOrCreate(['name' => 'job-management']);
        $role->syncPermissions([
            "list-job",
            "store-job",
            "view-job",
            "update-job",
            'delete-job'
        ]);

        $role = Role::firstOrCreate(['name' => 'dataset-management']);
        $role->syncPermissions([
            "list-dataset",
            "store-dataset",
            "view-dataset",
            "update-dataset",
            'delete-dataset'
        ]);

        $admin->syncPermissions([
            "list-user",
            "store-user",
            "view-user",
            "update-user",
            "list-device",
            "store-device",
            "view-device",
            "update-device",
            "list-job",
            "store-job",
            "view-job",
            "update-job",
            "list-dataset",
            "store-dataset",
            "view-dataset",
            "update-dataset",
        ]);

        // $admin->assignRole('user-management');
        // $admin->assignRole('device-management');
        // $admin->assignRole('job-management');
        // $admin->assignRole('dataset-management');

    }
}
