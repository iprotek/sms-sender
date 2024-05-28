<?php

use Illuminate\Support\Facades\Route;
use iProtek\SmsSender\Http\Controllers\SmsController;

Route::prefix('sms-sender')->name('sms-sender')->group(function(){
    Route::get('/', [SmsController::class, 'index'])->name('.index');
});