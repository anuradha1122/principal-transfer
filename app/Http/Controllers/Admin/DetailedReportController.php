<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArrayReportExport;
use App\Http\Controllers\Controller;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Models\TransferDocument;
use App\Models\Zone;
use App\Services\DetailedTransferReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class DetailedReportController extends Controller
{
    public function __construct(
        private readonly DetailedTransferReportService $reportService
    ) {}

    public function applications(
        Request $request
    ): Response {
        $this->authorizeReport($request);

        $filters =
            $this->filters($request);

        return Inertia::render(
            'Admin/Reports/Applications',
            [
                'rows' => $this->reportService
                    ->applications(
                        $request->user(),
                        $filters
                    ),

                ...$this->sharedProps(
                    $request,
                    $filters
                ),
            ]
        );
    }

    public function decisions(
        Request $request
    ): Response {
        $this->authorizeReport($request);

        $filters =
            $this->filters($request);

        return Inertia::render(
            'Admin/Reports/Decisions',
            [
                'rows' => $this->reportService
                    ->decisions(
                        $request->user(),
                        $filters
                    ),

                ...$this->sharedProps(
                    $request,
                    $filters
                ),
            ]
        );
    }

    public function appeals(
        Request $request
    ): Response {
        $this->authorizeReport($request);

        $filters =
            $this->filters($request);

        return Inertia::render(
            'Admin/Reports/Appeals',
            [
                'rows' => $this->reportService
                    ->appeals(
                        $request->user(),
                        $filters
                    ),

                ...$this->sharedProps(
                    $request,
                    $filters
                ),
            ]
        );
    }

    public function documents(
        Request $request
    ): Response {
        $this->authorizeReport($request);

        $filters =
            $this->filters($request);

        return Inertia::render(
            'Admin/Reports/Documents',
            [
                'rows' => $this->reportService
                    ->documents(
                        $request->user(),
                        $filters
                    ),

                ...$this->sharedProps(
                    $request,
                    $filters
                ),

                'documentTypes' => [
                    TransferDocument::TYPE_TRANSFER_ORDER,
                    TransferDocument::TYPE_APPOINTMENT_LETTER,
                    TransferDocument::TYPE_DECISION_LETTER,
                ],
            ]
        );
    }

    public function applicationsPdf(
        Request $request
    ): HttpResponse {
        return $this->pdf(
            $request,
            'Transfer Application Report',
            'applications',
            $this->reportService->applications(
                $request->user(),
                $this->filters($request),
                false
            ),
            'transfer-applications-report.pdf'
        );
    }

    public function decisionsPdf(
        Request $request
    ): HttpResponse {
        return $this->pdf(
            $request,
            'Transfer Decision Report',
            'decisions',
            $this->reportService->decisions(
                $request->user(),
                $this->filters($request),
                false
            ),
            'transfer-decisions-report.pdf'
        );
    }

    public function appealsPdf(
        Request $request
    ): HttpResponse {
        return $this->pdf(
            $request,
            'Transfer Appeal Report',
            'appeals',
            $this->reportService->appeals(
                $request->user(),
                $this->filters($request),
                false
            ),
            'transfer-appeals-report.pdf'
        );
    }

    public function documentsPdf(
        Request $request
    ): HttpResponse {
        return $this->pdf(
            $request,
            'Transfer Document Report',
            'documents',
            $this->reportService->documents(
                $request->user(),
                $this->filters($request),
                false
            ),
            'transfer-documents-report.pdf'
        );
    }

    public function applicationsExcel(
        Request $request
    ): BinaryFileResponse {
        $this->authorizeExport($request);

        $rows = $this->reportService
            ->applications(
                $request->user(),
                $this->filters($request),
                false
            );

        return Excel::download(
            new ArrayReportExport(
                [
                    'Application Number',
                    'Principal',
                    'NIC',
                    'Employee Number',
                    'Cycle',
                    'Zone',
                    'Current School',
                    'Designation',
                    'Service Grade',
                    'Transfer Reason',
                    'Status',
                    'Submitted At',
                ],
                $rows->map(
                    fn (array $row): array => [
                        $row['application_number'],
                        $row['principal_name'],
                        $row['nic'],
                        $row['employee_number'],
                        $row['cycle'],
                        $row['zone'],
                        $row['current_school'],
                        $row['designation'],
                        $row['service_grade'],
                        $row['transfer_reason'],
                        $row['status'],
                        $row['submitted_at'],
                    ]
                )
            ),
            'transfer-applications-report.xlsx'
        );
    }

    public function decisionsExcel(
        Request $request
    ): BinaryFileResponse {
        $this->authorizeExport($request);

        $rows = $this->reportService
            ->decisions(
                $request->user(),
                $this->filters($request),
                false
            );

        return Excel::download(
            new ArrayReportExport(
                [
                    'Application Number',
                    'Principal',
                    'Cycle',
                    'Zone',
                    'Current School',
                    'Decision',
                    'Decision Reference',
                    'Recommended School',
                    'Effective Date',
                    'Remarks',
                    'Decided At',
                ],
                $rows->map(
                    fn (array $row): array => [
                        $row['application_number'],
                        $row['principal_name'],
                        $row['cycle'],
                        $row['zone'],
                        $row['current_school'],
                        $row['decision'],
                        $row['decision_reference'],
                        $row['recommended_school'],
                        $row['effective_date'],
                        $row['remarks'],
                        $row['decided_at'],
                    ]
                )
            ),
            'transfer-decisions-report.xlsx'
        );
    }

    public function appealsExcel(
        Request $request
    ): BinaryFileResponse {
        $this->authorizeExport($request);

        $rows = $this->reportService
            ->appeals(
                $request->user(),
                $this->filters($request),
                false
            );

        return Excel::download(
            new ArrayReportExport(
                [
                    'Appeal Number',
                    'Application Number',
                    'Principal',
                    'Cycle',
                    'Zone',
                    'Application Status',
                    'Appeal Status',
                    'Grounds',
                    'Submitted At',
                    'Decided At',
                ],
                $rows->map(
                    fn (array $row): array => [
                        $row['appeal_number'],
                        $row['application_number'],
                        $row['principal_name'],
                        $row['cycle'],
                        $row['zone'],
                        $row['application_status'],
                        $row['appeal_status'],
                        $row['grounds'],
                        $row['submitted_at'],
                        $row['decided_at'],
                    ]
                )
            ),
            'transfer-appeals-report.xlsx'
        );
    }

    public function documentsExcel(
        Request $request
    ): BinaryFileResponse {
        $this->authorizeExport($request);

        $rows = $this->reportService
            ->documents(
                $request->user(),
                $this->filters($request),
                false
            );

        return Excel::download(
            new ArrayReportExport(
                [
                    'Document Number',
                    'Document Type',
                    'Application Number',
                    'Principal',
                    'Cycle',
                    'Zone',
                    'Application Status',
                    'Issued Date',
                    'Effective Date',
                    'Issuer',
                    'Signed Copy',
                    'Published',
                    'Published By',
                    'Published At',
                ],
                $rows->map(
                    fn (array $row): array => [
                        $row['document_number'],
                        $row['document_type'],
                        $row['application_number'],
                        $row['principal_name'],
                        $row['cycle'],
                        $row['zone'],
                        $row['application_status'],
                        $row['issued_date'],
                        $row['effective_date'],
                        $row['issuer'],
                        $row['has_signed_copy']
                            ? 'Yes'
                            : 'No',
                        $row['is_published']
                            ? 'Yes'
                            : 'No',
                        $row['published_by'],
                        $row['published_at'],
                    ]
                )
            ),
            'transfer-documents-report.xlsx'
        );
    }

    private function sharedProps(
        Request $request,
        array $filters
    ): array {
        $user = $request->user();

        return [
            'filters' => $filters,

            'transferCycles' => TransferCycle::query()
                ->orderByDesc('id')
                ->get([
                    'id',
                    'name',
                ]),

            'zones' => Zone::query()
                ->when(
                    $user->hasRole(
                        'Zonal Director'
                    ),
                    fn ($query) => $query->where(
                        'id',
                        $user
                            ->assigned_zone_id
                    )
                )
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                ]),

            'statuses' => TransferApplication::statusOptions(),

            'canExport' => $user->can(
                'export reports'
            )
                || $user->can(
                    'export transfer applications'
                ),

            'scope' => [
                'is_zonal' => $user->hasRole(
                    'Zonal Director'
                ),
            ],
        ];
    }

    private function filters(
        Request $request
    ): array {
        return $request->only([
            'transfer_cycle_id',
            'zone_id',
            'status',
            'document_type',
            'is_published',
            'date_from',
            'date_to',
        ]);
    }

    private function pdf(
        Request $request,
        string $title,
        string $type,
        Collection $rows,
        string $filename
    ): HttpResponse {
        $this->authorizeExport($request);

        return Pdf::loadView(
            'reports.detailed',
            [
                'title' => $title,
                'type' => $type,
                'rows' => $rows,
                'filters' => $this->filters($request),
                'generatedAt' => now(),
            ]
        )
            ->setPaper(
                'a4',
                'landscape'
            )
            ->download(
                $filename
            );
    }

    private function authorizeReport(
        Request $request
    ): void {
        abort_unless(
            $request->user()->can(
                'view reports'
            ),
            403
        );
    }

    private function authorizeExport(
        Request $request
    ): void {
        abort_unless(
            $request->user()->can(
                'export reports'
            )
            || $request->user()->can(
                'export transfer applications'
            ),
            403
        );
    }
}
