<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Principal\UpdateOwnProfileRequest;
use App\Models\PrincipalProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $profile = $this->profile($request);

        $profile->load([
            'appointments' => fn ($query) =>
                $query
                    ->with('school.division.zone')
                    ->orderByDesc('start_date'),
        ]);

        return Inertia::render(
            'Principal/Profile/Show',
            [
                'profile' => $profile,
            ]
        );
    }

    public function edit(Request $request): Response
    {
        $profile = $this->profile($request);

        return Inertia::render(
            'Principal/Profile/Edit',
            [
                'profile' => $profile,
                'genders' => [
                    'Male',
                    'Female',
                    'Other',
                ],
            ]
        );
    }

    public function update(
        UpdateOwnProfileRequest $request
    ): RedirectResponse {
        $profile = $this->profile($request);

        $profile->update(
            $request->validated()
        );

        return redirect()
            ->route('principal.profile.show')
            ->with(
                'success',
                'Your profile was updated successfully.'
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
            'Your principal profile has not been created yet.'
        );

        return $profile;
    }
}
