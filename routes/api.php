<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SmsController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/send-sms', [SmsController::class, 'sendSms']);
Route::get('/sms-reports', [SmsController::class, 'getSmsReports']);
Route::get('/sms-reports/{id}', [SmsController::class, 'getSmsReportDetail']);
Route::get('/sms-reports/filter/{date}', [SmsController::class, 'filterSmsReportsByDate']);



