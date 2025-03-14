<?php

namespace iProtek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsClientApiRequestLink extends _CommonModel
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        "group_id",
        "pay_created_by",
        "pay_updated_by",
        "pay_deleted_by",
        "name",
        "api_name",
        "api_username",
        "api_password",
        "api_url",
        "is_active",
        "inactive_reason",
        "inactive_at",
        "webhook_response_url",
        "is_webhook_active",
        "priority",
        "last_sending_at",
        "is_auto",
        
        "type",
        "header_info",
        "is_default",
        "api_version",
        "messenger_sms_api_request_link_id"
    ];

    
    protected $hidden = [
        'api_password'
    ];

    public $casts = [
        "is_active"=>"boolean",
        "is_webhook_active"=>"boolean",
        "is_default"=>"boolean"
    ];
}
