<?php

namespace iProtek\SmsSender\Helpers;

use DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use iProtek\SmsSender\Models\SmsClientApiRequestLink;
use iProtek\SmsSender\Models\SmsClientMessage;

class MessengerSmsHelper
{ 

    //Required Sending..
    /**
     * message_sms_api_request_link_id
     * valid_mobile_no
     * api_request_link_id
     * message
     * target_id
     * target_name
     */
    public static function send(array $data){
 
        $message_sms_api_request_link_id = $data['message_sms_api_request_link_id'];
        $valid_mobile_no = $data['mobile_no'];
        $api_request_link_id = $data['api_request_link_id'];
        $message = $data['message'];
        $target_id = $data['target_id'];
        $target_name = $data['target_name'];  

        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::post_client('api/sms-service-apis/send', $data );


        return $result; 


    }
 

}
