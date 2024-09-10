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
    /**
     * $url - required
     * $raw_response - true actual response / false modified formatted response
     * $error_default - if the result is not OK then it will return the error_default value
     */
    public static function get_client( $url, $raw_response = false, $error_default = null){

        //PRECHECKING
        $pay_message_url = config('iprotek_sms_sender.pay_message_url');
        if(!$pay_message_url){
            return [
                "status"=>0,
                "message"=>"Messaging not set"
            ];
        }
        
        $client = static::client();
        
        $response = $client->get($url);
        return static::response_result($response, $raw_response, $error_default);
    }

    public static function response_result( $response, $raw_response, $error_default){
        $response_code = $response->getStatusCode(); 
        if($raw_response){
            if($response_code != 200 && $response_code != 201){
                if($error_default){
                    return $error_default;
                }
            }
            return $response;
        } 

        if($response_code != 200 && $response_code != 201){
            if(!$error_default){
                $result = $response->getBody();
                if($result){
                    return [
                        "status"=>0,
                        "result"=> json_decode($response->getBody(), true),
                        "Api Invalidated."
                    ]; 
                }
                return [
                    "status"=>0,
                    "result"=>[],
                    "Api Invalidated."
                ];
            }
            return [
                "status"=>0,
                "result"=> $error_default,
                "Api Invalidated."
            ]; 
        }
        $result = json_decode($response->getBody(), true);
        return [
            "status"=>1, 
            "result"=> $result,
            "message"=>"Api Successful."
        ];

    }
 

}
