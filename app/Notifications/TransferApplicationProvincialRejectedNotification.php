<?php

namespace App\Notifications;

use App\Models\TransferApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransferApplicationProvincialRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TransferApplication $application
    ) {}

    public function via(object $notifiable): array
    {
        return [
            'database',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Provincial Review Rejected',

            'message' => "Application {$this->application->application_number} was rejected at Provincial level.",

            'transfer_application_id' => $this->application->id,

            'status' => $this->application->status,

            'url' => route(
                'principal.transfer-applications.show',
                $this->application
            ),
        ];
    }
}
