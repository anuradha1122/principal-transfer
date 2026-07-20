<?php

namespace App\Services;

use App\Models\TransferApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransferApplicationPdfService
{
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
