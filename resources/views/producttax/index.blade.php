@extends('layouts.admin')
@section('page-title')
    {{__('Product Tax')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Product Tax')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Product Tax')}}</li>
@endsection
@section('action-btn')
    @can('Create ProductTax')
        <a href="#" data-size="lg" data-url="{{ route('product_tax.create') }}" data-ajax-popup="true" data-title="{{__('Create New Product Tax')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="name">{{__('Tax Name')}}</th>
                    <th scope="col" class="sort" data-sort="name">{{__('Rate %')}}</th>
                    @if(Gate::check('Edit ProductTax') || Gate::check('Delete ProductTax'))
                        <th class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($product_taxs as $product_tax)
                    <tr>
                        <td class="sorting_1">{{$product_tax->tax_name}}</td>
                        <td class="sorting_1">{{$product_tax->rate}}</td>
                        @if(Gate::check('Edit ProductTax') || Gate::check('Delete ProductTax'))
                            <td class="action text-right">
                                @can('Edit ProductTax')
                                    <a href="#" data-size="lg" data-url="{{ route('product_tax.edit',$product_tax->id) }}" data-ajax-popup="true" data-title="{{__('Edit type')}}" class="action-item">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can('Delete ProductTax')
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$product_tax->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['product_tax.destroy', $product_tax->id],'id'=>'delete-form-'.$product_tax->id]) !!}
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
