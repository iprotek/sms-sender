<?php
    $title =  config('app.name')." | Create Ticket";
?>

@extends('iprotek_core::layouts.app')
@section('nav_bar_color', 'navbar-dark shadow-sm')
@section('head')
    @if($title)
      <title> 
          {{ $title  }}
      </title>
    @endif
    <meta name="X-CSRF-TOKEN" value="{{csrf_token()}}" /> 
    <!-- Theme style -->
    <link rel="stylesheet" href="/iprotek/design/templates/adminlte3.1.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
@endsection


@section('content')
    <div class="container pt-2" id="create-ticket-container">  
        <create-ticket></create-ticket>
    </div> 
    <script src="/iprotek/js/helpdesk/create-customer-ticket.js?v=1.0.0.0"></script>
@endsection

@section('foot')
    <script src="/iprotek/design/templates/adminlte3.1.0/plugins/sweetalert2/sweetalert2.min.js"></script> 
@endsection