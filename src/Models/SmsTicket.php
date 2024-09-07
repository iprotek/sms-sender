<?php

namespace Protek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTicket extends Model
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
        "cater_by_name"
    
    ];
}
