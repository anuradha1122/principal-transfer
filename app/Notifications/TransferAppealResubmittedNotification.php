<?php

namespace App\Notifications;

use App\Models\TransferAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferAppealResubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TransferAppeal $appeal
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Transfer Appeal Resubmitted')
            ->greeting('Hello '.$notifiable->name.',')
            ->line(
                'Clarification has been submitted for appeal '.
                $this->appeal->appeal_number.'.'
            )
            ->action(
                'Review Appeal',
                route(
                    'transfer-board.transfer-appeals.show',
                    $this->appeal
                )
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transfer_appeal_resubmitted',
            'appeal_id' => $this->appeal->id,
            'appeal_number' => $this->appeal->appeal_number,
            'message' => 'A transfer appeal has been resubmitted.',
        ];
    }
}
