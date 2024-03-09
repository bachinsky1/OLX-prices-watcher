<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Ad;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PriceChangedNotification extends Notification
{
    use Queueable;

    public $ads;
    public $newPrice;

    /**
     * Створення нового екземпляру повідомлення.
     *
     * @param  \App\Models\Ad  $ad
     * @param  string  $newPrice
     */
    public function __construct($ads)
    {
        $this->ads = $ads;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Зміна ціни на оголошення')
            ->greeting('Вітаємо!');

        $mailMessage
            ->line(new HtmlString('<h2>Ціни на наступні товари було змінено:</h2>'))
            ->line(new HtmlString('<hr>'));

        foreach ($this->ads as $ad) {
            $mailMessage
                ->line(new HtmlString("<h3>{$ad['title']}</h3>"))
                ->line(new HtmlString("<p>Нова ціна: {$ad['newPrice']} {$ad['currency_symbol']}<p>"))
                ->line(new HtmlString("<a href='" . url($ad['url']) . "' style='background-color: #3490dc; color: #ffffff; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; border-radius: 5px;' target='_blank'>Переглянути оголошення</a>"))
                ->line(new HtmlString('<hr>'));
        }


        return $mailMessage->line('Дякуємо, що користуєтесь нашим сервісом!');
    }

    public function toArray($notifiable): array
    {
        $adsArray = [];

        foreach ($this->ads as $ad) {
            $adsArray[] = [
                'title' => $ad['title'],
                'new_price' => $ad['newPrice'],
                'url' => $ad['url'],
                'currency_symbol' => $ad['currency_symbol'],
            ];
        }

        return [
            'ads' => $adsArray,
        ];
    }

}
