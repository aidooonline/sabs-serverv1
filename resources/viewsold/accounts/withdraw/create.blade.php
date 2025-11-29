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
            Withdrawal : <span id="withdrawalname" class="text-warning"> {{$useraccount1->first_name}} {{$useraccount1->middle_name}} {{$useraccount1->surname}}</span>
        </h4>

        <input style="display:none;" id="useraccountphonenumber" type="text" value="{{$useraccount1->phone_number}}"
            class="form-control" />


        <h5 class="card-title mb-4" style="margin-top:20px;margin-left:30px;">
            Account No. : <span id="account_number" class="text-info">
                @foreach($mainaccountnumber as $maccountno)
                {{$maccountno}}
                @endforeach
            </span>
            <br />
            @foreach($accounttype as $actype)

            <span style="font-size:14px !important;color:#c39dc7;margin-bottom:30px;"
                id="accounttype">{{$actype}}</span>
            @endforeach
            
            <br />
            Current Balance : <span id="balanceamount" class="text-info">{{number_format($totalbalance, 2, '.', '')}} </span><br />
            Available Balance : <span id="availbalanceamount" class="text-info">{{number_format($totalbalance - 0.033 * $totalbalance,2,'.','')}}</span>

            <input id="totalbalancehidden" style="display:none"   value="{{number_format($totalbalance - 0.033 * $totalbalance,2,'.','')}}" />
            <input id="totalbalancehiddenwithout_deductions" style="display:none"   value="{{number_format($totalbalance,2,'.','')}}" />
        </h5>
        @endforeach

        <a href="#" class="rounded pl-2 pr-2 mt-0" style="border:solid 1px;margin-left:30px;"
            onclick="showotheraccountno();">Show Other Acc. No.s</a>

        <div style="margin:10px 30px !important;" id="showotheraccountno">
            @foreach($useraccountnumbers as $useraccountnumber)
            @if($useraccountnumber->account_number == $accountsid)

            @else

            <input type="button" class="btn-purple rounded" style="margin:2px 2px;"
                value="{{$useraccountnumber->account_number}}" />

            @endif



            @endforeach
        </div>




        <div class="col-11 insetshadow">

            <div class="form-group">
                <span class="text-warning">Step 1</span>
                <input type="number" min="0.00" id="amount" name="amount" class="form-control mb-2" placeholder="Amount Here.." step="any" />
            </div>
        </div>



        <div class="col-11 insetshadow" id="confirmdiv" style="display:none;">
            <span class="text-warning">Step 2</span>
            <div class="form-group">
                <input class="btn btn-purple mb-2 customercodebtn" id="sendcustomercode" type="button"
                    onclick="sendconfirmationcode()" value="Send Confirmation Code to Customer" />
                <div style="display:none;" id="confirmationmessage" class="text-secondary"> Customer Confirmation Code
                    Sent. Enter Below..
                    <div class="spinner-border text-info" role="status">
                        <span class="sr-only"> Waiting.. </span>
                    </div>

                </div>
                <input style="display:none;" type="text" id="confirmationcode" class="form-control mb-2"
                    placeholder="Enter Code Here.." step="any" />



            </div>
        </div>


        <div class="col-11 insetshadow"  id="withdrawbtndiv">

            <div class="form-group">

                <span class="text-warning">Step 2</span>


                @foreach($account as $useraccount2)
                <input class="btn btn-purple mb-2 customercodebtn" id="confirmcustomercode" type="button"
                    onclick="makewithdrawaljvs('{{$useraccount2->phone_number}}',withdrawalmsg())" value="Initiate Withdrawal" />
                @endforeach




            </div>
        </div>










        <div
            style="margin-top:100px;width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;">
            <table id="tdetailstable" class=" table-striped tableFixHead table-bordered"
                style="padding-bottom:0;position:relative;">
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
                        <td title="{{\Auth::user()->dateFormat($transactions->created_at)}}">
                            {{$transactions->created_at->diffForHumans()}}</td>
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
//resources/views/accounts/withdraw/create.blade.php


    function showotheraccountno() {

        $("#showotheraccountno").toggle();
    }


    function randomIntFromInterval(min, max) { // min and max included 
        return Math.floor(Math.random() * (max - min + 1) + min)
    }

    
    let confirmationcodeval = null;


    function getconfirmcode() {
        return confirmationcodeval;
    }

    function uuidv4() {
        return ([1e7] + 1e3 + 4e3 + 8e3 + 1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }


    function grtcn() {
        return ([1e1] + 1e1).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }



    function hideshowconfirmbox(isvalid) {

        if (isvalid == true) {

            $('#confirmdiv').show();
        } else {
            $('#confirmdiv').hide();

        }
    }


    function sendconfirmationcode() {
     
        if ($('#amount').val() == '') {
            alert('Kindly Enter Valid Amount');
        } else {

            let enteredamount = $('#amount').val();
            let availbalanceamount = $('#totalbalancehidden').val();


            if (parseInt(enteredamount) > parseInt(availbalanceamount)) {
                alert('The Withdrawal Amount: ' + enteredamount + ' is in-sufficient.');
            } else {
                //Confirm Code Here.
                showconfirmationtext();
                //let balance = availbalanceamount - enteredamount;
                callconfirmationcode();
            }



        }
    }


    function calculatecurrentbalance() {
        let totalamt = parseFloat($('#amount').val());
        let balance2 ='';
        let realbalance ='';
        //totalbalancehiddenwithout_deductions
        
        if($('#accounttype').html() == '["Regular Account"]'||$('#accounttype').html() == 'Regular Account'){
            
          balance2 =  parseFloat($('#totalbalancehidden').val());
          realbalance = parseFloat(balance2 - totalamt);
        }else{
          balance2 =  parseFloat($('#totalbalancehiddenwithout_deductions').val());
          realbalance = parseFloat(balance2 - totalamt);
            
        }
        
        

        return realbalance;
    }


    function showconfirmationtext() {
        $('#confirmationcode').show();
        $('#confirmationmessage').show();
        $('#withdrawbtndiv').show();

    }


    function formatAMPM(date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }


    function getcurrentdatejv() {
 const monthNames2 = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        let today = new Date();
        let date = today.getFullYear() + '-' + (monthNames2[today.getMonth()]) + '-' + today.getDate() + '-' + formatAMPM(new Date);
        return date;
    }


    function withdrawalmsg() {
        


        let amountval = formatter.format($('#amount').val());
        let message1 = ' Withdrawal from ';
        let accountnumberval = $('#account_number').html();
        let customer_accountnameval = $('#withdrawalname').html();
        let customer_accounttype = $('#accounttype').html();
        
        
    
        let message2 = 'on';
        let currendate = getcurrentdatejv();
        let mybalance = formatter.format(calculatecurrentbalance());

        let mymessage = amountval +" "+   message1 + ' ' + accountnumberval + ' ' + customer_accountnameval + ' (' + customer_accounttype + ') ' + message2 + ' ' + currendate + ' Balance: ' + mybalance;
 
        return mymessage;
    }
    
    
    

    function callconfirmationcode(balance) {
        confirmationcodeval = grtcn();
        var today = new Date();

        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];



        //var date = monthNames[today.getMonth()]+'-'+(today.getDate()+1)+'-'+today.getFullYear();

        let mymessage = 'New Withdrawal Confirmation Code: ' + confirmationcodeval;


        sndmsg($('#useraccountphonenumber').val(), mymessage);

    }



</script>





@include("layouts.modalview1")


@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush