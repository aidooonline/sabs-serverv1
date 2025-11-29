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
    <a href="{{ route('user.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
    </a>
    @can('Create User')
        <a href="#" data-size="lg" data-url="{{ route('user.create') }}" data-ajax-popup="true" data-title="{{__('Create New User')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="card">
        <div class="actions-toolbar border-0">
            <div class="actions-search" id="actions-search">
                <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control form-control-flush" placeholder="Type and hit enter ...">
                    <div class="input-group-append">
                        <a href="#" class="input-group-text bg-transparent" data-action="search-close" data-target="#actions-search"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    
                    <th scope="col" class="sort" data-sort="username">{{__('Avatar')}}</th>
                    <th scope="col" class="sort" data-sort="username">{{__('User Name')}}</th>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="email">{{__('Email')}}</th>
                    @if(\Auth::user()->type != 'super admin')
                        <th scope="col" class="sort" data-sort="title">{{__('Type')}}</th>    
                        <th scope="col" class="sort" data-sort="isactive">{{__('Status')}}</th>
                    @endif
                    @if(Gate::check('Edit User') || Gate::check('Delete User'))
                    <th class="text-right" scope="col">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($users as $user)
           
                    <tr>
                        <td>
                            <img alt=""  src="{{ asset(Storage::url("upload/profile/")).'/'}}{{ !empty($user->avatar)?$user->avatar:'avatar.png' }}"  width="30px" class="rounded-circle">
                        
                            <span class="position-relative padtop"><a href="#" class="name h6 text-sm ml-2"> </a></span>
                        </td>
                        <td class="budget">
                            <a href="#" data-size="lg" data-url="{{ route('user.show',$user->id) }}" data-ajax-popup="true" data-title="{{__('User Details')}}" class="action-item">
                                {{ ucfirst($user->username) }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-dot">
                                 <a href="#" data-size="lg" data-url="{{ route('user.show',$user->id) }}" data-ajax-popup="true" data-title="{{__('User Details')}}" class="action-item">
                                    {{ ucfirst($user->name) }}
                                 </a>
                            </span>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot">{{ $user->email }}</a>
                        </td>
                        @if(\Auth::user()->type != 'super admin')
                        <td>
                            {{ ucfirst($user->type) }}
                        </td>
                        <td>
                            @if($user->is_active == 1)
                                <span class="badge badge-success">{{__('Active')}}</span>
                            @else
                                <span class="badge badge-danger">{{__('In Active')}}</span>
                            @endif
                        </td>
                        @endif
                        @if(Gate::check('Edit User') || Gate::check('Delete User'))
                            <td class="text-right">
                                @can('Show User')
                                    <a href="#" data-size="lg" data-url="{{ route('user.show',$user->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('User Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Manage Plan')
                                <a href="#" class="action-item" data-size="lg" data-url="{{ route('plan.upgrade',$user->id) }}" data-ajax-popup="true" data-toggle="tooltip" data-title="{{__('Upgrade Plan')}}">
                                    <i class="fas fa-trophy"></i>
                                </a>
                                @endcan
                                @can('Edit User')
                                    <a href="{{ route('user.edit',$user->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit User')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete User')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$user->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id],'id'=>'delete-form-'.$user->id]) !!}
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
