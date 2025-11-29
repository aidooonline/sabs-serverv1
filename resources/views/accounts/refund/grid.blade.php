@extends('layouts.admin')
@section('page-title')
    {{__('Product')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Product')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Product')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('product.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create Product')
        <a href="#" data-size="lg" data-url="{{ route('product.create') }}" data-ajax-popup="true" data-title="{{__('Create New Product')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($products as $product)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($product->avatar)) src="{{(!empty($product->avatar))? asset(Storage::url("upload/profile/".$product->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$product->name}}" @endif>
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            <a href="#" data-size="lg" data-url="{{ route('product.show',$product->id) }}" data-ajax-popup="true" data-title="{{__('Create New Product')}}" class="action-item badge badge-dot">
                                {{ ucfirst($product->name)}}
                            </a>
                        </h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Status">
                                @if($product->status == 0)
                                    <span class="badge badge-success">{{ __(\App\Product::$status[$product->status]) }}</span>
                                @elseif($product->status == 1)
                                    <span class="badge badge-danger">{{ __(\App\Product::$status[$product->status]) }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    @if(Gate::check('Show Product') || Gate::check('Edit Product') || Gate::check('Delete Product'))
                        <div class="card-footer text-center">
                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show Product')
                                    <a href="#" data-size="lg" data-url="{{ route('product.show',$product->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Create New Product')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Product')
                                    <a href="{{ route('product.edit',$product->id) }}" data-toggle="tooltip" data-original-title="{{__('Edit')}}" class="action-item" data-title="{{__('Edit Product')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Product')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$product->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id],'id'=>'delete-form-'.$product->id]) !!}
                                    {!! Form::close() !!}
                                @endcan
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection

