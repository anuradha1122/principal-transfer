<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the application's roles and permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view admin dashboard',
            'view principal dashboard',
            'view zonal dashboard',
            'view provincial dashboard',
            'view transfer board dashboard',

            // Users and access control
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles and permissions',

            // Organization structure
            'view zones',
            'create zones',
            'edit zones',
            'delete zones',

            'view divisions',
            'create divisions',
            'edit divisions',
            'delete divisions',

            'view schools',
            'create schools',
            'edit schools',
            'delete schools',

            // Principal registry
            'view principal registry',
            'import principal registry',
            'create principal registry',
            'edit principal registry',
            'delete principal registry',

            // Principal profiles
            'view principal profiles',
            'edit own principal profile',
            'manage principal profiles',

            // Transfer applications
            'create transfer applications',
            'view own transfer applications',
            'edit draft transfer applications',
            'submit transfer applications',
            'withdraw transfer applications',

            // transfer cycles
            'view transfer cycles',
            'manage transfer cycles',
            'view transfer applications',
            'create transfer applications',

            // Zonal review
            'view zonal transfer applications',
            'approve zonal transfer applications',
            'reject zonal transfer applications',
            'return zonal transfer applications',

            // Provincial review
            'view provincial transfer applications',
            'approve provincial transfer applications',
            'reject provincial transfer applications',
            'return provincial transfer applications',

            // Transfer board
            'view board transfer applications',
            'record transfer board decisions',
            'edit transfer board decisions',

            // Reports and exports
            'view reports',
            'export reports',
            'export transfer applications',
            'download own transfer documents',

            // Settings
            'manage system settings',
            'view audit logs',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdmin = Role::findOrCreate('Super Admin', 'web');
        $principal = Role::findOrCreate('Principal', 'web');
        $zonalDirector = Role::findOrCreate('Zonal Director', 'web');
        $provincialDirector = Role::findOrCreate(
            'Provincial Director',
            'web'
        );
        $transferBoardMember = Role::findOrCreate(
            'Transfer Board Member',
            'web'
        );
        $dataEntryOfficer = Role::findOrCreate(
            'Data Entry Officer',
            'web'
        );

        $superAdmin->syncPermissions(Permission::all());

        $principal->syncPermissions([
            'view principal dashboard',
            'edit own principal profile',
            'create transfer applications',
            'view own transfer applications',
            'edit draft transfer applications',
            'submit transfer applications',
            'withdraw transfer applications',
            'download own transfer documents',
        ]);

        $zonalDirector->syncPermissions([
            'view zonal dashboard',
            'view zonal transfer applications',
            'approve zonal transfer applications',
            'reject zonal transfer applications',
            'return zonal transfer applications',
            'export transfer applications',
        ]);

        $provincialDirector->syncPermissions([
            'view provincial dashboard',
            'view provincial transfer applications',
            'approve provincial transfer applications',
            'reject provincial transfer applications',
            'return provincial transfer applications',
            'export transfer applications',
            'view reports',
            'export reports',
        ]);

        $transferBoardMember->syncPermissions([
            'view transfer board dashboard',
            'view board transfer applications',
            'record transfer board decisions',
            'edit transfer board decisions',
            'export transfer applications',
            'view reports',
        ]);

        $dataEntryOfficer->syncPermissions([
            'view admin dashboard',
            'view users',
            'view zones',
            'view divisions',
            'view schools',
            'view principal registry',
            'import principal registry',
            'create principal registry',
            'edit principal registry',
            'view principal profiles',
            'manage principal profiles',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
