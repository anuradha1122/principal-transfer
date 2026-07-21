<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Principal\StoreTransferApplicationRequest;
use App\Http\Requests\Principal\SubmitTransferApplicationRequest;
use App\Http\Requests\Principal\UpdateTransferApplicationRequest;
use App\Models\PrincipalProfile;
use App\Models\School;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Services\TransferApplicationPdfService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransferApplicationController extends Controller
{
    public function index(
        Request $request
    ): Response {
        $profile = $this->profile($request);

        $applications = TransferApplication::query()
            ->where(
                'principal_profile_id',
                $profile->id
            )
            ->with([
                'transferCycle:id,name,code,transfer_year,application_close_date,status',
                'currentSchool:id,name',
            ])
            ->latest('id')
            ->paginate(15);

        $availableCycles = TransferCycle::query()
            ->published()
            ->whereDate(
                'application_open_date',
                '<=',
                today()
            )
            ->whereDate(
                'application_close_date',
                '>=',
                today()
            )
            ->whereDoesntHave(
                'applications',
                function ($query) use ($profile): void {
                    $query
                        ->where(
                            'principal_profile_id',
                            $profile->id
                        )
                        ->whereNotIn(
                            'status',
                            [
                                'Withdrawn',
                                'Cancelled',
                            ]
                        );
                }
            )
            ->orderByDesc('transfer_year')
            ->get([
                'id',
                'name',
                'code',
                'transfer_type',
                'transfer_year',
                'application_close_date',
            ]);

        return Inertia::render(
            'Principal/TransferApplications/Index',
            [
                'applications' => $applications,
                'availableCycles' => $availableCycles,
            ]
        );
    }

    public function create(
        Request $request
    ): Response|RedirectResponse {
        $profile = $this->profile($request);

        $currentAppointment = $profile
            ->currentAppointment()
            ->with('school.division.zone')
            ->first();

        if (! $currentAppointment) {
            return redirect()
                ->route('principal.profile.show')
                ->with(
                    'warning',
                    'Please add your current appointment before applying for a transfer.'
                );
        }

        $cycleId = $request->integer(
            'transfer_cycle_id'
        );

        if (! $cycleId) {
            return redirect()
                ->route(
                    'principal.transfer-applications.index'
                )
                ->with(
                    'warning',
                    'Please select an available transfer cycle.'
                );
        }

        $cycle = TransferCycle::query()
            ->published()
            ->find($cycleId);

        if (! $cycle) {
            return redirect()
                ->route(
                    'principal.transfer-applications.index'
                )
                ->with(
                    'error',
                    'The selected transfer cycle is unavailable.'
                );
        }

        if (! $cycle->isApplicationOpen()) {
            return redirect()
                ->route(
                    'principal.transfer-applications.index'
                )
                ->with(
                    'warning',
                    'This transfer cycle is not open for applications.'
                );
        }

        $existingApplication = TransferApplication::query()
            ->activeForPrincipal(
                $cycle->id,
                $profile->id
            )
            ->latest('id')
            ->first();

        if ($existingApplication) {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $existingApplication
                )
                ->with(
                    'warning',
                    'You already have an active application for this transfer cycle.'
                );
        }

        try {
            $this->ensureEligible(
                $profile,
                $cycle,
                $currentAppointment
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->route(
                    'principal.transfer-applications.index'
                )
                ->with(
                    'warning',
                    collect($exception->errors())
                        ->flatten()
                        ->first()
                    ?? 'You are not eligible for this transfer cycle.'
                );
        }

        return Inertia::render(
            'Principal/TransferApplications/Create',
            [
                'profile' => $this->profileData(
                    $profile,
                    $currentAppointment
                ),

                'cycle' => $cycle,

                'schools' => $this->eligibleSchools(
                    $currentAppointment->school_id
                ),

                'reasons' => $this->reasons(),
            ]
        );
    }

    public function store(
        StoreTransferApplicationRequest $request
    ): RedirectResponse {
        $profile = $this->profile($request);

        $cycle = TransferCycle::query()
            ->find(
                $request->integer(
                    'transfer_cycle_id'
                )
            );

        if (! $cycle) {
            return redirect()
                ->route(
                    'principal.transfer-applications.index'
                )
                ->with(
                    'error',
                    'The selected transfer cycle could not be found.'
                );
        }

        if (! $cycle->isApplicationOpen()) {
            return redirect()
                ->route(
                    'principal.transfer-applications.index'
                )
                ->with(
                    'warning',
                    'This transfer cycle is no longer accepting applications.'
                );
        }

        $currentAppointment = $profile
            ->currentAppointment()
            ->with('school.division.zone')
            ->first();

        if (! $currentAppointment) {
            return redirect()
                ->route('principal.profile.show')
                ->with(
                    'warning',
                    'Please add your current appointment before applying.'
                );
        }

        $existingApplication = TransferApplication::query()
            ->activeForPrincipal(
                $cycle->id,
                $profile->id
            )
            ->exists();

        if ($existingApplication) {
            return redirect()
                ->route(
                    'principal.transfer-applications.index'
                )
                ->with(
                    'warning',
                    'You already have an active application for this transfer cycle.'
                );
        }

        $this->ensureEligible(
            $profile,
            $cycle,
            $currentAppointment
        );

        $validated = $request->validated();

        $application = DB::transaction(
            function () use (
                $validated,
                $request,
                $profile,
                $cycle,
                $currentAppointment
            ): TransferApplication {
                $application = TransferApplication::create([
                    'transfer_cycle_id' => $cycle->id,

                    'principal_profile_id' => $profile->id,

                    'current_appointment_id' => $currentAppointment->id,

                    'principal_name' => $profile->full_name,

                    'nic' => $profile->nic,

                    'employee_number' => $profile->employee_number,

                    'current_school_id' => $currentAppointment->school_id,

                    /*
                     * origin_zone_id is intentionally not set here.
                     *
                     * It is captured when the Principal officially
                     * submits the application. This preserves the
                     * submitted Zone snapshot.
                     */
                    'origin_zone_id' => null,

                    'current_designation' => $currentAppointment->designation,

                    'service_grade' => $profile->service_grade,

                    'current_appointment_start_date' => $currentAppointment->start_date,

                    'current_school_service_months' => Carbon::parse(
                        $currentAppointment->start_date
                    )->diffInMonths(today()),

                    'transfer_reason' => $validated['transfer_reason'],

                    'reason_details' => $validated['reason_details'],

                    'has_medical_reason' => $validated['has_medical_reason'],

                    'has_spouse_employment_reason' => $validated['has_spouse_employment_reason'],

                    'is_mutual_transfer' => $validated['is_mutual_transfer'],

                    'mutual_principal_nic' => $validated['is_mutual_transfer']
                            ? (
                                $validated['mutual_principal_nic']
                                ?? null
                            )
                            : null,

                    'principal_remarks' => $validated['principal_remarks']
                        ?? null,

                    'status' => 'Draft',

                    'declaration_accepted' => false,

                    'created_by' => $request->user()->id,

                    'updated_by' => $request->user()->id,
                ]);

                $this->syncPreferences(
                    $application,
                    $validated['preferences']
                );

                return $application;
            }
        );

        return redirect()
            ->route(
                'principal.transfer-applications.show',
                $application
            )
            ->with(
                'success',
                'Transfer application saved as a draft.'
            );
    }

    public function show(
        Request $request,
        TransferApplication $transferApplication
    ): Response {
        $this->authorizeOwnership(
            $request,
            $transferApplication
        );

        $transferApplication->load([
            'transferCycle',
            'currentSchool.division.zone',
            'originZone',
            'preferences.school.division.zone',
        ]);

        return Inertia::render(
            'Principal/TransferApplications/Show',
            [
                'application' => $transferApplication,
            ]
        );
    }

    public function edit(
        Request $request,
        TransferApplication $transferApplication
    ): Response|RedirectResponse {
        $this->authorizeOwnership(
            $request,
            $transferApplication
        );

        if (! $transferApplication->isEditableByPrincipal()) {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'Only draft applications can be edited.'
                );
        }

        if (! $transferApplication->transferCycle->isApplicationOpen()) {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'The application period has closed.'
                );
        }

        $currentAppointment = $transferApplication
            ->principalProfile
            ->currentAppointment()
            ->with('school.division.zone')
            ->first();

        if (! $currentAppointment) {
            return redirect()
                ->route('principal.profile.show')
                ->with(
                    'warning',
                    'Please add your current appointment before editing this application.'
                );
        }

        $transferApplication->load([
            'transferCycle',
            'preferences',
        ]);

        return Inertia::render(
            'Principal/TransferApplications/Edit',
            [
                'application' => $transferApplication,

                'profile' => $this->profileData(
                    $transferApplication->principalProfile,
                    $currentAppointment
                ),

                'cycle' => $transferApplication->transferCycle,

                'schools' => $this->eligibleSchools(
                    $currentAppointment->school_id
                ),

                'reasons' => $this->reasons(),
            ]
        );
    }

    public function update(
        UpdateTransferApplicationRequest $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->authorizeOwnership(
            $request,
            $transferApplication
        );

        if (! $transferApplication->isEditableByPrincipal()) {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'Only draft applications can be edited.'
                );
        }

        if (! $transferApplication->transferCycle->isApplicationOpen()) {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'The application period has closed.'
                );
        }

        $validated = $request->validated();

        DB::transaction(
            function () use (
                $validated,
                $request,
                $transferApplication
            ): void {
                $transferApplication->update([
                    'transfer_reason' => $validated['transfer_reason'],

                    'reason_details' => $validated['reason_details'],

                    'has_medical_reason' => $validated['has_medical_reason'],

                    'has_spouse_employment_reason' => $validated['has_spouse_employment_reason'],

                    'is_mutual_transfer' => $validated['is_mutual_transfer'],

                    'mutual_principal_nic' => $validated['is_mutual_transfer']
                            ? (
                                $validated['mutual_principal_nic']
                                ?? null
                            )
                            : null,

                    'principal_remarks' => $validated['principal_remarks']
                        ?? null,

                    'updated_by' => $request->user()->id,
                ]);

                $this->syncPreferences(
                    $transferApplication,
                    $validated['preferences']
                );
            }
        );

        return redirect()
            ->route(
                'principal.transfer-applications.show',
                $transferApplication
            )
            ->with(
                'success',
                'Transfer application updated successfully.'
            );
    }

    public function submit(
        SubmitTransferApplicationRequest $request,
        TransferApplication $transferApplication,
        TransferApplicationPdfService $pdfService
    ): RedirectResponse {
        $this->authorizeOwnership(
            $request,
            $transferApplication
        );

        if ($transferApplication->status !== 'Draft') {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'Only draft applications can be submitted.'
                );
        }

        if (! $transferApplication->transferCycle->isApplicationOpen()) {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'The application period has closed.'
                );
        }

        if ($transferApplication->preferences()->count() < 1) {
            return redirect()
                ->route(
                    'principal.transfer-applications.edit',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'At least one school preference is required.'
                );
        }

        try {
            DB::transaction(
                function () use (
                    $request,
                    $transferApplication
                ): void {
                    /*
                     * Reload and lock the application so two submission
                     * requests cannot update it simultaneously.
                     */
                    $lockedApplication = TransferApplication::query()
                        ->lockForUpdate()
                        ->with([
                            'transferCycle',
                            'currentSchool.division',
                        ])
                        ->findOrFail(
                            $transferApplication->id
                        );

                    /*
                     * Recheck the status after locking because another
                     * request may have already submitted it.
                     */
                    if ($lockedApplication->status !== 'Draft') {
                        throw ValidationException::withMessages([
                            'status' => 'This application has already been submitted or is no longer editable.',
                        ]);
                    }

                    /*
                     * Capture the immutable origin Zone from the current
                     * School snapshot.
                     */
                    $originZoneId = $lockedApplication
                        ->currentSchool
                        ?->division
                        ?->zone_id;

                    if ($originZoneId === null) {
                        throw ValidationException::withMessages([
                            'current_school_id' => 'The current school does not belong to a valid education Zone. Please contact the system administrator.',
                        ]);
                    }

                    $lockedApplication->update([
                        'origin_zone_id' => $originZoneId,

                        'application_number' => $this->generateApplicationNumber(
                            $lockedApplication
                        ),

                        'status' => 'Submitted',

                        'submitted_at' => now(),

                        'declaration_accepted' => true,

                        'updated_by' => $request->user()->id,
                    ]);
                }
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->withErrors(
                    $exception->errors()
                )
                ->with(
                    'warning',
                    collect($exception->errors())
                        ->flatten()
                        ->first()
                    ?? 'The application could not be submitted.'
                );
        }

        /*
         * Reload the submitted application and all PDF-related data.
         *
         * PDF generation stays outside the transaction so a PDF error
         * does not reverse a successful application submission.
         */
        $transferApplication->refresh();

        try {
            $pdfService->generate(
                $transferApplication
            );
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'The application was submitted successfully, but the PDF could not be generated. You can try downloading it again from the application page.'
                );
        }

        return redirect()
            ->route(
                'principal.transfer-applications.show',
                $transferApplication
            )
            ->with(
                'success',
                'Transfer application submitted successfully. The submitted PDF is ready for download.'
            );
    }

    public function withdraw(
        Request $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->authorizeOwnership(
            $request,
            $transferApplication
        );

        if (! $transferApplication->canBeWithdrawn()) {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'This application can no longer be withdrawn.'
                );
        }

        $validated = $request->validate([
            'withdrawal_reason' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
        ]);

        $transferApplication->update([
            'status' => 'Withdrawn',

            'withdrawn_at' => now(),

            'withdrawal_reason' => $validated['withdrawal_reason'],

            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route(
                'principal.transfer-applications.index'
            )
            ->with(
                'success',
                'The application was withdrawn. You may now create a new application for the same transfer cycle.'
            );
    }

    public function destroy(
        Request $request,
        TransferApplication $transferApplication
    ): RedirectResponse {
        $this->authorizeOwnership(
            $request,
            $transferApplication
        );

        if ($transferApplication->status !== 'Draft') {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'Only draft applications can be deleted.'
                );
        }

        $transferApplication->delete();

        return redirect()
            ->route(
                'principal.transfer-applications.index'
            )
            ->with(
                'success',
                'Draft application deleted successfully.'
            );
    }

    private function profile(
        Request $request
    ): PrincipalProfile {
        $profile = $request
            ->user()
            ->principalProfile;

        abort_unless(
            $profile,
            404,
            'Your principal profile has not been created.'
        );

        return $profile;
    }

    private function authorizeOwnership(
        Request $request,
        TransferApplication $application
    ): void {
        abort_unless(
            $application->principal_profile_id
            === $this->profile($request)->id,
            403
        );
    }

    private function syncPreferences(
        TransferApplication $application,
        array $preferences
    ): void {
        $application
            ->preferences()
            ->delete();

        foreach (
            array_values($preferences) as $index => $preference
        ) {
            $application
                ->preferences()
                ->create([
                    'preference_order' => $index + 1,

                    'school_id' => $preference['school_id'],

                    'preference_reason' => $preference['preference_reason']
                        ?? null,
                ]);
        }
    }

    private function generateApplicationNumber(
        TransferApplication $application
    ): string {
        $application->loadMissing(
            'transferCycle'
        );

        return strtoupper(
            sprintf(
                '%s-%s',
                $application->transferCycle->code,
                str_pad(
                    (string) $application->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                )
            )
        );
    }

    private function ensureEligible(
        PrincipalProfile $profile,
        TransferCycle $cycle,
        $currentAppointment
    ): void {
        if ($profile->employment_status !== 'Active') {
            throw ValidationException::withMessages([
                'transfer_cycle_id' => 'Only active principals can apply for transfers.',
            ]);
        }

        $serviceYears = Carbon::parse(
            $currentAppointment->start_date
        )->diffInYears(today());

        if (
            $serviceYears
            < $cycle->minimum_service_years
        ) {
            throw ValidationException::withMessages([
                'transfer_cycle_id' => "A minimum of {$cycle->minimum_service_years} years at the current school is required.",
            ]);
        }
    }

    private function eligibleSchools(
        int $currentSchoolId
    ) {
        return School::query()
            ->with([
                'division:id,zone_id,name',
                'division.zone:id,name',
            ])
            ->where(
                'is_active',
                true
            )
            ->whereKeyNot(
                $currentSchoolId
            )
            ->orderBy('name')
            ->get([
                'id',
                'division_id',
                'name',
                'census_number',
                'school_type',
                'gender_type',
            ]);
    }

    private function profileData(
        PrincipalProfile $profile,
        $currentAppointment
    ): array {
        return [
            'id' => $profile->id,

            'full_name' => $profile->full_name,

            'nic' => $profile->nic,

            'employee_number' => $profile->employee_number,

            'service_grade' => $profile->service_grade,

            'employment_status' => $profile->employment_status,

            'current_appointment' => [
                'id' => $currentAppointment->id,

                'designation' => $currentAppointment->designation,

                'start_date' => $currentAppointment->start_date,

                'school' => $currentAppointment->school,
            ],
        ];
    }

    private function reasons(): array
    {
        return [
            'Long Service',
            'Medical',
            'Spouse Employment',
            'Family Requirement',
            'Travel Difficulty',
            'Personal Request',
            'Mutual Transfer',
            'Administrative Reason',
            'Other',
        ];
    }

    public function downloadPdf(
        Request $request,
        TransferApplication $transferApplication,
        TransferApplicationPdfService $pdfService
    ): BinaryFileResponse|RedirectResponse {
        $this->authorizeOwnership(
            $request,
            $transferApplication
        );

        if (
            ! in_array(
                $transferApplication->status,
                [
                    'Submitted',
                    'Zonal Review',
                    'Zonal Approved',
                    'Zonal Rejected',
                    'Provincial Review',
                    'Provincial Approved',
                    'Provincial Rejected',
                    'Board Review',
                    'Approved',
                    'Rejected',
                    'Waitlisted',
                    'Withdrawn',
                ],
                true
            )
        ) {
            return redirect()
                ->route(
                    'principal.transfer-applications.show',
                    $transferApplication
                )
                ->with(
                    'warning',
                    'A PDF is available only after the application has been submitted.'
                );
        }

        $path = $pdfService->ensureExists(
            $transferApplication
        );

        return response()->download(
            Storage::disk('local')->path(
                $path
            ),
            $pdfService->downloadName(
                $transferApplication
            ),
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }
}
