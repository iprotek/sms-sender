<?php

namespace iProtek\SmsSender\Mailables;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL; 

class GuestVerifyEmailMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $tries = 1; 
 
    //OTHER SETTINGS

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $code = null;
    public $person_name = null;
    public $url = null;


    public function __construct( $person_name, $code)
    {  

        $this->subject= 'Verification Code - Chat Request';  
        $this->code = $code;
        $this->person_name = $person_name;
        $this->url = config('app.url');

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('iprotek_sms_sender::mail.chat.guest-chat');
    }
}
