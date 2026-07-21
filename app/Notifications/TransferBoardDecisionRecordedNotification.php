<?php

namespace App\Notifications;

class TransferBoardDecisionRecordedNotification extends BaseSystemNotification
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
                'board approved',
                'board_approved',
            ],
            true
        );

        $rejected = in_array(
            $normalizedDecision,
            [
                'rejected',
                'board rejected',
                'board_rejected',
            ],
            true
        );

        $waitlisted = in_array(
            $normalizedDecision,
            [
                'waitlisted',
                'waitlist',
            ],
            true
        );

        $severity = match (true) {
            $approved => 'success',
            $rejected => 'danger',
            $waitlisted => 'warning',
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
                ? "The Transfer Board decision for application {$applicationNumber} is {$readableDecision}."
                : "The Transfer Board decision for {$principalName}'s application {$applicationNumber} is {$readableDecision}.";

        parent::__construct(
            title: 'Transfer Board Decision Recorded',

            message: $message,

            category: 'transfer_board_decision',

            severity: $severity,

            actionUrl: $actionUrl,

            actionLabel: 'View Decision',

            metadata: [
                'application_number' => $applicationNumber,

                'principal_name' => $principalName,

                'decision' => $decision,

                'recipient_type' => $recipientType,
            ]
        );
    }
}
