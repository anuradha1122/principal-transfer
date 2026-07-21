<?php

namespace App\Http\Controllers\Provincial;

use App\Http\Controllers\Controller;
use App\Models\TransferApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->hasAnyRole([
                'Provincial Director',
                'Super Admin',
            ]),
            403
        );

        $applications =
            TransferApplication::query();

        return Inertia::render(
            'Provincial/Dashboard/Index',
            [
                'summary' => [
                    'awaiting_review' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_ZONAL_APPROVED
                        )
                        ->count(),

                    'under_review' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_PROVINCIAL_REVIEW
                        )
                        ->count(),

                    'approved' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_PROVINCIAL_APPROVED
                        )
                        ->count(),

                    'rejected' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_PROVINCIAL_REJECTED
                        )
                        ->count(),

                    'returned_to_zone' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_RETURNED_TO_ZONE
                        )
                        ->count(),
                ],

                'zoneSummary' => TransferApplication::query()
                    ->selectRaw(
                        'origin_zone_id, COUNT(*) as total'
                    )
                    ->whereIn(
                        'status',
                        [
                            TransferApplication::STATUS_ZONAL_APPROVED,
                            TransferApplication::STATUS_PROVINCIAL_REVIEW,
                            TransferApplication::STATUS_PROVINCIAL_APPROVED,
                            TransferApplication::STATUS_PROVINCIAL_REJECTED,
                            TransferApplication::STATUS_RETURNED_TO_ZONE,
                        ]
                    )
                    ->with(
                        'originZone:id,name,code'
                    )
                    ->groupBy(
                        'origin_zone_id'
                    )
                    ->get()
                    ->map(
                        fn (
                            TransferApplication $application
                        ): array => [
                            'zone' => $application
                                ->originZone
                                ?->name
                                ?? 'Unknown Zone',

                            'code' => $application
                                ->originZone
                                ?->code,

                            'total' => (int) $application->total,
                        ]
                    ),
            ]
        );
    }
}
