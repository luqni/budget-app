<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class DailyQuoteNotification extends Notification
{
    use Queueable;

    public $quote;

    public function __construct($quote)
    {
        $this->quote = $quote;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Renungan Pagi ☀️')
            ->icon('https://cdn-icons-png.flaticon.com/512/2344/2344132.png')
            ->body($this->quote->content)
            ->action('Lihat Detail', 'view_quote')
            ->data(['id' => $this->quote->id]);
    }
}
