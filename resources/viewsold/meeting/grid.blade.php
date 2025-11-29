@extends('layouts.admin')
@section('page-title')
    {{__('Meeting')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Meeting')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Meeting')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('meeting.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create Meeting')
        <a href="#" data-size="lg" data-url="{{ route('meeting.create') }}" data-ajax-popup="true" data-title="{{__('Create New Meeting')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($meetings as $meeting)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($meeting->avatar)) src="{{(!empty($meeting->avatar))? asset(Storage::url("upload/profile/".$meeting->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$meeting->name}}" @endif>
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            {{ ucfirst($meeting->name)}}
                        </h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Status">
                                @if($meeting->status == 0)
                                    <span class="badge badge-success">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                                @elseif($meeting->status == 1)
                                    <span class="badge badge-warning">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                                @elseif($meeting->status == 2)
                                    <span class="badge badge-danger">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-center">

                        @if(Gate::check('Show Meeting') || Gate::check('Edit Meeting') || Gate::check('Delete Meeting'))

                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show Meeting')
                                    <a href="#" data-size="lg" data-url="{{ route('meeting.show',$meeting->id) }}" data-ajax-popup="true" data-title="{{__('Create New Meeting')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Meeting')
                                    <a href="{{ route('meeting.edit',$meeting->id) }}" class="action-item" data-title="{{__('Edit Meeting')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Meeting')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$meeting->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['meeting.destroy', $meeting->id],'id'=>'delete-form-'.$meeting->id]) !!}
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
