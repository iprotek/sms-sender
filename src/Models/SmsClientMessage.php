<?php

namespace iProtek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsClientMessage extends _CommonModel
{
    use HasFactory, SoftDeletes;
    
    public $fillable = [
        "to_number",
        "message",
        "status_id",
        "status_info",
        "status_at",
        "sms_client_api_request_link_id",
        "sms_api_request_link_id",
        "data_id",
        "sender_id",
        "sent_at"
    ];

}