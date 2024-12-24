<?php

namespace iProtek\SmsSender\Helpers;

use DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Pusher\Pusher;
use WebSocket\Client;

class SocketHelper
{
    public static function setting($full = false){
        
        //GETTING SOCKET PUSH INFO
        $client_info = \iProtek\Core\Helpers\PayHttp::client_info();
        if($client_info){

            if(is_array($client_info)){
                //return $client_info['socket_settings'];
                
                $socket_settings = isset( $client_info['socket_settings'] ) ?  $client_info['socket_settings'] : null;

                if($socket_settings){
                    if($full){
                        return [
                            "is_active"=> isset($socket_settings['is_active']) ? $socket_settings['is_active']: false,
                            "app_id"=>isset($socket_settings['app_id']) ? $socket_settings['app_id'] :"",
                            "name"=>isset($socket_settings['socket_name']) ? $socket_settings['socket_name'] :"",
                            "key"=>isset($socket_settings['key']) ? $socket_settings['key']:"",
                            "secret"=>isset($socket_settings['secret']) ? $socket_settings['secret']:"",
                            "cluster"=>isset($socket_settings['cluster']) ? $socket_settings['cluster'] :""
                        ];
                    }
                    return [
                        "is_active"=> isset($socket_settings['is_active']) ? $socket_settings['is_active']: false,
                        "name"=>isset($socket_settings['socket_name']) ? $socket_settings['socket_name'] :"",
                        "key"=>isset($socket_settings['key']) ? $socket_settings['key']:"",
                        "cluster"=>isset($socket_settings['cluster']) ? $socket_settings['cluster'] :""
                    ];
                }
            }
            else{
                $socket_settings =  $client_info->socket_settings;
                if($socket_settings){
                    if($full){
                        return [
                            "is_active"=>$socket_settings->is_active,
                            "app_id"=>$socket_settings->app_id,
                            "name"=>$socket_settings->socket_name,
                            "key"=>$socket_settings->key,
                            "secret"=>$socket_settings->secret,
                            "cluster"=>$socket_settings->cluster
                        ];
                    }
                    return [
                        "is_active"=>$socket_settings->is_active,
                        "name"=>$socket_settings->socket_name,
                        "key"=>$socket_settings->key,
                        "cluster"=>$socket_settings->cluster
                    ];
                }
            }
        }
        return [
            "is_active"=>false,
            "name"=>"",
            "key"=>"",
            "cluster"=>"",
            "message"=>"Not Found."
        ];
    }

    public static function notify(array $data ){
        $setting = static::setting(true);
        if(!isset($setting['is_active']) || !$setting['is_active'])
            return ["status"=>0, "message"=>"Unavailable notification"];


        //SUBMIT USING PUSHER
        if($setting['name'] == "PUSHER.COM"){
            
            $key = $setting['key'];
            $cluster = $setting['cluster'];
            $secret = $setting['secret'];
            $app_id = $setting['app_id'];


            $options = array(
                'cluster' => $cluster,
                'useTLS' => request()->secure() ? true : false //for http:false or https:true
            );
            
            $pusher = new Pusher(
                $key,
                $secret,
                $app_id,
                $options
            );
            
            $pusher->trigger('chat-channel', 'notify', $data);

        }
        else if( $setting['name'] == "iProtek WebSocket" ){

            $url = $setting['url'];
            $cluster = $setting['cluster'];
            $app_id = $setting['app_id'];
            $key = $setting['key'];
            $secret = $setting['secret'];
            
            try{    
                $client = new Client($url."?cluster=".$cluster."&app_id=".$app_id."&key=".$key);
                $client->send( json_encode(
                    [
                        "secret"=>$secret,
                        "type"=>"message",
                        "data"=>$data,
                        "event"=>"notify",
                        "channel"=>"chat-channel",
                        "key"=>$key
                    ]
                ));

                $client->close();
                Log::error("Sent Successfully");

            }catch(\Exception $ex){
                Log::error($ex->getMessage());
            }
        }   
        Log::error("Triggered");
        

        return ["status"=>1, "message"=>"Submitted"];

    }
    
}
