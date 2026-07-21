<?php

namespace App\Notifications;

use App\Models\TransferApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferApplicationZonalApprovedNotification extends Notification
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
            ->subject('Transfer Application Approved at Zonal Level')
            ->greeting("Dear {$notifiable->name},")
            ->line(
                'Your Principal transfer application has been approved at Zonal level.'
            )
            ->line(
                'Application Number: '
                .($this->transferApplication->application_number ?? 'Pending')
            )
            ->line(
                'Recommendation: '
                .(
                    $this->transferApplication
                        ->zonalReview
                        ?->recommendation
                    ?? 'Not specified'
                )
            )
            ->action(
                'View Application',
                route(
                    'principal.transfer-applications.show',
                    $this->transferApplication
                )
            )
            ->line(
                'The application will proceed according to the Provincial review workflow.'
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transfer_application_id' => $this->transferApplication->id,
            'application_number' => $this->transferApplication->application_number,
            'status' => $this->transferApplication->status,
            'recommendation' => $this->transferApplication
                ->zonalReview
                ?->recommendation,
            'message' => 'Your transfer application was approved at Zonal level.',
        ];
    }
}
