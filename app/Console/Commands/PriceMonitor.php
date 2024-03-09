<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use Illuminate\Support\Facades\Http;
use App\Notifications\PriceChangedNotification;
use Illuminate\Support\Facades\Notification;

class PriceMonitor extends Command
{
    protected $signature = 'price:monitor';
    protected $description = 'Monitor and update price changes for subscribed ads';

    public function handle()
    {
        $this->info('Monitoring price changes...');

        $ads = Ad::whereHas('subscriptions.email', function ($query) {
            $query->where('confirmed', true);
        })->get();

        $changes = [];

        foreach ($ads as $ad) {
            $this->info("Processing: {$ad->url}");
            try {
                $response = Http::get($ad->url);
                if ($response->successful()) {
                    $content = $response->body();
                    $startMarker = 'window.__PRERENDERED_STATE__= "';
                    $endMarker = '}";';

                    // Знаходимо початкову позицію JSON
                    $startPos = strpos($content, $startMarker);
                    if ($startPos === false) {
                        echo "Start marker not found";
                        return;
                    }

                    // Додаємо довжину маркера до початкової позиції, щоб почати звідти
                    $startPos += strlen($startMarker);

                    // Знаходимо кінцеву позицію JSON
                    $endPos = strpos($content, $endMarker, $startPos);
                    if ($endPos === false) {
                        echo "End marker not found";
                        return;
                    }

                    // Враховуємо закриваючу дужку JSON
                    $endPos += 1; // Залишаємо закриваючу дужку включеною в виділений текст

                    // Виділяємо JSON
                    $jsonString = substr($content, $startPos, $endPos - $startPos);

                    // Відновлюємо видалені символи через escape-секвенції
                    $jsonString = stripslashes($jsonString);

                    // Перевіряємо і декодуємо JSON
                    $jsonData = json_decode($jsonString, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        // Визначення даних з $jsonData
                        $adData = $jsonData['ad']['ad'];
                        $priceData = $adData['price'];

                        $lastPrice = $ad->prices()->latest()->first();
                        // $newPrice = 200; // Для тесування відправки повідомлення про зміну ціни розкоментовуємо це і закоментовуємо натупну строку
                        $newPrice = $priceData['regularPrice']['value'];

                        if (!$lastPrice || $lastPrice->value != $newPrice) {
							$ad->prices()->create([
                                'title' => $adData['title'] ?? '',
                                'display_value' => $priceData['displayValue'] ?? '',
                                'value' => $newPrice ?? 0,
                                'currency_code' => $priceData['regularPrice']['currencyCode'] ?? '',
                                'currency_symbol' => $priceData['regularPrice']['currencySymbol'] ?? '',
                            ]);

                            $subscriptions = $ad->subscriptions()->whereHas('email', function ($query) {
                                $query->where('confirmed', true);
                            })->get();

                            foreach ($subscriptions as $subscription) {
                                $this->info("ad->url: {$ad->url}");
                                // Записуємо зміни для кожного підписника
                                $changes[$subscription->email->email][] = [
                                    'title' => $adData['title'],
                                    'newPrice' => $newPrice,
                                    'url' => $ad->url,
                                    'currency_symbol' => $priceData['regularPrice']['currencySymbol'] ?? '',
                                ];
                            }
                            
                            $this->info("Price updated for Ad ID: {$ad->id}");
                        } else {
                            $this->info("No price change for Ad ID: {$ad->id}");
                        }

                    } else {
                        $this->error("JSON decode error: " . json_last_error_msg());
                    }
                } else {
                    $this->error("Failed to load {$ad->url}");
                }
            } catch (\Exception $e) {
                $this->error("Error processing {$ad->url}: " . $e->getMessage());
            }
        }

        foreach ($changes as $email => $ads) {
            Notification::route('mail', $email)->notify(new PriceChangedNotification($ads));
        }
    
        $this->info('Price monitoring completed.');
    }
}
