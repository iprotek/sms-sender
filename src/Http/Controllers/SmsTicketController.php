<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\SmsSender\Models\SmsTicket;
use iProtek\Core\Http\Controllers\_Common\_CommonController;

class SmsTicketController extends _CommonController
{
    //
    public $guard = 'admin';
    public function list(Request $request){
        return SmsTicket::paginate(10);
    }

    public function get_info(Request $request, SmsTicket $id){
        return $id;
    }

    public function add(Request $request){

        //CHECK SYSTEM SUPPORT EMAIL
        $support_email = config('iprotek_sms_sender.support_email');
        if(!$support_email && $request->ticket_type == 'system-support' ){
            return ["status"=>0, "message"=>"No customer support available"];
        }


        $this->validate($request, [
            'ticket_type' => 'required',
            "title"=>'required',
            'details'=>'required'
        ]);


        if($request->ticket_type == 'customer'){
            //Requires customer info
            
            $this->validate($request, [
                'customer_account_no' => 'required',
                'customer_name' => 'required',
                'customer_email' => 'required|email',
                'customer_contact_no' => 'required',
            ]);
        }
        $user = auth()->user();
        $user_id = auth()->user()->id;

        $customer_account_no = $request->customer_account_no;
        $customer_name = $request->customer_name;
        $customer_email = $request->customer_email;
        $customer_contact_no = $request->customer_contact_no;

        if($request->ticket_type == 'system-support'){ 
            
            $customer_account_no = $user->id;
            $customer_name = $user->name;
            $customer_email = $user->email;
            $customer_contact_no = $user->contact_no;

        }

        $ticket = SmsTicket::create([
            "title"=>$request->title,
            "details"=>$request->details,
            "created_by"=>$user_id,
            "app_url"=>config('app.url'),
            "app_name"=>config('app.name'),
            "ticket_type"=>$request->ticket_type,
            "category_name"=>"",
            "customer_account_no"=>$customer_account_no,
            "customer_name"=>$customer_name,
            "customer_email"=>$customer_email,
            "customer_contact_no"=>$customer_contact_no
        ]);

        if($request->ticket_type == 'system-support'){ 
            //SEND TO SYSTEM SUPPORT EMAIL

        }

        

        return ["status"=>1, "message"=>"Successfully updated", "data"=>$ticket];
    }

    public function update(Request $request, SmsTicket $id ){


    }

    public function remove(Request $request, SmsTicket $id ){


    }



}
