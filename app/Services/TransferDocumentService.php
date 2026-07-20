<?php

namespace App\Services;

use App\Models\TransferApplication;
use App\Models\TransferDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TransferDocumentService
{
    public function __construct(
        private readonly TransferOrderPdfService $transferOrderPdfService,
        private readonly AppointmentLetterPdfService $appointmentLetterPdfService,
        private readonly DecisionLetterPdfService $decisionLetterPdfService
    ) {
    }

    public function create(
        TransferApplication $application,
        User $user,
        array $data
    ): TransferDocument {
        if (
            ! $application->canGenerateDocumentType(
                $data['document_type']
            )
        ) {
            throw ValidationException::withMessages([
                'document_type' =>
                    'The selected document type is not available for this application decision.',
            ]);
        }

        return DB::transaction(
            function () use (
                $application,
                $user,
                $data
            ): TransferDocument {
                $document =
                    TransferDocument::create([
                        'transfer_application_id' =>
                            $application->id,

                        'document_type' =>
                            $data['document_type'],

                        'document_number' =>
                            $data['document_number'],

                        'issued_date' =>
                            $data['issued_date'],

                        'effective_date' =>
                            $data['effective_date']
                            ?? $application
                                ->transferBoardDecision
                                ?->effective_date,

                        'issued_by' =>
                            $user->id,

                        'remarks' =>
                            $data['remarks']
                            ?? null,
                    ]);

                $generatedPath =
                    $this->generatePdf(
                        $document
                    );

                $document->update([
                    'generated_file_path' =>
                        $generatedPath,

                    'generated_at' =>
                        now(),
                ]);

                return $document->fresh([
                    'transferApplication',
                    'issuer',
                ]);
            }
        );
    }

    public function uploadSignedCopy(
        TransferDocument $document,
        UploadedFile $file
    ): TransferDocument {
        return DB::transaction(
            function () use (
                $document,
                $file
            ): TransferDocument {
                if (
                    $document->signed_file_path
                    && Storage::disk('local')
                        ->exists(
                            $document
                                ->signed_file_path
                        )
                ) {
                    Storage::disk('local')
                        ->delete(
                            $document
                                ->signed_file_path
                        );
                }

                $path = $file->storeAs(
                    "transfer-documents/{$document->transfer_application_id}/signed",
                    "signed-{$document->id}.pdf",
                    'local'
                );

                $document->update([
                    'signed_file_path' =>
                        $path,
                ]);

                return $document->fresh();
            }
        );
    }

    public function publish(
        TransferDocument $document,
        User $user
    ): TransferDocument {
        if (
            ! $document->signed_file_path
        ) {
            throw ValidationException::withMessages([
                'signed_document' =>
                    'Upload the signed document before publishing the result.',
            ]);
        }

        $document->update([
            'is_published' =>
                true,

            'published_at' =>
                now(),

            'published_by' =>
                $user->id,
        ]);

        return $document->fresh();
    }

    public function unpublish(
        TransferDocument $document
    ): TransferDocument {
        $document->update([
            'is_published' =>
                false,

            'published_at' =>
                null,

            'published_by' =>
                null,
        ]);

        return $document->fresh();
    }

    public function regenerate(
        TransferDocument $document
    ): TransferDocument {
        if (
            $document->generated_file_path
            && Storage::disk('local')
                ->exists(
                    $document
                        ->generated_file_path
                )
        ) {
            Storage::disk('local')
                ->delete(
                    $document
                        ->generated_file_path
                );
        }

        $path = $this->generatePdf(
            $document
        );

        $document->update([
            'generated_file_path' =>
                $path,

            'generated_at' =>
                now(),
        ]);

        return $document->fresh();
    }

    private function generatePdf(
        TransferDocument $document
    ): string {
        return match (
            $document->document_type
        ) {
            TransferDocument::TYPE_TRANSFER_ORDER =>
                $this
                    ->transferOrderPdfService
                    ->generate($document),

            TransferDocument::TYPE_APPOINTMENT_LETTER =>
                $this
                    ->appointmentLetterPdfService
                    ->generate($document),

            TransferDocument::TYPE_DECISION_LETTER =>
                $this
                    ->decisionLetterPdfService
                    ->generate($document),

            default =>
                throw ValidationException::withMessages([
                    'document_type' =>
                        'Unsupported transfer document type.',
                ]),
        };
    }
}
