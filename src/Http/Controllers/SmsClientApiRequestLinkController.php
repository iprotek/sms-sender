<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use iProtek\SmsSender\Models\SmsClientApiRequestLink;
use iProtek\Core\Http\Controllers\_Common\_CommonController;
use iProtek\SmsSender\Helpers\PaySmsHelper;
use iProtek\SmsSender\Models\SmsClientReceivedMessage;

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

        //return $this->apiModelSelect(SmsClientApiRequestLink::class, $request, true, true);
        if($request->search_text && trim($request->search_text)){
            $search_text = '%'.str_replace(' ', '%', $request->search_text).'%';
            $data->whereRaw(' CONCAT(name,type) LIKE ? ', [$search_text]);
        }

        if($request->active_only){
            $data->where('is_active', true);
        }

        $data->select('*', 'name as text');

        return $data->paginate(10);
    
    }

    public function client_validate(Request $request, $is_add = true, SmsClientApiRequestLink $sms_api_client = null){

        if($request->sender_type == 'iprotek'){ 
            $this->validate($request, [
                "name"=>"required|min:3|unique:sms_client_api_request_links,name".($sms_api_client ? ",".$sms_api_client->id:""),
                "api_name"=>"required",
                "api_username"=>"required",
                "api_password"=>($is_add ? "required":"nullable"),
                "api_url"=>"required|unique:sms_client_api_request_links,api_url".($sms_api_client ? ",".$sms_api_client->id:"")
            ]); 
        }
        else if($request->sender_type == 'm360'){
            $this->validate($request, [
                "name"=>"required|min:3|unique:sms_client_api_request_links,name".($sms_api_client ? ",".$sms_api_client->id:""),
                "api_name"=>"required", //Registered ID
                "api_username"=>"required", //app_key
                "api_password"=>($is_add ? "required":"nullable"), //app_secret
                "api_version"=>"required"
            ]);
        }
        else if($request->sender_type == 'iprotek-messenger'){
            $this->validate($request, [
                "name"=>"required|min:3|unique:sms_client_api_request_links,name".($sms_api_client ? ",".$sms_api_client->id:""),
            ]);
            if(!$request->messenger_sms_api_request_link_id || !is_numeric($request->messenger_sms_api_request_link_id)){
                return ["status"=>0, "message"=>"Please select messenger API"];
            }
            $sms_link_id = $request->messenger_sms_api_request_link_id * 1;
            if($sms_link_id <= 0){
                return ["status"=>0, "message"=>"Required messenger API"];
            }
        }
        else{
            return ["status"=>0, "message"=>"Api Type invalidated"];
        }
        return ["status"=>1, "message"=>"valid"];
    }

    public function add_client(Request $request){
        
        $validate = $this->client_validate($request);
        if($validate["status"] == 0){
            return $validate;
        }

        $is_active = $request->is_active ? 1 : 0;

        //Validate API IF ACTIVE
            //PERFORM HTTP REQUEST WITH HEADERS
                //HEADERS:
                    //NAME
                    //USERNAME
                    //PASSWORD
        //Constraint checking from iprotek sms server
        if($is_active && $request->sender_type == 'iprotek'){
           $result = PaySmsHelper::checkApi($request->api_url,$request->api_name, $request->api_username, $request->api_password );
            if($result['status'] == 0){
                return ["status"=>0, "message"=>"Something goes wrong.".$request->api_url];
                return $result;
            }
        }
        $data = SmsClientApiRequestLink::create([
            "name"=>$request->name,
            "api_name"=>$request->api_name,
            "api_username"=>$request->api_username ?? "" ,
            "api_password"=>$request->api_password ?? "",
            "api_url"=>$request->api_url ?? "",
            "is_active"=>$is_active,
            "is_webhook_active" => 1,
            "priority"=>$request->priority,
            
            //NEW
            "type"=>$request->sender_type,
            "is_default"=>$request->is_default,
            "api_version"=>$request->api_version,
            "messenger_sms_api_request_link_id"=>$request->messenger_sms_api_request_link_id
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

    public function list_selection(Request $request){
        $data = SmsClientApiRequestLink::on();
        if($request->search_text){
            $search_text = '%'.str_replace(' ','%', $request->search_text).'%';
            $data->where('name','LIKE', $search_text);
        }
        $data->where('is_active', 1);
        $data->select('id', \DB::raw(" CONCAT(  name, ' ( ', type, ' )') as text"), 'type');
        $data->orderBy('priority', 'ASC');
        if($request->is_all){
            return $data->get();
        }

        return $data->paginate(10); 
    }

    public function send_message(Request $request, SmsClientApiRequestLink  $sms_api_client_id){

        if(!$sms_api_client_id->is_active){
            return ["status"=>0, "message"=>"Sms Api Client Inactive"];
        }


        $this->validate($request, [ 
            "to_number"=>["required", function ($attribute, $value, $fail) {
                    $to_number = str_replace(' ', '', $value);
                    if(strlen($to_number)<=10 ){
                        $fail('CP Number should be greater than 10 digit with or without +');
                    }
                }
            ],
            "message"=>["required", function ($attribute, $value, $fail) {
                $message = trim(  $value);
                if(!$message ){
                    $fail('Message is Required');
                }
            }]
        ]);



        $result = \iProtek\SmsSender\Helpers\PaySmsHelper::send($request->to_number, $request->message, $sms_api_client_id);
        return $result;
        return ["status"=>1, "message"=>"Request sent successfully"];
    }

    public function update_client(Request $request, SmsClientApiRequestLink $sms_api_client_id){

        $validate = $this->client_validate($request, false, $sms_api_client_id);
        if($validate["status"] == 0){
            return $validate;
        }
 

        $is_active = $request->is_active ? 1 : 0;
 
        if($is_active && $request->sender_type == 'iprotek'){
            if($request->api_password)
              $result = PaySmsHelper::checkApi($request->api_url,$request->api_name, $request->api_username, $request->api_password );
            else
              $result = PaySmsHelper::checkApi($request->api_url,$request->api_name, $request->api_username, $sms_api_client_id->api_password );
            
            if($result['status'] == 0){
                return $result;
            }
        }

        if($request->name)
            $sms_api_client_id->name = $request->name;
        if($request->api_name)
            $sms_api_client_id->api_name = $request->api_name;
        if($request->api_username)
            $sms_api_client_id->api_username = $request->api_username;
        if($request->api_password)
            $sms_api_client_id->api_password = $request->api_password;
        
        if($request->api_url)
            $sms_api_client_id->api_url = $request->api_url;
        $sms_api_client_id->is_active = $request->is_active; 
        $sms_api_client_id->is_webhook_active = $request->is_webhook_active;
        $sms_api_client_id->priority = $request->priority;
 
        //ADDITIONAL
        $sms_api_client_id->is_default = $request->is_default;
        $sms_api_client_id->type = $request->sender_type;
        $sms_api_client_id->messenger_sms_api_request_link_id = $request->messenger_sms_api_request_link_id;
        $sms_api_client_id->api_version = $request->api_version;

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

        $this->validate($request, [
            "from_mobile_no"=>"required",
            "sender_id"=>"required",
            "sms_sender_data_id"=>"required",
            "message"=>"required",
            "sms_api_request_link_id"=>"required"
        ]);

        
        //ACTION: update-status
        //UPDATE SENT MESSAGE

        //ACTION: add-message
        //ADD NEW MESSAGE

        $received =  SmsClientReceivedMessage::create([
            "from_number"=>$request->from_mobile_no,
            "sender_id"=>$request->sms_sender_id,
            "sms_sender_data_id"=>$request->sms_sender_data_id,
            "message"=>$request->message,
            "received_at"=>\Carbon\Carbon::now(),
            "sms_client_api_request_link_id"=>$request->sms_client_api_id,
            "sms_api_request_link_id"=>$request->sms_api_request_link_id
        ]);

        //SEND NOTIFICATIONS

        return ["status"=>1, "message"=>"Successfully received.", "data_id"=>$received->id];
    }

    public function api_service_list(Request $request){
         
        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::get_client('api/sms-service-apis/list?search='.$request->search_text );
        return $result["result"];
    }

}
