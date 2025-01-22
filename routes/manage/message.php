<?php

use iProtek\SmsSender\Http\Controllers\MessageController;

Route::prefix('message')->name('.message')->group(function(){
    Route::get('users', [MessageController::class, 'users'])->name('.users');
    Route::get('/push-notif-info', [MessageController::class, 'push_notif_info'])->name('.push-notif-info');
    Route::get('/notifications', [MessageController::class, 'notifications'])->name('.notifications'); 

    Route::prefix('dm')->name('.dm')->group(function(){ 
        Route::get('contact/{contact_id}', [MessageController::class, 'get_contact_message'])->name('.get-contact');
        Route::post('contact/{contact_id}', [MessageController::class, 'post_contact_message'])->name('.post-contact');
    });
});