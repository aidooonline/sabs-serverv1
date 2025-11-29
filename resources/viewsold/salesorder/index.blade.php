@extends('layouts.admin')
@section('page-title')
    {{__('Sales Order')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Sales Order')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Sales Order')}}</li>
@endsection
@section('action-btn')
    @can('Create SalesOrder')
        <a href="#" data-size="lg" data-url="{{ route('salesorder.create',['salesorder',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Sales Order')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                    @if(Gate::check('Show SalesOrder') || Gate::check('Edit SalesOrder') || Gate::check('Delete SalesOrder'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($salesorders as $salesorder)
                    <tr>
                        <td>
                            <a href="{{ route('salesorder.show',$salesorder->id) }}" class="action-item" data-title="{{__('Quote Details')}}">
                                {{\Auth::user()->salesorderNumberFormat($salesorder->salesorder_id)}}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('salesorder.show',$salesorder->id) }}" class="badge badge-dot action-item" data-title="{{__('SalesOrders Details')}}">
                                {{ ucfirst($salesorder->name) }}
                            </a>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot"> {{  ucfirst(!empty($salesorder->accounts)?$salesorder->accounts->name:'--')}}</a>
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
                          
                            <span class="badge badge-dot">{{\Auth::user()->priceFormat($salesorder->getTotal())}}</span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{ ucfirst(!empty($salesorder->assign_user)?$salesorder->assign_user->name:'-')}}</span>
                        </td>
                        @if(Gate::check('Show SalesOrder') || Gate::check('Edit SalesOrder') || Gate::check('Delete SalesOrder'))
                            <td class="text-right">
                                @can('Show SalesOrder')
                                    <a href="{{ route('salesorder.show',$salesorder->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('SalesOrders Details')}}">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit SalesOrder')
                                    <a href="{{ route('salesorder.edit',$salesorder->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit SalesOrders')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete SalesOrder')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$salesorder->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['salesorder.destroy', $salesorder->id],'id'=>'delete-form-'.$salesorder->id]) !!}
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

            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {
            
            $.ajax({
                url: '{{route('salesorder.getaccount')}}',
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
