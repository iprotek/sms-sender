<?php

namespace iProtek\SmsSender\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{ 
    public function users(Request $request){

        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::get_client('/api/client-users' );
        return $result; 
    }

    public function push_notif_info(Request $request){

        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::get_client('/api/push-info', false, [
                "is_active"=>false,
                "name"=>"",
                "key"=>"",
                "cluster"=>"",
                "message"=>"Error Messenger"
            ]);
        return $result; 
    }

    public function notifications(Request $request){
        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::get_client('/api/message-notifications');
        return $result; 

    }

}