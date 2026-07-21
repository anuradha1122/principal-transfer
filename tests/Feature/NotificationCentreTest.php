<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationCentreTest extends TestCase
{
    use RefreshDatabase;

    private User $principal;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Super Admin');
        Role::findOrCreate('Principal');
        Role::findOrCreate('Zonal Director');
        Role::findOrCreate('Provincial Director');
        Role::findOrCreate('Transfer Board Member');
        Role::findOrCreate('Data Entry Officer');

        $this->seed(
            RolePermissionSeeder::class
        );

        $this->principal = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->principal->assignRole(
            'Principal'
        );
    }

    public function test_authenticated_user_can_view_own_notification_centre(): void
    {
        $notificationId = $this->createNotification(
            user: $this->principal,
            title: 'Application Submitted',
            message: 'Your transfer application was submitted.'
        );

        $this
            ->actingAs($this->principal)
            ->get(
                route('notifications.index')
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page): Assert =>
                    $page
                        ->component(
                            'Notifications/Index'
                        )
                        ->where(
                            'counts.all',
                            1
                        )
                        ->where(
                            'counts.unread',
                            1
                        )
                        ->has(
                            'notifications.data',
                            1
                        )
                        ->where(
                            'notifications.data.0.id',
                            $notificationId
                        )
                        ->where(
                            'notifications.data.0.title',
                            'Application Submitted'
                        )
            );
    }

    public function test_notification_centre_reports_correct_unread_count(): void
    {
        $this->createNotification(
            user: $this->principal,
            title: 'Unread One'
        );

        $this->createNotification(
            user: $this->principal,
            title: 'Unread Two'
        );

        $this->createNotification(
            user: $this->principal,
            title: 'Already Read',
            readAt: now()
        );

        $this
            ->actingAs($this->principal)
            ->get(
                route('notifications.index')
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page): Assert =>
                    $page
                        ->where(
                            'counts.all',
                            3
                        )
                        ->where(
                            'counts.unread',
                            2
                        )
                        ->where(
                            'counts.read',
                            1
                        )
                        ->has(
                            'notifications.data',
                            3
                        )
            );
    }

    public function test_user_can_open_own_notification_and_it_is_marked_as_read(): void
    {
        $notificationId = $this->createNotification(
            user: $this->principal,
            title: 'Review Started'
        );

        $this
            ->actingAs($this->principal)
            ->get(
                route(
                    'notifications.show',
                    $notificationId
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page): Assert =>
                    $page
                        ->component(
                            'Notifications/Show'
                        )
                        ->where(
                            'notification.id',
                            $notificationId
                        )
                        ->where(
                            'notification.is_read',
                            true
                        )
            );

        $this->assertDatabaseMissing(
            'notifications',
            [
                'id' => $notificationId,
                'read_at' => null,
            ]
        );
    }

    public function test_user_cannot_open_another_users_notification(): void
    {
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $otherUser->assignRole(
            'Principal'
        );

        $notificationId = $this->createNotification(
            user: $otherUser,
            title: 'Private Notification'
        );

        $this
            ->actingAs($this->principal)
            ->get(
                route(
                    'notifications.show',
                    $notificationId
                )
            )
            ->assertNotFound();
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $notificationId = $this->createNotification(
            user: $this->principal,
            title: 'Unread Notification'
        );

        $this
            ->actingAs($this->principal)
            ->post(
                route(
                    'notifications.read',
                    $notificationId
                )
            )
            ->assertRedirect();

        $this->assertNotNull(
            DB::table('notifications')
                ->where(
                    'id',
                    $notificationId
                )
                ->value('read_at')
        );
    }

    public function test_user_can_mark_notification_as_unread(): void
    {
        $notificationId = $this->createNotification(
            user: $this->principal,
            title: 'Read Notification',
            readAt: now()
        );

        $this
            ->actingAs($this->principal)
            ->post(
                route(
                    'notifications.unread',
                    $notificationId
                )
            )
            ->assertRedirect();

        $this->assertNull(
            DB::table('notifications')
                ->where(
                    'id',
                    $notificationId
                )
                ->value('read_at')
        );
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $firstNotificationId =
            $this->createNotification(
                user: $this->principal,
                title: 'First Notification'
            );

        $secondNotificationId =
            $this->createNotification(
                user: $this->principal,
                title: 'Second Notification'
            );

        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $otherUser->assignRole(
            'Principal'
        );

        $otherNotificationId =
            $this->createNotification(
                user: $otherUser,
                title: 'Other User Notification'
            );

        $this
            ->actingAs($this->principal)
            ->post(
                route(
                    'notifications.mark-all-as-read'
                )
            )
            ->assertRedirect();

        $this->assertNotNull(
            DB::table('notifications')
                ->where(
                    'id',
                    $firstNotificationId
                )
                ->value('read_at')
        );

        $this->assertNotNull(
            DB::table('notifications')
                ->where(
                    'id',
                    $secondNotificationId
                )
                ->value('read_at')
        );

        $this->assertNull(
            DB::table('notifications')
                ->where(
                    'id',
                    $otherNotificationId
                )
                ->value('read_at')
        );
    }

    public function test_user_can_delete_own_notification(): void
    {
        $notificationId = $this->createNotification(
            user: $this->principal,
            title: 'Delete Me'
        );

        $this
            ->actingAs($this->principal)
            ->delete(
                route(
                    'notifications.destroy',
                    $notificationId
                )
            )
            ->assertRedirect();

        $this->assertDatabaseMissing(
            'notifications',
            [
                'id' => $notificationId,
            ]
        );
    }

    public function test_user_cannot_delete_another_users_notification(): void
    {
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $otherUser->assignRole(
            'Principal'
        );

        $notificationId = $this->createNotification(
            user: $otherUser,
            title: 'Do Not Delete'
        );

        $this
            ->actingAs($this->principal)
            ->delete(
                route(
                    'notifications.destroy',
                    $notificationId
                )
            )
            ->assertNotFound();

        $this->assertDatabaseHas(
            'notifications',
            [
                'id' => $notificationId,
            ]
        );
    }

    public function test_user_can_clear_only_their_read_notifications(): void
    {
        $readNotificationId =
            $this->createNotification(
                user: $this->principal,
                title: 'Read Notification',
                readAt: now()
            );

        $unreadNotificationId =
            $this->createNotification(
                user: $this->principal,
                title: 'Unread Notification'
            );

        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $otherUser->assignRole(
            'Principal'
        );

        $otherReadNotificationId =
            $this->createNotification(
                user: $otherUser,
                title: 'Other Read Notification',
                readAt: now()
            );

        $this
            ->actingAs($this->principal)
            ->delete(
                route(
                    'notifications.clear-read'
                )
            )
            ->assertRedirect();

        $this->assertDatabaseMissing(
            'notifications',
            [
                'id' =>
                    $readNotificationId,
            ]
        );

        $this->assertDatabaseHas(
            'notifications',
            [
                'id' =>
                    $unreadNotificationId,
            ]
        );

        $this->assertDatabaseHas(
            'notifications',
            [
                'id' =>
                    $otherReadNotificationId,
            ]
        );
    }

    public function test_user_without_notification_permission_receives_forbidden_response(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this
            ->actingAs($user)
            ->get(
                route(
                    'notifications.index'
                )
            )
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this
            ->get(
                route(
                    'notifications.index'
                )
            )
            ->assertRedirect(
                route('login')
            );
    }

    private function createNotification(
        User $user,
        string $title = 'Test Notification',
        string $message = 'This is a notification test.',
        string $category = 'system',
        string $severity = 'info',
        mixed $readAt = null
    ): string {
        $notificationId =
            (string) Str::uuid();

        DB::table('notifications')
            ->insert([
                'id' =>
                    $notificationId,

                'type' =>
                    'App\\Notifications\\AdministrativeAlertNotification',

                'notifiable_type' =>
                    User::class,

                'notifiable_id' =>
                    $user->id,

                'data' =>
                    json_encode([
                        'title' =>
                            $title,

                        'message' =>
                            $message,

                        'category' =>
                            $category,

                        'severity' =>
                            $severity,

                        'action_url' =>
                            route(
                                'notifications.index'
                            ),

                        'action_label' =>
                            'View Notifications',

                        'metadata' => [
                            'test' => true,
                        ],
                    ], JSON_THROW_ON_ERROR),

                'read_at' =>
                    $readAt,

                'created_at' =>
                    now(),

                'updated_at' =>
                    now(),
            ]);

        return $notificationId;
    }
}
