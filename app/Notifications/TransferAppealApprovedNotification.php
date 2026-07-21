<?php

namespace App\Notifications;

use App\Models\TransferAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferAppealApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TransferAppeal $appeal
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Transfer Appeal Approved')
            ->greeting('Hello '.$notifiable->name.',')
            ->line(
                'Your transfer appeal '.$this->appeal->appeal_number.
                ' has been approved.'
            )
            ->action(
                'View Appeal Decision',
                route(
                    'principal.transfer-appeals.show',
                    $this->appeal
                )
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transfer_appeal_approved',
            'appeal_id' => $this->appeal->id,
            'appeal_number' => $this->appeal->appeal_number,
            'message' => 'Your transfer appeal was approved.',
        ];
    }
}
