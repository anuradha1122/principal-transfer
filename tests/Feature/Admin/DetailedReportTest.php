<?php

namespace Tests\Feature\Admin;

use App\Models\PrincipalProfile;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DetailedReportTest extends TestCase
{
    use RefreshDatabase;

    private Role $zonalDirectorRole;

    private Role $provincialDirectorRole;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        Permission::findOrCreate(
            'view reports',
            'web'
        );

        Permission::findOrCreate(
            'export reports',
            'web'
        );

        Permission::findOrCreate(
            'export transfer applications',
            'web'
        );

        $this->zonalDirectorRole =
            Role::findOrCreate(
                'Zonal Director',
                'web'
            );

        $this->provincialDirectorRole =
            Role::findOrCreate(
                'Provincial Director',
                'web'
            );

        $this->zonalDirectorRole
            ->syncPermissions([
                'view reports',
                'export transfer applications',
            ]);

        $this->provincialDirectorRole
            ->syncPermissions([
                'view reports',
                'export reports',
            ]);

        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }

    public function test_user_without_permission_cannot_view_detailed_reports(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.applications'
                )
            )
            ->assertForbidden();

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.decisions'
                )
            )
            ->assertForbidden();

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.appeals'
                )
            )
            ->assertForbidden();

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.documents'
                )
            )
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_all_detailed_report_pages(): void
    {
        $user = $this->createProvincialDirector();

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.applications'
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'Admin/Reports/Applications'
                    )
                    ->has('rows')
                    ->has('filters')
                    ->has('transferCycles')
                    ->has('zones')
                    ->has('statuses')
            );

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.decisions'
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'Admin/Reports/Decisions'
                    )
                    ->has('rows')
                    ->has('filters')
            );

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.appeals'
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'Admin/Reports/Appeals'
                    )
                    ->has('rows')
                    ->has('filters')
            );

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.documents'
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'Admin/Reports/Documents'
                    )
                    ->has('rows')
                    ->has('filters')
                    ->has('documentTypes')
            );
    }

    public function test_zonal_director_only_sees_applications_from_assigned_zone(): void
    {
        $assignedZone = Zone::factory()->create([
            'name' => 'Ratnapura Zone',
        ]);

        $otherZone = Zone::factory()->create([
            'name' => 'Kegalle Zone',
        ]);

        $user = $this->createZonalDirector(
            $assignedZone
        );

        $cycle = TransferCycle::factory()->create();

        $visibleApplication =
            $this->createTransferApplication(
                $assignedZone,
                $cycle,
                [
                    'application_number' => 'APP-ZONE-001',

                    'status' => TransferApplication::STATUS_SUBMITTED,
                ]
            );

        $hiddenApplication =
            $this->createTransferApplication(
                $otherZone,
                $cycle,
                [
                    'application_number' => 'APP-ZONE-002',

                    'status' => TransferApplication::STATUS_SUBMITTED,
                ]
            );

        $response = $this
            ->actingAs($user)
            ->get(
                route(
                    'admin.reports.applications'
                )
            );

        $response
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'Admin/Reports/Applications'
                    )
                    ->where(
                        'scope.is_zonal',
                        true
                    )
                    ->has(
                        'rows.data',
                        1
                    )
                    ->where(
                        'rows.data.0.id',
                        $visibleApplication->id
                    )
                    ->where(
                        'rows.data.0.application_number',
                        'APP-ZONE-001'
                    )
            );

        $response->assertDontSee(
            $hiddenApplication
                ->application_number
        );
    }

    public function test_application_report_can_filter_by_status(): void
    {
        $user = $this->createProvincialDirector();

        $zone = Zone::factory()->create();

        $cycle = TransferCycle::factory()->create();

        $approvedApplication =
            $this->createTransferApplication(
                $zone,
                $cycle,
                [
                    'application_number' => 'APP-APPROVED-001',

                    'status' => TransferApplication::STATUS_APPROVED,
                ]
            );

        $this->createTransferApplication(
            $zone,
            $cycle,
            [
                'application_number' => 'APP-REJECTED-001',

                'status' => TransferApplication::STATUS_REJECTED,
            ]
        );

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.applications',
                    [
                        'status' => TransferApplication::STATUS_APPROVED,
                    ]
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->where(
                        'filters.status',
                        TransferApplication::STATUS_APPROVED
                    )
                    ->has(
                        'rows.data',
                        1
                    )
                    ->where(
                        'rows.data.0.id',
                        $approvedApplication->id
                    )
                    ->where(
                        'rows.data.0.status',
                        TransferApplication::STATUS_APPROVED
                    )
            );
    }

    public function test_application_report_can_filter_by_transfer_cycle(): void
    {
        $user = $this->createProvincialDirector();

        $zone = Zone::factory()->create();

        $selectedCycle =
            TransferCycle::factory()->create([
                'name' => '2026 Annual Transfer',
            ]);

        $otherCycle =
            TransferCycle::factory()->create([
                'name' => '2027 Annual Transfer',
            ]);

        $matchingApplication =
            $this->createTransferApplication(
                $zone,
                $selectedCycle,
                [
                    'application_number' => 'APP-CYCLE-2026',
                ]
            );

        $this->createTransferApplication(
            $zone,
            $otherCycle,
            [
                'application_number' => 'APP-CYCLE-2027',
            ]
        );

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.applications',
                    [
                        'transfer_cycle_id' => $selectedCycle->id,
                    ]
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->where(
                        'filters.transfer_cycle_id',
                        (string) $selectedCycle->id
                    )
                    ->has(
                        'rows.data',
                        1
                    )
                    ->where(
                        'rows.data.0.id',
                        $matchingApplication->id
                    )
                    ->where(
                        'rows.data.0.cycle',
                        '2026 Annual Transfer'
                    )
            );
    }

    public function test_application_report_can_filter_by_zone(): void
    {
        $user = $this->createProvincialDirector();

        $selectedZone =
            Zone::factory()->create([
                'name' => 'Balangoda Zone',
            ]);

        $otherZone =
            Zone::factory()->create([
                'name' => 'Mawanella Zone',
            ]);

        $cycle = TransferCycle::factory()->create();

        $matchingApplication =
            $this->createTransferApplication(
                $selectedZone,
                $cycle,
                [
                    'application_number' => 'APP-BALANGODA',
                ]
            );

        $this->createTransferApplication(
            $otherZone,
            $cycle,
            [
                'application_number' => 'APP-MAWANELLA',
            ]
        );

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.applications',
                    [
                        'zone_id' => $selectedZone->id,
                    ]
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->where(
                        'filters.zone_id',
                        (string) $selectedZone->id
                    )
                    ->has(
                        'rows.data',
                        1
                    )
                    ->where(
                        'rows.data.0.id',
                        $matchingApplication->id
                    )
                    ->where(
                        'rows.data.0.zone',
                        'Balangoda Zone'
                    )
            );
    }

    public function test_application_report_can_filter_by_date_range(): void
    {
        $user = $this->createProvincialDirector();

        $zone = Zone::factory()->create();

        $cycle = TransferCycle::factory()->create();

        $matchingApplication =
            $this->createTransferApplication(
                $zone,
                $cycle,
                [
                    'application_number' => 'APP-DATE-001',

                    'created_at' => '2026-07-10 09:00:00',
                ]
            );

        $this->createTransferApplication(
            $zone,
            $cycle,
            [
                'application_number' => 'APP-DATE-OLD',

                'created_at' => '2026-06-01 09:00:00',
            ]
        );

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.applications',
                    [
                        'date_from' => '2026-07-01',

                        'date_to' => '2026-07-31',
                    ]
                )
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->has(
                        'rows.data',
                        1
                    )
                    ->where(
                        'rows.data.0.id',
                        $matchingApplication->id
                    )
            );
    }

    public function test_user_with_view_permission_but_without_export_permission_cannot_export_reports(): void
    {
        $user = User::factory()->create();

        $user->givePermissionTo(
            'view reports'
        );

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.applications.pdf'
                )
            )
            ->assertForbidden();

        $this->actingAs($user)
            ->get(
                route(
                    'admin.reports.applications.excel'
                )
            )
            ->assertForbidden();
    }

    #[DataProvider('pdfExportRouteProvider')]
    public function test_authorized_user_can_download_pdf_reports(
        string $routeName,
        string $expectedFilename
    ): void {
        $user = $this->createProvincialDirector();

        $response = $this
            ->actingAs($user)
            ->get(
                route($routeName)
            );

        $response->assertOk();

        $response->assertHeader(
            'content-type',
            'application/pdf'
        );

        $contentDisposition =
            $response->headers->get(
                'content-disposition'
            );

        $this->assertNotNull(
            $contentDisposition
        );

        $this->assertStringContainsString(
            $expectedFilename,
            $contentDisposition
        );
    }

    #[DataProvider('excelExportRouteProvider')]
    public function test_authorized_user_can_download_excel_reports(
        string $routeName,
        string $expectedFilename
    ): void {
        $user = $this->createProvincialDirector();

        $response = $this
            ->actingAs($user)
            ->get(
                route($routeName)
            );

        $response->assertOk();

        $contentType = (string) $response
            ->headers
            ->get('content-type');

        $this->assertTrue(
            str_contains(
                $contentType,
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            )
            || str_contains(
                $contentType,
                'application/zip'
            ),
            sprintf(
                'Unexpected Excel content type: %s',
                $contentType
            )
        );

        $contentDisposition =
            $response->headers->get(
                'content-disposition'
            );

        $this->assertNotNull(
            $contentDisposition
        );

        $this->assertStringContainsString(
            $expectedFilename,
            $contentDisposition
        );
    }

    public static function pdfExportRouteProvider(): array
    {
        return [
            'application report' => [
                'admin.reports.applications.pdf',
                'transfer-applications-report.pdf',
            ],

            'decision report' => [
                'admin.reports.decisions.pdf',
                'transfer-decisions-report.pdf',
            ],

            'appeal report' => [
                'admin.reports.appeals.pdf',
                'transfer-appeals-report.pdf',
            ],

            'document report' => [
                'admin.reports.documents.pdf',
                'transfer-documents-report.pdf',
            ],
        ];
    }

    public static function excelExportRouteProvider(): array
    {
        return [
            'application report' => [
                'admin.reports.applications.excel',
                'transfer-applications-report.xlsx',
            ],

            'decision report' => [
                'admin.reports.decisions.excel',
                'transfer-decisions-report.xlsx',
            ],

            'appeal report' => [
                'admin.reports.appeals.excel',
                'transfer-appeals-report.xlsx',
            ],

            'document report' => [
                'admin.reports.documents.excel',
                'transfer-documents-report.xlsx',
            ],
        ];
    }

    private function createProvincialDirector(): User
    {
        $user = User::factory()->create();

        $user->assignRole(
            $this->provincialDirectorRole
        );

        return $user;
    }

    private function createZonalDirector(
        Zone $zone
    ): User {
        $user = User::factory()->create([
            'assigned_zone_id' => $zone->id,
        ]);

        $user->assignRole(
            $this->zonalDirectorRole
        );

        return $user;
    }

    private function createTransferApplication(
        Zone $zone,
        TransferCycle $cycle,
        array $attributes = []
    ): TransferApplication {
        $principalProfile =
            PrincipalProfile::factory()->create();

        return TransferApplication::factory()
            ->create(
                array_merge(
                    [
                        'transfer_cycle_id' => $cycle->id,

                        'principal_profile_id' => $principalProfile->id,

                        'origin_zone_id' => $zone->id,

                        'status' => TransferApplication::STATUS_SUBMITTED,

                        'submitted_at' => now(),

                        'application_number' => 'APP-'.fake()
                            ->unique()
                            ->numerify(
                                '######'
                            ),
                    ],
                    $attributes
                )
            );
    }
}
