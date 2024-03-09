<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
Route::get('/confirm-email/{token}', [SubscriptionController::class, 'confirmEmail']);
Route::get('/changes', [SubscriptionController::class, 'changes']);
Route::get('/subscriptions', [SubscriptionController::class, 'subscriptions']);


