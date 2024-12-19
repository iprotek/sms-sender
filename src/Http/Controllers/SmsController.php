<?php

namespace iProtek\SmsSender\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function index(Request $request)
    {
        return view('sms-sender::index');
    }

    public function push_info(Request $request){
        return \iProtek\SmsSender\Helpers\SocketHelper::setting(false);
    }

}