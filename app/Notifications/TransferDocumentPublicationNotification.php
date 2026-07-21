<?php

namespace App\Notifications;

class TransferDocumentPublicationNotification extends BaseSystemNotification
{
    public function __construct(
        string $documentNumber,
        string $applicationNumber,
        string $principalName,
        bool $published,
        string $actionUrl,
        string $recipientType = 'principal'
    ) {
        $status =
            $published
                ? 'published'
                : 'unpublished';

        $message =
            $recipientType === 'principal'
                ? "Transfer document {$documentNumber} for application {$applicationNumber} has been {$status}."
                : "Transfer document {$documentNumber} for {$principalName}'s application {$applicationNumber} has been {$status}.";

        parent::__construct(
            title: $published
                ? 'Transfer Document Published'
                : 'Transfer Document Unpublished',

            message: $message,

            category: 'transfer_document',

            severity: $published
                ? 'success'
                : 'warning',

            actionUrl: $actionUrl,

            actionLabel: 'View Document',

            metadata: [
                'document_number' => $documentNumber,

                'application_number' => $applicationNumber,

                'principal_name' => $principalName,

                'published' => $published,

                'recipient_type' => $recipientType,
            ]
        );
    }
}
