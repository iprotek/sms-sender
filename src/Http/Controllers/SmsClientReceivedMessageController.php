<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\Core\Http\Controllers\_Common\_CommonController;
use iProtek\SmsSender\Models\SmsClientReceivedMessage;

class SmsClientReceivedMessageController extends _CommonController
{
    //
    public $guard = "admin";
}
