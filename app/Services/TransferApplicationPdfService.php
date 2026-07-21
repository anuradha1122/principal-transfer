<?php

namespace App\Services;

use App\Models\TransferApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransferApplicationPdfService
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    public function generate(
        TransferApplication $application
    ): string {
        $application->loadMissing([
            'transferCycle',
            'principalProfile',
            'currentAppointment',
            'currentSchool.division.zone',
            'preferences.school.division.zone',
        ]);

        $oldPath = $application->submitted_pdf_path;
        $oldGeneratedAt =
            $application->submitted_pdf_generated_at;

        $directory =
            'private/transfer-applications/'
            .$application->transfer_cycle_id;

        $fileName = $this->fileName(
            $application
        );

        $path = "{$directory}/{$fileName}";

        $pdf = Pdf::loadView(
            'pdf.transfer-applications.submitted',
            [
                'application' => $application,
            ]
        )
            ->setPaper('a4')
            ->setOption(
                'defaultFont',
                'DejaVu Sans'
            );

        Storage::disk('local')->put(
            $path,
            $pdf->output()
        );

        $application->forceFill([
            'submitted_pdf_path' => $path,
            'submitted_pdf_generated_at' => now(),
        ])->saveQuietly();

        $event = $oldPath
            ? 'transfer_application.submitted_pdf_regenerated'
            : 'transfer_application.submitted_pdf_generated';

        $description = $oldPath
            ? sprintf(
                'Submitted application PDF for %s was regenerated.',
                $application->application_number
                    ?? $application->id
            )
            : sprintf(
                'Submitted application PDF for %s was generated.',
                $application->application_number
                    ?? $application->id
            );

        $this->auditLogService->document(
            $event,
            $application,
            [
                'description' => $description,
                'old_values' => [
                    'submitted_pdf_generated_at' => $oldGeneratedAt,
                ],
                'new_values' => [
                    'submitted_pdf_generated_at' => $application
                        ->submitted_pdf_generated_at,
                    'file_name' => $fileName,
                ],
                'metadata' => [
                    'transfer_cycle_id' => $application->transfer_cycle_id,
                    'application_number' => $application->application_number,
                    'storage_disk' => 'local',
                    'regenerated' => (bool) $oldPath,
                ],
                'user' => auth()->user(),
            ]
        );

        return $path;
    }

    public function ensureExists(
        TransferApplication $application
    ): string {
        if (
            $application->submitted_pdf_path
            && Storage::disk('local')->exists(
                $application->submitted_pdf_path
            )
        ) {
            return $application
                ->submitted_pdf_path;
        }

        return $this->generate(
            $application
        );
    }

    public function downloadName(
        TransferApplication $application
    ): string {
        return $this->fileName(
            $application
        );
    }

    private function fileName(
        TransferApplication $application
    ): string {
        $applicationNumber =
            $application->application_number
            ?: "APPLICATION-{$application->id}";

        return Str::slug(
            $applicationNumber
        ).'-submitted.pdf';
    }
}
