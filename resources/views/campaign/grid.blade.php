@extends('layouts.admin')
@section('page-title')
    {{__('Campaign')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Campaign')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Campaign')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('campaign.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create Campaign')
        <a href="#" data-size="lg" data-url="{{ route('campaign.create',['campaign',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Campaign')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($campaigns as $campaign)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($campaign->avatar)) src="{{(!empty($campaign->avatar))? asset(Storage::url("upload/profile/".$campaign->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$campaign->name}}" @endif>
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            {{ ucfirst($campaign->name)}}
                        </h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Status">
                                @if($campaign->status == 0)
                                    <span class="badge badge-warning">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                                @elseif($campaign->status == 1)
                                    <span class="badge badge-success">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                                @elseif($campaign->status == 2)
                                    <span class="badge badge-danger">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                                @elseif($campaign->status == 3)
                                    <span class="badge badge-info">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    @if(Gate::check('Show Campaign') || Gate::check('Edit Campaign') || Gate::check('Delete Campaign'))
                        <div class="card-footer text-center">
                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show Campaign')
                                    <a href="#" data-size="lg" data-url="{{ route('campaign.show',$campaign->id) }}" data-ajax-popup="true" data-title="{{__('Create New Campaign')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Campaign')
                                    <a href="{{ route('campaign.edit',$campaign->id) }}" class="action-item" data-title="{{__('Edit Campaign')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Campaign')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$campaign->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['campaign.destroy', $campaign->id],'id'=>'delete-form-'.$campaign->id]) !!}
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

