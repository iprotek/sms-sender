<?php

use iProtek\SmsSender\Http\Controllers\MessageController;

Route::prefix('message')->name('.message')->group(function(){

    Route::get('users', [MessageController::class, 'users'])
        ->defaults("_description", "Get all users for the entire application")
        ->defaults("_is_visible",false)
        ->defaults("_is_allow",true)
        ->name('.users');

    Route::get('/push-notif-info', [MessageController::class, 'push_notif_info'])
        ->defaults("_description", "Get push notification info")
        ->defaults("_is_visible",false)
        ->defaults("_is_allow",true)
        ->name('.push-notif-info');

    Route::get('/notifications',  [MessageController::class, 'notifications'])
        ->defaults("_description", "Show notification")
        ->defaults("_is_visible",false)
        ->defaults("_is_allow",true)
        ->name('.notifications');

    Route::prefix('dm')->name('.dm')->group(function(){ 
        Route::get('contact/{contact_id}',  [MessageController::class, 'get_contact_message'])
            ->defaults("_description", "Show dm")
            ->defaults("_is_visible",true)
            ->defaults("_is_allow",true)
            ->name('.get-contact');
        Route::post('contact/{contact_id}', [MessageController::class, 'post_contact_message'])
            ->defaults("_description", "Send dm")
            ->defaults("_is_visible",true)
            ->defaults("_is_allow",true)
            ->name('.post-contact');
    });

    Route::prefix('sms')->name('.sms')->group(function(){
        Route::get('contact/{mobile_no}', [MessageController::class, 'get_sms_message'])
             ->defaults("_description", "Show sms")
             ->defaults("_is_visible",true)
             ->defaults("_is_allow",true)
             ->name('.get-contact'); 
        Route::post('contact/{mobile_no}', [MessageController::class, 'post_sms_message'])
            ->defaults("_description", "Send sms")
            ->defaults("_is_visible",true)
            ->defaults("_is_allow",true)
            ->name('.post-contact');
    });
});