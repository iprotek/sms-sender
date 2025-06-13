<?php

namespace iProtek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use iProtek\Core\Models\_CommonModel;
use Illuminate\Support\Facades\Log;
use iProtek\SmsSender\Helpers\PaySmsHelper;

class SmsClientMessage extends _CommonModel
{
    use HasFactory, SoftDeletes;
    
    public $fillable = [
        "pay_created_by",
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
        "webhook_response",
        "client_id",
        "source_name",
        "source_url"
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
            
            $mobile_info = PaySmsHelper::constraint_mobile_no($model->to_number, $model, true);

            //FORCE CONSTRAINT TO UNIFIED NUMBER OF +63, 63 and 0
            $model->to_number =  $mobile_info->mobile_no;

            if(!isset($model->client_id) ){
                $model->client_id = config('iprotek.pay_client_id');
            }
            if(!isset($model->source_url) ){
                $model->source_url = config('app.url');
            }
            if(!isset($model->source_name)){
                $model->source_name = config('app.name');
            }

            // Example: Add or modify a value before insert
            //$model->slug = Str::slug($model->name);
        });
    }

}
