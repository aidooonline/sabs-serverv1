@extends('layouts.admin')
@section('page-title')
    {{__('Quote Edit')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Quote Edit')}} {{ '('. $quote->name .')' }}</h5>
    </div>
@endsection
@section('action-btn')
    <div class="btn-group" role="group">
        @if(!empty($previous))
            <a href="{{ route('quote.edit',$previous) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif
        @if(!empty($next))
            <a href="{{ route('quote.edit',$next) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="{{__('Next')}}">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action disabled" data-toggle="tooltip" data-original-title="{{__('Next')}}">
                <i class="fas fa-chevron-right"></i>
            </a>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('quote.index')}}">{{__('Quote')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Details')}}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#account_edit" class="list-group-item custom-list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-user"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Overview')}}</a>
                                <p class="mb-0 text-sm">{{__('Edit about your quote information')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#salesorders" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-file-invoice"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Sales Orders')}}</a>
                                <p class="mb-0 text-sm">{{__('Assign sales orders for this quote')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#invoice" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-receipt"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Invoice')}}</a>
                                <p class="mb-0 text-sm">{{__('Assign invoice for this quote')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <!--account edit -->
            <div id="account_edit" class="tabs-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0">{{__('Overview')}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{Form::model($quote,array('route' => array('quote.update', $quote->id), 'method' => 'PUT')) }}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('name',__('Name')) }}
                                    {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
                                    @error('name')
                                    <span class="invalid-name" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('opportunity',__('Opportunity')) }}
                                    {!! Form::select('opportunity', $opportunity, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
                                    @error('opportunity')
                                    <span class="invalid-opportunity" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('status',__('Status')) }}
                                    {!! Form::select('status', $status, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('status')
                                    <span class="invalid-status" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('account',__('Account')) }}
                                    {!! Form::select('account', $account, null,array('class' => 'form-control','data-toggle'=>'select','disabled')) !!}
                                    @error('account')
                                    <span class="invalid-account" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('amount',__('Amount')) }}
                                    {{Form::text('amount',null,array('class'=>'form-control','placeholder'=>__('Enter Amount'),'required'=>'required'))}}
                                    @error('amount')
                                    <span class="invalid-amount" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('date_quoted',__('Date Quoted')) }}
                                    {{Form::date('date_quoted',null,array('class'=>'form-control','placeholder'=>__('Enter Date Quoted'),'required'=>'required'))}}
                                    @error('date_quoted')
                                    <span class="invalid-date_quoted" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{Form::label('quote_number',__('Quote Number')) }}
                                    {{Form::text('quote_number',null,array('class'=>'form-control','placeholder'=>__('Enter Quote Number'),'required'=>'required'))}}
                                    @error('quote_number')
                                    <span class="invalid-quote_number" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('billing_address',__('Billing Address')) }}
                                    {{Form::text('billing_address',null,array('class'=>'form-control','placeholder'=>__('Enter Billing Address'),'required'=>'required'))}}
                                    @error('billing_address')
                                    <span class="invalid-billing_address" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('shipping_address',__('Shipping Address')) }}
                                    {{Form::text('shipping_address',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Address'),'required'=>'required'))}}
                                    @error('shipping_address')
                                    <span class="invalid-shipping_address" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::text('billing_city',null,array('class'=>'form-control','placeholder'=>__('Enter Billing City'),'required'=>'required'))}}
                                    @error('billing_city')
                                    <span class="invalid-billing_city" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::text('billing_state',null,array('class'=>'form-control','placeholder'=>__('Enter Billing State'),'required'=>'required'))}}
                                    @error('billing_state')
                                    <span class="invalid-billing_state" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::text('shipping_city',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping City'),'required'=>'required'))}}
                                    @error('shipping_city')
                                    <span class="invalid-shipping_city" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::text('shipping_state',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping State'),'required'=>'required'))}}
                                    @error('shipping_state')
                                    <span class="invalid-shipping_state" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::text('billing_country',null,array('class'=>'form-control','placeholder'=>__('Enter Billing country'),'required'=>'required'))}}
                                    @error('billing_country')
                                    <span class="invalid-billing_country" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::text('billing_postalcode',null,array('class'=>'form-control','placeholder'=>__('Enter Billing Postal Code'),'required'=>'required'))}}
                                    @error('billing_postalcode')
                                    <span class="invalid-billing_postalcode" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::text('shipping_country',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Country'),'required'=>'required'))}}
                                    @error('shipping_country')
                                    <span class="invalid-shipping_country" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::text('shipping_postalcode',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Postal Code'),'required'=>'required'))}}
                                    @error('shipping_postalcode')
                                    <span class="invalid-shipping_postalcode" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('billing_contact',__('Billing Contact')) }}
                                    {!! Form::select('billing_contact', $billing_contact, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
                                    @error('billing_contact')
                                    <span class="invalid-billing_contact" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('shipping_contact',__('Shipping Contact')) }}
                                    {!! Form::select('shipping_contact', $billing_contact, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
                                    @error('shipping_contact')
                                    <span class="invalid-shipping_contact" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('tax',__('Tax')) }}
                                    {!!Form::select('tax[]', $tax, explode(',',$quote->tax),array('class' => 'form-control','data-toggle'=>'select','multiple')) !!}
                                    @error('tax')
                                    <span class="invalid-tax" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('shipping_provider',__('Shipping Provider')) }}
                                    {!! Form::select('shipping_provider', $shipping_provider, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('shipping_provider')
                                    <span class="invalid-shipping_provider" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{Form::label('description',__('Description')) }}
                                    {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Name')))}}
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="mt-2 mb-2">
                                <h6>{{__('Assigned')}}</h6>
                            </div>
                            <div class="col-6">
                                {{Form::label('user',__('User')) }}
                                {!! Form::select('user', $user, $quote->user_id,array('class' => 'form-control','data-toggle'=>'select')) !!}
                                @error('user')
                                <span class="invalid-user" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="w-100 mt-3 text-right">
                                {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
            <!--account edit end-->

            <!--salesorders -->
            <div id="salesorders" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Sales Orders')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('salesorder.create',['quote',$quote->id]) }}" data-ajax-popup="true" data-title="{{__('Create New Sales Orders')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{__('Account')}}</th>
                                        <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Created At')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Amount')}}</th>
                                        <th scope="col" class="text-right">{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($salesorders as $salesorder)
                                        <tr>
                                            <td>
                                                <a href="{{ route('salesorder.show',$salesorder->id) }}" class="action-item" data-title="{{__('SalesOrders Details')}}">
                                                    {{ $salesorder->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" class="name mb-0 h6 text-sm"> {{ !empty($salesorder->accounts->name)?$salesorder->accounts->name:'-' }}</a>
                                            </td>
                                            <td>
                                                @if($salesorder->status == 0)
                                                    <span class="badge badge-info">{{ __(\App\SalesOrder::$status[$salesorder->status]) }}</span>
                                                @elseif($salesorder->status == 1)
                                                    <span class="badge badge-info">{{ __(\App\SalesOrder::$status[$salesorder->status]) }}</span>
                                                @elseif($salesorder->status == 2)
                                                    <span class="badge badge-info">{{ __(\App\SalesOrder::$status[$salesorder->status]) }}</span>
                                                @elseif($salesorder->status == 3)
                                                    <span class="badge badge-success">{{ __(\App\SalesOrder::$status[$salesorder->status]) }}</span>
                                                @elseif($salesorder->status == 4)
                                                    <span class="badge badge-warning">{{ __(\App\SalesOrder::$status[$salesorder->status]) }}</span>
                                                @elseif($salesorder->status == 5)
                                                    <span class="badge badge-danger">{{ __(\App\SalesOrder::$status[$salesorder->status]) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{\Auth::user()->dateFormat($salesorder->created_at)}}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{\Auth::user()->priceFormat($salesorder->amount)}}</span>
                                            </td>
                                            <td class="text-right">
                                                @can('Show SalesOrder')
                                                <a href="{{ route('salesorder.show',$salesorder->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" class="action-item" data-title="{{__('SalesOrders Details')}}">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('Edit SalesOrder')
                                                <a href="{{ route('salesorder.edit',$salesorder->id) }}" data-toggle="tooltip" data-original-title="{{__('Edit')}}" class="action-item" data-title="{{__('Edit SalesOrders')}}"><i class="far fa-edit"></i></a>
                                                @endcan
                                                @can('Delete SalesOrder')
                                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$salesorder->id}}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['salesorder.destroy', $salesorder->id],'id'=>'delete-form-'.$salesorder->id]) !!}
                                                {!! Form::close() !!}
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--salesorders end-->

            <!--account invoice -->
            <div id="invoice" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Invoice')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('invoice.create',['quote',$quote->id]) }}" data-ajax-popup="true" data-title="{{__('Create New Invoice')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{__('Account')}}</th>
                                        <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Created At')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Amount')}}</th>
                                        <th scope="col">{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <a href="{{ route('invoice.show',$invoice->id) }}" class="action-item" data-title="{{__('Invoice Details')}}">
                                                    {{ $invoice->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" class="name mb-0 h6 text-sm"> {{!empty( $invoice->accounts->name )? $invoice->accounts->name:'-'}}</a>
                                            </td>
                                            <td>
                                                @if($invoice->status == 0)
                                                    <span class="badge badge-info">{{ __(\App\Invoice::$status[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 1)
                                                    <span class="badge badge-info">{{ __(\App\Invoice::$status[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 2)
                                                    <span class="badge badge-info">{{ __(\App\Invoice::$status[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 3)
                                                    <span class="badge badge-success">{{ __(\App\Invoice::$status[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 4)
                                                    <span class="badge badge-warning">{{ __(\App\Invoice::$status[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 5)
                                                    <span class="badge badge-danger">{{ __(\App\Invoice::$status[$invoice->status]) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{\Auth::user()->dateFormat($invoice->created_at)}}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{\Auth::user()->priceFormat($invoice->amount)}}</span>
                                            </td>
                                            <td class="text-right">
                                                @can('Show Invoice')
                                                <a href="{{ route('invoice.show',$invoice->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" class="action-item" data-title="{{__('Invoice Details')}}">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('Edit Invoice')
                                                <a href="{{ route('invoice.edit',$invoice->id) }}" data-toggle="tooltip" data-original-title="{{__('Edit')}}" class="action-item" data-title="{{__('Edit Invoice')}}"><i class="far fa-edit"></i></a>
                                                @endcan
                                                @can('Delete Invoice')
                                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$invoice->id}}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $invoice->id],'id'=>'delete-form-'.$invoice->id]) !!}
                                                {!! Form::close() !!}
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--account invoice end-->
        </div>
    </div>
@endsection
@push('script-page')

    <script>
        $(document).on('click', '#billing_data', function () {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })

        $(document).on('change', 'select[name=opportunity]', function () {

            var opportunities = $(this).val();
            console.log(opportunities);
            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {
            $.ajax({
                url: '{{route('quote.getaccount')}}',
                type: 'POST',
                data: {
                    "opportunities_id": opportunities_id, "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    console.log(data);
                    $('#amount').val(data.opportunitie.amount);
                    $('#name').val(data.opportunitie.name);
                    $('#account_name').val(data.account.name);
                    $('#account_id').val(data.account.id);
                    $('#billing_address').val(data.account.billing_address);
                    $('#shipping_address').val(data.account.shipping_address);
                    $('#billing_city').val(data.account.billing_city);
                    $('#billing_state').val(data.account.billing_state);
                    $('#shipping_city').val(data.account.shipping_city);
                    $('#shipping_state').val(data.account.shipping_state);
                    $('#billing_country').val(data.account.billing_country);
                    $('#billing_postalcode').val(data.account.billing_postalcode);
                    $('#shipping_country').val(data.account.shipping_country);
                    $('#shipping_postalcode').val(data.account.shipping_postalcode);

                }
            });
        }

    </script>
@endpush
