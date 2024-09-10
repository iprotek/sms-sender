<?php

namespace iProtek\SmsSender\Helpers;

use DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
//use iProtek\Core\Models\UserAdminPayAccount;

class PayMessageHttp
{
    //AUTH 

    public static function auth_client($token){
        $pay_url = config('iprotek.pay_url');
        $pay_message = config('iprotek_sms_sender.pay_message_url');
        $client_id = config('iprotek.pay_client_id');
        $client_secret = config('iprotek.pay_client_secret'); 
        
        $headers = [
            "Accept"=>"application/json",
            "CLIENT-ID"=>$client_id,
            "SECRET"=>$client_secret,
            "PAY-URL"=>$pay_url,
            "Authorization"=>"Bearer ",$token
        ];
        
        $client = new \GuzzleHttp\Client([
            'base_uri' => $pay_message,
            "http_errors"=>false, 
            "verify"=>false, 
            "curl"=>[
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0, // Specify HTTP/2
            ],
            "headers"=>$headers
         ]);
        return $client;

    }

    //CLIENT
    public static function client(){
        //Preparation of Headers
        $pay_message = config('iprotek_sms_sender.pay_message_url');
        $pay_url = config('iprotek.pay_url');
        $client_id = config('iprotek.pay_client_id');
        $client_secret = config('iprotek.pay_client_secret'); 
 
        $headers = [
            "Accept"=>"application/json",
            "CLIENT-ID"=>$client_id,
            "SECRET"=>$client_secret,
            "PAY-URL"=>$pay_url,
        ];
        
        $client = new \GuzzleHttp\Client([
            'base_uri' => $pay_message,
            "http_errors"=>false, 
            "verify"=>false, 
            "curl"=>[
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0, // Specify HTTP/2
            ],
            "headers"=>$headers
         ]);
        return $client;

    }

}
