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
    <h4 class="card-title">
        Individual Registration
      </h4>

    <div id="mainsearchdiv">


        <div class="listdiv2" style="padding-top:20px;margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;border-radius:10px 10px;">

            {{Form::open(array('url'=>'accounts','method'=>'post','enctype'=>'multipart/form-data'))}}
            @csrf  
 
            <div>

                <div class="col-12">
                    <div class="form-group">
                        <label>New Account Number</label>
                        <input readonly id="accountnumbergen" name="account_number" class="form-control" type="text" value="" />

                        <input style="display:none" readonly id="userid" name="__id__" class="form-control" type="text" value="" />

                        
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>First Name</label>
                        {{Form::text('first_name',null,array('class'=>'form-control','required'=>'required'))}}
                    </div>
                </div>



                <div class="col-12">
                    <div class="form-group">
                        <label>Middle Name</label>
                        {{Form::text('middle_name',null,array('class'=>'form-control'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Surname</label>
                        {{Form::text('surname',null,array('class'=>'form-control','required'=>'required'))}}
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <label>Phone Number</label>
                        {{Form::text('phone_number',null,array('class'=>'form-control','required'=>'required'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Other Phone Number</label>
                        {{Form::text('sec_phone_number',null,array('class'=>'form-control','placeholder'=>__('')))}}
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Gender</label>

                        {!! Form::select('gender', $gender, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!} 
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Occupation</label>
                        {{Form::text('occupation',null,array('class'=>'form-control','placeholder'=>__('')))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Residential Address</label>
                        {{Form::text('residential_address',null,array('class'=>'form-control','placeholder'=>__('')))}}
                    </div>
                </div>

                 

                <div class="col-12">
                    <div class="form-group">
                        <label>Date of Birth</label>
                        {!!Form::date('date_of_birth2', null,array('class' => 'form-control','required'=>'required')) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Postal Address</label>
                        {{Form::text('postal_address',null,array('class'=>'form-control'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Email</label>
                        {{Form::text('email',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Select Nationality</label>

                        {!! Form::select('nationality', $countries, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!} 
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Marital Status</label>

                        {!! Form::select('marital_status', $maritalstatus, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!} 
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>ID Number</label>
                        {{Form::text('id_number',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>ID Type</label>

                        {!! Form::select('id_type', $idtype, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!} 
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Next of Kin</label>
                        {{Form::text('next_of_kin',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>
                

                <div class="col-12">
                    <div class="form-group">
                        <label>Next of Kin Id Number</label>
                        {{Form::text('next_of_kin_id_number',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Next of Kin Phone Number</label>
                        {{Form::text('next_of_kin_phone_number',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Customer Picture</label>
                        <img id="img" src="" class="image round-pill"  style="border-radius:14px 14px;" />
                        <input onchange="readURL(this)" name="customer_picture" type="file" class="form-control">
                    </div>
                </div>

                


                
                <div class="col-12">
                    <div class="form-group">
                        <label>Legal Consent:</label>
                        <p style="padding:5px 10px;">I certify that the information provided above is true and I am aware that detection of any false declaration renders my application void.</p>
                        {{Form::checkbox('legalconsent',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>
                 


                
                <div class="col-12" style="margin-bottom:20px;padding-bottom:20px;">
                    <div class="form-group">
                    {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-purple rounded-pill
                    mr-auto'))}}{{Form::close()}}
                    </div>
                </div>
            </div>


        </div>
        {{Form::close()}}

    </div>



</div>
</div>
<style>
    table td[class='mintd'] {
        padding: 5px 25px !important;
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

    .listdiv {
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


    .listdiv2 {
        height: auto !important;
        height: 600px;
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

    .listdiv2 label{
        color:purple !important;
    }

    /*Profile Pic Start*/
.picture-container{
    position: relative;
    cursor: pointer;
    text-align: center;
}
.picture{
    width: 106px;
    height: 106px;
    background-color: #999999;
    border: 4px solid #CCCCCC;
    color: #FFFFFF;
    border-radius: 50%;
    margin: 0px auto;
    overflow: hidden;
    transition: all 0.2s;
    -webkit-transition: all 0.2s;
}
.picture:hover{
    border-color: #2ca8ff;
}
.content.ct-wizard-green .picture:hover{
    border-color: #05ae0e;
}
.content.ct-wizard-blue .picture:hover{
    border-color: #3472f7;
}
.content.ct-wizard-orange .picture:hover{
    border-color: #ff9500;
}
.content.ct-wizard-red .picture:hover{
    border-color: #ff3b30;
}
.picture input[type="file"] {
    cursor: pointer;
    display: block;
    height: 100%;
    left: 0;
    opacity: 0 !important;
    position: absolute;
    top: 0;
    width: 100%;
}

.picture-src{
    width: 100%;
    
}
/*Profile Pic End*/
</style>


<script type="text/javascript">
function randomIntFromInterval(min, max) { // min and max included 
  return Math.floor(Math.random() * (max - min + 1) + min)
}

const rndInt = randomIntFromInterval(10000, 90000);
const rndIn2 = randomIntFromInterval(100, 999);
const accountnumbergen = 'NBM400' + rndIn2.toString() + rndInt.toString();
 

$(function() {
  // Handler for .ready() called.
 
$("#accountnumbergen").val(accountnumbergen);
$("#userid").val(uuidv4());


});


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