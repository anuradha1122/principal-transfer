<?php

namespace App\Http\Controllers\Zonal;

use App\Http\Controllers\Controller;
use App\Models\TransferApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request
    ): Response {
        $user = $request->user();

        abort_unless(
            $user->hasRole('Super Admin')
            || (
                $user->hasRole('Zonal Director')
                && $user->assigned_zone_id !== null
            ),
            403
        );

        $applications =
            TransferApplication::query()
                ->when(
                    ! $user->hasRole('Super Admin'),
                    fn (Builder $query) => $query->where(
                        'origin_zone_id',
                        $user->assigned_zone_id
                    )
                );

        return Inertia::render(
            'Zonal/Dashboard/Index',
            [
                'zone' => $user->hasRole('Super Admin')
                    ? null
                    : $user->assignedZone
                        ?->only([
                            'id',
                            'name',
                            'code',
                            'district',
                        ]),

                'summary' => [
                    'submitted' => (
                        clone $applications
                    )
                        ->where(
                            'status',
                            TransferApplication::STATUS_SUBMITTED
                        )
                        ->count(),

                    'under_review' => (
                        clone $applications
                    )
                        ->where(
                            'status',
                            TransferApplication::STATUS_ZONAL_REVIEW
                        )
                        ->count(),

                    'approved' => (
                        clone $applications
                    )
                        ->where(
                            'status',
                            TransferApplication::STATUS_ZONAL_APPROVED
                        )
                        ->count(),

                    'rejected' => (
                        clone $applications
                    )
                        ->where(
                            'status',
                            TransferApplication::STATUS_ZONAL_REJECTED
                        )
                        ->count(),
                ],
            ]
        );
    }
}
