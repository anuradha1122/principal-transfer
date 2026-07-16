<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterPrincipalRequest;
use App\Http\Requests\Auth\VerifyPrincipalNicRequest;
use App\Models\PrincipalRegistry;
use App\Models\User;
use App\Services\NicService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PrincipalRegistrationController extends Controller
{
    public function verifyPage(): Response
    {
        return Inertia::render(
            'Auth/PrincipalRegistration/VerifyNic'
        );
    }

    public function verify(
        VerifyPrincipalNicRequest $request,
        NicService $nicService
    ): RedirectResponse {
        $normalizedNic = $nicService->normalize(
            $request->validated('nic')
        );

        $rateLimitKey = sprintf(
            'principal-registration-nic:%s',
            $request->ip()
        );

        if (
            RateLimiter::tooManyAttempts(
                $rateLimitKey,
                10
            )
        ) {
            throw ValidationException::withMessages([
                'nic' => 'Too many attempts. Please try again later.',
            ]);
        }

        RateLimiter::hit(
            $rateLimitKey,
            300
        );

        $registry = PrincipalRegistry::query()
            ->where(
                'normalized_nic',
                $normalizedNic
            )
            ->first();

        if (
            ! $registry ||
            ! $registry->isAvailableForRegistration()
        ) {
            throw ValidationException::withMessages([
                'nic' => 'This NIC is not currently eligible for registration. Contact the Provincial Department of Education if you believe this is incorrect.',
            ]);
        }

        $verificationToken = Str::random(64);

        $request->session()->put(
            'principal_registration',
            [
                'registry_id' => $registry->id,
                'normalized_nic' => $normalizedNic,
                'token_hash' => hash('sha256', $verificationToken),
                'verified_at' => now()->timestamp,
            ]
        );

        return redirect()->route(
            'principal-registration.create',
            [
                'token' => $verificationToken,
            ]
        );
    }

    public function create(
        Request $request
    ): Response|RedirectResponse {
        $registration = $request->session()->get(
            'principal_registration'
        );

        $token = (string) $request->query('token');

        if (
            ! $this->validRegistrationSession(
                $registration,
                $token
            )
        ) {
            return redirect()
                ->route(
                    'principal-registration.verify-page'
                )
                ->with(
                    'error',
                    'Verify your NIC before continuing registration.'
                );
        }

        $registry = PrincipalRegistry::query()
            ->with('school.division.zone')
            ->find($registration['registry_id']);

        if (
            ! $registry ||
            ! $registry->isAvailableForRegistration()
        ) {
            $request->session()->forget(
                'principal_registration'
            );

            return redirect()
                ->route(
                    'principal-registration.verify-page'
                )
                ->with(
                    'error',
                    'This NIC is no longer available for registration.'
                );
        }

        return Inertia::render(
            'Auth/PrincipalRegistration/Register',
            [
                'token' => $token,
                'registry' => [
                    'nic' => $registry->nic,
                    'full_name' => $registry->full_name,
                    'name_with_initials' => $registry->name_with_initials,
                    'designation' => $registry->designation,
                    'employee_number' => $registry->employee_number,
                    'school' => $registry->school
                        ? [
                            'name' => $registry->school->name,
                            'division' => $registry->school
                                ->division?->name,
                            'zone' => $registry->school
                                ->division
                                ?->zone?->name,
                        ]
                        : null,
                ],
            ]
        );
    }

    public function store(
        RegisterPrincipalRequest $request
    ): RedirectResponse {
        $registration = $request->session()->get(
            'principal_registration'
        );

        $token = (string) $request->input('token');

        if (
            ! $this->validRegistrationSession(
                $registration,
                $token
            )
        ) {
            throw ValidationException::withMessages([
                'registration' => 'Your NIC verification has expired. Verify the NIC again.',
            ]);
        }

        $user = DB::transaction(
            function () use (
                $request,
                $registration
            ): User {
                $registry = PrincipalRegistry::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $registration['registry_id']
                    );

                if (
                    ! $registry
                        ->isAvailableForRegistration()
                ) {
                    throw ValidationException::withMessages([
                        'registration' => 'This NIC has already been registered or disabled.',
                    ]);
                }

                $validated = $request->validated();

                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'is_active' => true,
                ]);

                $user->assignRole('Principal');

                $registry->update([
                    'full_name' => $registry->full_name
                            ?: $validated['name'],
                    'registered_user_id' => $user->id,
                    'registration_status' => 'registered',
                    'registered_at' => now(),
                ]);

                return $user;
            }
        );

        $request->session()->forget(
            'principal_registration'
        );

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route(
            'verification.notice'
        );
    }

    private function validRegistrationSession(
        mixed $registration,
        string $token
    ): bool {
        if (
            ! is_array($registration) ||
            $token === ''
        ) {
            return false;
        }

        $requiredKeys = [
            'registry_id',
            'normalized_nic',
            'token_hash',
            'verified_at',
        ];

        foreach ($requiredKeys as $key) {
            if (! array_key_exists(
                $key,
                $registration
            )) {
                return false;
            }
        }

        if (
            now()->timestamp -
                (int) $registration['verified_at']
            > 900
        ) {
            return false;
        }

        return hash_equals(
            $registration['token_hash'],
            hash('sha256', $token)
        );
    }
}
