<?php

namespace App\Notifications;

class ProvincialDecisionRecordedNotification extends BaseSystemNotification
{
    public function __construct(
        string $applicationNumber,
        string $principalName,
        string $decision,
        string $actionUrl,
        string $recipientType = 'principal'
    ) {
        $normalizedDecision =
            strtolower($decision);

        $approved = in_array(
            $normalizedDecision,
            [
                'approved',
                'provincial approved',
                'provincial_approved',
            ],
            true
        );

        $rejected = in_array(
            $normalizedDecision,
            [
                'rejected',
                'provincial rejected',
                'provincial_rejected',
            ],
            true
        );

        $returned = in_array(
            $normalizedDecision,
            [
                'returned to zone',
                'return to zone',
                'returned_to_zone',
            ],
            true
        );

        $severity = match (true) {
            $approved => 'success',
            $rejected => 'danger',
            $returned => 'warning',
            default => 'info',
        };

        $readableDecision =
            str_replace(
                '_',
                ' ',
                $decision
            );

        $message =
            $recipientType === 'principal'
                ? "The Provincial review for transfer application {$applicationNumber} has been recorded as {$readableDecision}."
                : "The Provincial review for {$principalName}'s application {$applicationNumber} has been recorded as {$readableDecision}.";

        parent::__construct(
            title: 'Provincial Review Updated',

            message: $message,

            category: 'provincial_review',

            severity: $severity,

            actionUrl: $actionUrl,

            actionLabel: 'View Application',

            metadata: [
                'application_number' => $applicationNumber,

                'principal_name' => $principalName,

                'decision' => $decision,

                'recipient_type' => $recipientType,
            ]
        );
    }
}
