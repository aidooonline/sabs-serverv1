@extends('layouts.admin')

@section('title')

@endsection
 
@section('content')

@include('layouts.inlinecss')

<div class="row dashboardtext" style="padding-bottom:50px;padding-top:10px;">
    <h4 class="card-title">
    Create New Loan Account
      </h4>

    <div id="mainsearchdiv">
        <div class="listdiv2" style="padding-top:20px;margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;border-radius:10px 10px;">
            {{Form::open(array('url'=>'loans','method'=>'post','enctype'=>'multipart/form-data'))}}
            
            <div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Loan Name</label>
                        {{Form::text('name',null,array('class'=>'form-control','required'=>'required'))}}
                    </div>
                </div>



                <div class="col-12">
                    <div class="form-group">
                        <label>Interest (%) </label>
                        {{Form::text('interest',null,array('class'=>'form-control'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Duration (In Days)</label>
                        {{Form::text('duration',null,array('class'=>'form-control','required'=>'required'))}}
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <label>Interest Per Anum</label>
                        {{Form::text('interest_per_anum',null,array('class'=>'form-control','required'=>'required'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Collateral Fee (%)</label>
                        {{Form::number('collateral_fee',null,array('class'=>'form-control'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Processing Fee (%)</label>
                        {{Form::number('processing_fee',null,array('class'=>'form-control'))}}
                    </div>
                </div>

                <div style="display:none;" class="col-12">
                    <div class="form-group">
                        <label>If Defaulted Charge</label>
                        {{Form::text('payment_default_interest',null,array('class'=>'form-control','placeholder'=>__('')))}}
                    </div>
                </div>


                <div style="display:none;" class="col-12">
                    <div class="form-group">
                        <label>Disable</label>
                        {{-- {{Form::text('is_shown',null,array('class'=>'form-control','placeholder'=>__('')))}} --}}
                        {!! Form::select('is_shown', ['yes','no'], null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!} 
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
<style>
    table td[class='mintd'] {
        padding: 5px 25px !important;
    }

    .name {
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







@include("layouts.modalview1")



@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush