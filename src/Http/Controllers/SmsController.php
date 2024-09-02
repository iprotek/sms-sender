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

    public function push_notif_info(Request $request){

        $cli = \iProtek\Core\Helpers\PayHttp::client_info(); 
        if($cli)
            return $cli['socket_settings'];
        return [
            "is_active"=>false,
            "name"=>"",
            "key"=>"",
            "cluster"=>""
        ];
    }
}