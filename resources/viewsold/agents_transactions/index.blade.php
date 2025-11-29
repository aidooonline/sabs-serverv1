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
        <h4 class="card-title" style="margin-top:20px;">
            Transactions By Agent: 
          </h4>
    <div class="listdiv"
        style="width:100%;margin-top:0 !important;padding-bottom:140px;background-color:#ffffff;padding:10px 10px;border-radius:10px 10px;">

        <table class="table" style="border-radius:10px !important;padding-top:25px;">

            <tbody>
                @foreach($account as $useraccount)
                <tr>
                    <td class="mintd"><strong style="">Name</strong>:</td>
                    <td class="mintd"><strong style="color:purple;">
                            {{$useraccount->first_name}} {{$useraccount->surname}}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="mintd"><strong style="">Account No.</strong>:</td>
                    <td class="mintd"><strong style="color:purple ">{{$useraccount->account_number}}</strong></td>
                </tr>

                <tr>
                    <td class="mintd"><strong style="">Account Types</strong>:</td>
                    <td class="mintd"><strong style="color:purple ">{{$useraccount->account_types}}</strong></td>
                </tr>

                @endforeach
                <tr>
                    <td class="mintd"><strong style="">Deposits:</strong>:</td>
                    <td class="mintd">GH¢ <strong id="totaldeposits"
                            style="color:purple ">{{number_format($totaldeposits,2)}}</strong></td>
                </tr>
                <tr>
                    <td class="mintd"><strong style="">Withdrawals</strong>:</td>
                    <td class="mintd">GH¢ <strong id="totalwithdrawals"
                            style="color:purple ">{{number_format($totalwithdrawals, 2)}}</strong></td>
                </tr>

                <tr>
                    <td class="mintd"><strong style="">Refunds</strong>:</td>
                    <td class="mintd">GH¢ <strong id="totalwithdrawals"
                            style="color:purple ">{{number_format($totalrefunds, 2)}}</strong></td>
                </tr>
                <tr>
                    <td class="mintd"><strong style="">Balance</strong>:</td>
                    <td class="mintd">GH¢ <strong id="totalbalance" style="color:rgb(58, 140, 247) ">{{number_format($totalbalance, 2)}}</strong></td>
                </tr>

            </tbody>
        </table>
    </div>

<div style="width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;">
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
                <td>{{$transactions->created_at->isoFormat('ddd d MMM Y, h:s A')}}</td>
                <td>{{$transactions->name}}</td>
            </tr>

            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th><strong>Tr Name</strong></th>
                <th><strong>Amount</strong></th>
                <th><strong>Date</strong></th>
                <th><strong>User</strong></th>
            </tr>
        </tfoot>
    </table>
</div>

      </div>
</div>
<style>

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







@include("layouts.modalview1")



@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush