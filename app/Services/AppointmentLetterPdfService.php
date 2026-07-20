<?php

namespace App\Services;

use App\Models\TransferDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class AppointmentLetterPdfService
{
    public function generate(
        TransferDocument $document
    ): string {
        $document->loadMissing([
            'transferApplication.currentSchool.division.zone',
            'transferApplication.transferBoardDecision.recommendedSchool.division.zone',
            'issuer',
        ]);

        $path = sprintf(
            'transfer-documents/%d/appointment-letter-%d.pdf',
            $document->transfer_application_id,
            $document->id
        );

        $pdf = Pdf::loadView(
            'pdf.transfers.appointment-letter',
            [
                'document' =>
                    $document,

                'application' =>
                    $document->transferApplication,

                'decision' =>
                    $document
                        ->transferApplication
                        ->transferBoardDecision,
            ]
        )->setPaper(
            'a4',
            'portrait'
        );

        Storage::disk('local')->put(
            $path,
            $pdf->output()
        );

        return $path;
    }
}
