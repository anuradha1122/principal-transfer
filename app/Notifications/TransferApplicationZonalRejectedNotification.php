<?php

namespace App\Notifications;

use App\Models\TransferApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferApplicationZonalRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly TransferApplication $transferApplication
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Transfer Application Rejected at Zonal Level')
            ->greeting("Dear {$notifiable->name},")
            ->line(
                'Your Principal transfer application has been rejected at Zonal level.'
            )
            ->line(
                'Application Number: '
                . ($this->transferApplication->application_number ?? 'Pending')
            )
            ->line(
                'Reason: '
                . (
                    $this->transferApplication
                        ->zonalReview
                        ?->rejection_reason
                    ?? 'No reason was recorded.'
                )
            )
            ->action(
                'View Application',
                route(
                    'principal.transfer-applications.show',
                    $this->transferApplication
                )
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transfer_application_id' =>
                $this->transferApplication->id,
            'application_number' =>
                $this->transferApplication->application_number,
            'status' => $this->transferApplication->status,
            'rejection_reason' =>
                $this->transferApplication
                    ->zonalReview
                    ?->rejection_reason,
            'message' =>
                'Your transfer application was rejected at Zonal level.',
        ];
    }
}
