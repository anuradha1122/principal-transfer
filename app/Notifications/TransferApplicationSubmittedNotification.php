<?php

namespace App\Notifications;

class TransferApplicationSubmittedNotification extends BaseSystemNotification
{
    public function __construct(
        string $applicationNumber,
        string $principalName,
        string $recipientType,
        string $actionUrl
    ) {
        $isPrincipal =
            $recipientType === 'principal';

        parent::__construct(
            title: $isPrincipal
                ? 'Transfer Application Submitted'
                : 'New Transfer Application Submitted',

            message: $isPrincipal
                ? "Your transfer application {$applicationNumber} has been submitted successfully."
                : "{$principalName} submitted transfer application {$applicationNumber} for review.",

            category: 'transfer_application',

            severity: 'info',

            actionUrl: $actionUrl,

            actionLabel: 'View Application',

            metadata: [
                'application_number' => $applicationNumber,

                'principal_name' => $principalName,

                'recipient_type' => $recipientType,
            ]
        );
    }
}
