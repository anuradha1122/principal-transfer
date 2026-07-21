<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed all application roles and permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        $permissions = [
            /*
            |--------------------------------------------------------------------------
            | Dashboards
            |--------------------------------------------------------------------------
            */

            'view admin dashboard',
            'view principal dashboard',
            'view zonal dashboard',
            'view provincial dashboard',
            'view transfer board dashboard',

            /*
            |--------------------------------------------------------------------------
            | Users and access control
            |--------------------------------------------------------------------------
            */

            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles and permissions',

            /*
            |--------------------------------------------------------------------------
            | Organization structure
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Principal registry
            |--------------------------------------------------------------------------
            */

            'view principal registry',
            'import principal registry',
            'create principal registry',
            'edit principal registry',
            'delete principal registry',

            /*
            |--------------------------------------------------------------------------
            | Principal profiles and appointments
            |--------------------------------------------------------------------------
            */

            'view principal profiles',
            'create principal profiles',
            'edit principal profiles',
            'delete principal profiles',
            'manage principal profiles',

            'edit own principal profile',

            'view own principal appointments',
            'create own principal appointments',
            'edit own principal appointments',
            'delete own principal appointments',

            'view principal appointments',
            'create principal appointments',
            'edit principal appointments',
            'delete principal appointments',

            /*
            |--------------------------------------------------------------------------
            | Transfer cycles
            |--------------------------------------------------------------------------
            */

            'view transfer cycles',
            'manage transfer cycles',

            /*
            |--------------------------------------------------------------------------
            | Principal transfer applications
            |--------------------------------------------------------------------------
            */

            'create transfer applications',
            'view own transfer applications',
            'edit draft transfer applications',
            'submit transfer applications',
            'withdraw transfer applications',
            'delete draft transfer applications',
            'download own transfer application pdfs',

            /*
            |--------------------------------------------------------------------------
            | Administrative transfer application access
            |--------------------------------------------------------------------------
            */

            'view transfer applications',
            'download transfer application pdfs',

            /*
            |--------------------------------------------------------------------------
            | Zonal review
            |--------------------------------------------------------------------------
            */

            'view zonal transfer applications',
            'review zonal transfer applications',
            'approve zonal transfer applications',
            'reject zonal transfer applications',
            'return zonal transfer applications',
            'download zonal transfer application pdfs',

            /*
            |--------------------------------------------------------------------------
            | Provincial review
            |--------------------------------------------------------------------------
            */

            'view provincial transfer applications',
            'review provincial transfer applications',
            'approve provincial transfer applications',
            'reject provincial transfer applications',
            'return provincial transfer applications',
            'download provincial transfer application pdfs',

            /*
            |--------------------------------------------------------------------------
            | Transfer Board
            |--------------------------------------------------------------------------
            */

            'view board transfer applications',
            'review board transfer applications',
            'record transfer board decisions',
            'edit transfer board decisions',
            'download board transfer application pdfs',

            /*
            |--------------------------------------------------------------------------
            | Transfer documents and publication
            |--------------------------------------------------------------------------
            */

            'view transfer documents',
            'generate transfer documents',
            'upload signed transfer documents',
            'publish transfer results',
            'unpublish transfer results',
            'download transfer documents',

            'view own transfer documents',
            'download own transfer documents',

            /*
            |--------------------------------------------------------------------------
            | Transfer appeals - Principal
            |--------------------------------------------------------------------------
            */

            'view own transfer appeals',
            'create transfer appeals',
            'edit draft transfer appeals',
            'submit transfer appeals',
            'withdraw transfer appeals',
            'upload transfer appeal documents',

            /*
            |--------------------------------------------------------------------------
            | Transfer appeals - Provincial and Transfer Board review
            |--------------------------------------------------------------------------
            */

            'view transfer appeals',
            'review transfer appeals',
            'approve transfer appeals',
            'reject transfer appeals',
            'return transfer appeals',
            'download transfer appeal documents',

            /*
            |--------------------------------------------------------------------------
            | Reports and exports
            |--------------------------------------------------------------------------
            */

            'view reports',
            'export reports',
            'export transfer applications',

            /*
            |--------------------------------------------------------------------------
            | Settings and audit
            |--------------------------------------------------------------------------
            */

            'manage system settings',
            'view audit logs',
        ];

        /*
        |--------------------------------------------------------------------------
        | Create permissions
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate(
                $permissionName,
                'web'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create roles
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::findOrCreate(
            'Super Admin',
            'web'
        );

        $principal = Role::findOrCreate(
            'Principal',
            'web'
        );

        $zonalDirector = Role::findOrCreate(
            'Zonal Director',
            'web'
        );

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

        /*
        |--------------------------------------------------------------------------
        | Super Admin permissions
        |--------------------------------------------------------------------------
        */

        $superAdmin->syncPermissions(
            Permission::query()
                ->where(
                    'guard_name',
                    'web'
                )
                ->pluck('name')
                ->all()
        );

        /*
        |--------------------------------------------------------------------------
        | Principal permissions
        |--------------------------------------------------------------------------
        */

        $principal->syncPermissions([
            'view principal dashboard',

            'edit own principal profile',

            'view own principal appointments',
            'create own principal appointments',
            'edit own principal appointments',
            'delete own principal appointments',

            'create transfer applications',
            'view own transfer applications',
            'edit draft transfer applications',
            'submit transfer applications',
            'withdraw transfer applications',
            'delete draft transfer applications',
            'download own transfer application pdfs',

            'view own transfer documents',
            'download own transfer documents',

            /*
            |--------------------------------------------------------------------------
            | Transfer appeals
            |--------------------------------------------------------------------------
            */

            'view own transfer appeals',
            'create transfer appeals',
            'edit draft transfer appeals',
            'submit transfer appeals',
            'withdraw transfer appeals',
            'upload transfer appeal documents',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Zonal Director permissions
        |--------------------------------------------------------------------------
        */

        $zonalDirector->syncPermissions([
            'view zonal dashboard',

            'view zonal transfer applications',
            'review zonal transfer applications',
            'approve zonal transfer applications',
            'reject zonal transfer applications',
            'return zonal transfer applications',
            'download zonal transfer application pdfs',

            'export transfer applications',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Provincial Director permissions
        |--------------------------------------------------------------------------
        */

        $provincialDirector->syncPermissions([
            'view provincial dashboard',

            'view provincial transfer applications',
            'review provincial transfer applications',
            'approve provincial transfer applications',
            'reject provincial transfer applications',
            'return provincial transfer applications',
            'download provincial transfer application pdfs',

            'view transfer documents',
            'generate transfer documents',
            'upload signed transfer documents',
            'download transfer documents',

            /*
            |--------------------------------------------------------------------------
            | Transfer appeals
            |--------------------------------------------------------------------------
            */

            'view transfer appeals',
            'review transfer appeals',
            'approve transfer appeals',
            'reject transfer appeals',
            'return transfer appeals',
            'download transfer appeal documents',

            'export transfer applications',
            'view reports',
            'export reports',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Transfer Board Member permissions
        |--------------------------------------------------------------------------
        */

        $transferBoardMember->syncPermissions([
            'view transfer board dashboard',

            'view board transfer applications',
            'review board transfer applications',
            'record transfer board decisions',
            'edit transfer board decisions',
            'download board transfer application pdfs',

            'view transfer documents',
            'generate transfer documents',
            'upload signed transfer documents',
            'publish transfer results',
            'unpublish transfer results',
            'download transfer documents',

            /*
            |--------------------------------------------------------------------------
            | Transfer appeals
            |--------------------------------------------------------------------------
            */

            'view transfer appeals',
            'review transfer appeals',
            'approve transfer appeals',
            'reject transfer appeals',
            'return transfer appeals',
            'download transfer appeal documents',

            'export transfer applications',
            'view reports',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Data Entry Officer permissions
        |--------------------------------------------------------------------------
        */

        $dataEntryOfficer->syncPermissions([
            'view admin dashboard',

            'view users',

            'view zones',
            'create zones',
            'edit zones',

            'view divisions',
            'create divisions',
            'edit divisions',

            'view schools',
            'create schools',
            'edit schools',

            'view principal registry',
            'import principal registry',
            'create principal registry',
            'edit principal registry',

            'view principal profiles',
            'create principal profiles',
            'edit principal profiles',
            'manage principal profiles',

            'view principal appointments',
            'create principal appointments',
            'edit principal appointments',

            'view transfer cycles',

            'view transfer applications',
            'download transfer application pdfs',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Clear cached permissions
        |--------------------------------------------------------------------------
        */

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }
}
