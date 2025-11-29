@extends('layouts.admin')
@section('page-title')
    {{__('Properties')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Properties')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Properties')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('product.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
    </a>
    @can('Create Product')
        <a href="#" data-size="lg" data-url="{{ route('product.create') }}" data-ajax-popup="true" data-title="{{__('Create New Property')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('content')
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="Brand">{{__('Category')}}</th>
                    <th scope="col" class="sort" data-sort="Status">{{__('Status')}}</th>
                    <th scope="col" class="sort" data-sort="Price">{{__('Price')}}</th>
                    <th scope="col" class="sort" data-sort="No of Bedrooms">{{__('No of Bedrooms')}}</th>
                    <th style="display:none;" scope="col" class="sort" data-sort="assign User">{{__('assign User')}}</th>
                    @if(Gate::check('Show Product') || Gate::check('Edit Property') || Gate::check('Delete Product'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($products as $product)
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('product.show',$product->id) }}" data-ajax-popup="true" data-title="{{__('Product Details')}}" class="badge badge-dot action-item">
                                {{ ucfirst($product->name) }}
                            </a>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot"> {{ ucfirst($product->brands->name) }}</a>
                        </td>
                        <td>
                            @if($product->status == 0)
                                <span class="badge badge-success">{{ __(\App\Product::$status[$product->status]) }}</span>
                            @elseif($product->status == 1)
                                <span class="badge badge-danger">{{ __(\App\Product::$status[$product->status]) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-dot">{{\Auth::user()->priceFormat($product->price)}}</span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm">{{ ucfirst(!empty($product->assign_user)?$product->assign_user->name:'-')}}</span></span>
                        </td>
                        @if(Gate::check('Show Product') || Gate::check('Edit Product') || Gate::check('Delete Product'))
                            <td class="text-right">
                                @can('Show Product')
                                    <a href="#" data-size="lg" data-url="{{ route('product.show',$product->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Product Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Product')
                                    <a href="{{ route('product.edit',$product->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit Product')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Product')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$product->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id],'id'=>'delete-form-'.$product->id]) !!}
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
