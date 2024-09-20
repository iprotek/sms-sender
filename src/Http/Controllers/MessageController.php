<?php

namespace iProtek\SmsSender\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{ 
    public function users(Request $request){

        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::get_client('/api/client-users' );
        return $result; 
    }

    public function push_notif_info(Request $request){

        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::get_client('/api/push-info');/*, true, [
                "is_active"=>false,
                "name"=>"",
                "key"=>"",
                "cluster"=>"",
                "message"=>"Error Messenger"
            ]);*/
        return $result; 
    }

    public function notifications(Request $request){
        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::get_client('/api/message-notifications');
        return $result; 

    }

    public function get_contact_message(Request $request){
        
        $proxy_group_id = 0;
        
        if(auth()->check()){
            $user = auth()->user();
            $pay_account = \iProtek\Core\Models\UserAdminPayAccount::where('user_admin_id', $user->id)->first();
            if( $pay_account ){ 
                $proxy_group_id = $pay_account->own_proxy_group_id;
            }
        }
        //return '/api/group/'.$proxy_group_id.'/dm/'.$request->contact_id;
        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::get_client('/api/group/'.$proxy_group_id.'/dm/contact/'.$request->contact_id);
        return $result; 

        //return $request->contact_id;
    }
 
    public function post_contact_message(Request $request){
        
        $proxy_group_id = 0;
        
        if(auth()->check()){
            $user = auth()->user();
            $pay_account = \iProtek\Core\Models\UserAdminPayAccount::where('user_admin_id', $user->id)->first();
            if( $pay_account ){ 
                $proxy_group_id = $pay_account->own_proxy_group_id;
            }
        }
        //return '/api/group/'.$proxy_group_id.'/dm/'.$request->contact_id;
        $result = \iProtek\SmsSender\Helpers\PayMessageHttp::post_client('/api/group/'.$proxy_group_id.'/dm/contact/'.$request->contact_id, ["message"=>$request->message]);
        return $result; 
    }


}