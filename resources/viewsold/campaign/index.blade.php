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
    <a href="{{ route('campaign.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
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
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('Type')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Budget')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Assigned User')}}</th>
                    @if(Gate::check('Show Campaign') || Gate::check('Edit Campaign') || Gate::check('Delete Campaign'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($campaigns as $campaign)
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('campaign.show',$campaign->id) }}" data-ajax-popup="true" data-title="{{__('Campaign Details')}}" class="action-item">
                                {{ ucfirst($campaign->name) }}
                            </a>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot"> {{ ucfirst(!empty($campaign->types->name)?$campaign->types->name:'-') }}</a>
                        </td>
                        <td>
                            @if($campaign->status == 0)
                                <span class="badge badge-warning">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                            @elseif($campaign->status == 1)
                                <span class="badge badge-success">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                            @elseif($campaign->status == 2)
                                <span class="badge badge-danger">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                            @elseif($campaign->status == 3)
                                <span class="badge badge-info">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-dot">{{$campaign->budget}}</span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm">{{ ucfirst(!empty($campaign->assign_user)?$campaign->assign_user->name:'-')}}</span></span>
                        </td>
                       
                        @if(Gate::check('Show Campaign') || Gate::check('Edit Campaign') || Gate::check('Delete Campaign'))
                            <td class="text-right">
                                @can('Show Campaign')
                                    <a href="#" data-size="lg" data-url="{{ route('campaign.show',$campaign->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Campaign Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Campaign')
                                    <a href="{{ route('campaign.edit',$campaign->id) }}" data-toggle="tooltip" data-original-title="{{__('Edit')}}" class="action-item" data-title="{{__('Edit Campaign')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Campaign')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$campaign->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['campaign.destroy', $campaign->id],'id'=>'delete-form-'.$campaign->id]) !!}
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
