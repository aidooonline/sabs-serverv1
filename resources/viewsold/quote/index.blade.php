@extends('layouts.admin')
@section('page-title')
    {{__('Quote')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Quote')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Quote')}}</li>
@endsection
@section('action-btn')
    @can('Create Quote')
        <a href="#" data-size="lg" data-url="{{ route('quote.create',['quote',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Quote Item')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection

@section('content')
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('ID')}}</th>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('Account')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Created At')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Amount')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Assign User')}}</th>
                    @if(Gate::check('Show Quote') || Gate::check('Edit Quote') || Gate::check('Delete Quote'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($quotes as $quote)
                    <tr>
                        <td>
                            <a href="{{ route('quote.show',$quote->id) }}" class="action-item" data-title="{{__('Quote Details')}}">
                                {{\Auth::user()->quoteNumberFormat($quote->quote_id)}}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('quote.show',$quote->id) }}" class="action-item" data-title="{{__('Quote Details')}}">
                                {{ ucfirst($quote->name) }}
                            </a>
                        </td>
                        <td>
                            {{ ucfirst(!empty($quote->accounts)?$quote->accounts->name:'--') }}
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <span class="badge badge-dot">{{\Auth::user()->dateFormat($quote->created_at)}}</span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{\Auth::user()->priceFormat($quote->getTotal())}}</span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm">{{ ucfirst(!empty($quote->assign_user)?$quote->assign_user->name:'-')}}</span></span>
                        </td>
                        @if(Gate::check('Show Quote') || Gate::check('Edit Quote') || Gate::check('Delete Quote'))
                            <td class="text-right">
                                @if($quote->converted_salesorder_id == 0)
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Convert to Sales Order')}}" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('You want to confirm convert to sales order. Press Yes to continue or Cancel to go back')}}" data-confirm-yes="document.getElementById('quotes-form-{{$quote->id}}').submit();">
                                        <i class="fas fa-exchange-alt"></i>
                                        {!! Form::open(['method' => 'get', 'route' => ['quote.convert', $quote->id],'id'=>'quotes-form-'.$quote->id]) !!}
                                        {!! Form::close() !!}
                                    </a>
                                @else
                                    <a href="{{ route('salesorder.show',$quote->converted_salesorder_id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Sales Order Details')}}" data-title="{{__('SalesOrders Details')}}">
                                        <i class="fab fa-stack-exchange"></i>
                                    </a>
                                @endif
                                @can('Show Quote')
                                    <a href="{{ route('quote.show',$quote->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('Quote Details')}}">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Quote')
                                    <a href="{{ route('quote.edit',$quote->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit Quote')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Quote')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$quote->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['quote.destroy', $quote->id],'id'=>'delete-form-'.$quote->id]) !!}
                                    {!! Form::close() !!}
                                @endcan
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
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
