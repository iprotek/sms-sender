<?php

namespace iProtek\SmsSender\Helpers;

use DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use iProtek\Core\Models\UserAdminPayAccount;
use iProtek\SmsSender\Models\SmsClientApiRequestLink;
use iProtek\SmsSender\Models\SmsClientMessage;
use iProtek\Core\Helpers\PayModelHelper;
use iProtek\SmsSender\Models\SmsClientMobileNoInfo;
use Illuminate\Database\Eloquent\Model;

class PaySmsHelper
{
    
    public static function isValidInternationalMobile($number) {
        // Check format: starts with + and digits only OR starts with 0 and digits only
        return preg_match('/^(?:\+?[1-9]\d{7,14}|0\d{9,10})$/', $number);
    }

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
        Log::error($result);
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

    public static function send($to_number, $message, SmsClientApiRequestLink $smsClient=null, $target_id = null, $target_name = null, Request $request = null ){
        //action: add-sms
        if($smsClient == null){
            $smsClient = SmsClientApiRequestLink::where('is_active', 1)->orderBy('priority','ASC')->first();
        } 
        
        $pay_created_by = null;
        if(auth('admin')->check()){
            
            $user = auth()->user();
            $user_admin_id = $user->id;
            $session_id = session()->getId();
            $pay_account = null;
            if($session_id)
                $pay_account = \iProtek\Core\Models\UserAdminPayAccount::where(['user_admin_id'=>$user_admin_id, 'browser_session_id'=>$session_id])->first();
    
            if(!$pay_account)
                $pay_account = UserAdminPayAccount::where('user_admin_id', $user_admin_id)->first();

            if($pay_account != null){
                $pay_created_by = $pay_account->pay_app_user_account_id;
            }
        }else if($request){
            $pay_created_by = $request->header('PAY-USER-ACCOUNT-ID');
        }

        $requestData = [
            "pay_created_by"=>$pay_created_by,
            "to_number"=>$to_number,
            "message"=>$message,
            "target_id"=>$target_id,
            "target_name"=>$target_name,
            "sms_client_api_request_link_id"=>($smsClient ? $smsClient->id : null)
        ];

        //SOURCE ADDITIVES
        if($request){
            $requestData["client_id"] = $request->header('CLIENT-ID');
            $requestData["source_name"] = $request->header('SOURCE-NAME');
            $requestData["source_url"] = $request->header('SOURCE-URL');
        }


        $smsMessage = SmsClientMessage::create($requestData);  

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
        
        $response = $client->post('', ["body"=>json_encode($body)]);
        $result = static::response_result($response, false, null);
        if($result['status'] == 0){
            //Log::error($result);
            $smsMessage->status_id = 2;

            if(!isset($result['message']) && $result['result']){
                $smsMessage->status_info = "Failed: ". json_encode( $result['result']);//['message'];
            }
            else
                $smsMessage->status_info = "Failed: ".$result['message'];

            $smsMessage->save();
            return ["status"=>0, "message"=> $smsMessage->status_info];
        }
        else if($result['status'] == 1){
            if($result['result']['status'] == 0){
                $smsMessage->status_id = 2;
                $smsMessage->status_info = "Failed: ".$result['result']['message'];
                $smsMessage->save();
                return ["status"=>0, "message"=>$result['result']['message']];
            }
        }

        $result_data = $result['result'];

        //Log::error($result_data);

        $smsMessage->data_id = $result_data['data_id'];
        $smsMessage->sender_id = $result_data['sender_id'];
        $smsMessage->sms_api_request_link_id = $result_data['api_request_link_id'];

        //SET STATUS
        $smsMessage->status_id = 0;
        $smsMessage->status_info = "Pending";

        $smsMessage->save();


        //SENDING UPDATE
        $smsClient->last_sending_at = \Carbon\Carbon::now();
        if($smsClient->isDirty())
            $smsClient->save();


        return ["status"=>1, "message"=>"Request successfully sent. expect message from : ".$result_data['sender_mobile_no'] , "data"=>$smsMessage, "sender"=>$result_data['sender_mobile_no']];
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
            "PASSWORD" => $password,
            "CLIENT-ID"=>config('iprotek.pay_client_id'),
            "SOURCE-URL"=>config('app.url'),
            "SOURCE-NAME"=>config('app.name'),
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

    public static function finalize_reponse_result($response){
        $result = static::response_result($response, false, null);
        if(is_array($result)){

            if(  $result['status'] == 0){
                if(!isset($result['message']) && $result['result']){
                return ["status"=>0, "message"=> json_encode( $result['result'])];
                } 
                return ["status"=>0, "message"=> $result['message']];
            }
            else if($result['status'] == 1){
                if( is_array( $result['result'] ) ){
                    if($result['result']['status'] != 1)
                        return ["status"=>0, "message"=>$result['result']['message']];
                    //RETURN SUCCESS
                    return $result['result'];
                }     
            }

        }
        return ["status"=>0, "message"=>"Request invalidated.", "response"=>$response ];
    }

    public static function constraint_mobile_no($mobile_no, Model $model = null, bool $force_create_if_null = false){
        $mobile_info = null;
        if(strlen( trim($mobile_no)) >= 10){
            $mobile_info = SmsClientMobileNoInfo::whereRaw("mobile_no LIKE CONCAT('%', RIGHT(?, 10)) ",[$mobile_no])->first();
        }
        else{
            $mobile_info = SmsClientMobileNoInfo::whereRaw(" mobile_no = ? ", [$mobile_no])->first();
        }
        
        if(!$mobile_info && $force_create_if_null){
            $mobile_info = SmsClientMobileNoInfo::create([
                "pay_created_by"=> $model ? $model->pay_created_by : null,
                "group_id"=> $model ? $model->group_id : null,
                "branch_id"=> $model ? $model->branch_id : null,
                "mobile_no"=> $mobile_no
            ]);
        }

        return $mobile_info;
    }
 

}
