@extends('layouts.admin')

@section('title')

@endsection


@section('content')

@include('layouts.inlinecss')


<div class="row dashboardtext" style="padding-bottom:50px;padding-top:10px;margin-bottom:50px;">
    <h4 style="width:100%;" class="card-title row">
        Migrate Loan
    </h4>
    <h6 style="border-radius:8px;width:80%;margin-bottom:0;background-color:rgba(70, 0, 70, 0.308);
    padding-top:10px;padding-bottom:10px;padding-left:0 !important;margin-left:0;padding-left:10px;margin-left:10%;margin-right:10%;"
        id="amountapproveddynamic" class="text-warning row">

    </h6>

    <div style="margin-top:5px;" id="mainsearchdiv">

        <div class="listdiv2"
            style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;border-radius:10px 10px;background-color:rgb(255, 255, 255) !important;">

            <br />
            {{Form::open(array('url'=>'loanmigrations','method'=>'post','enctype'=>'multipart/form-data'))}}
            @foreach($customer_pic as $cpic)
            @if($cpic->customer_picture)
            <img src="" profilevalue="{{$cpic->customer_picture}}" class="profilepic"
                style="padding:0 0;width:80px;height:80px;position:absolute;top:2px;right:30px;z-index:2;border-radius:20px;border:solid 3px #5c1659 !important;" />

            @else
            <img src="{{env('NOBS_IMAGES')}}/useraccounts/profileimage.png"
                style="padding:0 0;width:80px;height:80px;position:absolute;top:2px;right:30px;z-index:2;border-radius:20px;border:solid 3px #5c1659 !important;" />

            @endif
    @endforeach

            @foreach($requestedloan as $reqloan)
            <div style="padding-bottom:42px;" id="tab1">

                <div class="col-12">

                   

                    <ul style="border:0 !important;" class="list-group">

                        <li class="list-group-item">
                            <div class="row mt-1">Customer Name:
                                <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;">
                                    {{$reqloan->first_name}} {{$reqloan->last_name}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row mt-1">Loan Account No: <div
                                    style="font-weight:normal !important;padding-left:10px;" class="textcolor1">
                                    {{$reqloan->loan_account_number}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row mt-1">Customer Account No: <div
                                    style="font-weight:normal !important;padding-left:10px;" class="textcolor1">
                                    {{$reqloan->account_number}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row mt-1">Business Capital: <div
                                    style="font-weight:normal !important;padding-left:10px;" class="textcolor1">
                                    {{$reqloan->bus_capital}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row mt-1">Requested Amount: <div
                                    style="font-weight:normal !important;padding-left:10px;" class="textcolor1">
                                    {{$reqloan->amount}}
                                </div>
                            </div>
                        </li>
                       

                    </ul>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Cash Collateral:</label>
                        <input type="number" class="form-control" name="cash_collateral" id="cash_collateral"
                            value="{{(10/100)*$reqloan->amount}}" />
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label>Approved Amount</label>
                        <input type="number" class="form-control" onkeyup="changeApprovedAmount();calculate_cash_collateral();calculate_processing_fee();"
                            name="approved_amount" id="approved_amount" value="{{$reqloan->amount}}" />
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label>Processing Fee</label>
                        <input type="number" class="form-control"
                            name="processing_fee" id="processing_fee" value="{{(5/100)*$reqloan->amount}}" />
                    </div>
                </div>



                <div id="tab1btn" class="col-12">
                    <div class="form-group">

                        <a href="#" onclick="getnavigation('loaninfo')" class="btn mr-1 btn-round btn-dark"><i
                                class="fas fa-angle-double-right"></i> Next</a>
                    </div>
                </div>

            </div>

            <div style="display:none;padding-bottom:30px;" id="tab2">
               
                <input type="hidden"   name="loan_migrated" value="" />
                <input type="hidden" name="user" value="{{\Auth::user()->created_by_user}}" />
                <input type="hidden" id="agent_id_hidden" name="agent_id" value="{{$reqloan->agent_id}}" />
                <input type="hidden" id="requested_amount" name="amount" value="{{$reqloan->amount}}" />
                <input type="hidden" name="customer_id" id="customer_account_id" value="{{$customerid}}" />
                <input type="hidden" name="customer_account_number" id="customer_account_number" value="{{$reqloan->account_number}}" />
                <input type="hidden" name="loan_account_number" id="loan_account_number2" value="{{$reqloan->loan_account_number}}" />
                <input type="hidden" name="loanrequestid" id="loan_account_number2" value="{{$loanrequestid}}" />
                <input type="hidden" name="loan_migration_id" id="loan_migration_id" value="" />
                <input type="hidden" name="loan_account_id" id="loan_account_id" value="{{$reqloan->loan_id}}" />
                <input type="hidden" name="loan_schedule_data" id="loan_schedule_data" value="" />
                

                <div class="col-12">
                    <div class="form-group row pl-2">
                        <label>Payment Begins At</label>
                        <input type="text" class="form-control date1" name="payment_start_at_text"
                            id="payment_start_at_text" readonly />
                        {{Form::date('payment_start_at_date2',null,array('id'=>'payment_start_at_date2','class'=>'form-control
                        date date2','onchange'=>'getselecteddate(event,"payment_start_at_text")'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Payment Scheduling</label>

                        {{Form::select('loan_schedule', $loanschedule,
                        null,array('onchange'=>'set_loan_schedule_value();','data-toggle'=>'select','id'=>'loanscheduleid'))
                        }}

                    </div>
                </div>





                <div class="col-12">
                    <div class="form-group">
                        <label>Payment Duration</label> (<output id="range_weight_disp">1</output> <span
                            id="range_weight_disp_span">Day/s </span>)

                        <input type="range" name="payment_duration" class="form-control" step="1" id="payment_duration"
                            value="1" min="1" max="3650" oninput="range_weight_disp.value = payment_duration.value;insertinterestinto('interest-layer');">
 
    <div id="selected_numberofdays_list"  class="btn-group" style="width:100%">
        
     </div>
                    </div>
                    
                </div>
                

                <div class="col-12">
                    <div class="card media">
                        <div class="p-1 media-body">
                          <label style="color:#444;">Principal:</label>
                          <b id="principal-layer" style="font-weight:bold;color:#676767">{{$reqloan->amount}}</b>
                        </div>
                        <div class="p-1 media-body">
                            <label style="color:#444">Interest:</label>
                            <b id="interest-layer" style="font-weight:bold;color:#676767"></b>
                            <input style="display:none;" id="interest_per_anum_hidden" type="text" value="{{$reqloan->interest_per_anum}}" />
                          </div>
                          <div class="p-1 media-body">
                            <label style="color:#444">Total:</label>
                            <label type="text" name="amount_to_be_paid" id="tobe-paid-layer" style="font-weight:bold;color:#676767" readonly></label>
                          </div>
                    </div>
                     
                </div>
                 

                <div class="col-8">
                    <div class="form-group">

                        <input type="button" class="form-control btn-purple round"
                            onclick="generate_loan_schedule_list()" id="generate_payment_schedule_list_id"
                            value="Generate Payment Schedule" />
                    </div>

                    <div id="myProgress" class="progress mb-2">
                        <div id="myBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" aria-valuenow="100" aria-valuemin="100" style="width:1%;" aria-valuemax="100"></div>
                    </div>

                     
                </div>

                <div class="col-12">
                    <div class="form-group" style="width:100%;">
                        <label>Payment Schedule</label> <span id="disp_days_count2"></span>

                        <table style="width:100%;" class="table-sm table-striped" id="payment_scheduling_list_ul">

                        </table>


                    </div>
                </div>





                <div id="tab2btn" class="col-12">
                    <div class="form-group">
                        <a href="#" onclick="getnavigation('customersinfo')" class="btn mr-1 btn-round btn-dark"><i
                                class="fas fa-angle-double-left"></i> Previous</a>
                        <input type="submit" style="position:absolute;right:20px;bottom:-3px;"
                            class="btn mr-1 btn-round btn-purple" value="Save" />
                    </div>
                </div>

            </div>

            <div style="display:none;padding-bottom:30px;" id="tab3">


                <div style="display:none;" id="tab3btn" class="col-12">
                    <div class="form-group">
                        <a href="#" onclick="getnavigation('loaninfo')" class="btn mr-1 btn-round btn-dark"><i
                                class="fas fa-angle-double-right"></i> Next</a>
                    </div>
                </div>

            </div>

            @endforeach

            {{Form::close()}}


        </div>


    </div>


</div>



</div>
</div>



<style>

#myProgress {
  width: 100%;
  display:none;
}

#myBar {
  width: 1%; 
}
   
#selected_numberofdays_list{
    width:100%; 
}

#selected_numberofdays_list a{
    border:solid 1px #e5e5e5;
    width:14.50%;
    color:#777;
    font-size:13px;
}





    .form-group2{
        margin-top:3px !important;
        margin-bottom:3px !important;
    }

    .form-group{
        margin-top:8px !important;
        margin-bottom:8px !important;
    }
    
    .col-12{
        margin-top:4px !important;
        margin-bottom:4px !important;
    }
    .moneyclass {
        color: #ad622f !important;
        font-weight:bold !important; 
    }

    #payment_scheduling_list_ul tr span {
        float: left;
        margin-bottom: 2px;
        margin-right:5px;
    }

    #payment_scheduling_list_ul tr {
        margin-bottom: 1px !important;
        margin-top: 5px !important;
        color: purple !important;
        font-size: 14px !important;
        float: left;
    }
 

    #payment_scheduling_list_ul {
        list-style: decimal !important;

        min-height: 200px;
        height: 200px;
        overflow-x: hidden;
        color: purple !important;
        padding: 10px 10px;
        background-color: #eee7e7;
        border-radius: 10px;

    }


    input[type=range] {
        -webkit-appearance: none;
    }

    input[type=range]::-webkit-slider-runnable-track {
        width: 300px;
        height: 5px;
        background: purple;
        border: none;
        border-radius: 3px;
    }

    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        border: none;
        height: 18px;
        width: 18px;
        border-radius: 50%;
        background: rgb(231, 99, 231);
        margin-top: -6px;
    }

    input[type=range]:focus {
        outline: none;
    }

    input[type=range]:focus::-webkit-slider-runnable-track {
        background: purple;
    }



    ul.list-group {

        padding: 10px 5px;
    }


    li div,
    li {
        position: relative;
        float: left;
    }


    .list-group-item {
        padding: 1px 14px;
        border: solid 0 !important;
        margin-bottom: 5px;
        border-radius: 8px;
        border-bottom: solid 2px #e4e4e4 !important;
        position: relative;
        width: 100%;
        margin-top: 2px;

    }


    .ghs {
        font-weight: normal !important;
        font-size: 13px;
    }

    .displaydivs {
        padding-bottom: 50px;

    }


    #thisweekdiv,
    #thismonthdiv,
    #thisyeardiv,
    #alltimediv {
        display: none;
    }
</style>

@include("loanmigration.generatedatelistscript")
@include("loanrequestdetail.addcustomertoloan")
@include("layouts.loanrequestscripts")

@include("layouts.modalview1")
@include("layouts.modalscripts")

@endsection

@push('script-page')

@endpush