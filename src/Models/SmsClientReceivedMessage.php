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
        "seen_by_pay_account_id"
    ];


    protected static function booted()
    {
        static::creating(function ($model) {
            // Access model values before inserting
            //logger('Creating model:', $model->toArray());
            //CHECK THE NUMBER IF EXISTS
            if(strlen( trim($model->to_number)) > 10){
                $mobile_info = SmsClientMobileNoInfo::whereRaw("mobile_no LIKE CONCAT('%', RIGHT(?, 10)) ",[$model->from_number])->first();
            }
            else{
                $mobile_info = SmsClientMobileNoInfo::whereRaw(" mobile_no = ? ", [$model->from_number])->first();
            }
            if(!$mobile_info){
                $mobile_info = SmsClientMobileNoInfo::create([
                    "pay_created_by"=>$model->pay_created_by,
                    "group_id"=>$model->group_id,
                    "branch_id"=>$model->branch_id,
                    "mobile_no"=>$model->from_number
                ]);
            }


            //FORCE CONSTRAINT TO UNIFIED NUMBER OF +63, 63 and 0
            $model->from_number =  $mobile_info->mobile_no;

            // Example: Add or modify a value before insert
            //$model->slug = Str::slug($model->name);
        });
    }
}
