<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirect the authenticated user to the correct dashboard.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('Super Admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Principal')) {
            return redirect()->route('principal.dashboard');
        }

        if ($user->hasRole('Zonal Director')) {
            return redirect()->route('zonal.dashboard');
        }

        if ($user->hasRole('Provincial Director')) {
            return redirect()->route('provincial.dashboard');
        }

        if ($user->hasRole('Transfer Board Member')) {
            return redirect()->route('transfer-board.dashboard');
        }

        if ($user->hasRole('Data Entry Officer')) {
            return redirect()->route('admin.dashboard');
        }

        abort(
            403,
            'Your account does not have an assigned system role.'
        );
    }
}
