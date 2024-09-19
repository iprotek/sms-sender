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
        
        //GETTING SOCKET PUSH INFO
        $client_info = \iProtek\Core\Helpers\PayHttp::client_info();
        if($client_info){

            if(is_array($client_info)){
                return $client_info['socket_settings'];
                
                $socket_settings = $client_info['socket_settings'];

                if($socket_settings){
                    return [
                        "is_active"=>$socket_settings->is_active,
                        "name"=>$socket_settings->socket_name,
                        "key"=>$socket_settings->key,
                        "cluster"=>$socket_settings->cluster
                    ];
                }
            }
            $socket_settings =  $client_info->socket_settings;
            if($socket_settings){
                return [
                    "is_active"=>$socket_settings->is_active,
                    "name"=>$socket_settings->socket_name,
                    "key"=>$socket_settings->key,
                    "cluster"=>$socket_settings->cluster
                ];
            }
        }
        return [
            "is_active"=>false,
            "name"=>"",
            "key"=>"",
            "cluster"=>"",
            "message"=>"Not Found."
        ];
    }

}