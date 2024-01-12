<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Route;


Route::middleware(['api'])->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::post('/send-sms', [SmsController::class, 'sendSms']);
        Route::get('/sms-reports', [SmsController::class, 'getSmsReports']);
        Route::get('/sms-reports/{smsReport}', [SmsController::class, 'getSmsReportDetail']);
    });
});





