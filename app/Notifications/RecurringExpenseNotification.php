<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class RecurringExpenseNotification extends Notification
{
    use Queueable;

    public $expense;

    public function __construct($expense)
    {
        $this->expense = $expense;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Pengeluaran Rutin Otomatis 💸')
            ->icon('https://cdn-icons-png.flaticon.com/512/2344/2344132.png')
            ->body("Pengeluaran '{$this->expense->note}' sebesar Rp " . number_format($this->expense->amount, 0, ',', '.') . " telah disalin ke bulan ini.")
            ->action('Cek Dashboard', 'view_dashboard')
            ->data(['id' => $this->expense->id]);
    }
}
