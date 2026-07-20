<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Principal\UpdateOwnProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(
        Request $request
    ): Response {
        $profile = $request
            ->user()
            ->principalProfile;

        abort_unless(
            $profile,
            404,
            'Your principal profile has not been created.'
        );

        $profile->load([
            'user:id,name,email,email_verified_at',
            'registry',
            'appointments.school.division.zone',
            'currentAppointment.school.division.zone',
        ]);

        return Inertia::render(
            'Principal/Profile/Show',
            [
                'profile' => $profile,
            ]
        );
    }

    public function edit(
        Request $request
    ): Response {
        $profile = $request
            ->user()
            ->principalProfile;

        abort_unless(
            $profile,
            404,
            'Your principal profile has not been created.'
        );

        $profile->load([
            'currentAppointment.school.division.zone',
        ]);

        return Inertia::render(
            'Principal/Profile/Edit',
            [
                'profile' => $profile,

                'options' => [
                    'genders' => [
                        'Male',
                        'Female',
                        'Other',
                    ],

                    'employmentStatuses' => [
                        'Active',
                        'Retired',
                        'Resigned',
                        'Deceased',
                        'Suspended',
                        'Other',
                    ],

                    'serviceGrades' => [
                        'SLPS I',
                        'SLPS II',
                        'SLPS III',
                        'Other',
                    ],
                ],
            ]
        );
    }

    public function update(
        UpdateOwnProfileRequest $request
    ): RedirectResponse {
        $profile = $request
            ->user()
            ->principalProfile;

        abort_unless(
            $profile,
            404,
            'Your principal profile has not been created.'
        );

        /*
         * NIC is not included in validated data,
         * therefore it cannot be changed here.
         */
        $profile->update(
            $request->validated()
        );

        return redirect()
            ->route(
                'principal.profile.show'
            )
            ->with(
                'success',
                'Your profile was updated successfully.'
            );
    }
}
