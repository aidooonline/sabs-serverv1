@extends('layouts.admin')

@section('title')

@endsection


@section('content')

@include('layouts.inlinecss')


<div class="row dashboardtext" style="padding-bottom:50px;padding-top:10px;">
    <h4 class="card-title">
        Edit Loan Request
    </h4>

    <div id="mainsearchdiv">


        <div class="listdiv2"
            style="padding-top:20px;margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;border-radius:10px 10px;background-color:rgb(250, 244, 244) !important;">
            {{Form::model($loanrequestdetail,array('route' => array('loanrequestdetail.update', $loanrequestdetail->id),
            'method' => 'PUT')) }}

<div id="tab1">

    

    <h4 class="row" style="margin-left:18px;width:100%;margin-bottom:40px;color:#898989">1. Customer Information</h4>
    
    <div class="row col-12">
        <div style="float:left;" class="form-group col-2 chat-avatar"> 
            <img src="{{env('NOBS_IMAGES')}}useraccounts/profileimage.png" class="rounded-circle" id="customerimage"  style="width:80px;height:auto;"/>
        </div>
        
        
        
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>FirstName</label>
             
            {{Form::text('first_name',null,array('class'=>'form-control','id'=>"first_name",'required'=>'required','readonly'=>'readonly'))}}
        </div>
    </div>
   
    <div class="col-12">
        <div class="form-group">
            <label>LastName</label>
            
            {{Form::text('last_name',null,array('class'=>'form-control','id'=>"last_name",'required'=>'required','readonly'=>'readonly'))}}
        </div>
    </div>
     

    <div class="col-12">
        <div class="form-group">
            <label>Contact</label>
            
            {{Form::text('phone_number',null,array('class'=>'form-control','id'=>"phone_number",'required'=>'required','readonly'=>'readonly'))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Customer Account Number</label>
            
            {{Form::text('account_number',null,array('class'=>'form-control','id'=>"account_number",'required'=>'required','readonly'=>'readonly'))}}
        </div>
            
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Loan Account Number</label>
           
            {{Form::text('customer_account_id',null,array('class'=>'form-control','id'=>"customer_account_id",'required'=>'required','readonly'=>'readonly'))}}
          
            {{Form::text('loan_account_number',null,array('class'=>'form-control','id'=>"loan_account_number",'required'=>'required','readonly'=>'readonly'))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Business Capital</label>
            {{Form::text('bus_capital',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Estimated Daily Sales</label>
            {{Form::text('est_daily_sales',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Estimated Daily Expense</label>
            {{Form::text('est_daily_exp',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>

    <div id="tab1btn" class="col-12">
        <div class="form-group">
            <a href="#" onclick="getnavigation('loaninfo')" class="btn mr-1 btn-round btn-dark"><i
                    class="fas fa-angle-double-right"></i> Next: Loan Info</a>
        </div>
    </div>



</div>

<div style="display:none;" id="tab2">
    
    <h4 class="row" style="margin-left:18px;width:100%;margin-bottom:40px;color:#898989">2. Loan Information</h4>
     

     
    <input type="hidden" name="user" value="" />

    <input type="hidden" name="id" value="{{$loanrequestdetail->id}}" />

    <input type="hidden" name="loan_migrated" value="{{$loanrequestdetail->loan_migrated}}" />

    <div style="display:none" class="col-12">
        <div class="form-group">
            <label>Loan Type</label>
            {{Form::text('loan_name',null,array('class'=>'form-control','required'=>'required','id' => 'loannameid'))}}
            {{Form::text('loan_id',null,array('class'=>'form-control','required'=>'required','id' => 'loanid'))}}
            {{Form::text('loan_purpose',null,array('class'=>'form-control','required'=>'required','id' => 'loanpurposeid'))}}
            
        </div>
    </div>


    <div class="col-12">
        <div class="form-group">
            <label>Loan Type</label>
            {{Form::select('loantypedraft', $loantypes, null,array('class' => 'form-control
            ','data-toggle'=>'select','onChange' => 'getselectedloantext(this)')) }}
        </div>
    </div>


    <div class="col-12">
        <div class="form-group">
            <label>Requested Amount</label>
            {{Form::text('amount',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Loan Purpose</label>
            {{Form::select('loan_purposes', $loanpurpose, null,array('class' => 'form-control
            ','data-toggle'=>'select','onChange' => 'getselectedloanpurpose(this)')) }}

        </div>
    </div>

    <div class="col-12">
        <div class="form-group row pl-2">
            <label>Requested Date</label>
            
            {{Form::text('created_at',null,array('class'=>'form-control date1','id'=>'requesteddate'))}}
            {{Form::date('created_at2',null,array('class'=>'form-control date date2','onchange'=>'getselecteddate(event,"requesteddate")'))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>IRPM</label>
            {{Form::text('irpm',null,array('class'=>'form-control'))}}
        </div>
    </div>


    <div  class="col-12">
        <div class="form-group row pl-2">
            <label>Expected Disbursement Date</label>
            {{Form::text('expected_disbursement_date',null,array('class'=>'form-control date1','id'=>'expecteddisbursementdate'))}}
            {{Form::date('expected_disbursement_date2',null,array('class'=>'form-control date date2','onchange'=>'getselecteddate(event,"expecteddisbursementdate")'))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Mode of Payment</label>
            {{Form::text('mode_of_pmt',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>

    <div  style="display:none" class="col-12">
        <div class="form-group">
            <label>Disbursement Date</label>

            {!!Form::date('disbursement_date',null,array('class'=>'form-control','placeholder'=>__('')))!!}
        </div>
    </div>



    <div class="col-12">
        <div class="form-group">
            <label>External Credit Facilty</label>
            {{Form::text('ext_credit_facility',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>External Credit Facility Amount</label>
            {{Form::text('ext_credit_facility_amt',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>




    <div style="display:none;">
        {{Form::text('loan_request_rating',null,array('class'=>'form-control','id'=>__('loan_request_rating')))}}

    </div>
    <div style="display:none" id="otherdiv" class="col-12">
        <div class="form-group">
            <label>State Other Purpose</label>
            {{Form::textarea('pri_pmt_src',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>


    <div class="col-12">
        <div class="form-group">
            <label>Loan Request Rating</label>
            <div style="padding-left:0 !important;width:auto !important;" class="stars">
                <input onchange="checkrating2('5')" class="star star-5" id="star-5" type="radio"
                    name="star" />
                <label class="star star-5" for="star-5"></label>
                <input onchange="checkrating2('4')" class="star star-4" id="star-4" type="radio"
                    name="star" />
                <label class="star star-4" for="star-4"></label>
                <input onchange="checkrating2('3')" class="star star-3" id="star-3" type="radio"
                    name="star" />
                <label class="star star-3" for="star-3"></label>
                <input onchange="checkrating2('2')" class="star star-2" id="star-2" type="radio"
                    name="star" />
                <label class="star star-2" for="star-2"></label>
                <input onchange="checkrating2('1')" class="star star-1" id="star-1" type="radio"
                    name="star" />
                <label class="star star-1" for="star-1"></label>
            </div>

        </div>
    </div>


    <div class="col-12">
        <div class="form-group">
            <label>Primary Payment Source</label>
            {{Form::text('pri_pmt_src',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Secondary Payment Source</label>
            {{Form::text('sec_pmt_src',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>

    <div id="tab2btn" class="col-12">
        <div class="form-group">
            <a href="#" onclick="getnavigation('customersinfo')" class="btn mr-1 btn-round btn-dark"><i
                    class="fas fa-angle-double-left"></i> Customer's Info</a>
            <a href="#" onclick="getnavigation('guarantorsinfo')"
                class="btn mr-1 btn-round btn-dark"><i class="fas fa-angle-double-right"></i>
                Guarantor's Info</a>
        </div>
    </div>


</div>

<div style="display:none;" id="tab3">
   
    <h4 class="row" style="margin-left:18px;width:100%;margin-bottom:40px;color:#898989">3. Guarantor's Information</h4>
    <div class="col-12">
        <div class="form-group">
            <label>Guarantor's Name</label>
            {{Form::text('guarantor_name',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Guarantor's Number</label>
            {{Form::text('guarantor_number',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Guarantor's GPS Loc</label>
            {{Form::text('guarantors_gps_loc',null,array('class'=>'form-control','placeholder'=>__('')))}}
        </div>
    </div>

    <div style="display:none;" id="tab3btn" class="col-12">
        <div class="form-group">
            <a href="#" onclick="getnavigation('loaninfo')" class="btn mr-1 btn-round btn-dark"><i
                    class="fas fa-angle-double-right"></i> Previous: Loan Info</a>

        </div>
    </div>
</div>



            <div class="col-12" style="margin-bottom:20px;padding-bottom:20px;">
                <div class="form-group">
                    {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-purple rounded-pill
                    mr-auto'))}}

                    {{Form::close()}}
                </div>
            </div>
        </div>


    </div>


</div>



</div>
</div>






@include("layouts.loanrequestscripts")


@include("layouts.modalview1")



@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush