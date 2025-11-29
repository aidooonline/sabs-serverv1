 

 

@extends('layouts.admin')
 
@section('title')
 
@endsection
 
@section('action-btn')

@endsection
@section('content')

@include('layouts.inlinecss')

<div  class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

 @include('layouts.searchrefund')
<div id="mainsearchdiv">
    
  @include('quickmenus.agentsmenu')
 
<style>
    .nav-tabs li a{padding-left:10px;padding-right:10px;}
    .nav-tabs li{
        width:31%;height:26px;
    }
    
    .nav-tabs li.btn-purple{
       height:auto;
    }
    
    .nav-tabs li a{
       height:24px !important;
    }
    .active{
        color:#ffffff !important;
    }
    
    .active strong{
        color:purple;
    }
    
</style> 

 <ul class="nav nav-tabs">
  <li class="active btn btn-purple"><a data-toggle="tab" style="font-size:12px;" href="#today">Today</a></li>
  <li class="btn btn-purple"><a data-toggle="tab" style="font-size:12px;" href="#thisweek">This Week</a></li>
  <li class="btn btn-purple"><a data-toggle="tab" style="font-size:12px;" href="#thismonth">This Month</a></li>
</ul>

<div class="tab-content">
  <div id="today"  class="tab-pane active">
        <strong  style="text-transform: capitalize;padding-left:10px;"> {{$nameoftransaction}} today (<span style="color:#800860">GH¢ {{number_format($todaytotal, 3, '.', ',')}}</span>).</strong>
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Date</strong></th>
                <th><strong>Amount</strong></th> 
                <th><strong>Acc Holder</strong></th>
                <th><strong>Agent</strong></th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($todaytotalWD as $transactions)

            <tr>
                 <td title="{{\Auth::user()->dateFormat($transactions->created_at)}}">{{$transactions->created_at}}</td>
                 <td><strong class="text-success">GH¢ {{$transactions->amount}}</strong></td> 
                 <td>{{$transactions->det_rep_name_of_transaction}}</td> 
                 <td>{{$transactions->agentname}}</td> 
                
                
            </tr>

            @endforeach
        </tbody>
       
    </table>
  </div>
  
  
  <div id="thisweek" class="tab-pane fade">
         <strong  style="text-transform: capitalize;padding-left:10px;"> {{$nameoftransaction}} this week (<span style="color:#800860">GH¢ {{number_format($thisweektotal, 3, '.', ',')}}</span>).</strong>
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Date</strong></th>
                <th><strong>Amount</strong></th> 
                <th><strong>Acc Holder</strong></th>
                <th><strong>Agent</strong></th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($thisweektotalWD as $transactions)
            <tr>
                 <td title="{{\Auth::user()->dateFormat($transactions->created_at)}}">{{$transactions->created_at}}</td>
                 <td><strong class="text-success">GH¢ {{$transactions->amount}}</strong></td> 
                 <td>{{$transactions->det_rep_name_of_transaction}}</td> 
                 <td>{{$transactions->agentname}}</td> 
                
                
            </tr>

            @endforeach
        </tbody>
       
    </table>
  </div>
  
  
  <div id="thismonth" class="tab-pane fade">  
 <strong  style="text-transform: capitalize;padding-left:10px;"> {{$nameoftransaction}} this month (<span style="color:#800860">GH¢ {{number_format($thismonthtotal, 3, '.', ',')}}</span>).</strong>
      
     <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Date</strong></th> 
                <th><strong>Amount</strong></th> 
                <th><strong>Acc Holder</strong></th>
                <th><strong>Agent</strong></th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($thismonthtotalWD as $transactions)

            <tr>
                 <td title="{{\Auth::user()->dateFormat($transactions->created_at)}}">{{$transactions->created_at}}</td> 
                 <td><strong class="text-success">GH¢ {{$transactions->amount}}</strong></td> 
                 <td>{{$transactions->det_rep_name_of_transaction}}</td> 
                 <td>{{$transactions->agentname}}</td> 
                
            </tr>

            @endforeach
        </tbody>
       
    </table>
  </div>
</div>
 
 
 
 
 
 
 
   
  
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
        height: 120px;

         
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