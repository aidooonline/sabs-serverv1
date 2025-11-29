@extends('layouts.admin')
@section('page-title')
    {{__('Invoice')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Invoice')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Invoice')}}</li>
@endsection
@section('action-btn')
    @can('Create Invoice')
        <a href="#" data-size="lg" data-url="{{ route('invoice.create',['invoice',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Invoice Item')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="id">{{__('ID')}}</th>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('Account')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Created At')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Amount')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                    @if(Gate::check('Show Invoice') || Gate::check('Edit Invoice') || Gate::check('Delete Invoice'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($invoices as $invoice)
                    <tr>
                        <td>
                            <a href="{{ route('invoice.show',$invoice->id) }}" class="action-item" data-title="{{__('Quote Details')}}">
                                {{\Auth::user()->invoiceNumberFormat($invoice->invoice_id)}}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('invoice.show',$invoice->id) }}" class="badge badge-dot action-item" data-title="{{__('Invoice Details')}}">
                                {{ ucfirst($invoice->name) }}
                            </a>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot"> {{ ucfirst(!empty( $invoice->accounts)? $invoice->accounts->name:'--') }}</a>
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
                            <span class="badge badge-dot">{{\Auth::user()->priceFormat($invoice->getTotal())}}</span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{ ucfirst(!empty($invoice->assign_user)?$invoice->assign_user->name:'-')}}</span>
                        </td>
                        @if(Gate::check('Show Invoice') || Gate::check('Edit Invoice') || Gate::check('Delete Invoice'))
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
                url: '{{route('invoice.getaccount')}}',
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
