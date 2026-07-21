<?php

namespace App\Notifications;

use App\Models\TransferAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferAppealSubmittedNotification extends Notification
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
            ->subject('New Transfer Appeal Submitted')
            ->greeting('Hello '.$notifiable->name.',')
            ->line(
                'A transfer appeal has been submitted under number '.
                $this->appeal->appeal_number.'.'
            )
            ->action(
                'Review Appeal',
                route(
                    'transfer-board.transfer-appeals.show',
                    $this->appeal
                )
            )
            ->line('Please review the appeal through the system.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transfer_appeal_submitted',
            'appeal_id' => $this->appeal->id,
            'appeal_number' => $this->appeal->appeal_number,
            'message' => 'A new transfer appeal has been submitted.',
        ];
    }
}
