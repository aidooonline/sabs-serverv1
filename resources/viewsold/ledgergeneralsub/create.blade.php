@extends('layouts.admin')

@section('title')

@endsection

@section('action-btn')

@endsection
@section('content')

@include('layouts.inlinecss')

<div class="row dashboardtext" style="padding-bottom:100px;">
    <h4 class="card-title">
        <span type="text" class="text-info" id="ledgername">{{$ledgername}}</span></label> > {{$parentname}}
        ({{$ledgergeneralsub_sum}})
    </h4>

    <div id="mainsearchdiv">



        <a href="#" onclick="$('#ledgerdetailcreateform').modal('show');" style="position:absolute;right:15px;top:8px;"
            href="#" class="btn btn-purple  mr-1 btn-fab btn-sm">
            <i class="fa fa-plus"></i>
        </a>

        <table id="accountstable" class="table listdiv2 stripes">
            <thead>
                <tr>
                    <th style="width:49% !important;">Account</th>
                    <th style="width:49% !important;">Debit</th>
                    <th style="width:49% !important;">Credit</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($ledgerdetail as $item)

                

                <tr>

                   
                    <td style="width:49% !important;word-wrap:break-word;border-bottom:solid 1px #979797 !important;">
                        {{$item->name}}

                    </td>
                   
 
                    <td style="width:49% !important;word-wrap:break-word;border-bottom:solid 1px #979797 !important;">
@if($item->dr_or_cr == 1)
<b>{{$item->dr_amount}}</b>
@endif
                      
                    </td>
                    <td style="width:49% !important;word-wrap:break-word;border-bottom:solid 1px #979797 !important;">
                        @if($item->dr_or_cr == 0)
                        <b>{{$item->cr_amount}}</b>
                        @endif
                        
                    </td>
                   

                   
                </tr>

                @endforeach

            </tbody>
        </table>


        <div class="modal fade" id="ledgerdetailcreateform" tabindex="-1" role="dialog"
            aria-labelledby="ledgerdetailcreateform" aria-hidden="true">
            <div class="modal-dialog modal-dialog-slideout modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-body" style="padding:5px 10px !important;">

                        <div
                            style="padding-top:10px;margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;border-radius:10px 10px;">

                            {{Form::open(array('url'=>'ledgergeneralsub/storesubledger/','method'=>'post','enctype'=>'multipart/form-data'))}}
                            @csrf

                            <div>
                                <h6 class="card-title" style="padding-left:0 !important;margin-left:5px !important;">
                                    <span type="text" class="text-info" id="ledgername">{{$ledgername}} >
                                        {{$parentname}}</span>
                                </h6>
                                <input type="text" id="parent_id" name="parent_id" class="form-control"
                                    value="{{$parentid}}" readonly />

                                <div style="display:none;" class="col-12">
                                    <div class="form-group">
                                        <label>Sub Ledger Name</label>
                                        <input id="name" name="name" class="form-control" type="text"
                                            value="{{$parentname}}" />
                                        <input id="dr_name" name="dr_name" class="form-control" type="text" value="" />
                                        <input id="cr_name" name="cr_name" class="form-control" type="text" value="" />
                                        <input type="text" id="ac_type" name="ac_type" class="form-control"
                                            value="{{$ledgertype}}" readonly />
                                        <input type="text" id="amount" name="amount" class="form-control" value="" />
                                        <input type="text" id="parent_id" name="parent_id" class="form-control"
                                            value="{{$parentid}}" readonly />
                                        <input type="text" id="trans_id" name="trans_id" class="form-control" value=""
                                            readonly />
                                    </div>
                                </div>



                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Debit</label>
                                        {{Form::select('dr_account',
                                        $ledger_type_list_query,null,array('onchange'=>'dr_getselectedtexter()','data-toggle'=>'select','id'=>'dr_account'))
                                        }}
                                    </div>

                                    <div class="form-group">
                                        <label>Credit</label>
                                        {{Form::select('cr_account',
                                        $ledger_type_list_query,null,array('onchange'=>'cr_getselectedtexter()','data-toggle'=>'select','id'=>'cr_account'))
                                        }}
                                    </div>

                                    <div style="display:none;" class="form-group">
                                        <label>Amount</label>
                                        <input type="text" id="dr_amount" name="dr_amount" class="form-control"
                                            value="" />
                                    </div>
                                </div>



                                <div class="col-12">


                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input type="text" id="cr_amount" name="cr_amount" class="form-control"
                                            value="" />
                                    </div>
                                </div>





                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        {{Form::textarea('description',null,array('class'=>'form-control','max'=>'200'))}}
                                    </div>
                                </div>


                                <div class="col-12" style="margin-bottom:20px;padding-bottom:20px;">
                                    <div class="form-group">
                                        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-purple rounded-pill
                                        mr-auto'))}}{{Form::close()}}
                                    </div>
                                </div>
                            </div>
                            {{Form::close()}}

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>





    </div>



</div>


</div>

<style>
    #accountstable td {
        padding: 3px 3px !important;
    }


    table td {
        padding: 1px 1px !important;
        max-height: 16px !important;
        height: 16px !important;

    }

    table td span,
    table td {
        padding: 1px 1px !important;
        max-height: 16px !important;

    }

    .col-12 {
        background-color: #fff;
        padding-left: 5px;
        padding-right: 5px;
        padding-bottom: 1px;
        padding-top: 5px;
        margin-bottom: 20px;
    }

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

    .listdiv2 label {
        color: purple !important;
    }

    /*Profile Pic Start*/
    .picture-container {
        position: relative;
        cursor: pointer;
        text-align: center;
    }

    .picture {
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

    .picture:hover {
        border-color: #2ca8ff;
    }

    .content.ct-wizard-green .picture:hover {
        border-color: #05ae0e;
    }

    .content.ct-wizard-blue .picture:hover {
        border-color: #3472f7;
    }

    .content.ct-wizard-orange .picture:hover {
        border-color: #ff9500;
    }

    .content.ct-wizard-red .picture:hover {
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

    .picture-src {
        width: 100%;

    }

    /*Profile Pic End*/
</style>

<script>

    $(document).ready(function () {
        dr_getselectedtexter();
        cr_getselectedtexter();
    });

    function dr_getselectedtexter() {
        let selectedtexter = $("#dr_account option:selected").text();

        $('#dr_name').val(selectedtexter);
    }
    function cr_getselectedtexter() {
        let selectedtexter = $("#cr_account option:selected").text();

        $('#cr_name').val(selectedtexter);
    }




</script>



@include("layouts.modalview1")



@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush