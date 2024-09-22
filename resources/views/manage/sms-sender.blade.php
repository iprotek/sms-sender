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
  <div id="main-content">
    
  <?php
      $user_id = auth()->user()->id;
      $pay_account = \iProtek\Core\Models\UserAdminPayAccount::where('user_admin_id', $user_id)->first();
      $group_id = 0;
      if($pay_account)
      {
        $group_id = $pay_account->default_proxy_group_id;
      }
    ?>
    <sms-sender-view group_id="{{$group_id}}"></sms-sender-view>
     

  </div>
   
@endsection

@section('foot') 
    <script src="/iprotek/js/manage/sms-sender/sms-sender.js?v=1.0"></script>
@endsection

