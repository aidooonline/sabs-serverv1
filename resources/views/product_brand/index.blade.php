@extends('layouts.admin')
@section('page-title')
    {{__('Product Brand')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Product Brand')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Product Brand')}}</li>
@endsection
@section('action-btn')
    @can('Create ProductBrand')
        <a href="#" data-size="lg" data-url="{{ route('product_brand.create') }}" data-ajax-popup="true" data-title="{{__('Create New Product Brand')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="name">{{__('name')}}</th>
                    @if(Gate::check('Edit ProductBrand') || Gate::check('Delete ProductBrand'))
                        <th class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($product_brands as $product_brand)
                    <tr>
                        <td class="sorting_1">{{$product_brand->name}}</td>
                        @if(Gate::check('Edit ProductBrand') || Gate::check('Delete ProductBrand'))
                            <td class="action text-right">
                                @can('Edit ProductBrand')
                                    <a href="#" data-size="lg" data-url="{{ route('product_brand.edit',$product_brand->id) }}" data-ajax-popup="true" data-title="{{__('Edit type')}}" class="action-item">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can('Detele ProductBrand')
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$product_brand->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['product_brand.destroy', $product_brand->id],'id'=>'delete-form-'.$product_brand->id]) !!}
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
