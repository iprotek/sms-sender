<?php

use Illuminate\Support\Facades\Route; 
use iProtek\Core\Http\Controllers\Manage\FileUploadController; 
use iProtek\Core\Http\Controllers\AppVariableController;
use iProtek\SmsSender\Http\Controllers\MessageController;

//Route::prefix('sms-sender')->name('sms-sender')->group(function(){
  //  Route::get('/', [SmsController::class, 'index'])->name('.index');
//});
Route::prefix('api/message')->middleware('api')->group(function(){ 

    
    Route::prefix('group/{group_id}')->middleware(['pay.api'])->name('api')->group(function(){
            
        //FILE UPLOADS
        //include(__DIR__.'/api/file-upload.php');

        //FILE UPLOADS
        //include(__DIR__.'/api/meta-data.php');


        

    });

    //Route::get('/users', [MessageController::class, 'users']);

         
});