<?php

namespace iProtek\SmsSender\Helpers;

use DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
//use iProtek\Core\Models\UserAdminPayAccount;
use iProtek\SmsSender\Models\SmsClientApiRequestLink;
use iProtek\SmsSender\Models\SmsClientMessage;

class AutoSelectSmsHelper
{ 
    public static function isValidInternationalMobile($number) {
        // Check format: starts with + and digits only OR starts with 0 and digits only
        return preg_match('/^(?:\+?[1-9]\d{7,14}|0\d{9,10})$/', $number);
    }
 
    public static function iprotek_sms_sender($mobile_no, $message, $target_id ){

        //SENDING SMS
        return 
        \iProtek\SmsSender\Helpers\PaySmsHelper::send($mobile_no, $message, null, $target_id, "iprotek-sms-sender");
    
    
    }

    public static function send($mobile_no, $message, SmsClientApiRequestLink $smsClient, $target_id = 0){


        //echo json_encode($smsClient->type);
        if( !static::isValidInternationalMobile($mobile_no) ){

            $smsMessage = SmsClientMessage::create([
                "to_number"=>$mobile_no,
                "message"=>$message,
                "target_id"=>$target_id,
                "target_name"=>$target_name,
                "status_id"=>2,
                "status_info"=>"Invalid number provided",
                "sms_client_api_request_link_id"=>($smsClient ? $smsClient->id : null)
            ]);
            return ["status"=>0, "message"=>"Invalid number"]; 
        }
        else if(!$smsClient->is_active){

            $smsMessage = SmsClientMessage::create([
                "to_number"=>$mobile_no,
                "message"=>$message,
                "target_id"=>$target_id,
                "target_name"=>$target_name,
                "status_id"=>2,
                "status_info"=>"SMS Sender API is currently disabled.",
                "sms_client_api_request_link_id"=>($smsClient ? $smsClient->id : null)
            ]);

            return ["status"=>0, "message"=>"Inactive Sender"];
        }
 
 
        if($smsClient->type == "m360"){            
            
            $details = M360Sms349ApiHelper::send($smsClient, $mobile_no, $message, $target_id);
            
            return ["status"=>1, "message"=>"m360 sms submitted."];
            
        }
        else if($smsClient->type == "iprotek-messenger"){

            
            $smsMessage = SmsClientMessage::create([
                "to_number"=>$mobile_no,
                "message"=>$message,
                "target_id"=>$target_id,
                "target_name"=>"iprotek-messenger",
                //"status_id"=>2,
                //"status_info"=>"Invalid number provided",
                "sms_client_api_request_link_id"=>($smsClient ? $smsClient->id : null)
            ]);

            $details = \iProtek\SmsSender\Helpers\MessengerSmsHelper::send([
                "message_sms_api_request_link_id" => $smsClient->messenger_sms_api_request_link_id,
                "mobile_no"=>$mobile_no,
                "api_request_link_id"=>$smsClient->id,
                "message"=>$message,
                "target_id"=>$target_id,
                "target_name"=>"iprotek-messenger"
            ]);
            
            if(is_object($details) && !is_array($details)){
                $details = json_decode( json_encode( $details) , TRUE);
            }

            if($details === null){
                return ["status"=>0, "message"=>"Something wrong with your sms sender endpoints."];
            }
            
            if(  isset($details["result"]) ){

                $result = json_decode(json_encode($details["result"]));

                if($result->status == 1){
                    $smsMessage->status_id = 1;
                }
                else{
                    $smsMessage->status_id = 2;
                }
                if(isset($result->data_id))
                    $smsMessage->data_id = $result->data_id;
                
                $smsMessage->status_info = $result->message;
                $smsMessage->save();
                return $details["result"];
            }
            else if($details["status"] == 0){
                $smsMessage->status_id = 2;
                $smsMessage->status_info = $details["message"];
                $smsMessage->save();
                return ["status"=>0, "message"=>$details["message"]];
            }
            $smsMessage->status_id = 2;
            $smsMessage->status_info = "Something goes wrong.";

            return ["status"=>0, "message"=>"Something goes wrong."];
        }

        //DEFAULT  IPROTEK
        return static::iprotek_sms_sender($mobile_no, $message, $target_id);



    }

}
