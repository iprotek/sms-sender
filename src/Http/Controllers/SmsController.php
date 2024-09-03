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
        $base = config('iprotek.pay_message_url');

        if( !$base || !trim($base)){

            return [
                "is_active"=>null,
                "name"=>"",
                "key"=>"",
                "cluster"=>""
            ];

        }
        
        $url = $base."/api/push-info";
        $cli = \iProtek\Core\Helpers\PayHttp::get_client_load($url); 
        if($cli){
            return $cli;
            //return $cli['socket_settings'];
        }
        return [
            "is_active"=>false,
            "name"=>"",
            "key"=>"",
            "cluster"=>""
        ];
    }
}