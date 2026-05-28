@extends('iprotek_core::layout.pages.view-dashboard')

@section('logout-link','/logout')
@section('site-title', 'SMS SENDER')
@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection
@section('breadcrumb')
    <!--
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Widgets</li>
    -->
@endsection
@section('content') 
  <div>
    
  <?php
      $user_id = auth()->user()->id;
      $pay_account = \iProtek\Core\Models\UserAdminPayAccount::where('user_admin_id', $user_id)->first();
      $group_id = 0;
      if($pay_account)
      {
        $group_id = $pay_account->default_proxy_group_id;
      }
      
      $jsonData = json_encode( [
        "group_id" => $group_id
      ] );
    ?>
     
  <div id="main-content"
      data-props='{{$jsonData}}'
      >
  </div>

  </div>
   
@endsection

@section('foot')
    @vite('resources/js/manage/sms-sender.js', 'iprotek/build')
@endsection

