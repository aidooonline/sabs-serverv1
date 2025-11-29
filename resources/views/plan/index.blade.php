@extends('layouts.admin')
@php
    $dir= asset(Storage::url('uploads/plan'));
@endphp
@push('script-page')
@endpush
@section('page-title')
    {{__('Plan')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{__('Plans')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Plan')}}</li>
@endsection
@section('action-btn')
    @if(\Auth::user()->type == 'super admin')
        <a href="#" data-url="{{ route('plan.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create New Plan')}}" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-4" data-toggle="tooltip">
            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
        </a>
    @endif
@endsection
@section('content')
    <div class="row">
        @foreach($plans as $plan)
            <div class="col-md-3">
                <div class="card card-fluid">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{$plan->name}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    @if( \Auth::user()->type == 'super admin')
                                        <a title="Edit Plan" data-size="lg" href="#" class="action-item" data-url="{{ route('plan.edit',$plan->id) }}" data-ajax-popup="true" data-title="{{__('Edit Plan')}}" data-toggle="tooltip" data-original-title="{{__('Edit')}}"><i class="fas fa-edit"></i></a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center {{!empty(\Auth::user()->type != 'super admin')?'plan-box':''}}">
                        <a href="#" class="avatar rounded-circle avatar-lg hover-translate-y-n3">
                            <img alt="Image placeholder" src="{{$dir.'/'.$plan->image}}" class="">
                        </a>

                        <h5 class="h6 my-4"> {{env('CURRENCY_SYMBOL').$plan->price.' / '.$plan->duration}}</h5>

                        @if(\Auth::user()->type=='owner' && \Auth::user()->plan == $plan->id)
                            <h5 class="h6 my-4">
                                {{__('Expired : ')}} {{\Auth::user()->plan_expire_date ? \Auth::user()->dateFormat(\Auth::user()->plan_expire_date):__('Unlimited')}}
                            </h5>

                        @endif

                        <p class="my-4">{{$plan->description}}</p>

                        @if(\Auth::user()->type == 'owner' && \Auth::user()->plan == $plan->id)
                            <span class="clearfix"></span>
                            <span class="badge badge-pill badge-success">{{__('Active')}}</span>
                        @endif
                        @if(($plan->id != \Auth::user()->plan) && \Auth::user()->type!='super admin' )
                            @if($plan->price > 0)
                                <a class="badge badge-pill badge-primary" href="{{route('stripe',\Illuminate\Support\Facades\Crypt::encrypt($plan->id))}}" data-toggle="tooltip" data-original-title="{{__('Buy Plan')}}">
                                    <i class="fas fa-cart-plus"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-4 text-center">
                                <span class="h5 mb-0">{{$plan->max_user}}</span>
                                <span class="d-block text-sm">{{__('Users')}}</span>
                            </div>
                            <div class="col-4 text-center">
                                <span class="h5 mb-0">{{$plan->max_account}}</span>
                                <span class="d-block text-sm"> {{__('Accounts')}}</span>
                            </div>
                            <div class="col-4 text-center">
                                <span class="h5 mb-0">{{$plan->max_contact}}</span>
                                <span class="d-block text-sm"> {{__('Contacts')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

