@section('action-btn')

<!-- literally user can create accounts -->
 
@endsection

 

@extends('layouts.admin')
 
@section('title')
 
@endsection
 
@section('action-btn')

@endsection
@section('content')

@include('layouts.inlinecss')

<div  class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;padding-left:15px;padding-right:15px;">

 @include('layouts.search')
<div id="mainsearchdiv">
    <h4 class="card-title" style="margin-top:20px;">
        {{$nameoftransaction}}
      </h4>
    

      @foreach($data as $thedata)
      <button id="accountbtnpanel_{{$thedata->id}}"  class="accordion card listdiv" style="background:#fff;width:99%;padding-left:0;padding-right:0;margin-left:0;margin-right:0;">
 
          <table>
              <tr>
                  <td width="23%">
  
                      <div style="padding-left:5px;"> 
  
                          @if($thedata->user_image == 'true')
                          <img style="position:relative;float:left;margin-right:10px;height:70px;width:70px;" class="rounded-circle" src="{{env('NOBS_IMAGES')}}images/user_avatar/avatar_{{$thedata->userid}}.jpg?lastmod=<?php echo date("m/d/Y h:i:s a", time())?>" is_dataimage="{{$thedata->is_dataimage}}">
                          @else 
                          <img style="position:relative;float:left;margin-right:10px;width:70px;width:70px;" class="rounded-circle" src="{{env('NOBS_IMAGES')}}useraccounts/profileimage.png">
                          @endif 
                      </div>
                 
                  </td>
                  <td width="77%" style="text-align:left; ">
                      <div style="text-align:left;padding-top:1px !important;">
                          <h6 class="account_name" style="padding-top:1px !important;">
                              {{$thedata->first_name}} {{$thedata->middle_name}} {{$thedata->surname}}</h6>
                              <h6 style="color:#724c78;text-align:left !important;">{{$thedata->amount}}</h6> 
                      
                      <h6 style="color:#5e7eb9;text-align:left !important;">{{$thedata->account_number}}</h6>
                      </div>
                  </td>
              </tr>
          </table>
  
      </button>
      
  
      
  
      @endforeach
      
      
</div>
</div>
<style>

table td[class='mintd'] {
        padding: 5px 25px !important;
    }

    .account_name{
        color:#666666;text-align:left !important;font-weight:bold;font-family:verdana;
    }

    .table-panel td{
        font-size:1em !important;
        color:rgb(65, 6, 65);
        font-family:Verdana, Geneva, Tahoma, sans-serif;
    }
    .accordion img {
        width: 65px;
        height: 65px;
    }

    .listdiv  {
        width: 25%;
        height: auto !important;

         
    }

    .listdiv .listdiv .image {
        width: 25%;
        height: 70px;

        
    }

    .listdiv .listdiv img {
        width: 70px;
        height: 70px;
    }

    .listdiv .listdiv .text {
        width: 75%;
        height: 70px;
        background-color: green;
    }

    .listdiv .listdiv .text a,
    .listdiv .listdiv .text span {
        float: left;
        color: purple;
    }


    .listdiv2{
        height:auto !important;
        height:600px;
    }

    .listdiv2 .listdiv2 .image {
        width: 25%;
        height: auto;
 
    }

    .listdiv2 .listdiv2 img {
        width: 70px;
        height: 70px;
    }

    .listdiv2 .listdiv2 .text {
        width: 75%;
        height: 70px;
        background-color: green;
    }

    .listdiv2 .listdiv2 .text a,
    .listdiv2 .listdiv2 .text span {
        float: left;
        color: purple;
    }


</style>







@include("layouts.modalview1")



@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush