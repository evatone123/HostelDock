<?php

use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\MomoCallbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/momo/callback', [MomoCallbackController::class, 'handle']);

Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify'])->name('api.whatsapp.webhook');
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'notify']);
