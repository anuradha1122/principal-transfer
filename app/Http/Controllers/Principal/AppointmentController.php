<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Principal\StoreOwnAppointmentRequest;
use App\Http\Requests\Principal\UpdateOwnAppointmentRequest;
use App\Models\PrincipalAppointment;
use App\Models\PrincipalProfile;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function create(
        Request $request
    ): Response {
        $profile = $this->profile(
            $request
        );

        return Inertia::render(
            'Principal/Appointments/Create',
            [
                'profile' => $profile,

                'schools' => $this->schools(),

                'options' => $this->options(),
            ]
        );
    }

    public function store(
        StoreOwnAppointmentRequest $request
    ): RedirectResponse {
        $profile = $this->profile(
            $request
        );

        $validated =
            $request->validated();

        DB::transaction(
            function () use (
                $validated,
                $request,
                $profile
            ): void {
                if (
                    $validated['is_current']
                ) {
                    $this
                        ->closeOtherCurrentAppointments(
                            $profile,
                            null,
                            $validated[
                                'start_date'
                            ]
                        );
                }

                $profile
                    ->appointments()
                    ->create([
                        ...$validated,

                        'end_date' => $validated[
                                'is_current'
                            ]
                                ? null
                                : (
                                    $validated[
                                        'end_date'
                                    ] ?? null
                                ),

                        'created_by' => $request
                            ->user()
                            ->id,

                        'updated_by' => $request
                            ->user()
                            ->id,
                    ]);
            }
        );

        return redirect()
            ->route(
                'principal.profile.show'
            )
            ->with(
                'success',
                'Appointment added successfully.'
            );
    }

    public function edit(
        Request $request,
        PrincipalAppointment $principalAppointment
    ): Response {
        $this->ensureOwnership(
            $request,
            $principalAppointment
        );

        $principalAppointment->load([
            'school.division.zone',
        ]);

        return Inertia::render(
            'Principal/Appointments/Edit',
            [
                'appointment' => $principalAppointment,

                'schools' => $this->schools(),

                'options' => $this->options(),
            ]
        );
    }

    public function update(
        UpdateOwnAppointmentRequest $request,
        PrincipalAppointment $principalAppointment
    ): RedirectResponse {
        $this->ensureOwnership(
            $request,
            $principalAppointment
        );

        $profile =
            $principalAppointment
                ->principalProfile;

        $validated =
            $request->validated();

        DB::transaction(
            function () use (
                $validated,
                $request,
                $profile,
                $principalAppointment
            ): void {
                if (
                    $validated['is_current']
                ) {
                    $this
                        ->closeOtherCurrentAppointments(
                            $profile,
                            $principalAppointment
                                ->id,
                            $validated[
                                'start_date'
                            ]
                        );
                }

                $principalAppointment
                    ->update([
                        ...$validated,

                        'end_date' => $validated[
                                'is_current'
                            ]
                                ? null
                                : (
                                    $validated[
                                        'end_date'
                                    ] ?? null
                                ),

                        'updated_by' => $request
                            ->user()
                            ->id,
                    ]);
            }
        );

        return redirect()
            ->route(
                'principal.profile.show'
            )
            ->with(
                'success',
                'Appointment updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        PrincipalAppointment $principalAppointment
    ): RedirectResponse {
        $this->ensureOwnership(
            $request,
            $principalAppointment
        );

        if (
            $principalAppointment
                ->is_current
        ) {
            return redirect()
                ->route(
                    'principal.profile.show'
                )
                ->with(
                    'warning',
                    'The current appointment cannot be deleted. Mark another appointment as current first.'
                );
        }

        $principalAppointment->delete();

        return redirect()
            ->route(
                'principal.profile.show'
            )
            ->with(
                'success',
                'Appointment deleted successfully.'
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

    private function ensureOwnership(
        Request $request,
        PrincipalAppointment $appointment
    ): void {
        abort_unless(
            $appointment
                ->principal_profile_id
            === $this
                ->profile($request)
                ->id,
            403
        );
    }

    private function closeOtherCurrentAppointments(
        PrincipalProfile $profile,
        ?int $exceptAppointmentId,
        string $newStartDate
    ): void {
        $query = $profile
            ->appointments()
            ->where(
                'is_current',
                true
            );

        if ($exceptAppointmentId) {
            $query->whereKeyNot(
                $exceptAppointmentId
            );
        }

        $query->update([
            'is_current' => false,

            'end_date' => Carbon::parse(
                $newStartDate
            )
                ->subDay()
                ->toDateString(),
        ]);
    }

    private function schools()
    {
        return School::query()
            ->with([
                'division:id,zone_id,name',
                'division.zone:id,name',
            ])
            ->where(
                'is_active',
                true
            )
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
            'appointmentTypes' => [
                'Permanent',
                'Acting',
                'Covering',
                'Temporary',
                'Other',
            ],

            'designations' => [
                'Principal',
                'Deputy Principal',
                'Assistant Principal',
                'Acting Principal',
                'Other',
            ],
        ];
    }
}
