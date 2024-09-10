<?php

namespace iProtek\SmsSender\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{ 
    public function users(Request $request){

        $client = \iProtek\SmsSender\Helpers\PayMessageHttp::client();
        
        $response = $client->get('/api/client-users');
        $response_code = $response->getStatusCode(); 
        if($response_code != 200 && $response_code != 201){
            return [
                "status"=>0,
                "data"=>[],
                "Api Invalidated."
            ]; 
        }
        $result = json_decode($response->getBody(), true);
        return [
            "status"=>1, 
            "data"=> $result,
            "message"=>"Api Successful."
        ];
    }

    public function push_notif_info(Request $request){

        $client = \iProtek\SmsSender\Helpers\PayMessageHttp::client();
        
        $response = $client->get('/api/push-info');
        $response_code = $response->getStatusCode(); 
        if($response_code != 200 && $response_code != 201){
            return [
                "is_active"=>false,
                "name"=>"",
                "key"=>"",
                "cluster"=>"",
                "message"=>"Error Messenger"
            ];
            return json_decode($response->getBody(), true);
        }
        $result = json_decode($response->getBody(), true);
        return $result;
    }
}