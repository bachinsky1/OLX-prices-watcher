<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Email;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmEmail;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'ad_url' => 'required|url',
            'email' => 'required|email',
        ]);

        // Знайдемо або створимо оголошення
        $ad = Ad::firstOrCreate(['url' => $validated['ad_url']]);

        // Перевіримо чи імейл вже існує
        $emailModel = Email::firstOrCreate(
            ['email' => $validated['email']],
            ['confirmation_token' => Str::random(60)] // Генеруємо токен для нових імейлів
        );

        // Створюємо підписку незалежно від статусу підтвердження електронної пошти
        $ad->subscriptions()->updateOrCreate([
            'email_id' => $emailModel->id
        ]);

        // Якщо електронна пошта ще не підтверджена, відправляємо запит на підтвердження
        if (!$emailModel->confirmed) {
            Mail::to($emailModel->email)->send(new ConfirmEmail($emailModel));
            return response()->json(['message' => 'Щоб завершити підписку, підтвердіть свою електронну адресу. Підтвердження надіслано електронною поштою.']);
        }

        return response()->json(['message' => 'Підписка успішна.']);
    }
    public function confirmEmail($token)
    {
        $email = Email::where('confirmation_token', $token)->first();

        // Якщо токен не знайдено, перевіримо, чи імейл вже підтверджено
        if (!$email) {
            $emailAlreadyConfirmed = Email::where('confirmed', true)->where('confirmation_token', null)->first();

            if ($emailAlreadyConfirmed) {
                // Якщо імейл вже підтверджено, покажемо відповідне повідомлення
                return view('email_already_confirmed'); // Blade шаблон для "Email already confirmed"
            } else {
                // Якщо імейл не знайдено і не підтверджено
                return view('email_not_found'); // Blade шаблон для "Email not found"
            }
        }

        // Якщо токен знайдено і імейл ще не підтверджено
        $email->confirmed = true;
        $email->confirmation_token = null;
        $email->save();

        return view('email_confirmed'); // Blade шаблон для "Email confirmed successfully"
    }

    public function changes()
    {
        $subscriptions = Subscription::with(['ad', 'email'])
            ->whereHas('email', function ($query) {
                $query->where('confirmed', true);
            })
            ->whereHas('ad.prices') // Перевіряємо, чи є ціни для оголошення
            ->paginate(10);
    
        // Перетворення результатів для включення додаткової інформації
        $subscriptions->getCollection()->transform(function ($subscription) {
            $subscription->ad->price = $subscription->ad->latest_price;
            return $subscription;
        });
    
        return response()->json($subscriptions);
    }
    
    
    public function subscriptions()
    {
        // Отримуємо підписки з відповідними зв'язками
        $subscriptions = Subscription::with(['ad', 'email'])->paginate(10);
    
        // Перетворення результатів для включення додаткової інформації
        $subscriptions->getCollection()->transform(function ($subscription) {
            return [
                'ad_id' => $subscription->ad_id,
                'url' => $subscription->ad->url,
                'email' => $subscription->email->email,
                'confirmed' => (bool) $subscription->email->confirmed,
                'created_at' => $subscription->created_at,
            ];
        });
    
        // Формування масиву links
        $links = collect([
            [
                'url' => $subscriptions->previousPageUrl(),
                'label' => '&laquo; Попередня',
                'active' => false,
            ],
            [
                'url' => $subscriptions->url(1),
                'label' => '1',
                'active' => $subscriptions->currentPage() === 1,
            ],
        ]);
    
        // Додавання посилань на проміжні сторінки
        for ($i = 2; $i <= $subscriptions->lastPage(); $i++) {
            $links->push([
                'url' => $subscriptions->url($i),
                'label' => (string) $i,
                'active' => $subscriptions->currentPage() === $i,
            ]);
        }
    
        // Додавання посилання на наступну сторінку
        $links->push([
            'url' => $subscriptions->nextPageUrl(),
            'label' => 'Наступна &raquo;',
            'active' => false,
        ]);
    
        return response()->json([
            'current_page' => $subscriptions->currentPage(),
            'data' => $subscriptions->getCollection(),
            'first_page_url' => $subscriptions->url(1),
            'from' => $subscriptions->firstItem(),
            'last_page' => $subscriptions->lastPage(),
            'last_page_url' => $subscriptions->url($subscriptions->lastPage()),
            'links' => $links,
            'next_page_url' => $subscriptions->nextPageUrl(),
            'path' => $subscriptions->path(),
            'per_page' => $subscriptions->perPage(),
            'prev_page_url' => $subscriptions->previousPageUrl(),
            'to' => $subscriptions->lastItem(),
            'total' => $subscriptions->total(),
        ]);
    }
}    
