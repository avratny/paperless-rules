<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

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

// Paperless NGX Webhook endpoint
// Usage: POST /api/webhook/paperless?action=create
//        POST /api/webhook/paperless?action=change
Route::post('/webhook/paperless', [WebhookController::class, 'handle'])
    ->name('webhook.paperless');

