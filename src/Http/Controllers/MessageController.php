<?php

namespace iProtek\SmsSender\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{ 
    public function users(Request $request){
        return config('iprotek_sms_sender.pay_message_url');
    }
}