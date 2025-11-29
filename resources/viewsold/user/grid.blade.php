@extends('layouts.admin')
@section('page-title')
    {{__('User')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Users')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('User')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('user.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create User')
        <a href="javascript:setunique();"  data-size="lg" data-url="{{ route('user.create') }}" data-ajax-popup="true" data-title="{{__('Create New User')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endif
@endsection
@section('filter')
@endsection

@section('content')
@if(\Auth::user()->type != 'super admin')
    <div class="row">
        @foreach($users as $user)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($user->avatar)) src="{{(!empty($user->avatar))? asset(Storage::url("upload/profile/".$user->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$user->name}}" @endif>
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            <a href="#" data-size="lg" data-url="{{ route('user.show',$user->id) }}" data-ajax-popup="true" data-title="{{__('User Details')}}" class="action-item">
                                {{ ucfirst($user->name)}}
                            </a>
                        </h5>
                        <div class="mb-1"><span href="#" class="text-sm small text-muted">{{ $user->email }}</span></div>

                        @if(Gate::check('Create User') || Gate::check('Edit User') || Gate::check('Delete User'))
                            <div class="actions d-flex justify-content-between px-4">
                                @can('Create User')
                                    <a href="#" data-size="lg" data-url="{{ route('user.show',$user->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('User Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit User')
                                    <a href="{{ route('user.edit',$user->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete User')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$user->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id],'id'=>'delete-form-'.$user->id]) !!}
                                {!! Form::close() !!}
                                @endcan
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <span class="btn-inner--icon text-sm small"><span data-toggle="tooltip" data-placement="top" title="Phone">{{ $user->phone}}</span></span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="row">
        @foreach($users as $user)
            <div class="col-lg-3 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" src="{{ asset(Storage::url("upload/profile/")).'/'}}{{ !empty($user->avatar)?$user->avatar:'avatar.png' }}" class="avatar  rounded-circle avatar-lg">
                        </div>
                        <h5 class="h6 mt-4 mb-0"> {{$user->name}}</h5>
                        <a href="#" class="d-block text-sm text-muted mb-3"> {{$user->email}}</a>
                            <div class="actions d-flex justify-content-between pl-6">
                            @can('Edit User')
                                <a href="{{ route('user.edit',$user->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                    <i class="far fa-edit"></i>
                                </a>
                            @endcan
                        
                                <a href="#" class="action-item" data-size="lg" data-url="{{ route('plan.upgrade',$user->id) }}" data-ajax-popup="true" data-toggle="tooltip" data-title="{{__('Upgrade Plan')}}">
                                    <i class="fas fa-trophy"></i>
                                </a>
                        
                            @can('Delete User')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$user->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id],'id'=>'delete-form-'.$user->id]) !!}
                                {!! Form::close() !!}
                            @endcan

                        </div>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id],'id'=>'delete-form-'.$user->id]) !!}
                        {!! Form::close() !!}
                    </div>
                    <div class="card-body border-top">
                        <div class="row justify-content-between align-items-center">
                            <div class="col text-center">
                                <span class="d-block h4 mb-0">{{$user->countUser($user->id)}}</span>
                                <span class="d-block text-sm text-muted">{{__('User')}}</span>
                            </div>
                            <div class="col text-center">
                                <span class="d-block h4 mb-0">{{$user->countAccount($user->id)}}</span>
                                <span class="d-block text-sm text-muted">{{__('Account')}}</span>
                            </div>
                            <div class="col text-center">
                                <span class="d-block h4 mb-0">{{$user->countContact($user->id)}}</span>
                                <span class="d-block text-sm text-muted">{{__('Contact')}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="actions d-flex justify-content-between">
                            <span class="d-block text-sm text-muted"> {{__('Plan') }} :  {{!empty($user->currentPlan)?$user->currentPlan->name:__('Free')}}</span>

                        </div>
                        <div class="actions d-flex justify-content-between mt-1">
                            <span class="d-block text-sm text-muted">{{__('Plan Expired') }} : {{!empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date):'Unlimited'}}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
@endsection
