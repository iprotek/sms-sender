<?php

namespace iProtek\SmsSender\Mailables;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL; 
use iProtek\SmsSender\Models\SmsTicket;

class SystemSupportMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $tries = 1; 
 
    //OTHER SETTINGS

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $ticket = null;
    public $status = null;
    public $latest_message = null;


    public function __construct( SmsTicket $ticket )
    { 
        $lastNumberSeven = function($str){
            return substr('0000000'.$str, -7);
        };

        $this->subject= 'Helpdesk:'.$lastNumberSeven($ticket->id).' | '.config('app.name')." | ".$ticket->title;  

        $newTicket = SmsTicket::with(['status','latest_chat'])->find($ticket->id);

        $this->ticket = $newTicket;

        //Get Status
        $this->status = isset($newTicket->status) ? $newTicket->status : null;

        //Get latest chat
        $this->latest_message = isset($newTicket->latest_chat) ? $newTicket->latest_chat : null;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('iprotek_sms_sender::mail.helpdesk.system-support.notif');
    }
}
