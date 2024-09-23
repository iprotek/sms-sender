<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\Core\Http\Controllers\_Common\_CommonController;
use iProtek\SmsSender\Models\SmsClientMessage;

class SmsClientMessageController extends _CommonController
{
    //
    public $guard = "admin";
 
    public function list(Request $request){
        $data = SmsClientMessage::on();
        $data->orderBy('id', 'DESC');
        return $data->paginate(10);
    }
}
