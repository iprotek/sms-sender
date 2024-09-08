<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\SmsSender\Models\SmsTicket;
use iProtek\SmsSender\Models\SmsTicketStatus;
use iProtek\Core\Http\Controllers\_Common\_CommonController;
use Illuminate\Support\Facades\URL;

class SmsTicketController extends _CommonController
{
    //
    public $guard = 'admin';
    public function list(Request $request){
        $tickets = SmsTicket::on();

        $tickets->orderBy('current_status_id', 'ASC')->orderBy('cater_by_id', 'ASC')->orderBy('updated_at', 'DESC');
        $tickets->with(
            [
                'creator'=>function($q){
                $q->select('id', 'name', 'contact_no', 'email');
                },
                'status'
            ]);
        if($request->action == 'notification'){
            $tickets->where('current_status_id', 0);
        }


        return $tickets->paginate(10);
    }

    public function get_info(Request $request, SmsTicket $id){
        return SmsTicket::with(
            [
                'creator'=>function($q){
                $q->select('id', 'name', 'contact_no', 'email');
                },
                'status'])->find($id->id); 
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

        $ticket->response_url =  URL::temporarySignedRoute(
            'helpdesk.ticket.response-get', now()->addMinutes(525600), [ 'id'=> $ticket ]
        );
        $ticket->save();

        if($request->ticket_type == 'system-support'){ 
            //SEND TO SYSTEM SUPPORT EMAIL
            \iProtek\SmsSender\Helpers\MailHelper::send($support_email, new \iProtek\SmsSender\Mailables\SystemSupportMailable($ticket));

        }

        

        return ["status"=>1, "message"=>"Successfully updated", "data"=>$ticket];
    }

    public function update(Request $request, SmsTicket $id ){


    }

    public function remove(Request $request, SmsTicket $id ){


    }

    public function cater_ticket(Request $request, SmsTicket $id){

        if($id->ticket_type == 'system-support'){
            return ["status"=>0, "message"=>"Can be only cater to system support."];
        }
        if($id->cater_by_id){
            return ["status"=>0, "message"=>"Already been catered by ".$id->cater_by_name];
        }

        $user = auth()->user();
        $id->cater_by_id = $user->id;
        $id->cater_by_name = $user->name;
        $id->cater_at = \Carbon\Carbon::now();
        $id->save();

        return ["status"=>1, "message"=>"You have now catered.", "data"=>$id];
        
    }

    public function update_status(Request $request, SmsTicket $id){

        //UPDATE STATUS
        $user = auth()->user();
        $status_name = "";
        $stat_id = $request->status_id;

        $id->current_status_id = $stat_id;
        $id->save();

        if($stat_id === 0)
            $status_name = "Pending";
        else if($stat_id == 1)
            $status_name = "Completed";
        else if($stat_id == 2)
            $status_name = "Failed";
        else if($stat_id == 3)
            $status_name = "Solved";
        else if($stat_id == 4)
            $status_name = "Cancelled";
        else if($stat_id == 5)
            $status_name = "Close";
        else 
            $status_name = "N/A";


        //RECORD STATUS LIST
        SmsTicketStatus::create([
            "sms_ticket_id"=>$id->id,
            "status_id"=>$stat_id,
            "status_name"=>$status_name,
            "remarks"=>$request->remarks,
            "created_by"=>$user->id
        ]);


        return ["status"=>1, "message"=>"Status updated."];
    }


    public function response_view(Request $request, SmsTicket $id ){
        return "123";
    }

    public function response_post(Request $request, SmsTicket $id ){

        //action - cater


        //action - chat



        return [ "status"=>1, "message"=>"Done Responding"];
    }



}
