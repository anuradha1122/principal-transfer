<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        if ($user->hasRole('Super Admin')) {
            return redirect()
                ->route('admin.dashboard');
        }

        if ($user->hasRole('Principal')) {
            return redirect()
                ->route('principal.dashboard');
        }

        if ($user->hasRole('Zonal Director')) {
            abort_unless(
                $user->assigned_zone_id !== null,
                403,
                'No Zone has been assigned to this Zonal Director.'
            );

            return redirect()
                ->route('zonal.dashboard');
        }

        if (
            $user->hasRole(
                'Provincial Director'
            )
        ) {
            return redirect()
                ->route(
                    'provincial.dashboard'
                );
        }

        if (
            $user->hasRole(
                'Transfer Board Member'
            )
        ) {
            return redirect()
                ->route(
                    'transfer-board.dashboard'
                );
        }

        abort(
            403,
            'No dashboard has been assigned to this account.'
        );
    }
}
