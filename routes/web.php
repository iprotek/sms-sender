<?php

use Illuminate\Support\Facades\Route; 
use iProtek\SmsSender\Http\Controllers\SmsController;
use iProtek\SmsSender\Http\Controllers\SmsTicketController;

Route::middleware(['web'])->group(function(){
 
    Route::middleware(['auth'])->prefix('manage/sms-sender')->name('manage.sms-sender')->group(function(){
        //Route::get('/test', [SysNotificationController::class, 'index'])->name('.test');
        //Route::get('/', [SysNotificationController::class, 'index'])->name('.index');
        //Route::get('/system-updates', [SysNotificationController::class, 'system_updates'])->name('.system-updates');
        //oute::post('/check-system-updates', [SysNotificationController::class, 'check_system_updates'])->name('.check-system-updates');
        //Route::get('/check-system-updates', [SysNotificationController::class, 'check_system_updates'])->name('.get-check-system-updates');
        //Route::get('/system-updates-summary', [SysNotificationController::class, 'system_updates_summary'])->name('.check-system-summary');
        //Route::post('/apply-system-updates', [SysNotificationController::class, 'apply_system_updates'])->name('.apply-system-updates');
     
        //Route::get('/', [SmsController::class, 'index'])->name('.index'); 
        Route::get('/push-notif-info', [SmsController::class, 'push_notif_info'])->name('.push-notif-info'); 

        //TICKETS
        Route::prefix('ticket')->group(function(){
            Route::get('list', [SmsTicketController::class, 'list'])->name('.push-notif-info'); 
            Route::get('get-info/{id}', [SmsTicketController::class, 'get_info'])->name('.get-info'); 
            Route::post('add', [SmsTicketController::class, 'add'])->name('.add'); 
            Route::put('update', [SmsTicketController::class, 'update'])->name('.update'); 
            Route::delete('delete/{id}', [SmsTicketController::class, 'remove'])->name('.delete'); 
        });

    });
  
});