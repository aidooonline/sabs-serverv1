@extends('layouts.admin')
@section('page-title')
    {{__('Common case')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Common case')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Case')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('commoncases.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create CommonCase')
        <a href="#" data-size="lg" data-url="{{ route('commoncases.create',['commoncases',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Common case')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($commonCases as $commonCase)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($commonCase->avatar)) src="{{(!empty($commonCase->avatar))? asset(Storage::url("upload/profile/".$commonCase->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$commonCase->name}}" @endif>
                        </div>
                        <h5 class="h6 mt-4 mb-1">{{ $commonCase->name}}</h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Account Name">{{ !empty($commonCase->accounts)? $commonCase->accounts->name:'-' }}</a></div>
                    </div>
                    <div class="card-footer text-center">
                        @if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase'))
                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show CommonCase')
                                    <a href="#" data-size="lg" data-url="{{ route('commoncases.show',$commonCase->id) }}" data-ajax-popup="true" data-title="{{__('Create New Common case')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit CommonCase')
                                    <a href="{{ route('commoncases.edit',$commonCase->id) }}" class="action-item" data-title="{{__('Edit Common case')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete CommonCase')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$commonCase->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['commoncases.destroy', $commonCase->id],'id'=>'delete-form-'.$commonCase->id]) !!}
                                {!! Form::close() !!}
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
