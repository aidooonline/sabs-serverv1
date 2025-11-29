@section('action-btn')

<!-- literally user can create accounts -->
@can('Create Product')
<a href="#" data-size="lg" data-url="{{ route('accounts.create') }}" data-ajax-popup="true"
    data-title="{{__('Create New Account')}}" class="btn btn-sm btn-purple btn-icon-only rounded-circle">
    <i class="fa fa-plus"></i>
</a>
@endcan
@endsection

@extends('layouts.admin')

@section('title')

@endsection

@section('action-btn')

@endsection

@section('content')

@include('layouts.inlinecss')

<div class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

    @include('layouts.search')

   

      <div id="mainsearchdiv">
        @foreach($account as $useraccount1)
        <h4 class="card-title" style="margin-top:20px;margin-left:30px;">
            Deposit to : <span class="text-warning"> {{$useraccount1->first_name}} {{$useraccount1->middle_name}} {{$useraccount1->surname}}</span>
          </h4>


          <input style="display:none;" type="text" value="{{$useraccount1->phone_number}}" class="form-control" />

          <h5 class="card-title mb-4    " style="margin-top:20px;margin-left:30px;">
            Account No. : <span id="account_number" class="text-info">
                @foreach($mainaccountnumber as $maccountno)
            {{$maccountno}}
                @endforeach
            </span> 
            <br/>
            @foreach($accounttype as $actype)
            
            <span style="font-size:14px !important;color:#c39dc7;" id="accounttype">{{$actype}}</span>
                @endforeach
            
           
          </h5>
          @endforeach
         
<a href="#" class="rounded pl-2 pr-2 mt-0" style="border:solid 1px;margin-left:30px;" onclick="showotheraccountno();">Show Other Acc. No.s</a>
      
<div style="margin:10px 30px !important;" id="showotheraccountno">
            @foreach($useraccountnumbers as $useraccountnumber)
@if($useraccountnumber->account_number == $accountsid)

@else
 
<input type="button" class="btn-purple rounded" style="margin:2px 2px;" value="{{$useraccountnumber->account_number}}" />
 
@endif

           

            @endforeach
        </div>
       


          
          <div class="col-11 insetshadow">
            
            <div class="form-group">
             
          <input type="number" min="0.00" id="amount" name="amount" class="form-control mb-2" placeholder="Amount Here.." step="any" />
            </div>
        </div>

             
 

        <div class="col-11 insetshadow">
            
            <div class="form-group">
                
               
                @foreach($account as $useraccount2)
                <input class="btn btn-purple mb-2 customercodebtn" id="confirmcustomercode" type="button" onclick="makedepositjvs('{{$useraccount2->phone_number}}','Deposit of GHs ' + $('#amount').val() + ' credited to your account.')" value="Deposit" />
                @endforeach


            </div>
    </div>

       


         



 

<div style="margin-top:100px;width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;">
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Tr Name</strong></th>
                <th><strong>Amount</strong></th>
                <th><strong>Date</strong></th>
                <th><strong>User</strong></th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $transactions)

            <tr>
                <td>{{$transactions->name_of_transaction}}</td>
                <td><strong>GH¢ {{$transactions->amount}}</strong></td>
                <td title="{{\Auth::user()->dateFormat($transactions->created_at)}}">{{$transactions->created_at->diffForHumans()}}</td>
                <td>{{$transactions->agentname}}</td>
            </tr>

            @endforeach
        </tbody>
       
    </table>
</div>

      </div>
</div>
<style>

    #showotheraccountno{
        display:none;
    }
    .customercodebtn{
        width:98%;
        margin-left:1%;
        margin-right:1%;

    }



    .insetshadow {
        margin:20px 20px;
        padding:10px 10px;
        border-radius:5px 5px 5px 5px;
   
}

    #tdetailstable{
        width:99% !important;
    }
    .tableFixHead {
        overflow: auto;
        height: 100px;
    }

    .tableFixHead thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    /* Just common table stuff. Really. */
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        padding: 8px 16px;
    }

    th {
        background: #eee;
    }

    .account_name {
        color: #666666;
        text-align: left !important;
        font-weight: bold;
        font-family: verdana;
    }

    .table-panel td {
        font-size: 1em !important;
        color: rgb(65, 6, 65);
        font-family: Verdana, Geneva, Tahoma, sans-serif;
    }

    .accordion img {
        width: 65px;
        height: 65px;
    }

    table td[class='mintd'] {
        padding: 5px 25px !important;
    }

    .listdiv {
        width:97% !important;
        height: auto;
    }

    .listdiv .listdiv .image {
        width: 25%;
        height: 70px;

        background-color: yellow;
    }

    .listdiv .listdiv img {
        width: 70px;

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


    .listdiv2 {
        height: auto !important;
        height: 600px;
        padding-left:0;padding-right:0;
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

<script type="text/javascript">

function showotheraccountno(){
    
$( "#showotheraccountno").toggle();
}


function randomIntFromInterval(min, max) { // min and max included 
      return Math.floor(Math.random() * (max - min + 1) + min)
    }
    
    const rndInt = randomIntFromInterval(10000, 90000);
    const rndIn2 = randomIntFromInterval(100, 999);
    const accountnumbergen = 'GCI001' + rndIn2.toString() + rndInt.toString();
     
     
    
    function uuidv4() {
      return ([1e7]+1e3+4e3+8e3+1e11).replace(/[018]/g, c =>
        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
      );
    }
</script>





@include("layouts.modalview1")
 

@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush