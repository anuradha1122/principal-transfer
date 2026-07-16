<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportPrincipalRegistryRequest;
use App\Http\Requests\Admin\StorePrincipalRegistryRequest;
use App\Http\Requests\Admin\UpdatePrincipalRegistryRequest;
use App\Models\PrincipalRegistry;
use App\Models\School;
use App\Services\NicService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PrincipalRegistryController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can(
                'view principal registry'
            ),
            403
        );

        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'status' => [
                'nullable',
                'in:unregistered,registered,disabled',
            ],
            'designation' => [
                'nullable',
                'in:Principal,Deputy Principal,Assistant Principal',
            ],
            'school_id' => [
                'nullable',
                'integer',
                'exists:schools,id',
            ],
        ]);

        $registries = PrincipalRegistry::query()
            ->with([
                'school:id,division_id,name,census_number',
                'school.division:id,zone_id,name',
                'school.division.zone:id,name',
                'registeredUser:id,name,email',
            ])
            ->when(
                $filters['search'] ?? null,
                function ($query, string $search): void {
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->where(
                                    'nic',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'normalized_nic',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'full_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'name_with_initials',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'employee_number',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, string $status) => $query->where(
                    'registration_status',
                    $status
                )
            )
            ->when(
                $filters['designation'] ?? null,
                fn ($query, string $designation) => $query->where(
                    'designation',
                    $designation
                )
            )
            ->when(
                $filters['school_id'] ?? null,
                fn ($query, $schoolId) => $query->where(
                    'school_id',
                    $schoolId
                )
            )
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render(
            'Admin/PrincipalRegistry/Index',
            [
                'registries' => $registries,
                'filters' => $filters,
                'schools' => $this->schoolOptions(),
                'designations' => $this->designations(),
                'statistics' => [
                    'total' => PrincipalRegistry::count(),
                    'unregistered' => PrincipalRegistry::where(
                        'registration_status',
                        'unregistered'
                    )->count(),
                    'registered' => PrincipalRegistry::where(
                        'registration_status',
                        'registered'
                    )->count(),
                    'disabled' => PrincipalRegistry::where(
                        'registration_status',
                        'disabled'
                    )->count(),
                ],
            ]
        );
    }

    public function create(Request $request): Response
    {
        abort_unless(
            $request->user()->can(
                'create principal registry'
            ),
            403
        );

        return Inertia::render(
            'Admin/PrincipalRegistry/Create',
            [
                'schools' => $this->schoolOptions(),
                'designations' => $this->designations(),
            ]
        );
    }

    public function store(
        StorePrincipalRegistryRequest $request,
        NicService $nicService
    ): RedirectResponse {
        $validated = $request->validated();

        PrincipalRegistry::create([
            ...$validated,
            'normalized_nic' => $nicService->normalize($validated['nic']),
            'registration_status' => $validated['is_active']
                    ? 'unregistered'
                    : 'disabled',
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.principal-registry.index')
            ->with(
                'success',
                'Principal registry record created successfully.'
            );
    }

    public function show(
        Request $request,
        PrincipalRegistry $principalRegistry
    ): Response {
        abort_unless(
            $request->user()->can(
                'view principal registry'
            ),
            403
        );

        $principalRegistry->load([
            'school.division.zone',
            'registeredUser:id,name,email,email_verified_at,is_active,last_login_at',
            'creator:id,name',
            'updater:id,name',
        ]);

        return Inertia::render(
            'Admin/PrincipalRegistry/Show',
            [
                'registry' => $principalRegistry,
            ]
        );
    }

    public function edit(
        Request $request,
        PrincipalRegistry $principalRegistry
    ): Response {
        abort_unless(
            $request->user()->can(
                'edit principal registry'
            ),
            403
        );

        return Inertia::render(
            'Admin/PrincipalRegistry/Edit',
            [
                'registry' => $principalRegistry,
                'schools' => $this->schoolOptions(),
                'designations' => $this->designations(),
            ]
        );
    }

    public function update(
        UpdatePrincipalRegistryRequest $request,
        PrincipalRegistry $principalRegistry,
        NicService $nicService
    ): RedirectResponse {
        $validated = $request->validated();

        $principalRegistry->update([
            ...$validated,
            'normalized_nic' => $nicService->normalize($validated['nic']),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.principal-registry.index')
            ->with(
                'success',
                'Principal registry record updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        PrincipalRegistry $principalRegistry
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'delete principal registry'
            ),
            403
        );

        if ($principalRegistry->registered_user_id) {
            return back()->with(
                'error',
                'A registry record linked to a registered account cannot be deleted.'
            );
        }

        $principalRegistry->delete();

        return redirect()
            ->route('admin.principal-registry.index')
            ->with(
                'success',
                'Principal registry record deleted successfully.'
            );
    }

    public function importPage(Request $request): Response
    {
        abort_unless(
            $request->user()->can(
                'import principal registry'
            ),
            403
        );

        return Inertia::render(
            'Admin/PrincipalRegistry/Import'
        );
    }

    public function import(
        ImportPrincipalRegistryRequest $request,
        NicService $nicService
    ): RedirectResponse {
        $file = $request->file('file');
        $updateExisting = $request->boolean(
            'update_existing'
        );

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $invalidRows = [];

        $handle = fopen(
            $file->getRealPath(),
            'r'
        );

        if ($handle === false) {
            return back()->with(
                'error',
                'The CSV file could not be opened.'
            );
        }

        $headers = fgetcsv($handle);

        if (! is_array($headers)) {
            fclose($handle);

            return back()->with(
                'error',
                'The CSV file does not contain a header row.'
            );
        }

        $headers = array_map(
            fn ($header) => strtolower(
                trim((string) $header)
            ),
            $headers
        );

        if (! in_array('nic', $headers, true)) {
            fclose($handle);

            return back()->with(
                'error',
                'The CSV file must contain a nic column.'
            );
        }

        $rowNumber = 1;

        while (
            ($row = fgetcsv($handle)) !== false
        ) {
            $rowNumber++;

            if (
                count(array_filter(
                    $row,
                    fn ($value) => trim((string) $value) !== ''
                )) === 0
            ) {
                continue;
            }

            $row = array_pad(
                $row,
                count($headers),
                null
            );

            $data = array_combine(
                $headers,
                array_slice(
                    $row,
                    0,
                    count($headers)
                )
            );

            if (! is_array($data)) {
                $invalidRows[] = $rowNumber;
                $skipped++;

                continue;
            }

            $nic = $data['nic'] ?? null;
            $normalizedNic = $nicService->normalize(
                $nic
            );

            if (! $nicService->isValidFormat($nic)) {
                $invalidRows[] = $rowNumber;
                $skipped++;

                continue;
            }

            $designation = trim(
                (string) ($data['designation'] ?? '')
            );

            if (
                $designation !== '' &&
                ! in_array(
                    $designation,
                    $this->designations(),
                    true
                )
            ) {
                $invalidRows[] = $rowNumber;
                $skipped++;

                continue;
            }

            $schoolId = null;
            $censusNumber = trim(
                (string) (
                    $data['school_census_number'] ?? ''
                )
            );

            if ($censusNumber !== '') {
                $schoolId = School::query()
                    ->where(
                        'census_number',
                        $censusNumber
                    )
                    ->value('id');
            }

            $existing = PrincipalRegistry::query()
                ->where(
                    'normalized_nic',
                    $normalizedNic
                )
                ->first();

            $values = [
                'nic' => trim((string) $nic),
                'normalized_nic' => $normalizedNic,
                'full_name' => $this->nullableString(
                    $data['full_name'] ?? null
                ),
                'name_with_initials' => $this->nullableString(
                    $data['name_with_initials']
                        ?? null
                ),
                'school_id' => $schoolId,
                'designation' => $designation !== ''
                        ? $designation
                        : null,
                'employee_number' => $this->nullableString(
                    $data['employee_number']
                        ?? null
                ),
                'notes' => $this->nullableString(
                    $data['notes'] ?? null
                ),
                'is_active' => true,
                'updated_by' => $request->user()->id,
            ];

            try {
                DB::transaction(
                    function () use (
                        $existing,
                        $values,
                        $request,
                        $updateExisting,
                        &$created,
                        &$updated,
                        &$skipped
                    ): void {
                        if ($existing) {
                            if (
                                ! $updateExisting ||
                                $existing->registered_user_id
                            ) {
                                $skipped++;

                                return;
                            }

                            $existing->update($values);
                            $updated++;

                            return;
                        }

                        PrincipalRegistry::create([
                            ...$values,
                            'registration_status' => 'unregistered',
                            'created_by' => $request->user()->id,
                        ]);

                        $created++;
                    }
                );
            } catch (Throwable) {
                $invalidRows[] = $rowNumber;
                $skipped++;
            }
        }

        fclose($handle);

        $message = sprintf(
            'Import completed: %d created, %d updated and %d skipped.',
            $created,
            $updated,
            $skipped
        );

        if ($invalidRows !== []) {
            $message .= ' Invalid rows: '
                .implode(', ', array_slice(
                    $invalidRows,
                    0,
                    20
                ));

            if (count($invalidRows) > 20) {
                $message .= ' and more.';
            }
        }

        return redirect()
            ->route('admin.principal-registry.index')
            ->with('success', $message);
    }

    public function template(
        Request $request
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'import principal registry'
            ),
            403
        );

        $content = implode(',', [
            'nic',
            'full_name',
            'name_with_initials',
            'school_census_number',
            'designation',
            'employee_number',
            'notes',
        ])."\n";

        $content .= implode(',', [
            '123456789V',
            'Example Principal',
            'E Principal',
            '12345',
            'Principal',
            'EMP001',
            'Example row',
        ])."\n";

        return response(
            $content,
            200,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="principal-registry-template.csv"',
            ]
        );
    }

    private function schoolOptions()
    {
        return School::query()
            ->with([
                'division:id,zone_id,name',
                'division.zone:id,name',
            ])
            ->orderBy('name')
            ->get([
                'id',
                'division_id',
                'name',
                'census_number',
            ]);
    }

    private function designations(): array
    {
        return [
            'Principal',
            'Deputy Principal',
            'Assistant Principal',
        ];
    }

    private function nullableString(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
