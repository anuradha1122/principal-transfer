<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\ReportFilterRequest;
use App\Services\Reports\ManagementAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ManagementReportController extends Controller
{
    public function index(
        ReportFilterRequest $request,
        ManagementAnalyticsService $analyticsService
    ): Response|RedirectResponse {
        $user = $request->user();

        if (! $user) {
            return redirect()
                ->route('login');
        }

        $filters = $request->validated();

        $analytics =
            $analyticsService->build(
                user: $user,
                filters: $filters
            );

        return Inertia::render(
            'Reports/Index',
            [
                'filters' => $filters,

                'summary' => $analytics['summary'],

                'statusDistribution' => $analytics[
                        'status_distribution'
                    ],

                'zoneDistribution' => $analytics[
                        'zone_distribution'
                    ],

                'monthlyTrend' => $analytics[
                        'monthly_trend'
                    ],

                'oldestPending' => $analytics[
                        'oldest_pending'
                    ],

                'filterOptions' => $analytics[
                        'filter_options'
                    ],

                'permissions' => [
                    'exportPdf' => $user->can(
                        'export reports pdf'
                    ),

                    'exportExcel' => $user->can(
                        'export reports excel'
                    ),
                ],
            ]
        );
    }
}
