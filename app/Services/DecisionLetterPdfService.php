<?php

namespace App\Services;

use App\Models\TransferDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DecisionLetterPdfService
{
    public function generate(
        TransferDocument $document
    ): string {
        $document->loadMissing([
            'transferApplication.currentSchool.division.zone',
            'transferApplication.transferBoardDecision',
            'issuer',
        ]);

        $path = sprintf(
            'transfer-documents/%d/decision-letter-%d.pdf',
            $document->transfer_application_id,
            $document->id
        );

        $pdf = Pdf::loadView(
            'pdf.transfers.decision-letter',
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
