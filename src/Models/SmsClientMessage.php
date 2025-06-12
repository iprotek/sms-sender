<?php

namespace iProtek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use iProtek\SmsSender\Models\_CommonModel;

class SmsClientMessage extends _CommonModel
{
    use HasFactory, SoftDeletes;
    
    public $fillable = [
        "to_number",
        "message",
        "target_id",
        "target_name",
        "status_id",
        "status_info",
        "status_at",
        "sms_client_api_request_link_id",
        "sms_api_request_link_id",
        "data_id",
        "sender_id",
        "sent_at",

        "sms_request_response",
        "sms_request_response_code",
        "webhook_response"
    ];

    public $casts =[
        "created_at"=>"datetime:Y-m-d h:i:s A",
        "updated_at"=>"datetime:Y-m-d h:i:s A",
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // Access model values before inserting
            //logger('Creating model:', $model->toArray());
            //CHECK THE NUMBER IF EXISTS

            $exists = SmsClientMobileNoInfo::whereRaw('mobile_no LIKE RIGHT(?, 10) ')->first();
            if(!$exists){
                SmsClientMobileNoInfo::create([
                    "pay_created_by"=>$model->pay_created_by,
                    "group_id"=>$model->group_id,
                    "branch_id"=>$model->branch_id,
                    "mobile_no"=>$model->to_number
                ]);
            }


            // Example: Add or modify a value before insert
            //$model->slug = Str::slug($model->name);
        });
    }

}
