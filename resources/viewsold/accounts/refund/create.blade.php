 

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
            Refund to : <span class="text-warning"> {{$useraccount1->first_name}} {{$useraccount1->middle_name}} {{$useraccount1->surname}}</span>
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
            
            <br/>
            Balance : <span id="balancespan" class="text-info">
              GH₵ <?php echo number_format($totalbalance, 2); ?>  
                </span>
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
                <input class="btn btn-purple mb-2 customercodebtn" id="confirmcustomercode" type="button" onclick="makerefundjvs('{{$useraccount2->phone_number}}','Refund of GHs ' + $('#amount').val() + ' to your account.')" value="Refund" />
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
 
 @include('layouts.depositwithdrawalcss')

<script type="text/javascript">

function showotheraccountno(){
    
$( "#showotheraccountno").toggle();
}


</script>





@include("layouts.modalview1")
 

@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush