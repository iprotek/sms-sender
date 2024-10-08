<?php

namespace iProtek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTicketMessage extends _CommonModel
{
    use HasFactory;

    public $fillable = [
        "sms_ticket_id",
        "message",
        "chat_by",
        "chat_by_email",
        "chat_by_name",
        "is_end_user"
    ];
}
