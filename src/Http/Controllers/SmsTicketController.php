<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\SmsSender\Models\SmsTicketMessage;
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

        if($request->search_text){
            $search_text = '%'.str_replace(' ', '%', $request->search_text).'%';
            $tickets->whereRaw(" CONCAT(id, title, IFNULL(customer_name,''), IFNULL(customer_email,''), IFNULL(customer_account_no,''), IFNULL(customer_contact_no,'') ) LIKE ?", [$search_text] );
        }

        $this->sort_filter($request, $tickets);


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
            $results = $tickets->paginate(10);
            //$results['manual_url'] = "GG";
            return [
                "pageData"=>$results,
                "manual_url"=>config('iprotek_sms_sender.manual_url','#')
            ];
        }
        else{
            $results = $tickets->paginate(10);//config('iprotek_sms_sender.manual_url','#');
        }
        
        return $results;
    }

    public function get_info(Request $request, SmsTicket $id){
        return SmsTicket::with([
            'creator'=>function($q){
            $q->select('id', 'name', 'contact_no', 'email');
            },
            'status'
        ])->find($id->id); 
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
            'helpdesk.ticket.response-get', now()->addMinutes(525600), [ 'id'=> $ticket->id ]
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

        $data = SmsTicket::with(['status','chats'])->find($id->id);

        return view( 'iprotek_sms_sender::system-support',["data"=>$data] );
        //return "123";
    }

    public function response_post(Request $request, SmsTicket $id ){
        $data = SmsTicket::with(['status'])->find($id->id);

        //action - cater
        if($request->action == 'cater' && $id->ticket_type == 'system-support'){
            $this->validate($request, [
                "account_no"=>"required|integer",
                "name"=>"required|min:5"
            ]);
            if($id->cater_by_id){
                return ["status"=>0, "message"=>"Already been catered."];
            }
            $id->cater_by_id = $request->account_no;
            $id->cater_by_name = $request->name;
            $id->cater_at = \Carbon\Carbon::now();
            $id->save();


            return ["status"=>1, "message"=>"You had successfully Catered"];
        }
        else if($request->action == 'message' && $id->cater_by_name && $id->ticket_type == 'system-support'){
            $this->validate($request, [
                "message"=>"required|min:10"
            ]);

            //CHECK EMAIL
            $support_email = config('iprotek_sms_sender.support_email');
            if(!$support_email  ){
                return ["status"=>0, "message"=>"System support disallowed."];
            }
            $chat_by_email = $support_email;
            $chat_by_name = $id->cater_by_name;
            $chat_by_id = $id->cater_by_id;
            SmsTicketMessage::create([
                "sms_ticket_id"=>$id->id,
                "message"=>$request->message,
                "chat_by"=>$chat_by_id,
                "chat_by_email"=>$chat_by_email,
                "chat_by_name"=>$chat_by_name,
                "is_end_user"=>1
            ]);
            
            return ["status"=>1, "message"=>"Message Submitted"];

        }
        else if($request->action == 'message'  && $id->ticket_type == 'customer'){
            $this->validate($request, [
                "message"=>"required|min:10"
            ]);
            
            $chat_by_email = $id->customer_email;
            $chat_by_name = $id->customer_name;
            $chat_by_id = is_numeric( $id->customer_account_no ) ? $id->customer_account_no : "0";
            SmsTicketMessage::create([
                "sms_ticket_id"=>$id->id,
                "message"=>$request->message,
                "chat_by"=>$chat_by_id ?: "0",
                "chat_by_email"=>$chat_by_email,
                "chat_by_name"=>$chat_by_name,
                "is_end_user"=>1
            ]);
            return ["status"=>1, "message"=>"Message Submitted"];
        }


        //action - chat


        return ["status"=>0, "message"=>"Action unavailable."];
        //return view( 'iprotek_sms_sender::system-support' , ["data"=>$data]);;
    }


    public function create_get(Request $request){
        return view( 'iprotek_sms_sender::customer-create'  );
    }

    public function create_post(Request $request){

        
        $this->validate($request, [
            "title"=>"required|min:10|max:50",
            "message"=>"required|min:10|max:1000",
            "customer_account_no"=>"required",
            "customer_name"=>"required|min:10|max:50",
            "customer_email"=>"required|email|max:50",
            "customer_contact_no"=>"required|min:7|max:50"
        ]);
        $customer_account_no = $request->customer_account_no;
        $customer_name = $request->customer_name;
        $customer_email = $request->customer_email;
        $customer_contact_no = $request->customer_contact_no;


        $ticket =  SmsTicket::create([
            "title"=>$request->title,
            "details"=>$request->message,
            "created_by"=>"0",
            "app_url"=>config('app.url'),
            "app_name"=>config('app.name'),
            "ticket_type"=>'customer',
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

        return ["status"=>1, "message"=>"Ticket Submitted. Please take note of your ticket.", "data"=>[ "id"=>$ticket->id]];
    }

    public function helpdesk(Request $request){

        return $this->view( 'iprotek_sms_sender::manage.helpdesk' );
    }

}
