<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use iProtek\SmsSender\Models\SmsClientApiRequestLink;
use iProtek\Core\Http\Controllers\_Common\_CommonController;

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
        return $this->view('iprotek_sms_sender::manage.sms-sender');
    }
 
    public function list(Request $request){
        $data = SmsClientApiRequestLink::on();
        if($request->sms_client){
            return $data->find($request->sms_client);
        }
        return $data->paginate(10);
    }

    public function add(Request $request){

        $this->validate($request, [
            "name"=>"required|min:5|unique:sms_client_api_request_links,name",
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
        
        /*
        
            $api_url = URL::signedRoute(
                'api.products', [ 'id'=> $market_link->id ]
            );
        */



        //CREATE LINK


        return ["status"=>1, "message"=>"Successfully Added", "data"=>$data];
    }

    public function api_response(Request $request){

        //ACTION: update-status
        //UPDATE SENT MESSAGE


        //ACTION: add-message
        //ADD NEW MESSAGE


    }

}
