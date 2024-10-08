<?php

namespace iProtek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTicket extends _CommonModel
{
    use HasFactory;

    public $fillable = [
        "title",
        "details",
        "created_by",
        "updated_by",
        "app_url",
        "app_name",
        "ticket_type",
        "category_name",

        "customer_account_no",
        "customer_name",
        "customer_email",
        "customer_contact_no",

        "cater_by_id",
        "cater_by_name",
        "cater_at",

        "current_status_id",
        "response_url"
    
    ];


    public $appends = [
        "created_diff",
        "updated_diff",
        "message_count"
    ];

    public function getMessageCountAttribute(){
        return \iProtek\SmsSender\Models\SmsTicketMessage::where('sms_ticket_id', $this->id)->count();
    }

    public function creator(){
        return $this->belongsTo(\iProtek\Core\Models\UserAdmin::class,'created_by');
    }

    public function getCreatedDiffAttribute(){
        return $this->created_at->diffForHumans();
    }

    public function getUpdatedDiffAttribute(){
        return $this->updated_at->diffForHumans();

    }

    public function status(){
        return $this->hasOne(\iProtek\SmsSender\Models\SmsTicketStatus::class, 'sms_ticket_id')->orderBy('id', 'DESC');
    }

    public function latest_chat(){
        return $this->hasOne(\iProtek\SmsSender\Models\SmsTicketMessage::class, 'sms_ticket_id')->orderBy('id', 'DESC');
    }

    public function chats(){
        return $this->hasMany(\iProtek\SmsSender\Models\SmsTicketMessage::class, 'sms_ticket_id')->orderBy('id', 'DESC')->limit(20);
    }



}
