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

    public static function send($mobile_no, $message, SmsClientApiRequestLink $smsClient = null, $target_id = 0){

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
 
 
        if($smsClient->type == "m360"){            
            
            $details = M360Sms349ApiHelper::send($smsClient, $valid_mobile_no, $message, $target_id);
            
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
                "mobile_no"=>$valid_mobile_no,
                "api_request_link_id"=>$smsClient->id,
                "message"=>$message,
                "target_id"=>$target_id,
                "target_name"=>"iprotek-messenger"
            ]);
             //Log::error("iprotek-messenger result");
             Log::error($details);
             Log::error($smsMessage);
            return ["status"=>1, "message"=>"iProtek Messenger submitted."];
        }

        //DEFAULT  IPROTEK
        return static::iprotek_sms_sender($mobile_no, $message, $target_id);



    }

}
