@extends('layouts.admin')
@section('page-title')
    {{__('Campaign Edit')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Campaign Edit')}} {{ '('. $campaign->name .')' }}</h5>
    </div>
@endsection
@section('action-btn')
    <div class="btn-group" role="group">
        @if(!empty($previous))
            <a href="{{ route('campaign.edit',$previous) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif
        @if(!empty($next))
            <a href="{{ route('campaign.edit',$next) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="{{__('Next')}}">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action disabled" data-toggle="tooltip" data-original-title="{{__('Next')}}">
                <i class="fas fa-chevron-right"></i>
            </a>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('campaign.index')}}">{{__('Campaign')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Details')}}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#account_edit" class="list-group-item custom-list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-user"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Overview')}}</a>
                                <p class="mb-0 text-sm">{{__('Edit about your campaign information')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accountopportunities" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-handshake"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Opportunities')}}</a>
                                <p class="mb-0 text-sm">{{__('Assign opportunities for this campaign')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#lead" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-address-card"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Lead')}}</a>
                                <p class="mb-0 text-sm">{{__('Assign lead for this campaign')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <!--account edit -->
            <div id="account_edit" class="tabs-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0">{{__('Overview')}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{Form::model($campaign,array('route' => array('campaign.update', $campaign->id), 'method' => 'PUT')) }}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('name',__('Name')) }}
                                    {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
                                    @error('name')
                                    <span class="invalid-name" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                {{Form::label('status',__('Status')) }}
                                {!!Form::select('status', $status, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                @error('status')
                                <span class="invalid-status" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                {{Form::label('type',__('Type')) }}
                                {!!Form::select('type', $type, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                @error('type')
                                <span class="invalid-type" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                {{Form::label('start_date',__('Start Date')) }}
                                {!!Form::date('start_date', null,array('class' => 'form-control','required'=>'required')) !!}
                                @error('start_date')
                                <span class="invalid-start_date" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                {{Form::label('budget',__('Budget')) }}
                                {{Form::text('budget',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
                                @error('budget')
                                <span class="invalid-budget" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('end_date',__('End Date')) }}
                                    {!!Form::date('end_date', null,array('class' => 'form-control','required'=>'required')) !!}
                                    @error('end_date')
                                    <span class="invalid-end_date" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                {{Form::label('target_list',__('Target Lists')) }}
                                {!!Form::select('target_list', $target_list, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                @error('target_list')
                                <span class="invalid-target_list" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                {{Form::label('excludingtarget_list',__('Excluding Target Lists')) }}
                                {!!Form::select('excludingtarget_list', $target_list, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                @error('excludingtarget_list')
                                <span class="invalid-excludingtarget_list" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{Form::label('description',__('Description')) }}
                                    {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))}}
                                    @error('description')
                                    <span class="invalid-description" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="mt-2 mb-2">
                                <h6>{{__('Assigned')}}</h6>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('user',__('User')) }}
                                    {!! Form::select('user', $user, $campaign->user_id,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                    @error('user')
                                    <span class="invalid-user" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="w-100 mt-3 text-right">
                                {{Form::submit(__('Update'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
            <!--account edit end-->

            <!--account lead -->
            <div id="lead" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Leads')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('lead.create',['campaign',$campaign->id]) }}" data-ajax-popup="true" data-title="{{__('Create New Lead')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{__('Email')}}</th>
                                        <th scope="col" class="sort" data-sort="status">{{__('Phone')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('City')}}</th>
                                        @if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                                            <th scope="col" class="text-right">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($leads as $lead)
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="{{ route('lead.show',$lead->id) }}" data-ajax-popup="true" data-title="{{__('Lead Details')}}" class="action-item">
                                                    {{ $lead->name }}
                                                </a>
                                            </td>
                                            <td class="budget">
                                                <a href="#">{{ $lead->email }}</a>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">
                                                    {{ $lead->phone }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{ $lead->lead_city }}</span>
                                            </td>
                                            @if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                                            <td class="text-right">
                                                <div class="d-flex">
                                                    @can('Show Lead')
                                                    <a href="#" data-size="lg" data-url="{{ route('lead.show',$lead->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Lead Details')}}" class="action-item">
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
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--account lead end-->

                    <!--account opportunities -->
            <div id="accountopportunities" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Opportunities')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('opportunities.create',['campaign',$campaign->id]) }}" data-ajax-popup="true" data-title="{{__('Create New Opportunities')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <!-- Files -->
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{__('Account')}}</th>
                                        <th scope="col" class="sort" data-sort="status">{{__('Stage')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Amount')}}</th>
                                        @if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                                            <th scope="col" class="text-right">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($opportunitiess as $opportunities)
                                        <tr>
                                            <td>
                                                <a href="#" class="name mb-0 h6 text-sm"> {{ $opportunities->name }}</a>
                                            </td>
                                            <td class="budget">
                                                <a href="#">{{ !empty($opportunities->accounts)?$opportunities->accounts->name:''  }}</a>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">
                                                    <a href="#">{{ !empty( $opportunities->stages->name)? $opportunities->stages->name:''  }}</a>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{  !empty($opportunities->assign_user)?$opportunities->assign_user->name:'' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{\Auth::user()->priceFormat($opportunities->amount)}}</span>
                                            </td>
                                            @if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                                            <td class="text-right">
                                                @can('Show Opportunities')
                                                <a href="#" data-size="lg" data-url="{{ route('opportunities.show', $opportunities->id) }}" data-ajax-popup="true" data-title="{{__('show Opportunities')}}" class="action-item">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('Edit Opportunities')
                                                <a href="{{ route('opportunities.edit',$opportunities->id) }}" class="action-item" data-title="{{__('Edit Opportunities')}}"><i class="far fa-edit"></i></a>
                                                @endcan
                                                @can('Delete Opportunities')
                                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$opportunities->id}}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunities->id],'id'=>'delete-form-'.$opportunities ->id]) !!}
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
                    </div>
                </div>
            </div>
            <!--account opportunities end-->

        </div>
    </div>
@endsection
