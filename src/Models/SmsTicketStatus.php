<?php

namespace iProtek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTicketStatus extends Model
{
    use HasFactory;

    
    public $fillable = [
        "sms_ticket_id",
        "status_id",
        "status_name",
        "remarks",
        "created_by"
    ];
}
