<?php

namespace App\Notifications;

class ZonalDecisionRecordedNotification extends BaseSystemNotification
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
                'zonal approved',
                'zonal_approved',
            ],
            true
        );

        $rejected = in_array(
            $normalizedDecision,
            [
                'rejected',
                'zonal rejected',
                'zonal_rejected',
            ],
            true
        );

        $severity = match (true) {
            $approved => 'success',
            $rejected => 'danger',
            default => 'warning',
        };

        $readableDecision =
            str_replace(
                '_',
                ' ',
                $decision
            );

        $message =
            $recipientType === 'principal'
                ? "The Zonal review for transfer application {$applicationNumber} has been recorded as {$readableDecision}."
                : "The Zonal review for {$principalName}'s application {$applicationNumber} has been recorded as {$readableDecision}.";

        parent::__construct(
            title: 'Zonal Review Updated',

            message: $message,

            category: 'zonal_review',

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
