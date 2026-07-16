<?php

namespace Tests\Feature\Auth;

use App\Models\PrincipalRegistry;
use App\Models\User;
use App\Services\NicService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NicControlledRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_nic_service_normalizes_supported_formats(): void
    {
        $service = app(NicService::class);

        $this->assertSame(
            '123456789V',
            $service->normalize(
                '123 456 789 v'
            )
        );

        $this->assertTrue(
            $service->isValidFormat(
                '123456789V'
            )
        );

        $this->assertTrue(
            $service->isValidFormat(
                '200012345678'
            )
        );

        $this->assertFalse(
            $service->isValidFormat(
                '12345'
            )
        );
    }

    public function test_unknown_nic_cannot_continue_registration(): void
    {
        $response = $this->post(
            '/principal-registration/verify-nic',
            [
                'nic' => '123456789V',
            ]
        );

        $response->assertSessionHasErrors(
            'nic'
        );
    }

    public function test_disabled_nic_cannot_continue_registration(): void
    {
        PrincipalRegistry::create([
            'nic' => '123456789V',
            'normalized_nic' => '123456789V',
            'registration_status' => 'disabled',
            'is_active' => false,
        ]);

        $response = $this->post(
            '/principal-registration/verify-nic',
            [
                'nic' => '123456789V',
            ]
        );

        $response->assertSessionHasErrors(
            'nic'
        );
    }

    public function test_unused_registry_nic_can_be_verified(): void
    {
        PrincipalRegistry::create([
            'nic' => '123456789V',
            'normalized_nic' => '123456789V',
            'registration_status' => 'unregistered',
            'is_active' => true,
        ]);

        $response = $this->post(
            '/principal-registration/verify-nic',
            [
                'nic' => '123 456 789 v',
            ]
        );

        $response->assertRedirect();

        $this->assertNotNull(
            session('principal_registration')
        );
    }

    public function test_verified_principal_can_register(): void
    {
        $registry = PrincipalRegistry::create([
            'nic' => '123456789V',
            'normalized_nic' => '123456789V',
            'registration_status' => 'unregistered',
            'is_active' => true,
        ]);

        $token = Str::random(64);

        $response = $this
            ->withSession([
                'principal_registration' => [
                    'registry_id' => $registry->id,
                    'normalized_nic' => '123456789V',
                    'token_hash' => hash(
                        'sha256',
                        $token
                    ),
                    'verified_at' => now()->timestamp,
                ],
            ])
            ->post(
                '/principal-registration',
                [
                    'token' => $token,
                    'name' => 'Registered Principal',
                    'email' => 'principal@example.com',
                    'password' => 'Password123',
                    'password_confirmation' => 'Password123',
                    'declaration' => true,
                ]
            );

        $response->assertRedirect(
            '/verify-email'
        );

        $this->assertAuthenticated();

        $user = User::query()
            ->where(
                'email',
                'principal@example.com'
            )
            ->firstOrFail();

        $this->assertTrue(
            $user->hasRole('Principal')
        );

        $this->assertDatabaseHas(
            'principal_registries',
            [
                'id' => $registry->id,
                'registered_user_id' => $user->id,
                'registration_status' => 'registered',
            ]
        );
    }

    public function test_same_nic_cannot_register_twice(): void
    {
        $existingUser =
            User::factory()->create();

        $existingUser->assignRole(
            'Principal'
        );

        PrincipalRegistry::create([
            'nic' => '123456789V',
            'normalized_nic' => '123456789V',
            'registration_status' => 'registered',
            'registered_user_id' => $existingUser->id,
            'registered_at' => now(),
            'is_active' => true,
        ]);

        $response = $this->post(
            '/principal-registration/verify-nic',
            [
                'nic' => '123456789V',
            ]
        );

        $response->assertSessionHasErrors(
            'nic'
        );
    }
}
