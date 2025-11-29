 

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
            Deposit to : <span id="customer_accountname" class="text-warning"> {{$useraccount1->first_name}} {{$useraccount1->surname}}</span>
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
                
                <span style="display:none" id="balancespan2" class="text-info"><?php echo $totalbalance; ?>  
                </span>
                
           
          </h5>
          @endforeach
         
<a href="#" class="rounded pl-2 pr-2 mt-0" style="border:solid 1px;margin-left:30px;" onclick="showotheraccountno();">Show Other Acc. No.s</a>
      
<div style="margin:10px 30px !important;" id="showotheraccountno">
           
  @foreach($account as $useraccount1)
<input type="button" class="btn-purple rounded" style="margin:2px 2px;" value="{{$useraccount1->account_number}}" />
    @endforeach
 
        </div>
       
          <div class="col-11 insetshadow">
            
            <div class="form-group">
             
          <input  type="number" min="0.00" id="amount" name="amount" class="form-control mb-2" placeholder="Amount Here.." step="any" />
            </div>
        </div>
        
        <script>
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];


function calculatecurrentbalance(){
    let totalamt = parseFloat($('#amount').val());
    let balance2 = parseFloat($('#balancespan2').html());
    let realbalance = parseFloat(balance2 + totalamt);
    
    return realbalance;
}

function formatAMPM(date) {
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'pm' : 'am';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = hours + ':' + minutes + ' ' + ampm;
  return strTime;
}

            
            function getcurrentdatejv(){
 
                let today = new Date();
                let date = today.getFullYear()+'-'+(monthNames[today.getMonth()])+'-'+today.getDate()+'-'+formatAMPM(new Date);
                return date;
            }



            function getdepositmessage(){
                
                let amountval = formatter.format($('#amount').val());
                let message1 = 'Deposited to';
                let accountnumberval = $('#account_number').html();
                let customer_accountnameval = $('#customer_accountname').html();
                let customer_accounttype = $('#accounttype').html();
                let message2 = 'on';
                let currendate = getcurrentdatejv();
                let mybalance = formatter.format(calculatecurrentbalance());
                 
                return amountval + ' ' + message1 + ' ' + accountnumberval + ' '+ customer_accountnameval + ' ('+ customer_accounttype + ') ' + message2 + ' '+ currendate + ' Balance: '+ mybalance;
            }
        </script>

             
 

        <div class="col-11 insetshadow">
            
            <div class="form-group">
                 
                @foreach($account as $useraccount2)
                <input class="btn btn-purple mb-2 customercodebtn" id="confirmcustomercode" type="button" onclick="makedepositjvs('{{$useraccount2->phone_number}}',getdepositmessage()),''" value="Deposit" />
                @endforeach


            </div>
    </div>

       
 

 

<div style="margin-top:100px;width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;overflow-x:hidden;">
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Tr ID</strong></th>
                <th><strong>Tr Name</strong></th>
                <th><strong>Amount</strong></th>
                <th><strong>Date</strong></th>
                <th><strong>User</strong></th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $transactions)

            <tr>
                <td>{{$transactions->transaction_id}}</td>
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