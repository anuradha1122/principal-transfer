<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\TransferApplication;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_service_records_workflow_action(): void
    {
        $user = User::factory()->create();

        $application = TransferApplication::factory()->create([
            'status' => 'Draft',
        ]);

        $this->actingAs($user);

        app(AuditLogService::class)->workflow(
            'transfer_application.submitted',
            $application,
            'Draft',
            'Submitted',
            [
                'description' => 'Transfer application was submitted.',
                'user' => $user,
            ]
        );

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'transfer_application.submitted',
            'category' => AuditLog::CATEGORY_WORKFLOW,
            'auditable_type' => TransferApplication::class,
            'auditable_id' => $application->id,
            'user_id' => $user->id,
            'old_status' => 'Draft',
            'new_status' => 'Submitted',
        ]);
    }

    public function test_sensitive_values_are_redacted(): void
    {
        $user = User::factory()->create();

        app(AuditLogService::class)->record(
            'user.password_changed',
            $user,
            [
                'new_values' => [
                    'password' => 'secret-password',
                    'name' => 'Updated Name',
                ],
                'user' => $user,
            ]
        );

        $auditLog = AuditLog::query()->latest('id')->firstOrFail();

        $this->assertSame(
            '[REDACTED]',
            $auditLog->new_values['password']
        );

        $this->assertSame(
            'Updated Name',
            $auditLog->new_values['name']
        );
    }

    public function test_user_without_permission_cannot_view_audit_logs(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get(
                route('admin.audit-logs.index')
            );

        $response->assertForbidden();
    }

    public function test_authorized_user_can_view_audit_logs(): void
    {
        Permission::findOrCreate(
            'view audit logs',
            'web'
        );

        $role = Role::findOrCreate(
            'Super Admin',
            'web'
        );

        $role->givePermissionTo(
            'view audit logs'
        );

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user->assignRole($role);

        $response = $this
            ->actingAs($user)
            ->get(
                route('admin.audit-logs.index')
            );

        $response->assertOk();
    }

    public function test_audit_log_cannot_be_updated_normally(): void
    {
        $auditLog = AuditLog::query()->create([
            'category' => AuditLog::CATEGORY_SYSTEM,
            'event' => 'test.event',
            'description' => 'Original description',
            'occurred_at' => now(),
        ]);

        $result = $auditLog->update([
            'description' => 'Changed description',
        ]);

        $this->assertFalse($result);

        $this->assertDatabaseHas('audit_logs', [
            'id' => $auditLog->id,
            'description' => 'Original description',
        ]);
    }

    public function test_audit_log_cannot_be_deleted_normally(): void
    {
        $auditLog = AuditLog::query()->create([
            'category' => AuditLog::CATEGORY_SYSTEM,
            'event' => 'test.event',
            'description' => 'Protected audit entry',
            'occurred_at' => now(),
        ]);

        $result = $auditLog->delete();

        $this->assertFalse($result);

        $this->assertDatabaseHas('audit_logs', [
            'id' => $auditLog->id,
        ]);
    }
}
