@extends('layouts.admin')
@section('page-title')
    {{__('Account')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Account')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Account')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('account.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create Account')
        <a href="#" data-size="lg" data-url="{{ route('account.create',['account',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Account')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($accounts as $account)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" src="{{asset(url("./storage/upload/profile/profile.png"))}}" />
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            <a href="#" data-size="lg" data-url="{{ route('account.show',$account->id) }}" data-ajax-popup="true" data-title="{{__('Account Details')}}" class="action-item">
                                {{ ucfirst($account->name)}}
                            </a>
                        </h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted">{{ $account->email }}</a></div>

                        @if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))

                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show Account')
                                    <a href="#" data-size="lg" data-url="{{ route('account.show',$account->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Account Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Account')
                                    <a href="{{ route('account.edit',$account->id) }}" data-toggle="tooltip" data-original-title="{{__('Edit')}}" class="action-item" data-title="{{__('Account Edit')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Account')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$account->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['account.destroy', $account->id],'id'=>'delete-form-'.$account->id]) !!}
                                {!! Form::close() !!}
                                @endcan
                            </div>

                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <span class="btn-inner--icon text-sm small"><span data-toggle="tooltip" data-placement="top" title="Phone">{{ $account->phone}}</span></span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
