<?php

namespace iProtek\SmsSender\Helpers;

use DB; 
use Swift_SmtpTransport;
use Swift_Mailer;
use Illuminate\Support\Facades\Log;

class MailHelper
{
    
    public static function send($to,$mailable,$cc=null, $replyTo=null, $smtpConnection = null)
    {

        $valid_to = "";
        if(filter_var($to, FILTER_VALIDATE_EMAIL)){
            $valid_to = $to;
        }
        $valid_ccs = array();

        if($cc != null){
            foreach($cc as $cItem){
                if(filter_var( $cItem, FILTER_VALIDATE_EMAIL)){
                    $valid_ccs[] = $cItem;
                }
            }
        }
        //TEMPORARY
        //$valid_to = "joseph.aguilar@sportscity.com.ph";

        # Remove empty emails from array values
        if(!empty($valid_ccs))
            $valid_ccs = array_filter($valid_ccs);
        
        if( !empty($valid_to) && !empty($valid_ccs))
        {
            $mail = \Mail::to($valid_to)->cc($valid_ccs);
            if($smtpConnection){
                $mail->onConnection($smtpConnection);
            }
            if($replyTo && is_array($replyTo)){
                if($replyTo['email']){
                    $mailable->replyTo($replyTo['email'], $replyTo['name']);
                }
            }
           return  $mail->queue($mailable);
        }
        else if(!empty($valid_to))
        {
            $mail = \Mail::to($valid_to);
            if($smtpConnection){
                $mail->onConnection($smtpConnection);
            }
            if($replyTo && is_array($replyTo)){
                if($replyTo['email']){
                    $mailable->replyTo($replyTo['email'], $replyTo['name']);
                }
            }
           return  $mail->queue($mailable);
        }
        return null;
    }


    public static function check($host, $port, $encryption, $username, $password){ 
        try { 
            $transport = (new Swift_SmtpTransport($host, $port, $encryption))
            ->setUsername($username)
            ->setPassword($password); 
            $mailer = new Swift_Mailer($transport);
            $mailer->getTransport()->start();
            return true;
        } catch (\Exception $e) { 
            //Log::error($e->getMessage());
            return false;
        }
        return false;
    }

}
