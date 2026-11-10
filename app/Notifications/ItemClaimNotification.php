<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ItemClaimNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $event,
        public string $title,
        public string $message,
        public string $itemName,
        public int $reportId,
        public ?int $claimId = null
    ) {}

    /**
     * Use Laravel's database notification channel.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Store notification information in the database.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event' => $this->event,
            'title' => $this->title,
            'message' => $this->message,
            'item_name' => $this->itemName,
            'report_id' => $this->reportId,
            'claim_id' => $this->claimId,
        ];
    }
}
