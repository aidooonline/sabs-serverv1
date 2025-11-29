@extends('layouts.admin')
@section('page-title')
    {{__('Lead')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Leads')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Lead')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('lead.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Kanban View')}}
    </a>
    @can('Create Lead')
        <a href="#" data-size="lg" data-url="{{ route('lead.create',['lead',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Lead')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
            <table class="align-items-left dataTable table-sm table-striped table-hover table-light">
                <thead>
                <tr >
                    
                    <th scope="col" style="padding-left:15px !important;" class="sort" data-sort="created_at">{{__('Datetime')}}</th>
                    <th scope="col" class="sort" style="width:40px !important;" data-sort="lead_temperature">{{__('Response')}}</th>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="country">{{__('Ctry')}}</th>
                    <th scope="col" class="sort" data-sort="name">{{__('Status')}}</th>
                   {{-- <th scope="col" class="sort" data-sort="completion">{{__('Account')}}</th>--}}
                    <th scope="col" class="sort" data-sort="budget">{{__('Email')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Phone')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Assigned user')}}</th>
                    @if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($leads as $lead)
                    <tr>
                       
                                <td title="{{\Auth::user()->dateFormat($lead->created_at)}}" style="padding-left:15px !important;"> 
                           {{$lead->created_at->diffForHumans()}}  
                        </td>
                        <td>
                            <span class=@if($lead->lead_temperature == '1'){{"coldlead"}}@elseif($lead->lead_temperature == '2'){{"warmlead"}}@else{{"hotlead"}} @endif>
                                @if($lead->lead_temperature == '1'){{'cold'}}
                                @elseif($lead->lead_temperature == '2'){{'warm'}}
                                @else{{'hot'}} 
                                @endif
                            </span>
                        </td>
                        @php
                        $leadcountry =\App\Country::getcountry($lead->lead_country);
                        $leadiso =\App\Country::getcountryiso($lead->lead_country); 
                        @endphp
                       
                      
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('lead.show',$lead->id) }}" data-ajax-popup="true" data-title="{{__('Lead Details')}}" class="action-item">
                                {{ ucfirst($lead->name) }}
                            </a>
                        </td>

                        <td>
                            <span class="badge badge-dot">
                                <span title="{{$leadcountry[0]}}" class="flag-icon flag-icon-{{strtolower($leadiso[0])}}"></span>
                                
                            </span>
                        </td>

                        <td>
                            @if($lead  ->status == 0)
                            <span class="badge text-success" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                        @elseif($lead->status == 1)
                            <span class="badge text-info" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                        @elseif($lead->status == 2)
                            <span class="badge text-warning" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                        @elseif($lead->status == 3)
                            <span class="badge text-danger"  style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                        @elseif($lead->status == 4)
                            <span class="badge text-danger" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                        @elseif($lead->status == 5)
                            <span class="badge text-warning" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                        @endif
                        </td>
                      {{-- <td>
                            <span class="badge badge-dot">{{ ucfirst(!empty($lead->accounts)?$lead->accounts->name:'--')}}</span>
                        </td> --}} 
                        <td class="budget">
                            <a href="#" class="badge badge-dot">{{ $lead->email }}</a>
                        </td>
                        <td>
                            <span class="badge badge-dot">
                                {{ $lead->phone }}
                            </span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm">{{ ucfirst(!empty($lead->assign_user)?$lead->assign_user->name:'')}}</span></span>
                        </td>
                        @if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                            <td class="text-right" style="padding-right:15px !important;">
                                @can('Show Lead')
                                    <a href="#" data-size="lg" data-url="{{ route('lead.show',$lead->id) }}" data-ajax-popup="true" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('Lead Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Lead')
                                    <a href="{{ route('lead.edit',$lead->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit Lead')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Lead')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$lead->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['lead.destroy', $lead->id],'id'=>'delete-form-'.$lead ->id]) !!}
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
