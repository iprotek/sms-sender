<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsClientReceivedMessage extends Model
{
    use HasFactory;
    
    public $fillable = [
        "from_number",
        "message",
        "sms_client_api_request_link_id",
        "sms_api_request_link_id",
        "sender_id",
        "data_id",
        "received_at"
    ];
}
