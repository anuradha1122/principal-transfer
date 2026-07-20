<?php

namespace App\Notifications;

use App\Models\TransferApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransferApplicationRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TransferApplication $application
    ) {
    }

    public function via(
        object $notifiable
    ): array {
        return [
            'database',
        ];
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'title' =>
                'Transfer Application Rejected',

            'message' =>
                "Application {$this->application->application_number} was rejected by the Transfer Board.",

            'transfer_application_id' =>
                $this->application->id,

            'status' =>
                $this->application->status,

            'url' =>
                route(
                    'principal.transfer-applications.show',
                    $this->application
                ),
        ];
    }
}
