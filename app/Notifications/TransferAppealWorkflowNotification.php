<?php

namespace App\Notifications;

class TransferAppealWorkflowNotification extends BaseSystemNotification
{
    public function __construct(
        string $appealNumber,
        string $applicationNumber,
        string $principalName,
        string $status,
        string $actionUrl,
        string $recipientType = 'principal'
    ) {
        $normalizedStatus =
            strtolower($status);

        $approved = in_array(
            $normalizedStatus,
            [
                'approved',
                'appeal approved',
                'appeal_approved',
            ],
            true
        );

        $rejected = in_array(
            $normalizedStatus,
            [
                'rejected',
                'appeal rejected',
                'appeal_rejected',
            ],
            true
        );

        $returned = in_array(
            $normalizedStatus,
            [
                'returned',
                'returned for clarification',
                'returned_for_clarification',
            ],
            true
        );

        $submitted = in_array(
            $normalizedStatus,
            [
                'submitted',
                'appeal submitted',
                'appeal_submitted',
            ],
            true
        );

        $severity = match (true) {
            $approved => 'success',
            $rejected => 'danger',
            $returned => 'warning',
            $submitted => 'info',
            default => 'info',
        };

        $readableStatus =
            str_replace(
                '_',
                ' ',
                $status
            );

        $message =
            $recipientType === 'principal'
                ? "Your appeal {$appealNumber} for application {$applicationNumber} is now {$readableStatus}."
                : "{$principalName}'s appeal {$appealNumber} for application {$applicationNumber} is now {$readableStatus}.";

        parent::__construct(
            title: 'Transfer Appeal Updated',

            message: $message,

            category: 'transfer_appeal',

            severity: $severity,

            actionUrl: $actionUrl,

            actionLabel: 'View Appeal',

            metadata: [
                'appeal_number' => $appealNumber,

                'application_number' => $applicationNumber,

                'principal_name' => $principalName,

                'status' => $status,

                'recipient_type' => $recipientType,
            ]
        );
    }
}
