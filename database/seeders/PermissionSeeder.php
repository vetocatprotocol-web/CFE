<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = [
            'view_dashboard',
            'manage_master_data',
            'manage_users',
            'manage_services',
            'manage_drugs',
            'manage_products',
            'manage_settings',
            'view_reports',
            'manage_stock',
            'manage_billing',
            'create_visits',
            'edit_visits',
            'view_visits',
            'view_customers',
            'process_payments',
            'create_pos_orders',
            'view_invoices',
            'view_reports_assist',
            'manage_users_assist',
        ];

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['description' => 'Permission to ' . str_replace('_', ' ', $permissionName)]
            );
        }

        $rolePermissions = [
            'owner' => [
                'view_dashboard',
                'manage_master_data',
                'manage_users',
                'manage_services',
                'manage_drugs',
                'manage_products',
                'manage_settings',
                'view_reports',
                'manage_stock',
                'manage_billing',
            ],
            'dokter' => [
                'view_dashboard',
                'create_visits',
                'edit_visits',
                'view_visits',
                'view_customers',
                'view_master_data',
            ],
            'kasir' => [
                'view_dashboard',
                'process_payments',
                'create_pos_orders',
                'view_visits',
                'view_invoices',
                'view_billing',
                'view_customers',
            ],
            'admin' => [
                'view_dashboard',
                'manage_users_assist',
                'manage_stock',
                'view_reports_assist',
                'view_master_data',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
                $role->permissions()->sync($permissionIds);
            }
        }
    }
}
