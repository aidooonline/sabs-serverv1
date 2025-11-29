@extends('layouts.admin')
@section('page-title')
    {{__('Quote')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Quote')}} {{ '('. $quote->name .')' }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('quote.index')}}">{{__('Quote')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Details')}}</li>
@endsection
@section('action-btn')
    <a href="{{route('quote.pdf',\Crypt::encrypt($quote->id))}}" target="_blank" class="btn btn-sm btn-primary btn-icon rounded-pill">
        <span class="btn-inner--icon text-white"><i class="fa fa-print"></i></span>
        <span class="btn-inner--text text-white">{{__('Print')}}</span>
    </a>
    @can('Edit Quote')
        <a href="{{ route('quote.edit',$quote->id) }}" class="btn btn-sm btn-primary bor-radius" data-title="{{__('Quote Edit')}}"><i class="far fa-edit"></i>
        </a>
    @endcan
    <a href="#" data-size="lg" data-url="{{ route('quote.quoteitem',$quote->id) }}" data-ajax-popup="true" data-title="{{__('Create New Quote')}}" class="btn btn-sm btn-primary bor-radius">
        <i class="fa fa-plus">{{__(' Add Item')}}</i>
    </a>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <dl class="row">
                        <div class="col-12">
                            <div class="row align-items-center mb-5">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                </div>
                                <div class="col-sm-6 text-sm-right">
                                    <h6 class="d-inline-block m-0 d-print-none">{{__('Quote ')}}</h6>

                                    @if($quote->status == 0)
                                        <span class="badge badge-info">{{ __(\App\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 1)
                                        <span class="badge badge-info">{{ __(\App\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 2)
                                        <span class="badge badge-info">{{ __(\App\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 3)
                                        <span class="badge badge-success">{{ __(\App\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 4)
                                        <span class="badge badge-warning">{{ __(\App\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 5)
                                        <span class="badge badge-danger">{{ __(\App\Quote::$status[$quote->status]) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-8">
                                    <h6 class="d-inline-block m-0 d-print-none">{{__('Quote ID')}}</h6>
                                    <span class="col-sm-8"><span class="text-sm">{{ \Auth::user()->quoteNumberFormat($quote->id) }}</span></span>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-lg-6 col-md-8">
                                    <h6 class="d-inline-block m-0 d-print-none">{{__('Quote Date')}}</h6>
                                    <span class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($quote->date_quoted)}}</span></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h5>{{__('Item List')}}</h5>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                            <tr>
                                                <th class="px-0 bg-transparent border-top-0">{{__('Item')}}</th>
                                                <th class="px-0 bg-transparent border-top-0">{{__('Quantity')}}</th>
                                                <th class="px-0 bg-transparent border-top-0">{{__('Price')}}</th>
                                                <th class="px-0 bg-transparent border-top-0">{{__('Tax')}}</th>
                                                <th class="px-0 bg-transparent border-top-0">{{__('Discount')}}</th>
                                                <th class="px-0 bg-transparent border-top-0">{{__('Description')}}</th>
                                                <th class="px-0 bg-transparent border-top-0 text-right">{{__('Price')}}</th>
                                                <th class="px-0 bg-transparent border-top-0 text-right">#</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $totalQuantity=0;
                                                $totalRate=0;
                                                $totalAmount=0;
                                                $totalTaxPrice=0;
                                                $totalDiscount=0;
                                                $taxesData=[];
                                            @endphp
                                            @foreach($quote->items as $quoteitem)
                                                @php
                                                    $taxes=\Utility::tax($quoteitem->tax);
                                                    $totalQuantity+=$quoteitem->quantity;
                                                    $totalRate+=$quoteitem->price;
                                                    $totalDiscount+=$quoteitem->discount;

                                                    if(!empty($taxes[0]))
                                                    {
                                                        foreach($taxes as $taxe)
                                                        {
                                                            $taxDataPrice=\Utility::taxRate($taxe->rate,$quoteitem->price,$quoteitem->quantity);
                                                            if (array_key_exists($taxe->tax_name,$taxesData))
                                                            {
                                                                $taxesData[$taxe->tax_name] = $taxesData[$taxe->tax_name]+$taxDataPrice;
                                                            }
                                                            else
                                                            {
                                                                $taxesData[$taxe->tax_name] = $taxDataPrice;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="px-0">{{(!empty($quoteitem->items->name)?$quoteitem->items->name:'')}} </td>
                                                    <td class="px-0">{{$quoteitem->quantity}} </td>
                                                    <td class="px-0">{{\Auth::user()->priceFormat($quoteitem->price)}} </td>
                                                    <td class="px-0">
                                                        <div class="col">
                                                            @if(!empty($quoteitem->tax))
                                                                @foreach($quoteitem->tax($quoteitem->tax) as $tax)
                                                                    @php
                                                                        $taxPrice=\Utility::taxRate($tax->rate,$quoteitem->price,$quoteitem->quantity);
                                                                        $totalTaxPrice+=$taxPrice;
                                                                    @endphp
                                                                    <a href="#!" class="d-block text-sm text-muted">{{$tax->tax_name .' ('.$tax->rate .'%)'}} &nbsp;&nbsp;{{\Auth::user()->priceFormat($taxPrice)}}</a>
                                                                @endforeach
                                                            @else
                                                                <a href="#!" class="d-block text-sm text-muted">{{__('No Tax')}}</a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-0">{{\Auth::user()->priceFormat($quoteitem->discount)}} </td>
                                                    <td class="px-0">{{!empty($quoteitem->description)?$quoteitem->description:'--'}} </td>
                                                    <td class="text-right">{{\Auth::user()->priceFormat($quoteitem->price*$quoteitem->quantity)}}</td>
                                                    <td>
                                                        @can('Edit Quote')
                                                            <a href="#" data-url="{{ route('quote.quoteitem.edit',$quoteitem->id) }}" data-ajax-popup="true" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit Quote')}}"><i class="far fa-edit"></i></a>
                                                        @endcan
                                                        @can('Delete Quote')
                                                            <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$quoteitem->id}}').submit();">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['quote.items.delete', $quoteitem->id],'id'=>'delete-form-'.$quoteitem->id]) !!}
                                                            {!! Form::close() !!}
                                                        @endcan
                                                    </td>
                                                    @php
                                                        $totalQuantity+=$quoteitem->quantity;
                                                        $totalRate+=$quoteitem->price;
                                                        $totalDiscount+=$quoteitem->discount;
                                                        $totalAmount+=($quoteitem->price*$quoteitem->quantity);
                                                    @endphp

                                                </tr>
                                            @endforeach
                                            <tfoot>
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td class="px-0"></td>
                                                <td class="text-right"><strong>{{__('Sub Total')}}</strong></td>
                                                <td class="text-right subTotal">{{\Auth::user()->priceFormat($quote->getSubTotal())}}</td>
                                                <td></td>
                                            </tr>

                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td class="px-0"></td>
                                                <td class="text-right"><strong>{{__('Discount')}}</strong></td>
                                                <td class="text-right subTotal">{{\Auth::user()->priceFormat($quote->getTotalDiscount())}}</td>
                                                <td></td>
                                            </tr>
                                            @if(!empty($taxesData))
                                                @foreach($taxesData as $taxName => $taxPrice)
                                                    @if($taxName != 'No Tax')
                                                        <tr>
                                                            <td colspan="4"></td>
                                                            <td class="px-0"></td>
                                                            <td class="text-right"><b>{{$taxName}}</b></td>
                                                            <td class="text-right">{{ \Auth::user()->priceFormat($taxPrice) }}</td>
                                                            <td></td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td class="px-0"></td>
                                                <td class="text-right"><strong>{{__('Total')}}</strong></td>
                                                <td class="text-right subTotal">{{\Auth::user()->priceFormat($quote->getTotal())}}</td>
                                                <td></td>
                                            </tr>
                                            </tfoot>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card my-5 bg-secondary">
                                        <div class="card-body">
                                            <div class="row justify-content-between align-items-center">
                                                <div class="col-md-6 order-md-2 mb-4 mb-md-0">
                                                    <div class="d-flex align-items-center justify-content-md-end">
                                                        <span class="h6 text-muted d-inline-block mr-3 mb-0">{{__('Total value')}}:</span>
                                                        <span class="h4 mb-0">{{\Auth::user()->priceFormat($quote->getTotal())}}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 order-md-1">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <h5>{{__('From')}}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Company Address')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_address'] }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Company City')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_city'] }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Company Country')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_country'] }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Zip Code')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_zipcode'] }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Company Contact')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_telephone']}}</span></dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-4">
                                    <h5>{{ __('Billing Address') }}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Billing Address')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $quote->billing_address }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Billing City')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $quote->billing_city }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Billing Country')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $quote->billing_country }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Zip Code') }}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $quote->billing_postalcode }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Billing Contact')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ !empty($quote->contacts->name)?$quote->contacts->name:'--'}}</span></dd>
                                    </dl>
                                </div>
                                @if(\Utility::getValByName('shipping_display')=='on')
                                    <div class="col-12 col-md-4">
                                        <h5>{{ __('Shipping Address') }}</h5>
                                        <dl class="row mt-4 align-items-center">
                                            <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Shipping Address')}}</span></dt>
                                            <dd class="col-sm-8"><span class="text-sm">{{ $quote->shipping_address }}</span></dd>

                                            <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Shipping City')}}</span></dt>
                                            <dd class="col-sm-8"><span class="text-sm">{{ $quote->shipping_city }}</span></dd>

                                            <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Shipping Country')}}</span></dt>
                                            <dd class="col-sm-8"><span class="text-sm">{{ $quote->shipping_country }}</span></dd>

                                            <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Zip Code')}}</span></dt>
                                            <dd class="col-sm-8"><span class="text-sm">{{ $quote->shipping_postalcode }}</span></dd>

                                            <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Shipping Contact')}}</span></dt>
                                            <dd class="col-sm-8"><span class="text-sm">{{ !empty($quote->contacts->name)?$quote->contacts->name:'--'}}</span></dd>
                                        </dl>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-footer py-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                                <dd class="col-sm-12"><span class="text-sm">{{ !empty($quote->assign_user)?$quote->assign_user->name:''}}</span></dd>

                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                                <dd class="col-sm-12"><span class="text-sm">{{ $quote->created_at }}</span></dd>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')

    <script>
        $(document).on('change', 'select[name=item]', function () {
            var item_id = $(this).val();
            $.ajax({
                url: '{{route('quote.items')}}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'item_id': item_id,
                },
                cache: false,
                success: function (data) {
                    var invoiceItems = JSON.parse(data);
                    $('.taxId').val('');
                    $('.tax').html('');

                    $('.price').val(invoiceItems.price);
                    $('.quantity').val(1);
                    $('.discount').val(0);
                    var taxes = '';
                    var tax = [];

                    for (var i = 0; i < invoiceItems.taxes.length; i++) {
                        taxes += '<span class="badge badge-primary mr-1 mt-1">' + invoiceItems.taxes[i].tax_name + ' ' + '(' + invoiceItems.taxes[i].rate + '%)' + '</span>';
                    }
                    $('.taxId').val(invoiceItems.tax);
                    $('.tax').html(taxes);
                }
            });
        });
    </script>
@endpush
