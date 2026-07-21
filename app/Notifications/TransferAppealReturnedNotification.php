<?php

namespace App\Notifications;

use App\Models\TransferAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferAppealReturnedNotification extends Notification
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
            ->subject('Transfer Appeal Returned for Clarification')
            ->greeting('Hello '.$notifiable->name.',')
            ->line(
                'Your transfer appeal '.$this->appeal->appeal_number.
                ' has been returned for clarification.'
            )
            ->line($this->appeal->clarification_request)
            ->action(
                'Respond to Clarification',
                route(
                    'principal.transfer-appeals.show',
                    $this->appeal
                )
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transfer_appeal_returned',
            'appeal_id' => $this->appeal->id,
            'appeal_number' => $this->appeal->appeal_number,
            'message' => 'Your transfer appeal was returned for clarification.',
        ];
    }
}
