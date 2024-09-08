<?php

use Illuminate\Support\Facades\Route; 
use iProtek\SmsSender\Http\Controllers\SmsController;
use iProtek\SmsSender\Http\Controllers\SmsTicketController;

Route::middleware(['web'])->group(function(){
 
    Route::middleware(['auth'])->prefix('manage/sms-sender')->name('manage.sms-sender')->group(function(){
        
        //MESSAGE CHAT NOTIFICATIONS
        Route::get('/push-notif-info', [SmsController::class, 'push_notif_info'])->name('.push-notif-info'); 

        //TICKETS
        Route::prefix('ticket')->name('.ticket')->group(function(){
            Route::get('list', [SmsTicketController::class, 'list'])->name('.push-notif-info'); 
            Route::get('get-info/{id}', [SmsTicketController::class, 'get_info'])->name('.get-info'); 
            Route::post('add', [SmsTicketController::class, 'add'])->name('.add'); 
            Route::put('update', [SmsTicketController::class, 'update'])->name('.update'); 
            Route::delete('delete/{id}', [SmsTicketController::class, 'remove'])->name('.delete'); 
            Route::post('cater/{id}', [SmsTicketController::class, 'cater_ticket'])->name('.cater'); 
            Route::post('update-status/{id}', [SmsTicketController::class, 'update_status'])->name('.update-status'); 
        });

    });
    
    Route::prefix('helpdesk')->name('helpdesk')->group(function(){
        
        //REQUIRES SIGNATURE
        Route::middleware(['signed','throttle:20,1'])->prefix('ticket')->name('.ticket')->group(function(){
            Route::get('response/{id}', [SmsTicketController::class, 'response_view'])->name('.response-get');
            Route::post('response/{id}', [SmsTicketController::class, 'response_post'])->name('.response-post');
        });

    });
  
});