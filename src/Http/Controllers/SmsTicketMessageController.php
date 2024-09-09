<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\Core\Http\Controllers\_Common\_CommonController;
use iProtek\SmsSender\Models\SmsTicket;
use iProtek\SmsSender\Models\SmsTicketMessage;

class SmsTicketMessageController extends _CommonController
{
    //
    public $guard = 'admin';

    public function ticket_message(Request $request, SmsTicket $id){

        $messages = SmsTicketMessage::where('sms_ticket_id', $id->id);
        $messages->orderBy('id', 'DESC');

        return $messages->paginate(10);
    }

    public function add(Request $request, SmsTicket $id){

        $user = auth()->user();

        $chat_by_email = $user->email;
        $chat_by_name = $user->name;
        $chat_by_id = $user->id;
        SmsTicketMessage::create([
            "sms_ticket_id"=>$id->id,
            "message"=>$request->message,
            "chat_by"=>$chat_by_id ?: "0",
            "chat_by_email"=>$chat_by_email,
            "chat_by_name"=>$chat_by_name,
            "is_end_user"=>0
        ]);



        return ["status"=>1, "message"=>"Message Added"];
    }

}
