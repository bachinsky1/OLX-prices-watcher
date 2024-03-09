<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Ad;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmEmail;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_subscribe_to_an_ad()
    {
        Mail::fake();

        $response = $this->post('/api/subscribe', [
            'ad_url' => 'https://www.olx.ua/d/uk/obyavlenie/starter-yamaha-250fzr-IDn3mVA.html?reason=ip%7Clister',
            'email' => 'bachinsky1@gmail.com',
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Ad::all());
        $this->assertCount(1, Email::all());
        Mail::assertSent(ConfirmEmail::class);
    }

    /** @test */
    public function a_user_can_confirm_email()
    {
        // Створення екземпляру Email з токеном для підтвердження
        $email = Email::create([
            'email' => 'example@test.com',
            'confirmation_token' => 'some_unique_token',
            'confirmed' => false,
        ]);

        // Відправка GET запиту для підтвердження email
        $response = $this->get('/api/confirm-email/' . $email->confirmation_token);

        // Перевірка, що користувач отримав правильний HTTP відповідь
        $response->assertStatus(200); // Перевірка на код відповіді 200

        // Оновлення екземпляру Email для отримання останніх змін
        $email->refresh();

        // Перевірка, що поле `confirmed` встановлено в true
        $this->assertTrue((boolval($email->confirmed)));

        // Перевірка, що поле `confirmation_token` очищено
        $this->assertNull($email->confirmation_token);

        // Додатково, можна перевірити, що користувач отримав правильну сторінку після підтвердження
        $response->assertViewIs('email_confirmed');
    }

}
