<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use iProtek\Core\Models\_CommonModel;

class SmsClientMobileNoInfo extends _CommonModel
{
    use HasFactory;

    public $fillable = [
        "name",
        "address",
        "other_infos"
    ];


    public $casts = [
        "other_infos"=>"json"
    ];

}
