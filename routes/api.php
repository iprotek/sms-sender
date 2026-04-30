<?php

use Illuminate\Support\Facades\Route; 
use iProtek\Core\Http\Controllers\Manage\FileUploadController; 
use iProtek\Core\Http\Controllers\AppVariableController;
use iProtek\SmsSender\Http\Controllers\MessageController;
use iProtek\SmsSender\Http\Controllers\SmsClientApiRequestLinkController;
use iProtek\SmsSender\Http\Controllers\SmsController;
//Route::prefix('sms-sender')->name('sms-sender')->group(function(){
  //  Route::get('/', [SmsController::class, 'index'])->name('.index');
//});
Route::prefix('api')->middleware('api')->name('api')->group(function(){ 

    Route::prefix('message')->name('.message')->group(function(){

      Route::prefix('group/{group_id}')->middleware(['pay.api', 'policy.control'])->name('api')->group(function(){ 
          //FILE UPLOADS
          //include(__DIR__.'/api/file-upload.php');

          //FILE UPLOADS
          //include(__DIR__.'/api/meta-data.php'); 
      });

    });

    //Route::get('/users', [MessageController::class, 'users']); 
    
    Route::prefix('sms-sender')->name('.sms-sender')->group(function(){

      Route::prefix('group/{group_id}')->middleware(['pay.api'])->name('api')->group(function(){ 
          //FILE UPLOADS
          //include(__DIR__.'/api/file-upload.php');

          //FILE UPLOADS
          //include(__DIR__.'/api/meta-data.php'); 
      });

      //GET RESPONSE FROM SENDER
      Route::post('response/{sms_client_api_id}', [
        "uses"=>[SmsClientApiRequestLinkController::class, 'api_response'],
        "description"=>"Get response from sms sender",
        "is_visible"=>false,
        "is_allow"=>true
      ])->name('.response')->middleware(['signed']);

    });

    Route::get('push-info', [
      "uses"=>[SmsController::class, 'push_info'],
      "description"=>"Get push info for messaging such as websocket",
      "is_visible"=>false,
      "is_allow"=>true
    ])->name('push-info');
}); 
