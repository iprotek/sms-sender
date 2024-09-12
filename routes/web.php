<?php

use Illuminate\Support\Facades\Route; 
use iProtek\SmsSender\Http\Controllers\SmsController;
use iProtek\SmsSender\Http\Controllers\SmsTicketController;
use iProtek\SmsSender\Http\Controllers\SmsTicketMessageController;
use iProtek\SmsSender\Http\Controllers\MessageController;

include(__DIR__.'/api.php');

Route::middleware(['web'])->group(function(){
 
    Route::middleware(['auth'])->prefix('manage')->name('manage')->group(function(){
        
        Route::prefix('sms-sender')->name('.sms-sender')->group(function(){

            //MESSAGE CHAT NOTIFICATIONS
            
            //TICKETS
            Route::prefix('ticket')->name('.ticket')->group(function(){
                Route::get('list', [SmsTicketController::class, 'list'])->name('.push-notif-info'); 
                Route::get('get-info/{id}', [SmsTicketController::class, 'get_info'])->name('.get-info'); 
                Route::post('add', [SmsTicketController::class, 'add'])->name('.add'); 
                Route::put('update', [SmsTicketController::class, 'update'])->name('.update'); 
                Route::delete('delete/{id}', [SmsTicketController::class, 'remove'])->name('.delete'); 
                Route::post('cater/{id}', [SmsTicketController::class, 'cater_ticket'])->name('.cater'); 
                Route::post('update-status/{id}', [SmsTicketController::class, 'update_status'])->name('.update-status'); 

                Route::get('/{id}/messages', [SmsTicketMessageController::class, 'ticket_message'] )->name('.messages');
                Route::post('/{id}/message-add', [SmsTicketMessageController::class, 'add'] )->name('.message-add');

            });

            
        });

        //Route Message
        Route::prefix('message')->name('.message')->group(function(){
            Route::get('users', [MessageController::class, 'users'])->name('.users');
            Route::get('/push-notif-info', [MessageController::class, 'push_notif_info'])->name('.push-notif-info');
            Route::get('/notifications', [MessageController::class, 'notifications'])->name('.notifications'); 

            Route::prefix('dm')->name('.dm')->group(function(){ 
                Route::get('contact/{contact_id}', [MessageController::class, 'get_contact_message'])->name('.get-contact');
                Route::post('contact/{contact_id}', [MessageController::class, 'post_contact_message'])->name('.post-contact');
            });
        });
        

        //HELPDESK
        Route::prefix('helpdesk')->name('.helpdesk')->group(function(){
            Route::get('/', [SmsTicketController::class, 'helpdesk']); 
        });






    });
    
    Route::prefix('helpdesk')->name('helpdesk')->group(function(){
        
        //REQUIRES SIGNATURE
        Route::middleware(['signed','throttle:20,1'])->prefix('ticket')->name('.ticket')->group(function(){
            Route::get('response/{id}', [SmsTicketController::class, 'response_view'])->name('.response-get');
            Route::post('response/{id}', [SmsTicketController::class, 'response_post'])->name('.response-post');
        });
        
        Route::middleware(['throttle:10,1'])->group(function(){
            Route::get('create', [SmsTicketController::class, 'create_get'])->name('.create-get');
            Route::post('create', [SmsTicketController::class, 'create_post'])->name('.create-post');
         });

    });
  
});