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
        private readonly DecisionLetterPdfService $decisionLetterPdfService,
        private readonly AuditLogService $auditLogService
    ) {}

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
                'document_type' => 'The selected document type is not available for this application decision.',
            ]);
        }

        return DB::transaction(
            function () use (
                $application,
                $user,
                $data
            ): TransferDocument {
                $document = TransferDocument::query()->create([
                    'transfer_application_id' => $application->id,

                    'document_type' => $data['document_type'],

                    'document_number' => $data['document_number'],

                    'issued_date' => $data['issued_date'],

                    'effective_date' => $data['effective_date']
                        ?? $application
                            ->transferBoardDecision
                            ?->effective_date,

                    'issued_by' => $user->id,

                    'remarks' => $data['remarks']
                        ?? null,
                ]);

                $generatedPath = $this->generatePdf(
                    $document
                );

                $document->update([
                    'generated_file_path' => $generatedPath,

                    'generated_at' => now(),
                ]);

                $this->auditLogService->document(
                    'transfer_document.generated',
                    $document,
                    [
                        'description' => sprintf(
                            '%s %s was generated for transfer application %s.',
                            $this->documentTypeLabel(
                                $document->document_type
                            ),
                            $document->document_number,
                            $application->application_number
                                ?? $application->id
                        ),
                        'parent' => $application,
                        'new_values' => [
                            'document_type' => $document->document_type,
                            'document_number' => $document->document_number,
                            'issued_date' => $document->issued_date,
                            'effective_date' => $document->effective_date,
                            'generated_at' => $document->generated_at,
                            'is_published' => (bool) $document->is_published,
                        ],
                        'metadata' => [
                            'transfer_application_id' => $application->id,
                            'application_number' => $application->application_number,
                            'storage_disk' => 'local',
                        ],
                        'user' => $user,
                    ]
                );

                return $document->fresh([
                    'transferApplication',
                    'issuer',
                ]);
            }
        );
    }

    public function uploadSignedCopy(
        TransferDocument $document,
        UploadedFile $file,
        ?User $user = null
    ): TransferDocument {
        return DB::transaction(
            function () use (
                $document,
                $file,
                $user
            ): TransferDocument {
                $document->loadMissing(
                    'transferApplication'
                );

                $oldSignedFileExists = false;
                $oldOriginalName = null;

                if (
                    $document->signed_file_path
                    && Storage::disk('local')
                        ->exists(
                            $document->signed_file_path
                        )
                ) {
                    $oldSignedFileExists = true;

                    Storage::disk('local')->delete(
                        $document->signed_file_path
                    );
                }

                $path = $file->storeAs(
                    "transfer-documents/{$document->transfer_application_id}/signed",
                    "signed-{$document->id}.pdf",
                    'local'
                );

                $document->update([
                    'signed_file_path' => $path,
                ]);

                $this->auditLogService->document(
                    $oldSignedFileExists
                        ? 'transfer_document.signed_copy_replaced'
                        : 'transfer_document.signed_copy_uploaded',
                    $document,
                    [
                        'description' => $oldSignedFileExists
                            ? sprintf(
                                'The signed copy of document %s was replaced.',
                                $document->document_number
                            )
                            : sprintf(
                                'A signed copy was uploaded for document %s.',
                                $document->document_number
                            ),
                        'parent' => $document->transferApplication,
                        'old_values' => [
                            'signed_copy_exists' => $oldSignedFileExists,
                            'original_name' => $oldOriginalName,
                        ],
                        'new_values' => [
                            'signed_copy_exists' => true,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                        ],
                        'metadata' => [
                            'transfer_application_id' => $document->transfer_application_id,
                            'document_number' => $document->document_number,
                            'document_type' => $document->document_type,
                            'storage_disk' => 'local',
                        ],
                        'user' => $user ?? auth()->user(),
                    ]
                );

                return $document->fresh([
                    'transferApplication',
                    'issuer',
                ]);
            }
        );
    }

    public function publish(
        TransferDocument $document,
        User $user
    ): TransferDocument {
        if (! $document->signed_file_path) {
            throw ValidationException::withMessages([
                'signed_document' => 'Upload the signed document before publishing the result.',
            ]);
        }

        return DB::transaction(
            function () use (
                $document,
                $user
            ): TransferDocument {
                $document->loadMissing(
                    'transferApplication'
                );

                if ($document->is_published) {
                    throw ValidationException::withMessages([
                        'document' => 'This transfer document is already published.',
                    ]);
                }

                $oldValues = [
                    'is_published' => (bool) $document->is_published,
                    'published_at' => $document->published_at,
                    'published_by' => $document->published_by,
                ];

                $document->update([
                    'is_published' => true,

                    'published_at' => now(),

                    'published_by' => $user->id,
                ]);

                $this->auditLogService->document(
                    'transfer_document.published',
                    $document,
                    [
                        'description' => sprintf(
                            'Transfer document %s was published for the Principal.',
                            $document->document_number
                        ),
                        'parent' => $document->transferApplication,
                        'old_values' => $oldValues,
                        'new_values' => [
                            'is_published' => true,
                            'published_at' => $document->published_at,
                            'published_by' => $user->id,
                        ],
                        'metadata' => [
                            'transfer_application_id' => $document->transfer_application_id,
                            'application_number' => $document
                                ->transferApplication
                                ?->application_number,
                            'document_type' => $document->document_type,
                            'document_number' => $document->document_number,
                        ],
                        'user' => $user,
                    ]
                );

                return $document->fresh([
                    'transferApplication',
                    'publisher',
                    'issuer',
                ]);
            }
        );
    }

    public function unpublish(
        TransferDocument $document,
        ?User $user = null
    ): TransferDocument {
        return DB::transaction(
            function () use (
                $document,
                $user
            ): TransferDocument {
                $document->loadMissing(
                    'transferApplication'
                );

                if (! $document->is_published) {
                    throw ValidationException::withMessages([
                        'document' => 'This transfer document is not currently published.',
                    ]);
                }

                $oldValues = [
                    'is_published' => (bool) $document->is_published,
                    'published_at' => $document->published_at,
                    'published_by' => $document->published_by,
                ];

                $document->update([
                    'is_published' => false,

                    'published_at' => null,

                    'published_by' => null,
                ]);

                $this->auditLogService->document(
                    'transfer_document.unpublished',
                    $document,
                    [
                        'description' => sprintf(
                            'Transfer document %s was unpublished.',
                            $document->document_number
                        ),
                        'parent' => $document->transferApplication,
                        'old_values' => $oldValues,
                        'new_values' => [
                            'is_published' => false,
                            'published_at' => null,
                            'published_by' => null,
                        ],
                        'metadata' => [
                            'transfer_application_id' => $document->transfer_application_id,
                            'application_number' => $document
                                ->transferApplication
                                ?->application_number,
                            'document_type' => $document->document_type,
                            'document_number' => $document->document_number,
                        ],
                        'user' => $user ?? auth()->user(),
                    ]
                );

                return $document->fresh([
                    'transferApplication',
                    'issuer',
                ]);
            }
        );
    }

    public function regenerate(
        TransferDocument $document,
        ?User $user = null
    ): TransferDocument {
        return DB::transaction(
            function () use (
                $document,
                $user
            ): TransferDocument {
                $document->loadMissing(
                    'transferApplication'
                );

                $oldGeneratedAt =
                    $document->generated_at;

                $oldGeneratedFileExisted = false;

                if (
                    $document->generated_file_path
                    && Storage::disk('local')
                        ->exists(
                            $document->generated_file_path
                        )
                ) {
                    $oldGeneratedFileExisted = true;

                    Storage::disk('local')->delete(
                        $document->generated_file_path
                    );
                }

                $path = $this->generatePdf(
                    $document
                );

                $document->update([
                    'generated_file_path' => $path,

                    'generated_at' => now(),
                ]);

                $this->auditLogService->document(
                    'transfer_document.regenerated',
                    $document,
                    [
                        'description' => sprintf(
                            'Transfer document %s was regenerated.',
                            $document->document_number
                        ),
                        'parent' => $document->transferApplication,
                        'old_values' => [
                            'generated_at' => $oldGeneratedAt,
                            'generated_file_existed' => $oldGeneratedFileExisted,
                        ],
                        'new_values' => [
                            'generated_at' => $document->generated_at,
                            'generated_file_exists' => true,
                        ],
                        'metadata' => [
                            'transfer_application_id' => $document->transfer_application_id,
                            'application_number' => $document
                                ->transferApplication
                                ?->application_number,
                            'document_type' => $document->document_type,
                            'document_number' => $document->document_number,
                            'storage_disk' => 'local',
                        ],
                        'user' => $user ?? auth()->user(),
                    ]
                );

                return $document->fresh([
                    'transferApplication',
                    'issuer',
                ]);
            }
        );
    }

    private function generatePdf(
        TransferDocument $document
    ): string {
        return match (
            $document->document_type
        ) {
            TransferDocument::TYPE_TRANSFER_ORDER => $this
                ->transferOrderPdfService
                ->generate($document),

            TransferDocument::TYPE_APPOINTMENT_LETTER => $this
                ->appointmentLetterPdfService
                ->generate($document),

            TransferDocument::TYPE_DECISION_LETTER => $this
                ->decisionLetterPdfService
                ->generate($document),

            default => throw ValidationException::withMessages([
                'document_type' => 'Unsupported transfer document type.',
            ]),
        };
    }

    private function documentTypeLabel(
        string $documentType
    ): string {
        return match ($documentType) {
            TransferDocument::TYPE_TRANSFER_ORDER => 'Transfer Order',

            TransferDocument::TYPE_APPOINTMENT_LETTER => 'Appointment Letter',

            TransferDocument::TYPE_DECISION_LETTER => 'Decision Letter',

            default => 'Transfer Document',
        };
    }
}
