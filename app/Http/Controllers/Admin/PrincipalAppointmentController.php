<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePrincipalAppointmentRequest;
use App\Http\Requests\Admin\UpdatePrincipalAppointmentRequest;
use App\Models\PrincipalAppointment;
use App\Models\PrincipalProfile;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PrincipalAppointmentController extends Controller
{
    public function create(
        Request $request,
        PrincipalProfile $principalProfile
    ): Response {
        abort_unless(
            $request->user()->can(
                'manage principal profiles'
            ),
            403
        );

        return Inertia::render(
            'Admin/PrincipalAppointments/Create',
            [
                'profile' => [
                    'id' => $principalProfile->id,
                    'full_name' =>
                        $principalProfile->full_name,
                    'nic' => $principalProfile->nic,
                ],
                'schools' => $this->schools(),
                'options' => $this->options(),
            ]
        );
    }

    public function store(
        StorePrincipalAppointmentRequest $request,
        PrincipalProfile $principalProfile
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(
            function () use (
                $validated,
                $request,
                $principalProfile
            ): void {
                if ($validated['is_current']) {
                    $this->closeCurrentAppointments(
                        $principalProfile,
                        $validated['start_date']
                    );
                }

                PrincipalAppointment::create([
                    ...$validated,
                    'principal_profile_id' =>
                        $principalProfile->id,
                    'created_by' =>
                        $request->user()->id,
                    'updated_by' =>
                        $request->user()->id,
                ]);
            }
        );

        return redirect()
            ->route(
                'admin.principal-profiles.show',
                $principalProfile
            )
            ->with(
                'success',
                'Principal appointment added successfully.'
            );
    }

    public function edit(
        Request $request,
        PrincipalAppointment $principalAppointment
    ): Response {
        abort_unless(
            $request->user()->can(
                'manage principal profiles'
            ),
            403
        );

        $principalAppointment->load(
            'principalProfile:id,full_name,nic'
        );

        return Inertia::render(
            'Admin/PrincipalAppointments/Edit',
            [
                'appointment' =>
                    $principalAppointment,
                'profile' =>
                    $principalAppointment
                        ->principalProfile,
                'schools' => $this->schools(),
                'options' => $this->options(),
            ]
        );
    }

    public function update(
        UpdatePrincipalAppointmentRequest $request,
        PrincipalAppointment $principalAppointment
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(
            function () use (
                $validated,
                $request,
                $principalAppointment
            ): void {
                if ($validated['is_current']) {
                    $this->closeCurrentAppointments(
                        $principalAppointment
                            ->principalProfile,
                        $validated['start_date'],
                        $principalAppointment->id
                    );
                }

                $principalAppointment->update([
                    ...$validated,
                    'updated_by' =>
                        $request->user()->id,
                ]);
            }
        );

        return redirect()
            ->route(
                'admin.principal-profiles.show',
                $principalAppointment
                    ->principal_profile_id
            )
            ->with(
                'success',
                'Principal appointment updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        PrincipalAppointment $principalAppointment
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'manage principal profiles'
            ),
            403
        );

        $profileId =
            $principalAppointment
                ->principal_profile_id;

        $principalAppointment->delete();

        return redirect()
            ->route(
                'admin.principal-profiles.show',
                $profileId
            )
            ->with(
                'success',
                'Appointment record deleted successfully.'
            );
    }

    private function closeCurrentAppointments(
        PrincipalProfile $profile,
        string $newStartDate,
        ?int $exceptId = null
    ): void {
        $query = $profile
            ->appointments()
            ->where('is_current', true);

        if ($exceptId) {
            $query->whereKeyNot($exceptId);
        }

        $endDate = Carbon::parse(
            $newStartDate
        )
            ->subDay()
            ->toDateString();

        $query->update([
            'is_current' => false,
            'end_date' => DB::raw(
                "COALESCE(end_date, '{$endDate}')"
            ),
            'reason_for_end' => DB::raw(
                "COALESCE(reason_for_end, 'Superseded by a new appointment')"
            ),
            'updated_at' => now(),
        ]);
    }

    private function schools()
    {
        return School::query()
            ->with([
                'division:id,zone_id,name',
                'division.zone:id,name',
            ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'division_id',
                'name',
                'census_number',
            ]);
    }

    private function options(): array
    {
        return [
            'designations' => [
                'Principal',
                'Deputy Principal',
                'Assistant Principal',
            ],
            'appointmentTypes' => [
                'Permanent',
                'Acting',
                'Temporary',
                'Attached',
            ],
        ];
    }
}
