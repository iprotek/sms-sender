<?php
    $title = "System Support";
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
@endsection


@section('content')
    <div class="container pt-2" id="helpdesk-container"> 
        <helpdesk :data="{{$data}}"></helpdesk>
    </div>
    <script src="/iprotek/js/helpdesk/response.js?v=1.0.0.0"></script>
@endsection

@section('foot')
    <script src="/iprotek/design/templates/adminlte3.1.0/plugins/sweetalert2/sweetalert2.min.js"></script> 
@endsection