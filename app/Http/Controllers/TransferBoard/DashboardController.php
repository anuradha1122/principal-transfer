<?php

namespace App\Http\Controllers\TransferBoard;

use App\Http\Controllers\Controller;
use App\Models\TransferApplication;
use App\Models\TransferBoardDecision;
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
                'Transfer Board Member',
                'Super Admin',
            ]),
            403
        );

        $applications =
            TransferApplication::query();

        return Inertia::render(
            'TransferBoard/Dashboard/Index',
            [
                'summary' => [
                    'awaiting_review' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_PROVINCIAL_APPROVED
                        )
                        ->count(),

                    'under_review' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_BOARD_REVIEW
                        )
                        ->count(),

                    'approved' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_APPROVED
                        )
                        ->count(),

                    'rejected' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_REJECTED
                        )
                        ->count(),

                    'waitlisted' => (clone $applications)
                        ->where(
                            'status',
                            TransferApplication::STATUS_WAITLISTED
                        )
                        ->count(),
                ],

                'recentDecisions' => TransferBoardDecision::query()
                    ->with([
                        'transferApplication:id,application_number,principal_name,status,origin_zone_id',
                        'transferApplication.originZone:id,name,code',
                        'reviewer:id,name',
                        'recommendedSchool:id,name,census_number',
                    ])
                    ->whereNot(
                        'decision',
                        TransferBoardDecision::DECISION_PENDING
                    )
                    ->latest(
                        'decided_at'
                    )
                    ->limit(8)
                    ->get(),
            ]
        );
    }
}
