 

 

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
    <h4 class="card-title" style="margin-top:20px;">
       Search Transaction ID to <span class="text-warning">Reverse</span>
      </h4>

<div>{{$depositer}}</div>
    @foreach($accounts as $account)

    <a href="{{route('accounts.searchrefund')}}" id="accountbtnpanel_{{$account->id}}"  class="accordion card listdiv" style="background:#fff;width:99%;padding-left:0;padding-right:0;margin-left:0;margin-right:0;">

        <table>
            <tr>
                <td width="23%">
                    <div> <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle profilepic" src="{{env('NOBS_IMAGES')}}/profileimage.png" profilevalue="{{$account->customer_picture}}"></div>
                </td>
                <td width="77%" style="text-align:left; ">
                    <div style="text-align:left;padding-top:1px !important;">
                        <h6 class="account_name" style="padding-top:1px !important;">
                            {{$account->first_name}} {{$account->surname}}</h6>
                    <h6 style="color:#724c78;text-align:left !important;">{{$account->account_number}}</h6>
                    
                    {{$account->occupation}} -- {{$account->residential_address}}
                           
                    </div>
                </td>
            </tr>
        </table>

    </a>
     


    @endforeach
</div>



<div style="width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;overflow-x:hidden;">
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Tr ID</strong></th>
                <th><strong>Tr Name</strong></th>
                <th><strong>Amount</strong></th>
                <th><strong>Date</strong></th>
                <th><strong>Reverse</strong></th>
            </tr>
        </thead>
        <tbody>
            @foreach($tr_accounts as $transactions)

            <tr>
                <td>{{$transactions->transaction_id}}</td>
                
                 @if($transactions->name_of_transaction == 'Refund')
                 <td class="text-info">Reversal</td>
                @else
                 
                 <td class="text-success">{{$transactions->name_of_transaction}}</td>
                @endif
               
                <td><strong class="text-success">GH¢ {{$transactions->amount}}</strong></td>
                <td title="{{\Auth::user()->dateFormat($transactions->created_at)}}">{{$transactions->created_at}}</td>
                @if($transactions->name_of_transaction == 'Refund')
                 <td> </td>
                @else
                
                @if(\Carbon\Carbon::parse($transactions->created_at)->addMinutes(30) > \Carbon\Carbon::now()->subMinutes(30))
                 <td><a href="{{route('reverse.transaction')}}/{{$transactions->id}}" class="btn-secondary">Reverse</a></td>
                 
                 @else
                 
                 <td></td>
                @endif
                @endif
                
               
            </tr>

            @endforeach
        </tbody>
       
    </table>
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