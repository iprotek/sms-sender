<?php

namespace iProtek\SmsSender\Helpers;

use DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
//use iProtek\Core\Models\UserAdminPayAccount;
use iProtek\SmsSender\Models\SmsClientApiRequestLink;
use iProtek\SmsSender\Models\SmsClientMessage;

class PaySmsHelper
{
    public static function checkApi($api_url, $api_name, $api_username, $api_pass){


        //Validate API IF ACTIVE
            //PERFORM HTTP REQUEST WITH HEADERS
                //HEADERS:
                    //NAME
                    //USERNAME
                    //PASSWORD
        $client = static::client($api_url, $api_name, $api_username, $api_pass);
        
        $response = $client->get('');

        $result = static::response_result($response, false, null);
        if($result['status'] == 0){ 
            return ["status"=>0, "message"=>"Failed"];
        }
        else if($result['status'] == 1){
            if($result['result']['status'] == 0){ 
                return ["status"=>0, "message"=>"Failed"];
            }
        }

        return ["status"=>1, "message"=>"Successful"];
    }

    public static function send($to_number, $message, SmsClientApiRequestLink $smsClient=null, $target_id, $target_name){
        //action: add-sms
        if($smsClient == null){
            $smsClient = SmsClientApiRequestLink::where('is_active', 1)->orderBy('priority','ASC')->first();
        }
         
        $smsMessage = SmsClientMessage::create([
            "to_number"=>$to_number,
            "message"=>$message,
            "target_id"=>$target_id,
            "target_name"=>$target_name,
            "sms_client_api_request_link_id"=>($smsClient ? $smsClient->id : null)
        ]); 


        if($smsClient == null){

            return ["status"=>0, "message"=>"No sms api active client available."];

        }
 
        //PERFORM HTTP REQUEST WITH HEADERS
            //HEADERS:
                //NAME
                //USERNAME
                //PASSWORD

        $header_name = $smsClient->api_name;
        $header_username = $smsClient->api_username;
        $header_password = $smsClient->api_password;
        $url = $smsClient->api_url;

        $body = [
            "action"=>"add-sms",
            "to_number"=>$to_number,
            "message"=>$message
        ];

        $client = static::client($url, $header_name, $header_username, $header_password);
        
        $response = $client->post('', ["body"=>$body]);
        $result = static::response_result($response, false, null);
        if($result['status'] == 0){
            $smsMessage->status_id = 2;
            $smsMessage->status_info = "Failed: ".$result['message'];
            $smsMessage->save();
            return ["status"=>0, "message"=>"Failed"];
        }
        else if($result['status'] == 1){
            if($result['result']['status'] == 0){
                $smsMessage->status_id = 2;
                $smsMessage->status_info = "Failed: ".$result['result']['message'];
                $smsMessage->save();
                return ["status"=>0, "message"=>"Failed"];
            }
        }

        $result_data = $result['result'];

        $smsMessage->data_id = $result_data['data_id'];
        $smsMessage->sender_id = $result_data['sender_id'];
        $smsMessage->sms_api_request_link_id = $result_data['api_request_link_id'];
        $smsMessage->save();


        //SENDING UPDATE
        $smsClient->last_sending_at = \Carbon\Carbon::now();
        if($smsClient->isDirty())
            $smsClient->save();


        return ["status"=>0, "message"=>"", "data"=>$smsMessage, "sender"=>$result_data['sender_mobile_no']];
    }

 
    //CLIENT
    public static function client($url, $name, $username, $password){ 

 
    //PERFORM HTTP REQUEST WITH HEADERS
        //HEADERS:
            //NAME
            //USERNAME
            //PASSWORD

        $headers = [
            "Accept"=>"application/json",
            'Content-Type' => 'application/json',
            "NAME" => $name,
            "USERNAME" => $username,
            "PASSWORD" => $password
        ];
        
        $client = new \GuzzleHttp\Client([
            'base_uri' => $url,
            "http_errors"=>false, 
            "verify"=>false, 
            "curl"=>[
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0, // Specify HTTP/2
            ],
            "headers"=>$headers
         ]);
        return $client;

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

    public static function post_client($url, $body, $raw_response = false, $error_default = null){

        if(is_array($body)){
            $body = json_encode($body);
        }
        else if(is_object($body)){
            $body = json_encode($body);
        }


        //PRECHECKING
        $pay_message_url = config('iprotek_sms_sender.pay_message_url');
        if(!$pay_message_url){
            return [
                "status"=>0,
                "message"=>"Messaging not set"
            ];
        }
        
        $client = static::client();
        
        $response = $client->post($url, ["body"=>$body]);
        return static::response_result($response, $raw_response, $error_default);
    }
 

}
