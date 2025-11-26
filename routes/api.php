<?php
use App\Http\Controllers\DigiflazzWebhookController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
Route::post('/webhook/digiflazz', [DigiflazzWebhookController::class, 'handle']);
Route::get('/orders/{code}/status', [CheckoutController::class, 'status'])
    ->name('api.orders.status');