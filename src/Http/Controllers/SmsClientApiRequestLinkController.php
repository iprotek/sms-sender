<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use iProtek\SmsSender\Models\SmsClientApiRequestLink;
use iProtek\Core\Http\Controllers\_Common\_CommonController;
use iProtek\SmsSender\Helpers\PaySmsHelper;

class SmsClientApiRequestLinkController extends _CommonController
{
    //
    /*
    
        $api_url = URL::signedRoute(
            'api.products', [ 'id'=> $market_link->id ]
        );
    */
    public $guard = "admin";

    public function index(Request $request)
    {
        //return \iProtek\SmsSender\Helpers\PaySmsHelper::send("09081703461","hello world");
        return $this->view('iprotek_sms_sender::manage.sms-sender');
    }
 
    public function list(Request $request){
        $data = SmsClientApiRequestLink::on();
        if(isset($request->sms_api_client_id)){
            return $data->find($request->sms_api_client_id);
        }
        return $data->paginate(10);
    }

    public function add_client(Request $request){

        $this->validate($request, [
            "name"=>"required|min:3|unique:sms_client_api_request_links,name",
            "api_name"=>"required",
            "api_username"=>"required",
            "api_password"=>"required",
            "api_url"=>"required|unique:sms_client_api_request_links,api_url"
        ]);

        $is_active = $request->is_active ? 1 : 0;

        //Validate API IF ACTIVE
            //PERFORM HTTP REQUEST WITH HEADERS
                //HEADERS:
                    //NAME
                    //USERNAME
                    //PASSWORD
        if($is_active){
           $result = PaySmsHelper::checkApi($request->api_url,$request->api_name, $request->api_username, $request->api_password );
            if($result['status'] == 0){
                return $result;
            }
        }
        $data = SmsClientApiRequestLink::create([
            "name"=>$request->name,
            "api_name"=>$request->api_name,
            "api_username"=>$request->api_username,
            "api_password"=>$request->api_password,
            "api_url"=>$request->api_url,
            "is_active"=>$is_active,
            "is_webhook_active" => 1,
            "priority"=>$request->priority
        ]);

        /*
        
            $api_url = URL::signedRoute(
                'api.products', [ 'id'=> $market_link->id ]
            );
        */ 
        //CREATE LINK
        $data->webhook_response_url = URL::signedRoute(
            'api.sms-sender.response', [ 'sms_client_api_id'=> $data->id ]
        );
        $data->save();



        return ["status"=>1, "message"=>"Successfully Added", "data"=>$data];
    }


    public function update_client(Request $request, SmsClientApiRequestLink $sms_api_client_id){

        $this->validate($request, [
            "name"=>"required|min:3|unique:sms_client_api_request_links,name,".$sms_api_client_id->id,
            "api_name"=>"required",
            "api_username"=>"required",
            "api_url"=>"required|unique:sms_client_api_request_links,api_url,".$sms_api_client_id->id
        ]);

        $is_active = $request->is_active ? 1 : 0;
 
        if($is_active){
            if($request->api_password)
              $result = PaySmsHelper::checkApi($request->api_url,$request->api_name, $request->api_username, $request->api_password );
            else
              $result = PaySmsHelper::checkApi($request->api_url,$request->api_name, $request->api_username, $sms_api_client_id->api_password );
            
            if($result['status'] == 0){
                return $result;
            }
        }
        $sms_api_client_id->name = $request->name;
        $sms_api_client_id->api_name = $request->api_name;
        $sms_api_client_id->api_username = $request->api_username;
        if($request->api_password)
            $sms_api_client_id->api_password = $request->api_password;
        $sms_api_client_id->api_url = $request->api_url;
        $sms_api_client_id->is_active = $request->is_active;
        $sms_api_client_id->is_webhook_active = $request->is_webhook_active;
        $sms_api_client_id->priority = $request->priority;
        if($sms_api_client_id->isDirty()){
            $sms_api_client_id->save();
        } 
        return ["status"=>1, "message"=>"Successfully Added", "data"=>$sms_api_client_id]; 

    }

    public function api_response(Request $request){
        $sms_client_api = SmsClientApiRequestLink::where('is_active', 1)->where('is_webhook_active', 1)->find($request->sms_client_api_id);
        if(!$sms_client_api){
            abort(403, 'SMS API WEBHOOK INACTTIVE');
        }


        
        //ACTION: update-status
        //UPDATE SENT MESSAGE


        //ACTION: add-message
        //ADD NEW MESSAGE

        return ["status"=>1, "message"=>"Successfully received."];
    }

}
