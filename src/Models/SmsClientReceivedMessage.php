<?php

namespace iProtek\SmsSender\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class SmsClientReceivedMessage extends Model
{
    use HasFactory;
    
    public $fillable = [
        "from_number",
        "message",
        "target_id",
        "target_name",
        "sms_client_api_request_link_id",
        "sms_api_request_link_id",
        "sender_id",
        "data_id",
        "received_at",
        "sms_sender_data_id",
        "seen_at",
        "seen_by_pay_account_id",
        "client_id",
        "source_name",
        "source_url"
    ];


    protected static function booted()
    {
        static::creating(function ($model) {
            // Access model values before inserting
            //logger('Creating model:', $model->toArray());
            
            //CHECK THE NUMBER IF EXISTS
            $mobile_info = PaySmsHelper::constraint_mobile_no($model->from_number, $model, true);


            //FORCE CONSTRAINT TO UNIFIED NUMBER OF +63, 63 and 0
            $model->from_number =  $mobile_info->mobile_no;
            
            if(!isset($model->client_id) ){
                $model->client_id = config('iprotek.pay_client_id');
            }
            if(!isset($model->source_url) ){
                $model->source_url = config('app.url');
            }
            if(!isset($model->source_name) ){
                $model->source_name = config('app.name');
            }

            // Example: Add or modify a value before insert
            //$model->slug = Str::slug($model->name);
        });
    }
}
