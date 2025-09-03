<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\TransactionController;

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);
Route::post('/midtrans/test-settlement/{id}', [MidtransWebhookController::class, 'testSettlement']);