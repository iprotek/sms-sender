<?php

namespace iProtek\SmsSender\Helpers;

use DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use iProtek\SmsSender\Models\SmsClientApiRequestLink;
use iProtek\SmsSender\Models\SmsClientMessage;

class M360Sms349ApiHelper
{ 
    public static $version = "3.4.9";

    public static $url = "https://api.m360.com.ph/v3/api/broadcast";

    public static function client($headers=[]){
        
        $client = new \GuzzleHttp\Client([
            'base_uri' => "https://api.m360.com.ph",
            "http_errors"=>false, 
            "verify"=>false, 
            "curl"=>[
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0, // Specify HTTP/2
            ],
            "headers"=>$headers
         ]);
        return $client;
    }

    public static function send(SmsClientApiRequestLink $api, $to_cp_no, $message, $target_id = 0, Request $request = null, $is_json_response = true ){
        
        if($api->type != 'm360'){
            if(!$is_json_response){
                return ["status"=>0, "message"=>"Invalid API Type" ];
            }
            return  response()->json(["status"=>0, "message"=>"Invalid API Type" ], 403);
        }
        else if($api->api_version != static::$version){
            if(!$is_json_response){
                return ["status"=>0, "message"=>"Invalid API M360 Version" ];
            }
            return  response()->json(["status"=>0, "message"=>"Invalid API M360 Version" ], 403);
        }

        //broadcast
        $client = static::client();

        $requestData = [
            "to_number"=>$to_cp_no,
            "message"=>$message,
            "target_id"=>$target_id,
            "target_name"=>"m360",
            "sms_client_api_request_link_id"=>$api->id
        ];
        
        if($request){
            $requestData["client_id"] = $request->header('CLIENT-ID');
            $requestData["source_name"] = $request->header('SOURCE-NAME');
            $requestData["source_url"] = $request->header('SOURCE-URL');
            $requestData['pay_created_by'] = $request->header('PAY-USER-ACCOUNT-ID');
        }

        //ADD SENDING RECORD
        $smsMessage = SmsClientMessage::create($requestData); 
 
        $request_body = [
            "app_key"=>$api->api_username,
            "app_secret"=>$api->api_password,
            "msisdn"=>$to_cp_no,
            "content"=>$message,
            "shortcode_mask"=>$api->api_name,
            "rcvd_transid"=>$smsMessage->id,
            "is_intl"=>false,
        ];


        $response = $client->post('/v3/api/broadcast', 
        [
            "json"=>$request_body
        ]);
        $response_code = $response->getStatusCode(); 
        $result = $response->getBody();  

        //Log::error($request_body);
        //Log::error($result);


        $smsMessage->sms_request_response_code = $response_code;
        $smsMessage->sms_request_response = json_encode( json_decode( $result,TRUE) );
        $smsMessage->save();
        
        if($response_code != 200 && $response_code != 201){
            if(!$is_json_response){
                return ["status"=>0, "message"=>"Failed", "data"=>$result ];
            }
           return response()->json(["status"=>0, "message"=>"Failed", "data"=>$result ], $response_code);
        }
        
        if(!$is_json_response){
            return ["status"=>1, "message"=>"Successfully Rendered", "data"=>$result];
        }
        return response()->json(["status"=>1, "message"=>"Successfully Rendered", "data"=>$result], $response_code);

    }
    

}
