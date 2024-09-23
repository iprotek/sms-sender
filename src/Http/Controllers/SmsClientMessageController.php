<?php

namespace iProtek\SmsSender\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\Core\Http\Controllers\_Common\_CommonController;
use iProtek\SmsSender\Models\SmsClientMessage;

class SmsClientMessageController extends _CommonController
{
    //
    public $guard = "admin";
 
    public function list(Request $request){
        $data = SmsClientMessage::on();
        if($request->search){
            $search = '%'.str_replace(' ', '%', $request->search).'%';
            $data->whereRaw(' concat(to_number, message) LIKE ? ', [$search]);
        }
        $data->orderBy('id', 'DESC');
        return $data->paginate(10);
    }

    public function delete_message(Request $request, SmsClientMessage $id){
        $id->delete();
        return ["status"=>1, "message"=>"Successfully Removed"];
    }
}
