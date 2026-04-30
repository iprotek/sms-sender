<?php

use iProtek\SmsSender\Http\Controllers\MessageController;

Route::prefix('message')->name('.message')->group(function(){

    Route::get('users', [
        "uses"=>[MessageController::class, 'users'],
        "description"=>"Get all users for the entire application",
        "is_visible"=>false,
        "is_allow"=>true
    ])->name('.users');

    Route::get('/push-notif-info', [ "uses"=>[MessageController::class, 'push_notif_info'],
        "description"=>"Get push notification info",
        "is_visible"=>false,
        "is_allow"=>true
    ])->name('.push-notif-info');

    Route::get('/notifications', [
        "uses"=>[MessageController::class, 'notifications'],
        "description"=>"Show notification",
        "is_visible"=>false,
        "is_allow"=>true
    ])->name('.notifications'); 

    Route::prefix('dm')->name('.dm')->group(function(){ 
        Route::get('contact/{contact_id}', [
            "uses"=>[MessageController::class, 'get_contact_message'],
            "description"=>"Show dm",
            "is_visible"=>true,
            "is_allow"=>true
        ])->name('.get-contact');
        Route::post('contact/{contact_id}', [
            "uses"=>[MessageController::class, 'post_contact_message'],
            "description"=>"Send dm",
            "is_visible"=>true,
            "is_allow"=>true
        ])->name('.post-contact');
    });

    Route::prefix('sms')->name('.sms')->group(function(){
        Route::get('contact/{mobile_no}', [
            "uses"=>[MessageController::class, 'get_sms_message'],
            "description"=>"Show sms",
            "is_visible"=>true,
            "is_allow"=>true
        ])->name('.get-contact');
        Route::post('contact/{mobile_no}', [
            "uses"=>[MessageController::class, 'post_sms_message'],
            "description"=>"Send sms",
            "is_visible"=>true,
            "is_allow"=>true
        ])->name('.post-contact');
    });
});