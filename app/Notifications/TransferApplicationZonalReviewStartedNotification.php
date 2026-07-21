<?php

namespace App\Notifications;

use App\Models\TransferApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferApplicationZonalReviewStartedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly TransferApplication $transferApplication
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Transfer Application Zonal Review Started')
            ->greeting("Dear {$notifiable->name},")
            ->line(
                'Your Principal transfer application has entered Zonal review.'
            )
            ->line(
                'Application Number: '
                .($this->transferApplication->application_number ?? 'Pending')
            )
            ->line(
                'Current Status: '
                .$this->transferApplication->status
            )
            ->action(
                'View Application',
                route(
                    'principal.transfer-applications.show',
                    $this->transferApplication
                )
            )
            ->line(
                'You will be notified when the Zonal review decision is recorded.'
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transfer_application_id' => $this->transferApplication->id,
            'application_number' => $this->transferApplication->application_number,
            'status' => $this->transferApplication->status,
            'message' => 'Your transfer application has entered Zonal review.',
        ];
    }
}
